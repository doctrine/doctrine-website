---
title: Doctrine DBAL RC5 and ORM RC2 released
menuSlug: blog
authorName: beberlei 
authorEmail: 
categories: [release]
permalink: /:year/:month/:day/:basename.html
---
We are happy to announce the immediate availability of Doctrine
DBAL RC5 and Doctrine ORM RC2. There have been almost only minor
bugfixes in both packages that came up in the last week.

There is one notable change in ORM: If you execute a DQL Query
before RC2 a flush would be issued on the EntityManager if there
were pending insertions. This flush has now been removed. It was
never documented to be executed, so we don't think this will cause
major pain to anyone. Just make sure to call flush explicitly
whenever you need it.

See the changelogs for both projects:


-  `DBAL RC5 Changelog <http://www.doctrine-project.org/jira/browse/DBAL/fixforversion/10113>`_
-  `ORM RC2 Changelog <http://www.doctrine-project.org/jira/browse/DDC/fixforversion/10112>`_

Both DBAL and ORM are essentially bug-free. All the still open bug
reports are either:


-  Improvement/feature requests (only marked as bug)
-  Minor and will be fixed in 2.1, because they need some
   refactorings. We will make sure that they appear in the Known
   Issues section before the final release.
-  Some trivial bugs in Tools/Console namespace that don't affect
   the core of the ORM. We are waiting for more feedback on those
   issues.

Expect these Release Candidates to be tagged as final if no
critical or major issues are discovered in the next week.

You can grab the code from our
`downloads section <http://www.doctrine-project.org/projects>`_ or
`directly from Github <https://github.com/doctrine/doctrine2/commits/2.0.0RC2>`_.

In these last days before the final release we will focus on the
documentation. As you might have noticed we already switched the
ORM documentation to be rendered with Sphinx and ReStructured Text.
You are now able to search the docs. We will update the layout to
be more Doctrine friendly and migrate the other packages to
Sphinx/ReST in the next days.
