---
layout: default
title: RST Example
permalink: /rst-examples.html
---

RST Examples
============

.. sectionauthor:: Jonathan H. Wage <jonwage@gmail.com>

..

Jonathan H. Wage wrote these examples!

.. versionadded:: 2.2

    This was added new in version 2.2.

    .. code-block:: php

        echo 'Hello World';

.. versionadded:: 2.2

This was added new in version 2.2.

.. code-block:: php

    <?php

    echo 'Hello World';

.. code-block:: console

    echo "Hello World"

.. sectionauthor:: Marco

.. caution::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. hint::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. note::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. notice::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. tip::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. warning::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. note::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

    .. versionadded:: 2.4

    .. code-block:: php

        <?php

        echo 'Hello World';

    .. code-block:: console

        echo "Hello World"

.. configuration-block::

    .. code-block:: php

        <?php

        echo 'Hello World';


    .. code-block:: json

        {
            "Hello": "World"
        }

    .. code-block:: sql

        SELECT username, active FROM users WHERE username = 'jwage'

    .. code-block:: yaml

        username: jwage
        active: true

Lists
-----

- List Item 1
 - List Child 1
 - List Child 2
- List Item 2
- List Item 3

Grid Tables
-----------

+------------------------+------------+----------+----------+
| Header row, column 1   | Header 2   | Header 3 | Header 4 |
| (header rows optional) |            |          |          |
+========================+============+==========+==========+
| body row 1, column 1   | column 2   | column 3 | column 4 |
+------------------------+------------+----------+----------+
| body row 2             | ...        | ...      |          |
+------------------------+------------+----------+----------+

Simple Tables
-------------

=====  =====  =======
A      B      A and B
=====  =====  =======
False  False  False
True   False  False
False  True   False
True   True   True
=====  =====  =======
