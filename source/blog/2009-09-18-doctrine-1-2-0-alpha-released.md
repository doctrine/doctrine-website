---
title: "Doctrine 1.2.0-ALPHA Released"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: jwage
authorEmail:
categories: [release]
permalink: /2009/09/18/doctrine-1-2-0-alpha-released.html
---
Today I am happy to announce the immediate availability of Doctrine
1.2.0 ALPHA1. As you all may already know, 1.2 will most likely be the
last 1.x version and is a LTS(long term support) release. We will post
the official support schedule once 1.2 is stable and released.

You can download Doctrine 1.2.0-ALPHA1 on the
[download](http://www.doctrine-project.org/download) page just like
normal.

This release contains many changes, bug fixes and enhancements. Some of
them are highlighted below.

-   Removed string support from attributes system for performance
    increase
-   Cleaned up and removed deprecated code
-   Added ability to configure base Table, Query and Collection classes
-   Added ability to register custom hydrator and connection drivers
-   Enhanced Table magic finders to include conditions
-   Introduced Doctrine Extensions Repository
-   On Demand Hydration for better performance and less memory usage
-   Other various bug fixes, convenience enhancements and other minor
    performance improvements

You can view the full details of all the changes in the [upgrade
file](http://www.doctrine-project.org/upgrade/1_2). We use this to
document all major changes so that upgrading is easier for you.
