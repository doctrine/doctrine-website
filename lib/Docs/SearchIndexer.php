<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\TitleNode;

use function is_string;
use function md5;
use function strip_tags;
use function strpos;

/**
 * Influenced by Laravel.com website code search indexes that also use Algolia.
 *
 * @final
 */
class SearchIndexer
{
    final public const INDEX_NAME = 'pages';

    public function __construct(
        private readonly SearchClient $client,
    ) {
    }

    public function initSearchIndex(): void
    {
        $index = $this->getSearchIndex();

        $index->setSettings([
            'attributesToIndex' => ['projectName', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'content'],
            'customRanking' => ['asc(rank)'],
            'ranking' => ['words', 'typo', 'attribute', 'proximity', 'custom'],
            'minWordSizefor1Typo' => 3,
            'minWordSizefor2Typos' => 7,
            'allowTyposOnNumericTokens' => false,
            'minProximity' => 2,
            'ignorePlurals' => true,
            'advancedSyntax' => true,
            'removeWordsIfNoResults' => 'allOptional',
        ]);

        $index->clearObjects();
    }

    /** @param DocumentNode[] $documents */
    public function buildSearchIndexes(
        Project $project,
        ProjectVersion $version,
        array $documents,
    ): void {
        $records = [];

        foreach ($documents as $document) {
            $this->buildDocumentSearchRecords($document, $records, $project, $version);
        }

        $this->getSearchIndex()->saveObjects($records, ['autoGenerateObjectIDIfNotExist' => true]);
    }

    /** @param mixed[][] $records */
    private function buildDocumentSearchRecords(
        DocumentNode $document,
        array &$records,
        Project $project,
        ProjectVersion $version,
    ): void {
        $currentLink = $slug = $document->getFilePath() . '.html';

        $current = [
            'h1' => null,
            'h2' => null,
            'h3' => null,
            'h4' => null,
            'h5' => null,
        ];

        $nodes = $document->getNodes(TitleNode::class);

        foreach ($nodes as $node) {
            $value = $this->renderNodeValue($node);

            if (strpos($value, '{{ DOCS_SOURCE_PATH') !== false) {
                continue;
            }

            $records[] = $this->getNodeSearchRecord(
                $node,
                $current,
                $currentLink . '#' . $node->getId(),
                $project,
                $version,
            );
        }
    }

    /**
     * @param string[] $current
     *
     * @return mixed[]
     */
    private function getNodeSearchRecord(
        Node $node,
        array &$current,
        string $currentLink,
        Project $project,
        ProjectVersion $version,
    ): array {
        $level = $node instanceof TitleNode ? $node->getLevel() : false;

        if ($level !== false) {
            $current['h' . $level] = $this->renderNodeValue($node);

            for ($i = $level + 1; $i <= 5; $i++) {
                $current['h' . $i] = null;
            }

            $content = '';
        } else {
            $content = $this->renderNodeValue($node);
        }

        return [
            'objectID' => $version->getSlug() . '-' . $currentLink . '-' . md5($this->renderNodeValue($node)),
            'rank' => $this->getRank($node),
            'h1' => $current['h1'],
            'h2'  => $current['h2'],
            'h3'  => $current['h3'],
            'h4'  => $current['h4'],
            'h5'  => $current['h5'],
            'url' => '/projects/' . $project->getDocsSlug() . '/en/' . $version->getSlug() . '/' . $currentLink,
            'content' => $content !== '' ? strip_tags($content) : '',
            'projectName' => $project->getShortName(),
            '_tags' => [
                $version->getSlug(),
                $project->getSlug(),
            ],
        ];
    }

    private function getRank(Node $node): int
    {
        $ranks = [
            'h1' => 0,
            'h2' => 1,
            'h3' => 2,
            'h4' => 3,
            'h5' => 4,
            'h6' => 5,
            'p'  => 6,
        ];

        $elementName = 'p';

        if ($node instanceof TitleNode) {
            $elementName = 'h' . $node->getLevel();
        } elseif ($node instanceof ParagraphNode) {
            $elementName = 'p';
        }

        return $ranks[$elementName];
    }

    private function renderNodeValue(Node $node): string
    {
        $nodeValue = $node->getValue();

        if ($nodeValue === null) {
            return '';
        }

        if (is_string($nodeValue)) {
            return $nodeValue;
        }

        return $nodeValue->render();
    }

    private function getSearchIndex(): SearchIndex
    {
        return $this->client->initIndex(self::INDEX_NAME);
    }
}
