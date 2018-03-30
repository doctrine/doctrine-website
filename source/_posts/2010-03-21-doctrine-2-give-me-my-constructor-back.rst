---
title: Doctrine 2: Give me my constructor back
authorName: jwage 
authorEmail: 
categories: []
indexed: false
---
At
`ConFoo 2010 <http://www.doctrine-project.org/blog/doctrine-2-at-confoo-2010>`_
during my presentation, someone asked about the constructor of
entities in Doctrine 2 and whether or not it could be used. I think
this is something worth writing about since in Doctrine 1 this was
not possible. The constructor was hi-jacked from you and used
internally by Doctrine.

In Doctrine 2 it is possible to define the constructor in your
entity classes and is not required to be a zero argument
constructor! That's right, Doctrine 2 never instantiates the
constructor of your entities so you have complete control!

This is possible due to a small trick which is used by two other
projects,
`php-object-freezer <http://sebastian-bergmann.de/archives/831-Freezing-and-Thawing-PHP-Objects.html>`_
and Flow3. The gist of it is we store a prototype class instance
that is unserialized from a hand crafted serialized string where
the class name is concatenated into the string. The result when we
unserialize the string is an instance of the class which is stored
as a prototype and cloned everytime we need a new instance during
hydration.

Have a look at the method responsible for this:

.. code-block:: php

    <?php
    public function newInstance()
    {
        if ($this->_prototype === null) {
            $this->_prototype = unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->name), $this->name));
        }
        return clone $this->_prototype;
    }

The above code allows us to have entities where we make full use of
our constructor like the following class definition:

.. code-block:: php

    <?php
    namespace Entities;
    
    /** @Entity */
    class User
    {
        /** @Column */
        private $username;
    
        /** @Column */
        private $password;
    
        public function __construct($username, $password)
        {
            $this->username = $username;
            $this->password = md5($password);
        }
    }

Now of course you can use the constructor like you would expect:

.. code-block:: php

    <?php
    use Entities\User;
    
    $user = new User('jwage', 'changeme');

Conclusion
~~~~~~~~~~

It is an interesting solution and did not have much if any effect
on hydration performance. At any rate the cost is well worth it to
get complete control of your entities. No longer are the days where
Doctrine greedily steals your precious class constructor!
