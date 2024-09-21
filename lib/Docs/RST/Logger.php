<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Psr\Log\AbstractLogger;
use Stringable;

use function date;
use function sprintf;

use const PHP_EOL;

class Logger extends AbstractLogger
{
    /** @param mixed[] $context */
    public function log(mixed $level, Stringable|string $message, array $context = []): void
    {
        echo sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), $level, $message) . PHP_EOL;
    }
}
