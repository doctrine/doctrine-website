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

Example
=======

This is the example document.

Section
-------

This is a section.

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/example.rst }}