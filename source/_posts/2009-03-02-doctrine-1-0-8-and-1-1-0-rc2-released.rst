---
title: Doctrine 1.0.8 and 1.1.0-RC2 Released
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: [release]
indexed: false
---
Today I am happy to tell you that we have two new versions of
Doctrine available for you to use. The first is the monthly
maintenance release for Doctrine 1.0 and the second is another
release candidate for the newest major version of Doctrine, 1.1. As
always you can grab them from the
`downloads <http://www.doctrine-project.org/download>`_ page.

1.0.8 Highlights
~~~~~~~~~~~~~~~~


-  Backported a few fixes from 1.1
-  Several fixes and optimizations to Doctrine\_Query::count()
-  Dozens and dozens of other small fixes

You can read the full change log for the 1.0.8 release
`here <http://www.doctrine-project.org/change_log/1_0_8>`_.

1.1.0-RC2 Highlights
~~~~~~~~~~~~~~~~~~~~


-  Fixed issue with migration diff not paying attention to table
   options
-  Made the SoftDelete behavior backwards compatible with 1.0
-  Several fixes and optimizations to Doctrine\_Query::count()
-  Fixed duplicated params when using LIMIT/OFFSET under MySQL
-  Fixed issue with new utf8 search analyzer
-  Fixed issue with prefixes causing invalid results in the
   migration diff tool
-  Fixed issues that arrise when re-using query objects with DQL
   callbacks enabled.

You can read the full change log for the 1.1.0-RC2 release
`here <http://www.doctrine-project.org/change_log/1_1_0_RC2>`_.


.. raw:: html

   <hr />
   
I realize we are a little late on the 1.1.0 release but we had some
regressions reported that were a bit tricky and time consuming to
fix. All is well now and we were able to make hopefully the last
release candidate for the 1.1 version of Doctrine.
