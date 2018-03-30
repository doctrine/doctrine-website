<?php

namespace Timbroder\Bundle\AlgoliaBundle\Search;

/**
 * Search engine interface
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 */
interface SearchEngineInterface
{
    /**
     * Add a bulk of documents to the index
     *
     * @param array $documents
     */
    public function bulkAdd(array $documents);

    /**
     * Synchronize index
     * Remove documents from index not present in the array
     * add documents from the array in the index
     */
    public function synchronize(array $documents);
}
