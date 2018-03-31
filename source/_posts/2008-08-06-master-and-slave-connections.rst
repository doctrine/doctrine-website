---
title: Master and Slave Connections
menuSlug: blog
authorName: jwage 
authorEmail: 
categories: []
indexed: false
---
In this new cookbook recipe we demonstrate how you can setup
multiple connections and use them as master/slaves. All select
statements are issued to the slaves and any insert/update/delete
statements are issued to the master. This example accomplishes the
functionality by extending Doctrine\_Query and Doctrine\_Record.
This article is a perfect example of how you can extend and
override functionality in Doctrine to accomplish your needs when
Doctrine doesn't necessarily have a native solution ready to go for
you.
