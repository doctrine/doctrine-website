<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\Builder;

use Doctrine\Website\Builder\SourceFile;
use Doctrine\Website\Builder\SourceFileRepository;
use PHPUnit\Framework\TestCase;
use function assert;
use function is_string;
use function realpath;
use function strcmp;
use function usort;

class SourceFileRepositoryTest extends TestCase
{
    /** @var SourceFileRepository */
    private $sourceFileRepository;

    public function testGetFilesWithoutCustomInPath() : void
    {
        $files = $this->sortFiles($this->sourceFileRepository->getFiles(''));

        self::assertCount(6, $files);

        self::assertSame('html', $files[0]->getExtension());
        self::assertSame('/api/inflector.html', $files[0]->getWritePath());
        self::assertSame('/api/inflector.html', $files[0]->getUrl());
        self::assertSame([
            'layout' => 'default',
            'url' => '/api/inflector.html',
        ], $files[0]->getParameters()->getAll());
    }

    public function testGetFilesWithCustomInPath() : void
    {
        $buildDir = '';
        $inPath   = 'source/blog';

        $files = $this->sortFiles($this->sourceFileRepository->getFiles(
            $buildDir,
            $inPath
        ));

        self::assertCount(2, $files);

        self::assertSame('html', $files[0]->getExtension());
        self::assertSame('/blog/2018-09-01-test-blog-post2.html', $files[0]->getWritePath());
        self::assertSame('/blog/2018-09-01-test-blog-post2.html', $files[0]->getUrl());
        self::assertSame([
            'title' => 'Test Blog Post 2',
            'authorName' => 'Jonathan H. Wage',
            'authorEmail' => 'jonwage@gmail.com',
            'layout' => 'default',
            'url' => '/blog/2018-09-01-test-blog-post2.html',
        ], $files[0]->getParameters()->getAll());

        self::assertSame('html', $files[1]->getExtension());
        self::assertSame('/blog/2018-09-02-test-blog-post1.html', $files[1]->getWritePath());
        self::assertSame('/blog/2018-09-02-test-blog-post1.html', $files[1]->getUrl());
        self::assertSame([
            'title' => 'Test Blog Post 1',
            'authorName' => 'Jonathan H. Wage',
            'authorEmail' => 'jonwage@gmail.com',
            'layout' => 'default',
            'url' => '/blog/2018-09-02-test-blog-post1.html',
        ], $files[1]->getParameters()->getAll());
    }

    protected function setUp() : void
    {
        $rootDir = realpath(__DIR__ . '/..');
        assert(is_string($rootDir));

        $this->sourceFileRepository = new SourceFileRepository(
            $rootDir
        );
    }

    /**
     * @param SourceFile[] $files
     *
     * @return SourceFile[]
     */
    private function sortFiles(array $files) : array
    {
        usort($files, function (SourceFile $a, SourceFile $b) : int {
            return strcmp($a->getWritePath(), $b->getWritePath());
        });

        return $files;
    }
}
