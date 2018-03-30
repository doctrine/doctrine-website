<?php

namespace Doctrine\Website\SculpinRstBundle\Listener;

use Doctrine\Website\SculpinRstBundle\SculpinRstBundle;
use Sculpin\Core\Event\ConvertEvent;
use Sculpin\Core\Sculpin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DemoteHeadingsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Sculpin::EVENT_AFTER_CONVERT => ['afterConvert', 0],
        ];
    }

    public function afterConvert(ConvertEvent $event)
    {
        if ($event->converter() !== SculpinRstBundle::CONVERTER_NAME) {
            return;
        }

        // This is the only way to detect if we're getting a source that's already been converted. This happens when
        // something else, like a view template is changed during --watch.
        if ($event->source()->formattedContent()) {
            return;
        }

        $source = $event->source();
        $content = $source->content();

        $content = preg_replace_callback('~<(/)?h(\d)~', function (array $matches) {
            return sprintf('<%sh%d', $matches[1], min(6, $matches[2]+1));
        }, $content);

        $source->setContent($content);
    }
}
