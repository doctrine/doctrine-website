<?php

namespace Doctrine\Website\Docs;

use Doctrine\Website\ProcessFactory;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;

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
        $samiPharPath = $this->sculpinSourcePath.'/../sami.phar';

        $this->filePutContents($configPath, $renderedConfigContent);

        $command = 'php '.$samiPharPath.' update '.$configPath.' --verbose';

        $this->processFactory->run($command);

        $this->unlinkFile($configPath);
    }

    protected function filePutContents(string $path, string $contents)
    {
        file_put_contents($path, $contents);
    }

    protected function unlinkFile(string $path)
    {
        unlink($path);
    }
}
