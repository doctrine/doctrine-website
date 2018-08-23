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

Cookbook Article
================

This is a cookbook article that points to another subfolder that
exists in a directory one level back.

:ref:`Getting Started <../reference/getting-started>`

:ref:`Nested Reference <nested/nested>`

.. toctree::
    :depth: 3
    :glob:

    reference/*

    cookbook/*

    about
    example

**Test Nested TOC**

.. toctree::
    :depth: 1

    cookbook/nested/nested

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/cookbook/article.rst }}