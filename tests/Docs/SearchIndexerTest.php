<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Docs;

use Algolia\AlgoliaSearch\SearchClient;
use Algolia\AlgoliaSearch\SearchIndex;
use Doctrine\Common\EventManager;
use Doctrine\RST\Builder;
use Doctrine\RST\Configuration;
use Doctrine\RST\Event\PreNodeRenderEvent;
use Doctrine\RST\Kernel;
use Doctrine\Website\Docs\SearchIndexer;
use Doctrine\Website\Event\NodeValue;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function sys_get_temp_dir;

class SearchIndexerTest extends TestCase
{
    private SearchClient&MockObject $client;

    private SearchIndexer $searchIndexer;

    protected function setUp(): void
    {
        $this->client = $this->createMock(SearchClient::class);

        $this->searchIndexer = new SearchIndexer(
            $this->client,
        );
    }

    public function testInitSearchIndex(): void
    {
        $index = $this->createMock(SearchIndex::class);

        $this->client->expects(self::once())
            ->method('initIndex')
            ->with(SearchIndexer::INDEX_NAME)
            ->willReturn($index);

        $index->expects(self::once())
            ->method('setSettings')
            ->with([
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

        $index->expects(self::once())
            ->method('clearIndex');

        $this->searchIndexer->initSearchIndex();
    }

    public function testBuildSearchIndexes(): void
    {
        $project = $this->createProject([
            'shortName' => 'ORM',
            'docsSlug' => 'doctrine-orm',
            'slug' => 'orm',
        ]);
        $version = new ProjectVersion(['slug' => '1.0']);

        $index = $this->createMock(SearchIndex::class);

        $this->client->expects(self::once())
            ->method('initIndex')
            ->with(SearchIndexer::INDEX_NAME)
            ->willReturn($index);

        $configuration = new Configuration();
        $configuration->setUseCachedMetas(false);
        $kernel  = new Kernel($configuration);
        $builder = new Builder($kernel);

        $builder->build(__DIR__ . '/resources/search-indexer', sys_get_temp_dir() . '/search-indexer');

        $documents = $builder->getDocuments()->getAll();

        $expectedRecords = [
            [
                'objectID' => '1.0-index.html-8cf04a9734132302f96da8e113e80ce5',
                'rank' => 0,
                'h1' => 'Home',
                'h2' => null,
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-0aed5e2da8c50d700cc1aafd30de809e',
                'rank' => 1,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-dc4fb8880607a7c6b97dde83611834bc',
                'rank' => 6,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => 'Home Section 1 Content',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-a041c6359be2ca2e25e7acf458595316',
                'rank' => 2,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-fdc52e368cad3bcea0424d1e90a52ec7',
                'rank' => 6,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => 'Home Section 2 Content',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-3161aa72d4d81b937bda257622f5892e',
                'rank' => 3,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => 'Home Section 3',
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-0474a95e4451409dbba46e596d225dae',
                'rank' => 6,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => 'Home Section 3',
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => 'Home Section 3 Content',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-271aeb79d10e9e0f4b0be8476cff12e4',
                'rank' => 4,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => 'Home Section 3',
                'h5' => 'Home Section 4',
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-29903ed67d76dc043116657bb5b0fc5d',
                'rank' => 6,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => 'Home Section 2',
                'h4' => 'Home Section 3',
                'h5' => 'Home Section 4',
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => 'Home Section 4 Content',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
        ];

        $index->expects(self::once())
            ->method('addObjects')
            ->with($expectedRecords);

        $this->searchIndexer->buildSearchIndexes($project, $version, $documents);
    }

    public function testBuildSearchIndexesContainingQuotes(): void
    {
        $project = $this->createProject([
            'shortName' => 'ORM',
            'docsSlug' => 'doctrine-orm',
            'slug' => 'orm',
        ]);
        $version = new ProjectVersion(['slug' => '1.0']);

        $index = $this->createMock(SearchIndex::class);

        $this->client->expects(self::once())
            ->method('initIndex')
            ->with(SearchIndexer::INDEX_NAME)
            ->willReturn($index);

        $eventManager = new EventManager();
        $eventManager->addEventListener(PreNodeRenderEvent::PRE_NODE_RENDER, new NodeValue());
        $configuration = new Configuration();
        $configuration->setEventManager($eventManager);
        $configuration->setUseCachedMetas(false);
        $kernel  = new Kernel($configuration);
        $builder = new Builder($kernel);

        $builder->build(__DIR__ . '/resources/search-indexer-with-quotes', sys_get_temp_dir() . '/search-indexer-with-quotes');

        $documents = $builder->getDocuments()->getAll();

        $expectedRecords = [
            [
                'objectID' => '1.0-index.html-8cf04a9734132302f96da8e113e80ce5',
                'rank' => 0,
                'h1' => 'Home',
                'h2' => null,
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-0aed5e2da8c50d700cc1aafd30de809e',
                'rank' => 1,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => '',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
            [
                'objectID' => '1.0-index.html-dc4fb8880607a7c6b97dde83611834bc',
                'rank' => 6,
                'h1' => 'Home',
                'h2' => 'Home Section 1',
                'h3' => null,
                'h4' => null,
                'h5' => null,
                'url' => '/projects/doctrine-orm/en/1.0/index.html',
                'content' => 'Home Section 1 Content',
                'projectName' => 'ORM',
                '_tags' => ['1.0', 'orm'],
            ],
        ];

        $index->expects(self::once())
            ->method('addObjects')
            ->with($expectedRecords);

        $this->searchIndexer->buildSearchIndexes($project, $version, $documents);
    }
}
