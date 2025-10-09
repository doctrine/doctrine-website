<?php

declare(strict_types=1);

namespace Doctrine\Website\StaticGenerator\SourceFile;

interface SourceFileConverter
{
    /** @return string[] */
    public function getExtensions(): array;

    public function convertSourceFile(SourceFile $sourceFile): string;
}
