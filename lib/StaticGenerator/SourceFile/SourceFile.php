<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

use function count;
use function explode;
use function in_array;
use function pathinfo;
use function preg_replace;
use function sprintf;
use function strtotime;

use const PATHINFO_EXTENSION;

class SourceFile
{
    private const array TWIG_EXTENSIONS = ['html', 'md', 'rst', 'xml', 'txt'];

    private const array NEEDS_LAYOUT_EXTENSIONS = ['html', 'md', 'rst'];

    private string $contents;

    public function __construct(
        private string $sourcePath,
        string $contents,
        private SourceFileParameters $parameters,
    ) {
        $this->contents = $this->stripFileParameters($contents);
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function getUrl(): string
    {
        return (string) $this->parameters->getParameter('url');
    }

    public function getDate(): DateTimeImmutable
    {
        $e = explode('/', $this->getUrl());

        if (count($e) < 4) {
            return new DateTimeImmutable();
        }

        $date = strtotime(sprintf('%s/%s/%s', $e[1], $e[2], $e[3]));

        if ($date === false) {
            return new DateTimeImmutable();
        }

        return (new DateTimeImmutable())->setTimestamp($date);
    }

    public function getExtension(): string
    {
        return pathinfo($this->sourcePath, PATHINFO_EXTENSION);
    }

    public function isTwig(): bool
    {
        return in_array($this->getExtension(), self::TWIG_EXTENSIONS, true);
    }

    public function isLayoutNeeded(): bool
    {
        return in_array($this->getExtension(), self::NEEDS_LAYOUT_EXTENSIONS, true);
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getParameters(): SourceFileParameters
    {
        return $this->parameters;
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameters->getParameter($key);
    }

    public function hasController(): bool
    {
        return $this->parameters->getParameter('_controller') !== null;
    }

    /** @return string[]|null */
    public function getController(): array|null
    {
        return $this->parameters->getParameter('_controller');
    }

    public function getRequest(): Request
    {
        $requestAttributes = $this->parameters->getAll();

        $requestAttributes['sourceFile'] = $this;

        $request = Request::create($this->getUrl());
        $request->attributes->replace($requestAttributes);

        return $request;
    }

    private function stripFileParameters(string $contents): string
    {
        $result = preg_replace('/^\s*(?:---[\s]*[\r\n]+)(.*?)(?:---[\s]*[\r\n]+)(.*?)$/s', '$2', $contents);

        if ($result === null) {
            throw new RuntimeException('An error occurred running preg_match');
        }

        return $result;
    }
}
