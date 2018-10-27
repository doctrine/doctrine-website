---
title: "symfony Doctrine Schema Manager"
authorName: jwage
authorEmail:
categories: []
permalink: /2008/10/26/symfony-doctrine-schema-manager.html
---
As you all probably know, Doctrine has been tightly integrated with [a
few](http://trac.doctrine-project.org/wiki/integrate) different PHP
frameworks. Since symfony was my choice of framework a few years back, I
have dedicated a lot of time towards working on the integration between
the two.

Something I've always thought would be fun to build and has been one of
the most requested items by users is a nice web based interface for
managing your schema and generate your models from it. With the [new
form
framework](http://www.symfony-project.org/blog/2008/10/18/spice-up-your-forms-with-some-nice-widgets-and-validators)
as of [symfony
1.1](http://www.symfony-project.org/blog/2008/06/30/the-wait-is-over-symfony-1-1-released)
, you can build rich forms with a nice OOP interface. This made it
extremely easy to make the schema manager feature of the
[sfDoctrineManagerPlugin](http://www.symfony-project.com/plugins/sfDoctrineManagerPlugin).
It was as simple as automatically generating a set of forms from the
schema information data structure inside of Doctrine.

[![sfDoctrineManagerPlugin](http://www.symfony-project.org/uploads/plugins/5e25c2c7775a8ed169e2d9a6de8e2d1d98ffd110.png)](http://www.symfony-project.com/plugins/sfDoctrineManagerPlugin)

This symfony plugin allows you to manage all your schema information
from a nice web based interface. You can create new schemas or load your
existing schemas to edit. More information about it as well as screen
shots can be found
[here](http://www.symfony-project.com/plugins/sfDoctrineManagerPlugin).
