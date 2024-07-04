<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class ProjectStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    public function __construct(
        #[ORM\Column(type: 'integer')]
        private int $githubStars = 0,
        #[ORM\Column(type: 'integer')]
        private int $githubWatchers = 0,
        #[ORM\Column(type: 'integer')]
        private int $githubForks = 0,
        #[ORM\Column(type: 'integer')]
        private int $githubOpenIssues = 0,
        #[ORM\Column(type: 'integer')]
        private int $dependents = 0,
        #[ORM\Column(type: 'integer')]
        private int $suggesters = 0,
        #[ORM\Column(type: 'integer')]
        private int $totalDownloads = 0,
        #[ORM\Column(type: 'integer')]
        private int $monthlyDownloads = 0,
        #[ORM\Column(type: 'integer')]
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
