<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Algolia\AlgoliaSearch\Api\SearchClient;
use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Generator;
use phpDocumentor\Guides\Nodes\CompoundNode;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\ParagraphNode;
use phpDocumentor\Guides\Nodes\TitleNode;

use function assert;
use function md5;
use function str_replace;
use function strip_tags;

/**
 * Influenced by Laravel.com website code search indexes that also use Algolia.
 *
 * @phpstan-type headers = array{h1: string|null, h2: string|null, h3: string|null, h4: string|null, h5: string|null}
 * @phpstan-type searchRecord = array{
 *     objectID: string,
 *     rank: int,
 *     h1: string|null,
 *     h2: string|null,
 *     h3: string|null,
 *     h4: string|null,
 *     h5: string|null,
 *     url: string,
 *     content: string,
 *     projectName: string,
 *     _tags: string[],
 * }
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
        $this->client->setSettings(self::INDEX_NAME, [
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

        $this->client->clearObjects(self::INDEX_NAME);
    }

    /** @param DocumentNode[] $documents */
    public function buildSearchIndexes(
        Project $project,
        ProjectVersion $version,
        array $documents,
    ): void {
        $records = [];

        foreach ($documents as $document) {
            foreach ($this->buildDocumentSearchRecords($document, $project, $version) as $record) {
                $records[] = $record;
            }
        }

        $this->client->saveObjects(self::INDEX_NAME, $records, ['autoGenerateObjectIDIfNotExist' => true]);
    }

    /** @return Generator<searchRecord> */
    private function buildDocumentSearchRecords(
        DocumentNode $document,
        Project $project,
        ProjectVersion $version,
    ): Generator {
        $currentLink = $document->getFilePath() . '.html';

        $current = [
            'h1' => null,
            'h2' => null,
            'h3' => null,
            'h4' => null,
            'h5' => null,
        ];

        yield from $this->iterateNodes($document, $current, $currentLink, $project, $version);
    }

    /**
     * @param headers            $current
     * @param CompoundNode<Node> $node
     *
     * @return Generator<searchRecord>
     */
    private function iterateNodes(CompoundNode $node, array $current, string $currentLink, Project $project, ProjectVersion $version): Generator
    {
        foreach ($node->getChildren() as $child) {
            if ($child instanceof TitleNode) {
                yield $this->getNodeSearchRecord($child, $current, $currentLink, $project, $version);

                continue;
            }

            if ($child instanceof ParagraphNode) {
                yield $this->getNodeSearchRecord($child, $current, $currentLink, $project, $version);

                continue;
            }

            if (! ($child instanceof CompoundNode)) {
                continue;
            }

            foreach ($this->iterateNodes($child, $current, $currentLink, $project, $version) as $record) {
                yield $record;
            }
        }
    }

    /**
     * @param headers $current
     *
     * @return searchRecord
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
            assert($level >= 1 && $level <= 5);
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

            return $ranks[$elementName];
        }

        return $ranks[$elementName];
    }

    private function renderNodeValue(Node $node): string
    {
        if ($node instanceof TitleNode) {
            return $this->stripContent($node->toString());
        }

        if ($node instanceof ParagraphNode) {
            $content = '';
            foreach ($node->getChildren() as $child) {
                $content .= $this->stripContent($child->toString());
            }

            return $content;
        }

        return '';
    }

    private function stripContent(string $content): string
    {
        return str_replace(['"', '\''], '', $content);
    }
}
