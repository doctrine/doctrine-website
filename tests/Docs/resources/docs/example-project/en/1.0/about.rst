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

About
=====

This is the about document.

Section
-------

This is a section.

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/about.rst }}