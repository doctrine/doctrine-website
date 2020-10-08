<?php

declare(strict_types=1);

namespace Doctrine\Website\Email;

use Pelago\Emogrifier;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;

use function strip_tags;
use function trim;

final class RenderEmail
{
    /** @var Emogrifier */
    private $emogrifier;

    /** @var string */
    private $templatesDir;

    /** @var AbstractExtension[] */
    private $extensions;

    /**
     * @param AbstractExtension[] $extensions
     */
    public function __construct(
        Emogrifier $emogrifier,
        string $templatesDir,
        array $extensions
    ) {
        $this->emogrifier   = $emogrifier;
        $this->templatesDir = $templatesDir;
        $this->extensions   = $extensions;
    }

    /**
     * @param mixed[] $parameters
     */
    public function __invoke(string $template, array $parameters): RenderedEmail
    {
        $twig = $this->createTwigEnvironment($this->createFilesystemLoader());

        $template = $twig->loadTemplate($template);

        $subject   = $template->renderBlock('subject', $parameters);
        $inlineCss = $template->renderBlock('inline_css', $parameters);
        $bodyText  = $template->renderBlock('full_body_text', $parameters);
        $bodyHtml  = $template->renderBlock('full_body_html', $parameters);

        if (trim($bodyText) === '') {
            $bodyText = strip_tags($template->renderBlock('body_html', $parameters));
        }

        $this->emogrifier->setHtml($bodyHtml);
        $this->emogrifier->setCss($inlineCss);

        $mergedHtml = $this->emogrifier->emogrify();

        return new RenderedEmail($subject, $bodyText, $mergedHtml);
    }

    private function createTwigEnvironment(LoaderInterface $loader): Environment
    {
        $twig = new Environment($loader, ['strict_variables' => true]);

        foreach ($this->extensions as $extension) {
            $twig->addExtension($extension);
        }

        return $twig;
    }

    private function createFilesystemLoader(): FilesystemLoader
    {
        return new FilesystemLoader($this->templatesDir);
    }
}
