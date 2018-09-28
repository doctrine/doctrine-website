<?php

declare(strict_types=1);

namespace Doctrine\Website\Docs\RST;

use Doctrine\Website\Model\Project;
use Doctrine\Website\Model\ProjectVersion;
use Symfony\Component\Filesystem\Filesystem;
use function file_exists;
use function preg_replace;
use function sprintf;
use function str_replace;

class RSTCopier
{
    public const RST_TEMPLATE = <<<TEMPLATE
.. raw:: html
    {% block sidebar %}

{{ sidebar }}

.. raw:: html
    {% endblock %}


.. raw:: html
    {% block content %}

{% verbatim %}

{{ content }}

{% endverbatim %}

.. raw:: html
    {% endblock %}

TEMPLATE;

    public const DEFAULT_SIDEBAR = <<<SIDEBAR
.. toctree::
    :depth: 3
    :glob:

    *
SIDEBAR;

    /** @var RSTFileRepository */
    private $rstFileRepository;

    /** @var RSTLanguagesDetector */
    private $rstLanguagesDetector;

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $docsPath;

    public function __construct(
        RSTFileRepository $rstFileRepository,
        RSTLanguagesDetector $rstLanguagesDetector,
        Filesystem $filesystem,
        string $docsPath
    ) {
        $this->rstFileRepository    = $rstFileRepository;
        $this->rstLanguagesDetector = $rstLanguagesDetector;
        $this->filesystem           = $filesystem;
        $this->docsPath             = $docsPath;
    }

    public function copyRst(Project $project, ProjectVersion $version) : void
    {
        $languages = $this->rstLanguagesDetector->detectLanguages($project, $version);

        foreach ($languages as $language) {
            $outputPath = $project->getProjectVersionDocsPath($this->docsPath, $version, $language->getCode());

            // clear existing files before copying the rst over
            $this->filesystem->remove($this->rstFileRepository->findFiles($outputPath));

            $sidebar = $this->getSidebarRST($language->getPath());

            $files = $this->rstFileRepository->getSourceFiles($language->getPath());

            foreach ($files as $file) {
                $this->copyFile(
                    $project,
                    $version,
                    $language,
                    $file,
                    $outputPath,
                    $sidebar
                );
            }
        }
    }

    private function copyFile(
        Project $project,
        ProjectVersion $version,
        RSTLanguage $language,
        string $file,
        string $outputPath,
        string $sidebar
    ) : void {
        $filePath = str_replace($language->getPath(), '', $file);

        $fileContents = $this->rstFileRepository->getFileContents($file);

        $fixedRst = $this->fixRSTSyntax($project, $fileContents);

        $path       = str_replace('/' . $language->getCode(), '', $language->getPath());
        $sourceFile = str_replace($path, '', $file);

        $rstTemplate = $this->prepareRSTTemplate($fixedRst, $sidebar, $sourceFile);

        $writePath = $outputPath . $filePath;

        $this->filesystem->dumpFile($writePath, $rstTemplate);
    }

    private function prepareRSTTemplate(string $content, string $sidebar, string $sourceFile) : string
    {
        // replace the sidebar in the RST template
        $rstTemplate = str_replace('{{ sidebar }}', $sidebar, self::RST_TEMPLATE);

        // put the content in the RST template
        $content = str_replace('{{ content }}', $content, $rstTemplate);

        // append the source file name to the content so we can parse it back out
        // for use in the build process
        return $content . sprintf('{{ SOURCE_FILE:%s }}', $sourceFile);
    }

    private function fixRSTSyntax(Project $project, string $content) : string
    {
        // fix incorrect casing of note
        $content = str_replace('.. Note::', '.. note::', $content);

        // fix :maxdepth: to :depth:
        $content = str_replace(':maxdepth:', ':depth:', $content);

        // get rid of .. include:: toc.rst
        $content = str_replace('.. include:: toc.rst', '', $content);

        // replace \n::\n with \n.. code-block::\n
        // this corrects code blocks that don't render properly.
        // we should update the docs code but this makes old docs code render properly.
        $content = preg_replace("/\n::\n/", "\n.. code-block::\n", $content);
        $content = preg_replace("/\n:: \n/", "\n.. code-block::\n", $content);
        $content = preg_replace("/\n.. code-block :: (.*)\n/", "\n.. code-block:: $1\n", $content);

        // fix list syntax
        $content = str_replace("\n- \n", "\n- ", $content);

        // stuff from doctrine1 docs
        if ($project->getSlug() === 'doctrine1') {
            $content = preg_replace("/:code:(.*)\n/", '$1', $content);
            $content = preg_replace('/:php:(.*):`(.*)`/', '$2', $content);
            $content = preg_replace('/:file:`(.*)`/', '$1', $content);
            $content = preg_replace('/:code:`(.*)`/', '$1', $content);
            $content = preg_replace('/:literal:`(.*)`/', '$1', $content);
            $content = preg_replace('/:token:`(.*)`/', '$1', $content);
            $content = str_replace('.. productionlist::', '', $content);
            $content = preg_replace('/.. rubric:: Notes/', '', $content);
            $content = preg_replace("/.. sidebar:: (.*)\n/", '$1', $content);
            $content = str_replace('.. sidebar::', '', $content);
        }

        return $content;
    }

    private function getSidebarRST(string $docsPath) : string
    {
        // check if we have an explicit sidebar file to use
        // otherwise just use the default autogenerated sidebar
        $sidebarPath = $docsPath . '/sidebar.rst';

        return file_exists($sidebarPath)
            ? $this->rstFileRepository->getFileContents($sidebarPath)
            : self::DEFAULT_SIDEBAR;
    }
}
