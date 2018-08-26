---
title: "Doctrine 2 BETA1 Released"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: romanb
authorEmail:
categories: [release]
permalink: /2010/04/27/doctrine-2-0-0-beta1-released.html
---
Today we are pleased to announce the immediate release of the first beta
version of Doctrine 2. It comes with some delay which was caused partly
by our move to git and github and the switch to the Symfony Console
component for the CLI. We had to confront the alpha users with quite
some backwards compatibility problems and we apologize for that.
Starting with the beta period you can expect the amount of backwards
incompatible changes to be much lower.

Since the ALPHA4 release over 160 issues have been resolved. You can
find the full changelog
[here](http://www.doctrine-project.org/jira/secure/ReleaseNote.jspa?projectId=10032&styleName=Html&version=10030).

Some of the most important changes were the shift towards the Symfony
(2) Console component for the CLI as well as the introduction of the
inversedBy attribute for bidirectional associations, among others. For
some help with upgrading from ALPHA4 to BETA1, please consult [the
upgrade page](http://www.doctrine-project.org/upgrade/2_0).

You can get the new release as usual from our [download
page](http://www.doctrine-project.org/download) or [directly from
github](http://github.com/doctrine/doctrine2).

We would like to thank all the adopters of the early alpha releases. All
your issue reports, feature and enhancement requests and general
feedback and criticism have helped a lot to move the project forward.

Looking forward, we will likely have at least 2 or 3 beta releases,
about every 1-2 months, before we go RC. Once that happens, the API is
ultimately frozen until the stable release.
