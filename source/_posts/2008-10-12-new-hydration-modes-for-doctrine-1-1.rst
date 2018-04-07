---
title: New hydration modes for Doctrine 1.1
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
.. raw:: html

   <p>
   
I would like to announce the addition of two new hydration modes to
the 1.1 branch that will be included in the 1.1 release.

.. raw:: html

   </p><ul><li>
   
HYDRATE\_SCALAR - flat array where the key is made up of the query
component alias + field name. This method offers access to all the
same data in a flat array and the hydration process for it is much
faster.

.. raw:: html

   </li><li>
   
HYDRATE\_SINGLE\_SCALAR - Allows you to easily access single value
results, bypassing the expensive hydration process.

.. raw:: html

   </li></ul><p>
   
We feel that they fill an important gap between HYDRATE\_NONE and
HYDRATE\_RECORD/HYDRATE\_ARRAY.

.. raw:: html

   </p><p>
   
You can read more about the new hydration modes in the docs.
Starting at "Fetching data". You can also take a look at the new
test case.

.. raw:: html

   </p><p>
   
We encourage everyone to try them out and give us some feedback.
Note that this is a feature preview and the implementation and
syntax might change (or not) until the final 1.1 release, depending
on how many issues arise and depending on the feedback.

.. raw:: html

   </p>
