RST Examples
============

Versions
--------

Code
~~~~

.. code-block:: rst

    .. versionadded:: 2.2

        This was added new in version 2.2.

        .. code-block:: php

            echo 'Hello World';

Result
~~~~~~

.. versionadded:: 2.2

    This was added new in version 2.2.

    .. code-block:: php

        echo 'Hello World';

Code Examples
-------------

Code
~~~~

.. code-block:: rst

    .. code-block:: php

        echo 'Hello World';

.. code-block:: rst

    .. code-block:: console

        echo "Hello World"

Result
~~~~~~

.. code-block:: php

    echo 'Hello World';

.. code-block:: console

    echo "Hello World"

Boxes
-----

Code
~~~~

.. code-block:: rst

    .. caution::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. code-block:: rst

    .. hint::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. code-block:: rst

    .. note::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. code-block:: rst

    .. tip::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. code-block:: rst

    .. warning::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. code-block:: rst

    .. note::

        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

        .. versionadded:: 2.4

        .. code-block:: php

            echo 'Hello World';

        .. code-block:: console

            echo "Hello World"

Result
~~~~~~

.. caution::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. hint::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. note::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. tip::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. warning::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

.. note::

    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque dignissim feugiat imperdiet. Pellentesque porta elit lobortis lacinia fringilla. Donec scelerisque iaculis mi, ultricies molestie ipsum laoreet varius.

    .. versionadded:: 2.4

    .. code-block:: php

        echo 'Hello World';

    .. code-block:: console

        echo "Hello World"

Configuration Block
-------------------

RST
~~~

.. code-block:: rst

    .. configuration-block::

        .. code-block:: php

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

Result
~~~~~~

.. configuration-block::

    .. code-block:: php

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

RST
~~~

.. code-block:: rst

    - List Item 1

      - List Child 1
      - List Child 2

    - List Item 2
    - List Item 3

Result
~~~~~~

- List Item 1

  - List Child 1
  - List Child 2

- List Item 2
- List Item 3

Grid Tables
-----------

RST
~~~

.. code-block:: rst

    +------------------------+------------+----------+----------+
    | Header row, column 1   | Header 2   | Header 3 | Header 4 |
    | (header rows optional) |            |          |          |
    +========================+============+==========+==========+
    | body row 1, column 1   | column 2   | column 3 | column 4 |
    +------------------------+------------+----------+----------+
    | body row 2             | ...        | ...      |          |
    +------------------------+------------+----------+----------+

Result
~~~~~~

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

RST
~~~

.. code-block:: rst

    =====  =====  =======
    A      B      A and B
    =====  =====  =======
    False  False  False
    True   False  False
    False  True   False
    True   True   True
    =====  =====  =======

Result
~~~~~~

=====  =====  =======
A      B      A and B
=====  =====  =======
False  False  False
True   False  False
False  True   False
True   True   True
=====  =====  =======
