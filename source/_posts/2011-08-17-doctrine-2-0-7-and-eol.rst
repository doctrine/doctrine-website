---
title: Doctrine 2.0.7 and EOL
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: []
indexed: false
---
We released the last maintenance version of the 2.0.x branch
Doctrine 2.0.7 today. It contains a bunch of fixes backported from
the 2.1.x and master branches. You can find the list of fixes in
the Changelog:


-  `ORM Changelog 2.0.7 <http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10150>`_
-  `DBAL Changelog 2.0.7 <http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10151>`_

This release marks the end of line of the 2.0.x branch. We will not
port bugs for this branch anymore only security issues will get
applied. Please upgrade to 2.1.1 when it will be released later
this week. The upgrade to 2.1 is painless and the small number of
backwards incompatible changes
`is documented <https://github.com/doctrine/doctrine2/blob/master/UPGRADE_TO_2_1>`_.
Also most of the BC related bugs in the 2.1.0 release will be fixed
in 2.1.1.
