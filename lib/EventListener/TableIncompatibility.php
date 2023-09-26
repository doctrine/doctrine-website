<?php

declare(strict_types=1);

namespace Doctrine\Website\EventListener;

use Doctrine\RST\Event\PreParseDocumentEvent;

use function str_contains;
use function str_replace;

final readonly class TableIncompatibility
{
    private const BEFORE = '| **SQL Server**           |         +----------------------------------------------------------+';
    private const AFTER  = '| **SQL Server**           |         |                                                          |';

    public function preParseDocument(PreParseDocumentEvent $event): void
    {
        if (! str_contains($event->getContents(), self::BEFORE)) {
            return;
        }

        $content = str_replace(self::BEFORE, self::AFTER, $event->getContents());
        $event->setContents($content);
    }
}
