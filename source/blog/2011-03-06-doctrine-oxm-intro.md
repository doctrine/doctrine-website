---
title: "A Doctrine OXM Introduction"
authorName: richardfullmer
authorEmail:
categories: []
permalink: /2011/03/06/doctrine-oxm-intro.html
---
Greetings programmers!

Some of you may have noticed a new project being hosted by Doctrine's
[github](https://github.com/doctrine) named [Object XML
Mapper(OXM)](https://github.com/doctrine/oxm). The OXM is the newest
member of the Doctrine family, and serves as persistence and marshalling
framework for PHP objects to XML, and back again. I'd like to take a
moment to introduce myself, the company I work for, and the pain points
behind the project's inception.

We at [Opensoft](http://www.opensoftdev.com/) have always been fans of
open source software, and love to give back to the community. I recently
was required to implement some vendor specific XML within a corporate
PHP application. The XML itself had the following requirements and pain
points:

-   900+ page specification... over 600 unique XML elements.
-   The XML is used as a workflow. Lots of pull xml, alter it slightly,
    and save it back
-   The XML must be available to the end user upon request.
-   Some of the XML had to be able to be sent via POST to other
    services, and parse responses back

Working with objects is so much better than working with such complex
XML.

As a PHP enthusiest and fan of Doctrine, I created a small project that
was capable of marshalling, unmarshalling, and persisting PHP objects to
XML using [Doctrine Common](https://github.com/doctrine/common) package
as a base, and many ideas from the ORM, and ODM projects. The Java
[Castor](http://www.castor.org) project also brought a lot of ideas to
give a high degree of control to the developer during the
(un)marshalling process. ClassMetadata is collected for each @XmlEntity
class and used to perform the mapping from PHP objects to native XML.

Right now, the API is still quite unstable, but we're working to change
that. Currently Doctrine users should find a lot of familiarity between
OXM and other Doctrine initiatives. We've tried to stay as close to the
Doctrine ways of doing things as possible. If you feel like this project
could help you make working with XML better, please send me a line, fork
the project, and send some pull requests!

Enjoy!
