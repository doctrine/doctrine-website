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

Nested Cookbook
===============

Nested

:ref:`Test About <../../about>`

.. toctree::
    :depth: 1

    about

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/cookbook/nested/nested.rst }}