<?php

namespace Timbroder\Bundle\AlgoliaBundle\Search;

/**
 * Class AlgoliaSearchEngine
 * @author Tim Broder <timothy.broder@gmail.com>
 */
class AlgoliaSearchEngine implements SearchEngineInterface
{
    /**
     * @var \AlgoliaSearch\Client
     */
    private $client;

    /**
     * @var \AlgoliaSearch\Index
     */
    private $index;

    /**
     * @var
     */
    private $clearOnSync;

    /**
     * AlgoliaSearchEngine constructor.
     * @param \AlgoliaSearch\Client $client
     * @param $index
     */
    public function __construct(\AlgoliaSearch\Client $client, $index, $clearOnSync)
    {
        $this->client = $client;
        $this->index = $client->initIndex($index);
        $this->clearOnSync = $clearOnSync;

        // $this->index->setSettings([
        //     "attributesToIndex" => ["h1", "h2", "h3", "h4", "projectName"],
        //     "attributesForFaceting" => []
        // ]);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkAdd(array $documents)
    {
        $this->index->addObjects($documents);
    }

    /**
     * {@inheritDoc}
     */
    public function synchronize(array $documents)
    {
        if ( $this->clearOnSync ) {
            $this->index->clearIndex();
        }

        $this->index->addObjects($documents);
    }
}
