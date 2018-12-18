<?php

declare(strict_types=1);

namespace Doctrine\Website\DataBuilder;

class WebsiteDataBuilder
{
    /** @var ProjectDataBuilder */
    private $projectDataBuilder;

    /** @var ProjectContributorDataBuilder */
    private $projectContributorDataBuilder;

    /** @var ContributorDataBuilder */
    private $contributorDataBuilder;

    /** @var BlogPostDataBuilder */
    private $blogPostDataBuilder;

    public function __construct(
        ProjectDataBuilder $projectDataBuilder,
        ProjectContributorDataBuilder $projectContributorDataBuilder,
        ContributorDataBuilder $contributorDataBuilder,
        BlogPostDataBuilder $blogPostDataBuilder
    ) {
        $this->projectDataBuilder            = $projectDataBuilder;
        $this->projectContributorDataBuilder = $projectContributorDataBuilder;
        $this->contributorDataBuilder        = $contributorDataBuilder;
        $this->blogPostDataBuilder           = $blogPostDataBuilder;
    }

    /**
     * @return WebsiteData[]
     */
    public function build() : iterable
    {
        yield $this->projectDataBuilder->build();
        yield $this->projectContributorDataBuilder->build();
        yield $this->contributorDataBuilder->build();
        yield $this->blogPostDataBuilder->build();
    }
}
