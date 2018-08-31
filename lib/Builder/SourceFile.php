<?php

declare(strict_types=1);

namespace Doctrine\Website\Builder;

use DateTimeImmutable;
use function count;
use function explode;
use function in_array;
use function sprintf;
use function strpos;
use function strtotime;

class SourceFile
{
    private const TWIG_EXTENSIONS = ['html', 'md', 'rst', 'xml', 'txt'];

    private const NEEDS_LAYOUT_EXTENSIONS = ['html', 'md', 'rst'];

    private const MARKDOWN_EXTENSION = 'md';

    private const RESTRUCTURED_TEXT_EXTENSION = 'rst';

    /** @var string */
    private $extension;

    /** @var string */
    private $sourcePath;

    /** @var string */
    private $writePath;

    /** @var string */
    private $contents;

    /** @var SourceFileParameters */
    private $parameters;

    public function __construct(
        string $extension,
        string $sourcePath,
        string $writePath,
        string $contents,
        SourceFileParameters $parameters
    ) {
        $this->extension  = $extension;
        $this->sourcePath = $sourcePath;
        $this->writePath  = $writePath;
        $this->contents   = $contents;
        $this->parameters = $parameters;
    }

    public function getSourcePath() : string
    {
        return $this->sourcePath;
    }

    public function getWritePath() : string
    {
        return $this->writePath;
    }

    public function getUrl() : string
    {
        return (string) $this->parameters->getParameter('url');
    }

    public function getDate() : DateTimeImmutable
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

    public function getExtension() : string
    {
        return $this->extension;
    }

    public function isMarkdown() : bool
    {
        return $this->getExtension() === self::MARKDOWN_EXTENSION;
    }

    public function isRestructuredText() : bool
    {
        return $this->getExtension() === self::RESTRUCTURED_TEXT_EXTENSION;
    }

    public function isTwig() : bool
    {
        return in_array($this->getExtension(), self::TWIG_EXTENSIONS, true) && $this->isApiDocs() === false;
    }

    public function isLayoutNeeded() : bool
    {
        return in_array($this->getExtension(), self::NEEDS_LAYOUT_EXTENSIONS, true);
    }

    public function isApiDocs() : bool
    {
        return strpos($this->getUrl(), '/api/') === 0;
    }

    public function getContents() : string
    {
        return $this->contents;
    }

    public function getParameters() : SourceFileParameters
    {
        return $this->parameters;
    }

    /**
     * @return mixed
     */
    public function getParameter(string $key)
    {
        return $this->parameters->getParameter($key);
    }
}
