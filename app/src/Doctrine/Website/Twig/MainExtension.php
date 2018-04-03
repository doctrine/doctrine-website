<?php

namespace Doctrine\Website\Twig;

use Doctrine\Website\Projects\Project;
use Doctrine\Website\Projects\ProjectVersion;
use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleTest;

class MainExtension extends Twig_Extension
{
    /** @var array*/
    private $team;

    public function __construct(array $team)
    {
        $this->team = $team;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('get_asset_url', array($this, 'getAssetUrl')),
            new Twig_SimpleFunction('get_team', array($this, 'getTeam')),
        ];
    }

    public function getAssetUrl(string $path, string $siteUrl)
    {
        return $siteUrl.$path.'?'.$this->getAssetCacheBuster($path);
    }

    public function getTeam() : array
    {
        return $this->team;
    }

    private function getAssetCacheBuster(string $path) : string
    {
        $assetPath = realpath(__DIR__.'/../../../../../source/'.$path);

        return substr(md5(file_get_contents($assetPath)), 0, 6);
    }
}
