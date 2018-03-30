# README

This is the Doctrine Sculpin website code.

## Prepare Docs for Sculpin Build

This command accepts an argument where for the Doctrine repositories with the documentation will be cloned.

    ./vendor/bin/sculpin prepare-docs /data/doctrine

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
