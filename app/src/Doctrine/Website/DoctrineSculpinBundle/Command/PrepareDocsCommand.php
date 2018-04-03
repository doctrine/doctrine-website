<?php

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Doctrine\Website\Docs\Preparer;
use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use InvalidArgumentException;
use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareDocsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('doctrine:prepare-docs')
            ->setDescription('Prepare docs.')
            ->addArgument('dir', null, InputArgument::REQUIRED, 'The directory where the documentation repositories are cloned.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $sculpinRstKernel = $container->get('sculpin_rst.kernel.sculpin');

        $kernelRootDir = $container->getParameter('kernel.root_dir');

        /** Clone Doctrine repositories to this path */
        $projectsPath = $input->getArgument('dir');

        if (!$projectsPath) {
            throw new InvalidArgumentException('You must pass the directory argument ');
        }

        if (!is_dir($projectsPath)) {
            mkdir($projectsPath, 0777, true);
        }

        /** Path to Doctrine website Sculpin source files */
        $sculpinSourcePath = realpath($kernelRootDir.'/../source');

        $projects = $container->get('doctrine.project.repository')->findAll();

        foreach ($projects as $project) {
            foreach ($project->getVersions() as $version) {
                $preparer = new Preparer(
                    $sculpinSourcePath,
                    $projectsPath,
                    $sculpinRstKernel,
                    $project,
                    $version
                );

                $preparer->prepareGit($output);

                $output->writeln(sprintf('Generating api docs for project <info>%s</info> version <info>%s</info>',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                $this->buildApiDocs($projectsPath, $sculpinSourcePath, $project, $version);

                if (!$preparer->versionHasDocs($project, $version)) {
                    $output->writeln(sprintf('<warning>Skipping project %s version %s because it does not have any docs.</warning>',
                        $project->getSlug(),
                        $version->getSlug()
                    ));

                    continue;
                }

                $output->writeln(sprintf('Building docs for <info>%s</info> version <info>%s</info>',
                    $project->getSlug(),
                    $version->getSlug()
                ));

                $preparer->prepare($output);
            }
        }
    }

    private function buildApiDocs(
        string $projectsPath,
        string $sculpinSourcePath,
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

        $codeDir = $projectsPath.'/'.$project->getRepositoryName().$project->getCodePath();
        $buildDir = $sculpinSourcePath.'/api/'.$project->getSlug().'/'.$version->getSlug();
        $cacheDir = $projectsPath.'/'.$project->getRepositoryName().'/cache';

        $renderedConfigContent = sprintf($configContent,
            $codeDir,
            $buildDir,
            $cacheDir
        );

        $configPath = $projectsPath.'/'.$project->getRepositoryName().'/sami.php';
        $samiPharPath = realpath($sculpinSourcePath.'/../sami.phar');

        file_put_contents($configPath, $renderedConfigContent);

        $command = 'php '.$samiPharPath.' update '.$configPath;

        passthru('php '.$sculpinSourcePath.'/../sami.phar update '.$configPath);

        unlink($configPath);
    }
}
