# README

[![Build Status](https://travis-ci.org/doctrine/doctrine-website-sculpin.svg?branch=master)](https://travis-ci.org/doctrine/doctrine-website-sculpin)

This is the Doctrine Sculpin website code.

## Setup

First clone the source code for the website to a directory like `/data`:

    cd /data
    git clone git@github.com:doctrine/doctrine-website-sculpin.git
    composer install

Next clone the repository which holds the built source code to `/data/doctrine-website-sculpin-build-prod`:

    git clone git@github.com:doctrine/doctrine-website-sculpin-build.git /data/doctrine-website-sculpin-build-prod

Create a development directory for you to create dev builds in `/data/doctrine-website-sculpin-build-dev` for testing:

    mkdir /data/doctrine-website-sculpin-build-dev

## Prepare Docs for Sculpin Build

This command accepts an argument for where the Doctrine repositories with the documentation will be cloned:

    ./doctrine build-docs

## Build Search Indexes

To build the search indexes pass the `--search` option:

    ./doctrine build-docs --search

## Build the Website for Development

Now you are ready to build the website for the first time:

    ./doctrine build-website

Setup `lcl.doctrine-project.org` locally and point your webserver at `/data/doctrine-website-sculpin-build-dev` to see the website:

## Watch for Changes

You can watch for changes in the source code and automatically build the website:

    ./doctrine watch

The browser will automatically refresh after the build finishes.

## Build the Website for Production

Now to make a production build:

    ./doctrine build-website --env=prod

To publish the new version pass the `--publish` option:

    ./doctrine build-website --env=prod --publish

## TODO

- Build UX for switching between versions
- Enhance the /projects/{project} path in to a combined page that lists install, github link and documentation in one? Goal, reduce clicks from initial entry.
- Turn on HSTS?
- Rewrite /contribute, /about and /community pages
- Run a link checker to look for 404s

## Future TODO:

- Can we do the code highlighting server side on build instead of in the browser with highlightjs?
- Move app/src/Gregwar back composer.json and submit modifications upstream.

