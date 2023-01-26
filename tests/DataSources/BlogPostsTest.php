<?php

declare(strict_types=1);

namespace Doctrine\Website\Tests\DataSources;

use DateTimeImmutable;
use Doctrine\SkeletonMapper\DataSource\Sorter;
use Doctrine\Website\DataBuilder\BlogPostDataBuilder;
use Doctrine\Website\DataBuilder\WebsiteData;
use Doctrine\Website\DataBuilder\WebsiteDataReader;
use Doctrine\Website\DataSources\BlogPosts;
use Doctrine\Website\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use function usort;

class BlogPostsTest extends TestCase
{
    private WebsiteDataReader&MockObject $dataReader;

    private BlogPosts $blogPosts;

    public function testGetSourceRows(): void
    {
        $data = [
            ['date' => '2018-09-01'],
            ['date' => '2018-09-02'],
        ];

        $this->dataReader->expects(self::once())
            ->method('read')
            ->with(BlogPostDataBuilder::DATA_FILE)
            ->willReturn(new WebsiteData('test', $data));

        $blogPostRows = $this->blogPosts->getSourceRows();

        usort($blogPostRows, new Sorter(['date' => 'desc']));

        $expected = [
            [
                'date' => new DateTimeImmutable('2018-09-02'),
            ],
            [
                'date' => new DateTimeImmutable('2018-09-01'),
            ],

        ];

        self::assertEquals($expected, $blogPostRows);
    }

    protected function setUp(): void
    {
        $this->dataReader = $this->createMock(WebsiteDataReader::class);

        $this->blogPosts = new BlogPosts(
            $this->dataReader,
        );
    }
}
