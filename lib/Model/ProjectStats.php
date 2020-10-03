<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class ProjectStats
{
    /** @var int */
    private $githubStars = 0;

    /** @var int */
    private $githubWatchers = 0;

    /** @var int */
    private $githubForks = 0;

    /** @var int */
    private $githubOpenIssues = 0;

    /** @var int */
    private $dependents = 0;

    /** @var int */
    private $suggesters = 0;

    /** @var int */
    private $totalDownloads = 0;

    /** @var int */
    private $monthlyDownloads = 0;

    /** @var int */
    private $dailyDownloads = 0;

    public function __construct(
        int $githubStars,
        int $githubWatchers,
        int $githubForks,
        int $githubOpenIssues,
        int $dependents,
        int $suggesters,
        int $totalDownloads,
        int $monthlyDownloads,
        int $dailyDownloads
    ) {
        $this->githubStars      = $githubStars;
        $this->githubWatchers   = $githubWatchers;
        $this->githubForks      = $githubForks;
        $this->githubOpenIssues = $githubOpenIssues;
        $this->dependents       = $dependents;
        $this->suggesters       = $suggesters;
        $this->totalDownloads   = $totalDownloads;
        $this->monthlyDownloads = $monthlyDownloads;
        $this->dailyDownloads   = $dailyDownloads;
    }

    public function getGithubStars(): int
    {
        return $this->githubStars;
    }

    public function getGithubWatchers(): int
    {
        return $this->githubWatchers;
    }

    public function getGithubForks(): int
    {
        return $this->githubForks;
    }

    public function getGithubOpenIssues(): int
    {
        return $this->githubOpenIssues;
    }

    public function getDependents(): int
    {
        return $this->dependents;
    }

    public function getSuggesters(): int
    {
        return $this->suggesters;
    }

    public function getTotalDownloads(): int
    {
        return $this->totalDownloads;
    }

    public function getMonthlyDownloads(): int
    {
        return $this->monthlyDownloads;
    }

    public function getDailyDownloads(): int
    {
        return $this->dailyDownloads;
    }
}
