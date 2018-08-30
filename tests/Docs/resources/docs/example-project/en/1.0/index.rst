.. raw:: html
    {% block sidebar %}

.. toctree::
    :depth: 3
    :glob:

    *

.. raw:: html
    {% endblock %}


.. raw:: html
    {% block content %}

{% verbatim %}

Index
=====

This is the index document.

Section
-------

This is a section.

Lists
-----

- List item 1
multiline
- List item 2
- List item 3
multiline

Alternate List Syntax
---------------------

-
    Alternate list item 1
    multiline
-
    Alternate list item 2
-
    Alternate list item 3
    multiline

Anchors
-------

`@Anchor Section`_

@Anchor Section
---------------

Anchors
-------

.. _lists:

`go to lists <#lists>`_

Links
-----

- :doc:`About1 <about>`
- :doc:`Example <example>`
- :ref:`About2 <about>`
- :ref:`Cross Ref <cross-ref>`
- `TestLink`_

.. _`TestLink`: https://www.doctrine-project.org

Reference Anchor
----------------

.. _test_reference_anchor:

-  :ref:`@Test Reference Anchor <test_reference_anchor>`

- :ref:`Cross Ref <cross_ref_anchor>`
- :ref:`Cross Ref Section 1 <cross_ref_section_1_anchor>`
- :ref:`Cross Ref Section 2 <cross_ref_section_2_anchor>`
- :ref:`Cross Ref Section A <cross_ref_section_a_anchor>`

Glob TOC
--------

.. tocheader:: Glob TOC Title

.. toc::

.. toctree::
    :depth: 3
    :glob:

    *

TOC
---

.. tocheader:: TOC Title

.. toc::

.. toctree::
    :depth: 3
    :glob:

    about
    cross-ref
    example
    index

Folder
------

- :ref:`Getting Started <reference/getting-started>`

.. code-block::

    <?php

    echo 'Hello World';

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/index.rst }}