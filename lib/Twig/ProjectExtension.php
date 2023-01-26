<?php

declare(strict_types=1);

namespace Doctrine\Website\Twig;

use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\Repositories\ProjectRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function file_exists;
use function str_replace;
use function strpos;

class ProjectExtension extends AbstractExtension
{
    public function __construct(private ProjectRepository $projectRepository, private string $sourceDir)
    {
    }

    /** {@inheritDoc} */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_menu_projects', [$this->projectRepository, 'findPrimaryProjects']),
            new TwigFunction('get_url_version', [$this, 'getUrlVersion']),
        ];
    }

    public function getUrlVersion(ProjectVersion $projectVersion, string $url, string $currentVersion): string|null
    {
        if (strpos($url, 'current') !== false) {
            $otherVersionUrl = str_replace('current', $projectVersion->getSlug(), $url);
        } else {
            $otherVersionUrl = str_replace($currentVersion, $projectVersion->getSlug(), $url);
        }

        $otherVersionFile = $this->sourceDir . $otherVersionUrl;

        if (! $this->fileExists($otherVersionFile)) {
            return null;
        }

        return $otherVersionUrl;
    }

    protected function fileExists(string $file): bool
    {
        return file_exists($file);
    }
}
