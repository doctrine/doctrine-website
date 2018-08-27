---
layout: default
title: Contribute to Website
menuSlug: contribute
permalink: /contribute/website/index.html
---

# Contribute to Website

The source code for [doctrine-project.org](https://www.doctrine-project.org)
is completely open source and easy for you to setup locally so you can submit
contributions back to the project.

## Installation

First, create a fork of the [repository](https://github.com/doctrine/doctrine-website)
and clone it to a directory like `/data`:

```console
$ cd /data
$ git clone git@github.com:username/doctrine-website.git
```

Next run `composer install` to install all of the dependencies.

```console
$ cd doctrine-website
$ composer install
```

## Coding Standards

Copy the `pre-commit` hook to `.git/hooks/pre-commit` to ensure
coding standards are maintained:

```console
$ cp pre-commit .git/hooks/pre-commit
```

## Configuration

Copy the `config/local.yml.dist` config file that came with the repository:

```console
$ cp config/local.yml.dist config/local.yml
```

## Edit your Hosts File

Edit your `/etc/hosts` file and point `lcl.doctrine-project.org` at your
local web server. You will need to setup a virtual host in your web server
and point the root directory at `/data/doctrine-website/build-dev`.

## Build Documentation

Now are you ready to start building the website! First, build the
documentation with the `build-docs` command.

This command will clone all the repositories for the documentation and
switch to the appropriate branches for each version of a project when
you pass the `--sync-git` option.

```console
$ ./bin/console build-docs --sync-git
```

### API Documentation

By default only the RST docs are built. You need to pass the `--api`
option to also generate the API docs:

```console
$ ./bin/console build-docs --sync-git --api
```

We use [Sami](https://github.com/FriendsOfPHP/Sami) for generating the PHP
API documentation for each project.

### Search Indexes

To build the Algolia search indexes pass the `--search` option:

```console
$ ./bin/console build-docs --sync-git --search
```

You will need to have the `doctrine.website.algolia.admin_api_key` parameter in
`config/local.yml` in order to update the Algolia search indexes.

## Build the Website

Now you are ready to build the website for the first time:

```console
$ ./bin/console build-website
```

Go take a look at `lcl.doctrine-project.org` and the local website should render.
The built code for the website is written to `/data/doctrine-website/build-dev`.

## Submitting Pull Requests

If you see something that could be improved or a bug that needs fixing, submit a pull
request with the changes to [doctrine/doctrine-website](https://github.com/doctrine/doctrine-website/).

You can also take a look at the list of [open issues](https://github.com/doctrine/doctrine-website/issues)
on GitHub and look for something that needs help.
