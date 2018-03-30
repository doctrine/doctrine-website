<?php

namespace Timbroder\Bundle\AlgoliaBundle\Search;

use Knp\Menu\MenuItem;
use Sculpin\Core\Source\AbstractSource;
use TOC\MarkupFixer;
use TOC\TocGenerator;

/**
 * Class AlgoliaDocumentBuilder
 * @author Tim Broder <timothy.broder@gmail.com>
 */
class AlgoliaDocumentBuilder implements DocumentBuilderInterface
{
    /** @var array */
    private $projects;

    public function __construct(array $projects)
    {
        $this->projects = $projects;
    }

    /**
     * {@inheritDoc}
     */
    public function build(AbstractSource $source)
    {
        $content = $source->formattedContent();
        $markupFixer  = new MarkupFixer();
        $tocGenerator = new TocGenerator();

        $content = $markupFixer->fix($content, 1, 4);

        $menu = $tocGenerator->getMenu($content, 1, 4);

        $records = [];

        foreach ($menu->getChildren() as $h1) {
            $record = [];
            $record['h1'] = $h1->getName();

            $h1Children = $h1->getChildren();

            if (empty($h1Children)) {
                $this->buildRecords($records, $source, $record, $h1, $content);
            }

            foreach ($h1Children as $h2) {
                $record['h2'] = $h2->getName();

                $h2Children = $h2->getChildren();

                if (empty($h2Children)) {
                    $this->buildRecords($records, $source, $record, $h2, $content);
                }

                foreach ($h2Children as $h3) {
                    $record['h3'] = $h3->getName();

                    $h3Children = $h3->getChildren();

                    if (empty($h3Children)) {
                        $this->buildRecords($records, $source, $record, $h3, $content);
                    }

                    foreach ($h3Children as $h4) {
                        $record['h4'] = $h4->getName();

                        $h4Children = $h4->getChildren();

                        if (empty($h4Children)) {
                            $this->buildRecords($records, $source, $record, $h4, $content);
                        }
                    }
                }
            }
        }

        return $records;
    }

    private function buildRecords(array &$records, AbstractSource $source, array $record, MenuItem $item, string $content)
    {
        $record = array_values($record);
        $newRecord = [];

        $count = 0;

        foreach ($record as $key => $value) {
            if ($value) {
                $count++;
                $newRecord['h'.$count] = $value;
            }
        }

        $link = $source->permalink()->relativeUrlPath().$item->getUri();

        $project = $this->findProject($link);

        $newRecord['link'] = $link;
        $newRecord['projectName'] = $project['name'];

        $records[] = $newRecord;
    }

    private function findProject(string $link) : array
    {
        foreach ($this->projects as $project) {
            if (strpos($link, '/'.$project['slug'].'/') !== false) {
                return $project;
            }

            if (strpos($link, '/'.$project['docsSlug'].'/') !== false) {
                return $project;
            }
        }
    }
}
