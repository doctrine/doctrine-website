---
title: The Bleeding Edge: Website Upgraded
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
This weekend I decided it was time to completely dive in to the
awesomeness that is `symfony <http://www.symfony-project.com>`_
1.2. I upgraded the Doctrine website to 1.2 as well as Doctrine
1.1. I had several schema changes that I migrated to production
with the new
`Migrations features <http://www.doctrine-project.org/blog/new-to-migrations-in-1-1>`_
that were mentioned previously on the blog. It made the upgrade of
the website much easier. The primary motivation for diving in to
this was a blog post made on
`10/18/2008 <http://www.symfony-project.org/blog/2008/10/18/spice-up-your-forms-with-some-nice-widgets-and-validators>`_
and
`10/14/2008 <http://www.symfony-project.org/blog/2008/10/14/new-in-symfony-1-2-make-your-choice>`_
where Fabien talks about forms in symfony 1.2.

Below is an overview of some of the changes made.


-  Upgraded to symfony 1.2
-  Upgraded to Doctrine 1.1
-  New backend control panel for administering the content of the
   website
-  Single Sign-On(SSO) - We now have partial SSO. Trac and the main
   website authentication are shared now. The forum will be integrated
   soon.
-  New groundwork started for several new website features, new
   documentation, user contributed documentation, user contributed
   packages! and more.

Here are some screen shots of the backend magic that was generated
by symfony. I didn't have to do much and in one evening we have a
new content management system backend for the website.

Doctrine Releases
-----------------

The style and custom icons in this list of the available Doctrine
releases were added thanks to my custom theme built specifically
for the Doctrine backend.

.. figure:: http://www.doctrine-project.com/uploads/assets/api_release_list.png
   :align: center
   :alt: Doctrine Releases
   
   Doctrine Releases

Edit User SVN Access
--------------------

Thanks to the symfony form framework I was able to easily add a
custom widget for editing the website users svn access for all the
different Doctrine versions.

.. figure:: http://www.doctrine-project.com/uploads/assets/edit_user_svn_access.png
   :align: center
   :alt: Edit User SVN Access
   
   Edit User SVN Access

Doctrine Release Points
-----------------------

List of all the Doctrine sub-release points.

.. figure:: http://www.doctrine-project.com/uploads/assets/api_release_points_list.png
   :align: center
   :alt: Doctrine Release Points
   
   Doctrine Release Points

Edit Doctrine Release
---------------------

Edit a Doctrine release and control its stability as well as other
information.

.. figure:: http://www.doctrine-project.com/uploads/assets/edit_api_release.png
   :align: center
   :alt: Edit Doctrine Release
   
   Edit Doctrine Release

Edit Blog Post
--------------

Edit a blog post entry using markdown syntax.

.. figure:: http://www.doctrine-project.com/uploads/assets/edit_blog_post.png
   :align: center
   :alt: Edit Blog Post
   
   Edit Blog Post

        **NOTE** The source of this website can be downloaded via SVN at
        `http://www.doctrine-project.org/svnweb/trunk <http://www.doctrine-project.org/svnweb/trunk>`_
