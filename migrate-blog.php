<?php

$template = <<<TEMPLATE
---
title: %s
authorName: %s
authorEmail: %s
categories: [%s]
indexed: false
---
%s
TEMPLATE;

function recursiveGlob(array $paths)
{
    $allFiles = [];

    foreach ($paths as $path) {
        $files =  glob($path);

        $allFiles = array_merge($allFiles, $files);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $dirPath = $file.'/*';

                $dirFiles = recursiveGlob([$dirPath]);

                $allFiles = array_merge($allFiles, $dirFiles);
            }
        }
    }

    return $allFiles;
}

function parseAuthor(string $content) : array
{
    preg_match('/.. author:: (.*)/', $content, $match);

    $author = $match[1] ?? '';

    preg_match('/ <(.*)>/', $author, $match);

    $authorEmail = $match[1] ?? '';
    if (isset($match[1])) {
        $authorName = str_replace($match[1], '', $author);
    } else {
        $authorName = $author;
    }

    $authorName = str_replace(' <>', '', $authorName);

    return [$authorName, $authorEmail];
}

function parseCategories(string $content) : array
{
    preg_match('/.. categories:: (.*)/', $content, $match);

    if (!isset($match[1])) {
        return [];
    }

    $category = trim(strtolower($match[1]));

    $category = $category !== 'none' ? $category : '';

    if ($category) {
        return [$category];
    }

    return [];
}

$paths = [
    '/data/doctrine-website-sphinx/site/2007',
    '/data/doctrine-website-sphinx/site/2008',
    '/data/doctrine-website-sphinx/site/2009',
    '/data/doctrine-website-sphinx/site/2010',
    '/data/doctrine-website-sphinx/site/2011',
    '/data/doctrine-website-sphinx/site/2012',
    '/data/doctrine-website-sphinx/site/2013',
    '/data/doctrine-website-sphinx/site/2014',
    '/data/doctrine-website-sphinx/site/2015',
    '/data/doctrine-website-sphinx/site/2016',
    '/data/doctrine-website-sphinx/site/2017',
];

$files = array_filter(recursiveGlob($paths), 'is_file');

foreach ($files as $file) {
    $newPath = str_replace('/data/doctrine-website-sphinx/site/', '', $file);

    $e = explode('/', $newPath);

    $newPath = sprintf('%s-%s-%s-%s', $e[0], $e[1], $e[2], $e[3]);

    $content = file_get_contents($file);

    $e = explode("\n", $content);

    // the title is the first line
    $title = $e[0];

    // unset the first line and the ==== underneath
    unset($e[0], $e[1]);

    // put the content back together without the first two lines
    $content = trim(implode("\n", $e));

    list($authorName, $authorEmail) = parseAuthor($content);

    $categories = parseCategories($content);

    $contentToWrite = sprintf($template,
        $title,
        $authorName,
        $authorEmail,
        implode(', ', $categories),
        $content
    );

    $contentToWrite = preg_replace('/.. author::(.*)/', '', $contentToWrite);
    $contentToWrite = preg_replace('/.. categories::(.*)/', '', $contentToWrite);
    $contentToWrite = preg_replace('/.. tags::(.*)/', '', $contentToWrite);
    $contentToWrite = preg_replace('/.. comments::(.*)/', '', $contentToWrite);

    $contentToWrite = str_replace('.. code::', '.. code-block::', $contentToWrite);

    $contentToWrite = str_replace(':math:', '', $contentToWrite);

    $contentToWrite = trim($contentToWrite)."\n";

    file_put_contents(__DIR__.'/source/_posts/'.$newPath, $contentToWrite);
}
