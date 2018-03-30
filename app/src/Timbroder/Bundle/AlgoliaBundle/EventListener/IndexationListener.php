<?php

namespace Timbroder\Bundle\AlgoliaBundle\EventListener;

use Sculpin\Core\Sculpin;
use Sculpin\Core\Event\SourceSetEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Timbroder\Bundle\AlgoliaBundle\Search\SearchEngineInterface;
use Timbroder\Bundle\AlgoliaBundle\Search\DocumentBuilderInterface;


/**
 * Class IndexationListener
 * @author Jonathan Bouzekri <jonathan.bouzekri@gmail.com>
 */
class IndexationListener implements EventSubscriberInterface
{

    public function __construct(SearchEngineInterface $searchEngine, DocumentBuilderInterface $builder, $enabled = true)
    {
        $this->searchEngine = $searchEngine;
        $this->documentBuilder = $builder;
        $this->enabled = $enabled;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Sculpin::EVENT_AFTER_RUN => 'afterRun',
        );
    }
    /**
     * Override enabled parameter on runtime
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
    /**
     * Index on after run event
     *
     * @param \Sculpin\Core\Event\SourceSetEvent $event
     */
    public function afterRun(SourceSetEvent $event)
    {
        if (!$this->enabled) {
            return;
        }

        $documents = array();
        foreach ($event->allSources() as $item) {
            if ($item->data()->get('indexed')) {
                foreach ($this->documentBuilder->build($item) as $document) {
                    $documents[] = $document;
                }
            }
        }

        $this->searchEngine->synchronize($documents);
    }

}
