<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        echo sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), $level, $message) . PHP_EOL;
    }
}
