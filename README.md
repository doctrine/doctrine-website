# Doctrine Website

[![Build Status](https://travis-ci.org/doctrine/doctrine-website.svg?branch=master)](https://travis-ci.org/doctrine/doctrine-website)

This is the Doctrine website code.

## Setup

First clone the source code for the website to a directory like `/data`:

    cd /data
    git clone git@github.com:doctrine/doctrine-website.git
    composer install

Next clone the repository which holds the built source code to `/data/doctrine-website-build-prod`:

    git clone git@github.com:doctrine/doctrine-website-build.git /data/doctrine-website-build-prod

Create a development directory for you to create dev builds in `/data/doctrine-website-build-dev` for testing:

    mkdir /data/doctrine-website-build-dev

## Config

Copy the distribution config file:

    cp app/config/local.yml.dist app/config/local.yml

## Prepare Docs for Build

This command accepts an argument for where the Doctrine repositories with the documentation will be cloned:

    ./doctrine build-docs

By default only the RST docs are built but you can pass the `--api` option to generate the API docs
using Sami:

    ./doctrine build-docs --api

To build the search indexes pass the `--search` option:

    ./doctrine build-docs --search

## Build the Website for Development

Now you are ready to build the website for the first time:

    ./doctrine build-website

Setup `lcl.doctrine-project.org` locally and point your webserver at `/data/doctrine-website-build-dev` to see the website:

## Watch for Changes

You can watch for changes in the source code and automatically build the website:

    ./doctrine watch

The browser will automatically refresh after the build finishes.

## Build the Website for Production

Now to make a production build:

    ./doctrine build-website --env=prod

To publish the new version pass the `--publish` option:

    ./doctrine build-website --env=prod --publish
