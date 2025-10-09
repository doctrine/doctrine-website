<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use Symfony\Component\Yaml\Yaml;

use function preg_match;

class SourceFileParametersFactory
{
    public function createSourceFileParameters(string $contents): SourceFileParameters
    {
        $parameters = $this->extractParameters($contents);

        if (! isset($parameters['layout'])) {
            $parameters['layout'] = 'default';
        }

        if (! isset($parameters['title'])) {
            $parameters['title'] = '';
        }

        return new SourceFileParameters($parameters);
    }

    /** @return mixed[] */
    private function extractParameters(string $contents): array
    {
        $parameters = [];

        if (preg_match('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', $contents, $matches) > 0) {
            if (preg_match('/^(\s*[-]+\s*|\s*)$/', $matches[1]) === 0) {
                $parameters = Yaml::parse($matches[1], 1);
            }
        }

        return $parameters;
    }
}
