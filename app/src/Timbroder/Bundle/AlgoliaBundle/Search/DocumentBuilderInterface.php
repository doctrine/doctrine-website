<?php

namespace Timbroder\Bundle\AlgoliaBundle\Search;

use Sculpin\Core\Source\AbstractSource;

/**
 * Document Builder interface
 *
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 */
interface DocumentBuilderInterface
{
    /**
     * Build a document for the search engine
     *
     * @param \Sculpin\Core\Source\AbstractSource $source
     */
    public function build(AbstractSource $source);
}
