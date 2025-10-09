<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

interface SourceFileReader
{
    /** @return SourceFiles<SourceFile> */
    public function getSourceFiles(string $buildDir = ''): SourceFiles;
}
