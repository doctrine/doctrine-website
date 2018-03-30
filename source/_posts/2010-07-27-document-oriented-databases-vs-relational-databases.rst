---
title: MongoDB ODM: Document-Oriented Databases vs. Relational Databases.
authorName: avalanche123 
authorEmail: 
categories: []
indexed: false
---
`My last post <http://www.doctrine-project.org/blog/mongodb-for-ecommerce>`_
brought up a lot of questions on the differences between
document-oriented and relational databases, possible use cases for
each and approaches and gotchas one should remember when dealing
with either. I had some thoughts on the subject, but they didn't
feel complete, so I decided to do some research. I started out by
googling
`"document -oriented databases vs relational databases" <http://www.google.com/search?q=document+-oriented+databases+vs+relational+databases>`_ ,
which brought a number of interesting results. After some intense
reading and analyzing, I think I have a good enough understanding
of the concepts, strengths and weaknesses of different data stores
to write and share my findings.

Relational databases were traditionally the most obvious solution
for applications that needed to store retrieve/data. With the
growth of internet user-base, the number of reads and writes a
typical application needed to perform grew rapidly. This led to the
need for scaling. Traditional RDBMSs were hard to scale (SQL
operation or Transaction spanning multiple nodes doesn't scale
well). With solutions like
`MySQL Cluster <http://www.mysql.com/products/database/cluster/>`_
and
`Oracle RAC <http://www.oracle.com/technology/products/database/clustering/index.html>`_ ,
this is much less of a problem now, but it wasn't the case for a
while, which led to many companies abandoning traditional RDBMSs
for "noSQL" data stores.

    **SIDEBAR** Relational Databases, Object Databases, Key-Value
    Stores, Document Stores, and Extensible Record Stores:
    `A Comparison. <http://www.odbms.org/download/RickCattell.pdf>`_ By
    Rick Cattell:

    The NoSQL data stores can be categorized into three groups,
    according to their data model and functionality:

    
    -  Key-value Store provide a distributed index for object storage,
       where the objects are typically not even interpreted by the system:
       they are stored and handed back to the application as BLOBs, as in
       the popular memcached system I mentioned. However, these systems
       usually provide object replication for recovery, partitioning of
       the data over many machines, and rudimentary object persistence.
       Examples of key-value stores are memcached, Redis, Riak, Scalaris,
       and Voldemort.
    -  Document Stores provide more functionality: the system does
       recognize the structure of the objects stored. Objects (or
       documents) may have a variable number of named attributes of
       various types (integers, strings), objects can grouped into
       collections, and the system provides a simple query mechanism to
       search collections for objects with particular attribute values.
       Like the key-value stores, document stores can also partition the
       data over many machines, replicate data for automatic recovery, and
       persist the data. Examples of document stores are SimpleDB,
       CouchDB, MongoDB, and Dynamo.
    -  Extensible Record Stores, sometimes called wide column stores,
       provide a data model more like relational tables, but with a
       dynamic number of attributes, and like document stores, higher
       scalability and availability made possible by database partitioning
       and by abandoning database-wide ACID semantics. Examples of
       extensible records stores are BigTable, HBase, HyperTable, and
       Cassandra.


So when to use a document-oriented database and when to use a
relational database. The former is usually much better performing
and easier to scale, while doesn't provide ACID compliance and data
integrity that the later has by definition. This means that if we
choose to use document-oriented database, we get more performance
and scalability, but need to keep in mind, database level data
integrity, transactions and locks are no longer there and will need
to be embedded in the application logic itself, which will affect
how we write and structure our code. In my opinion,
document-oriented databases cannot replace relational databases,
and vice versa. Instead, they should be used to solve different
kinds of problems. At OpenSky we use both MySQL and MongoDB.
