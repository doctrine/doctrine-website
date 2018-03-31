---
title: MongoDB ODM: Atomic Operator Improvements
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
indexed: false
---
Recently we've improved the support for use of atomic operators and
how much it takes advantage of them in the Doctrine MongoDB ODM.
Now when updating embedded documents it will use dot annotation to
``$set`` the individual properties instead of sending the entire
document even if only one property changes.

Here is an example, first insert some new data:

.. code-block:: php

    <?php
    $address = new Address();
    $address->setStreet('6512 Mercomatic Ct');
    
    $user = new User();
    $user->setUsername('jwage');
    $user->setAddress($address);
    $user->addPhonenumber(new Phonenumber('6155139185'));
    
    $dm->persist($user);
    $dm->flush();

Now if we make some changes and ``flush()`` again it will perform
an update:

.. code-block:: php

    <?php
    $phonenumbers = $user->getPhonenumbers();
    
    $address->setCity('Nashville');
    $phonenumbers[0]->setPhonenumber('booooo');
    
    $user->addPhonenumber(new Phonenumber('1234'));
    
    $dm->flush();

The above will result in the following queries, first it must run a
``$set`` to modify existing embedded documents:

::

    Array
    (
        [$set] => Array
            (
                [address.city] => Nashville
                [phonenumbers.0.phonenumber] => booooo
            )
    
    )

Then it issues another update to ``$pushAll`` the new
phonenumbers:

::

    Array
    (
        [$pushAll] => Array
            (
                [phonenumbers] => Array
                    (
                        [0] => Array
                            (
                                [phonenumber] => 1234
                            )
    
                    )
    
            )
    
    )
