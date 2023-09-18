<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\EventListener;

use Doctrine\RST\Event\PreParseDocumentEvent;
use Doctrine\RST\Parser;
use Doctrine\Website\EventListener\TableIncompatibility;
use PHPUnit\Framework\TestCase;

class TableIncompatibilityTest extends TestCase
{
    public function testPreParseDocumentNothingToChange(): void
    {
        $parser = self::createStub(Parser::class);
        $event  = new PreParseDocumentEvent($parser, 'content');

        $eventListener = new TableIncompatibility();
        $eventListener->preParseDocument($event);

        self::assertSame('content', $event->getContents());
    }

    public function testPreParseDocumentTableChange(): void
    {
        $content = '#| **SQL Server**           |         +----------------------------------------------------------+#';

        $parser = self::createStub(Parser::class);
        $event  = new PreParseDocumentEvent($parser, $content);

        $eventListener = new TableIncompatibility();
        $eventListener->preParseDocument($event);

        $expected = '#| **SQL Server**           |         |                                                          |#';

        self::assertSame($expected, $event->getContents());
    }
}
