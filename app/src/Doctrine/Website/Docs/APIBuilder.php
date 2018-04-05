<?php

namespace Doctrine\Website\Docs;

use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Symfony\Component\Process\Exception\ProcessFailedException;

class APIBuilder
{
    /** @var ProcessFactory */
    private $processFactory;

    /** @var string */
    private $projectsPath;

    /** @var string */
    private $sculpinSourcePath;

    public function __construct(
        ProcessFactory $processFactory,
        string $projectsPath,
        string $sculpinSourcePath)
    {
        $this->processFactory = $processFactory;
        $this->projectsPath = $projectsPath;
        $this->sculpinSourcePath = $sculpinSourcePath;
    }

    public function buildAPIDocs(
        Project $project,
        ProjectVersion $version)
    {
        $configContent = <<<CONFIG
<?php

return new Sami\Sami('%s', [
    'build_dir' => '%s',
    'cache_dir' => '%s',
]);
CONFIG;

        $codeDir = $this->projectsPath.'/'.$project->getRepositoryName().$project->getCodePath();
        $buildDir = $this->sculpinSourcePath.'/api/'.$project->getSlug().'/'.$version->getSlug();
        $cacheDir = $this->projectsPath.'/'.$project->getRepositoryName().'/cache';

        $renderedConfigContent = sprintf($configContent,
            $codeDir,
            $buildDir,
            $cacheDir
        );

        $configPath = $this->projectsPath.'/'.$project->getRepositoryName().'/sami.php';
        $samiPharPath = realpath($this->sculpinSourcePath.'/../sami.phar');

        file_put_contents($configPath, $renderedConfigContent);

        $command = 'php '.$samiPharPath.' update '.$configPath.' --verbose';

        $process = $this->processFactory->run($command);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        unlink($configPath);
    }
}
