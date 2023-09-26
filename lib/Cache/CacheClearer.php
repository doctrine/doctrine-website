<?php

declare(strict_types=1);

namespace Doctrine\Website\Cache;

use Symfony\Component\Filesystem\Filesystem;

use function array_filter;
use function glob;
use function sprintf;

final readonly class CacheClearer
{
    public function __construct(
        private Filesystem $filesystem,
        private string $rootDir,
    ) {
    }

    /** @return string[] */
    public function clear(string $buildDir): array
    {
        // clear build directory
        $remove = [$buildDir];

        // built rst docs
        $remove[] = sprintf(
            '%s/source/projects/*',
            $this->rootDir,
        );

        $remove[] = sprintf(
            '%s/cache/*',
            $this->rootDir,
        );

        $matches = [];

        foreach ($remove as $glob) {
            $globMatches = $this->glob($glob);

            foreach ($globMatches as $match) {
                $matches[] = $match;
            }
        }

        $dirs = array_filter($matches, 'is_dir');

        foreach ($dirs as $path) {
            $this->filesystem->remove($path);
        }

        return $dirs;
    }

    /** @return string[] */
    protected function glob(string $pattern): array
    {
        return array_filter((array) glob($pattern));
    }
}
