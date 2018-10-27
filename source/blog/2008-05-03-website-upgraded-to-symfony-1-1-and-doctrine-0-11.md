---
title: "Website upgraded to symfony 1.1 and Doctrine 0.11"
authorName: jwage
authorEmail:
categories: []
permalink: /2008/05/03/website-upgraded-to-symfony-1-1-and-doctrine-0-11.html
---
<p>

Today I have upgraded the Doctrine website to symfony 1.1 and Doctrine
0.11. The integration between the symfony framework and Doctrine with
sfDoctrinePlugin has become pretty complete in the last few days. It now
has all the same functionality as the bundled sfPropelPlugin with
symfony 1.1 plus dozens more features a long with the Doctrine DBAL and
ORM. The main features introduced to the symfony framework are
migrations, DQL, behaviors, inheritance and a sprinkle of additional
admin generator features. I have also completely re-ported
sfGuardDoctrinePlugin from sfGuardPlugin so it is in sync with it and
has exactly the same features, nothing more. The removed functionality
will be moved to separate plugins that work with sfGuardDoctrinePlugin.

</p><p>

The source of the Doctrine website can be gotten from svn here
[http://www.phpdoctrine.org/svnweb](http://www.phpdoctrine.org/svnweb)

</p>


