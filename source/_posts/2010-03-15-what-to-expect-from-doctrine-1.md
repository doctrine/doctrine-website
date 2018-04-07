---
title: What to expect from Doctrine 1
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
As you all can obviously see, our focus around here these days has been
on Doctrine 2. While overall this is a great thing, we still have a
stable Doctrine 1.2 version to maintain. This blog post will give you a
little information about Doctrine 1.0, 1.1 and 1.2.

Doctrine 1.0 and 1.1 End of Life
================================

You may not have noticed, but the end of life for Doctrine 1.0 and 1.1
has come and gone earlier in the year. As of right now we will not be
committing anymore bug fixes to these branches. All development
resources will now focus on finishing Doctrine 2 and maintaining 1.2.

Doctrine 1.2 Maintenance Releases
=================================

Since Doctrine 1.2 is the last stable version to be released for the
Doctrine 1 series, we decided to open up the development a little bit
for the maintenance releases. Previously we were very strict with only
allowing bug fixes, but we will now allow a little more flexibility to
the types of things we commit to these releases. They can now contain
small enhancements and improvements as long as they do not break
backwards compatibility. Of course when adding things we cannot always
be 100% sure that something is BC, so we will announce each 1.2.x
release one week prior to it's packaging and deployment in order to give
people time to test things in SVN.

The reasoning behind this move is because we want to still improve the
small usability issues of the Doctrine 1 series without having to commit
to entirely new major versions. This way we are not leaving the Doctrine
1 code behind while we're focusing on the Doctrine 2 version. We hope
that you all are okay with this move. If you have any issues please let
us know!
