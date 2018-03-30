# README

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

    ./vendor/bin/sculpin doctrine:prepare-docs /data/doctrine

## Build the Website for Development

Now you are ready to build the website for the first time:

    ./vendor/bin/sculpin doctrine:build-website /data/doctrine-website-sculpin-build-dev --env=dev

Setup `lcl.doctrine-project.org` locally and point your webserver at `/data/doctrine-website-sculpin-build-dev` to see the website:

## Build the Website for Production

Now to make a production build:

    ./vendor/bin/sculpin doctrine:build-website /data/doctrine-website-sculpin-build-prod --env=prod

To publish the new version pass the `--publish` option:

    ./vendor/bin/sculpin doctrine:build-website /data/doctrine-website-sculpin-build-prod --env=prod --publish

## TODO

- Integrate API docs with new website.
- Get algolia upgraded and change indexing to use proper structured data from the meta.php files that the rst builder outputs.
- Build UX for switching between versions
- Enhance the /projects/{project} path in to a combined page that lists install, github link and documentation in one? Goal, reduce clicks from initial entry.
- Turn on HSTS?
- Add Doctrine team page. Example: https://nette.org/contributors
- Rewrite /contribute, /about and /community pages
- Run a link checker to look for 404s

## Future TODO:

- Can we do the code highlighting server side on build instead of in the browser with highlightjs?
