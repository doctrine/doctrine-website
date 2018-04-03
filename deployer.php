<?php

$env = $argv[1] ?? 'prod';

$deploy = trim(file_get_contents(sprintf('/data/doctrine/deploy-%s', $env)));
$lastDeploy = trim(file_get_contents(sprintf('/data/doctrine/last-deploy-%s', $env)));

if ($deploy === $lastDeploy) {
    echo "Nothing has changed. No need to deploy!\n";
    exit;
}

if ($env === 'prod') {
    $command = <<<COMMAND
cd /data/doctrine-website-sculpin-prod
&& git checkout master
&& git pull origin master
&& ./prepare-docs
&& ./publish prod
&& cp /data/doctrine/deploy-prod /data/doctrine/last-deploy-prod
COMMAND;

} elseif ($env === 'staging') {
    $command = <<<COMMAND
cd /data/doctrine-website-sculpin-staging
&& git fetch
&& git checkout %s
&& ./prepare-docs
&& ./publish staging
&& cp /data/doctrine/deploy-staging /data/doctrine/last-deploy-staging
COMMAND;

    // $deploy contains the commit sha to deploy
    $command = sprintf($command, $deploy);
}

shell_exec($command);
