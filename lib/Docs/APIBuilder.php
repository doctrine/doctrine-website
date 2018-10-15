<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Doctrine\Website\ProcessFactory;
use function file_put_contents;
use function sprintf;
use function unlink;

class APIBuilder
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsDir;

    /** @var string */
    private $sourceDir;

    public function __construct(
        ProcessFactory $processFactory,
        string $projectsDir,
        string $sourceDir
    ) {
        $this->processFactory = $processFactory;
        $this->projectsDir    = $projectsDir;
        $this->sourceDir      = $sourceDir;
    }

    public function buildAPIDocs(
        Project $project,
        ProjectVersion $version
    ) : void {
        $configContent = <<<CONFIG
<?php

use Sami\RemoteRepository\GitHubRemoteRepository;

return new Sami\Sami('%s', [
    'build_dir' => '%s',
    'cache_dir' => '%s',
    'remote_repository' => new GitHubRemoteRepository('%s', '%s'),
    'versions' => '%s',
]);
CONFIG;

        $codeDir  = $this->projectsDir . '/' . $project->getRepositoryName() . $project->getCodePath();
        $buildDir = $this->sourceDir . '/api/' . $project->getSlug() . '/' . $version->getSlug();
        $cacheDir = $this->projectsDir . '/' . $project->getRepositoryName() . '/cache';

        $renderedConfigContent = sprintf(
            $configContent,
            $codeDir,
            $buildDir,
            $cacheDir,
            'doctrine/' . $project->getRepositoryName(),
            $this->projectsDir . '/' . $project->getRepositoryName(),
            $version->getBranchName()
        );

        $configPath   = $this->projectsDir . '/' . $project->getRepositoryName() . '/sami.php';
        $samiPharPath = $this->sourceDir . '/../sami.phar';

        $this->filePutContents($configPath, $renderedConfigContent);

        $command = 'php ' . $samiPharPath . ' update ' . $configPath . ' --verbose';

        $this->processFactory->run($command);

        $this->unlinkFile($configPath);
    }

    protected function filePutContents(string $path, string $contents) : void
    {
        file_put_contents($path, $contents);
    }

    protected function unlinkFile(string $path) : void
    {
        unlink($path);
    }
}
