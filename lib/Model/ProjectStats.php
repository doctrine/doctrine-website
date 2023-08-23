<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

final class ProjectStats
{
    public function __construct(
        private int $githubStars = 0,
        private int $githubWatchers = 0,
        private int $githubForks = 0,
        private int $githubOpenIssues = 0,
        private int $dependents = 0,
        private int $suggesters = 0,
        private int $totalDownloads = 0,
        private int $monthlyDownloads = 0,
        private int $dailyDownloads = 0,
    ) {
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
