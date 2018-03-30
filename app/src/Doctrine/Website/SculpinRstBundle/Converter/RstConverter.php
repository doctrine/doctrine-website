<?php

namespace Doctrine\Website\SculpinRstBundle\Converter;

use ReflectionProperty;
use Doctrine\Website\SculpinRstBundle\Parser\ParserFactory;
use Doctrine\Website\SculpinRstBundle\SculpinRstBundle;
use Sculpin\Core\Converter\ConverterContextInterface;
use Sculpin\Core\Converter\ConverterInterface;
use Sculpin\Core\Converter\SourceConverterContext;
use Sculpin\Core\Event\SourceSetEvent;
use Sculpin\Core\Sculpin;
use Sculpin\Core\Source\SourceInterface;
use SplFileInfo;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TOC\MarkupFixer;
use TOC\TocGenerator;

class RstConverter implements ConverterInterface, EventSubscriberInterface
{
    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * @var string[]
     */
    private $extensions;

    public static function getSubscribedEvents()
    {
        return [
            Sculpin::EVENT_BEFORE_RUN => 'beforeRun',
        ];
    }

    /**
     * @param ParserFactory $parserFactory
     * @param string[] $extensions
     */
    public function __construct(ParserFactory $parserFactory, array $extensions = [])
    {
        $this->parserFactory = $parserFactory;
        $this->extensions = $extensions;
    }

    public function convert(ConverterContextInterface $converterContext)
    {
        $parser = $this->parserFactory->createParser();

        // What?! Read on.
        if (!$converterContext instanceof SourceConverterContext) {
            return;
        }

        $sourceProp = new ReflectionProperty($converterContext, 'source');
        $sourceProp->setAccessible(true);

        /** @var SourceInterface $source */
        $source = $sourceProp->getValue($converterContext);

        // This is the only way to detect if we're getting converted reST as HTML. This happens when something else,
        // like a view template is changed during --watch. This causes the HTML to be parsed as reST.
        if ($source->formattedContent()) {
            return;
        }

        $file = $source->file();

        if ($file instanceof SplFileInfo) {
            $parser->getEnvironment()->setCurrentFileName($file->getFilename());
            $parser->getEnvironment()->setCurrentDirectory($file->getPath());
        }

        $document = $parser->parse($converterContext->content());
        $content = $document->render();

        $converterContext->setContent($content);
    }

    public function beforeRun(SourceSetEvent $sourceSetEvent)
    {
        foreach ($sourceSetEvent->updatedSources() as $source) {
            foreach ($this->extensions as $extension) {
                if (fnmatch("*.{$extension}", $source->filename())) {
                    $source->data()->append('converters', SculpinRstBundle::CONVERTER_NAME);
                    break;
                }
            }
        }
    }
}
