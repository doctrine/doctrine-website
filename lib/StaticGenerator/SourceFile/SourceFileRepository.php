<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

class SourceFileRepository implements SourceFileReader
{
    /** @param SourceFileReader[] $sourceFileReaders */
    public function __construct(private array $sourceFileReaders = [])
    {
    }

    public function getSourceFiles(string $buildDir = ''): SourceFiles
    {
        $sourceFiles = [];

        foreach ($this->sourceFileReaders as $sourceFileReader) {
            foreach ($sourceFileReader->getSourceFiles($buildDir) as $sourceFile) {
                $sourceFiles[] = $sourceFile;
            }
        }

        return new SourceFiles($sourceFiles);
    }
}
