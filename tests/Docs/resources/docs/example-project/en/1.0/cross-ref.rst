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

Cross Ref
=========

.. _cross_ref_anchor:

Cross Ref Section 1
-------------------

.. _cross_ref_section_1_anchor:

Cross Ref Section 2
-------------------

.. _cross_ref_section_2_anchor:

Cross Ref Section A
~~~~~~~~~~~~~~~~~~~

.. _cross_ref_section_a_anchor:

{% endverbatim %}

.. raw:: html
    {% endblock %}
{{ SOURCE_FILE:/en/cross-ref.rst }}