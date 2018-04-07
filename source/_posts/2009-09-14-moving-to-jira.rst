---
title: Moving to JIRA
menuSlug: blog
authorName: romanb 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
While we really like Trac, especially its subversion integration,
it has a lot of shortcomings for bigger projects in the area of
project & issue/ticket management. Hence we decided to evaluate
alternatives and ended up with choosing
`JIRA <http://www.atlassian.com/software/jira/>`_. Its normally not
free software but the generous guys from Atlassian granted us a
free open source license. A big thanks from all of us to Atlassian
for their support of open source projects.

From now on all the project management, release management and
issue/ticket management will happen in our
`new JIRA instance <http://doctrine-project.org/jira>`_. While it
is open for everyone in read-only mode, we strongly encourage you
to create an account soon so that you can create/modify/comment
issues and content in JIRA.

Trac is from now on closed for tickets. All new tickets need to be
reported through JIRA. We are not going to automatically import all
tickets from Trac to JIRA because that does not work very well and
would require to import all user accounts as well, which can not be
done easily either. We will continue to work on the "old" tickets,
always starting with porting a ticket over to JIRA before working
on it. If you want to help, you recreate tickets that are
especially important to you in JIRA yourself. You can still log in
to Trac and view tickets for the purpose of migrating them to JIRA
but you can not modify them or create new ones.

    **CAUTION** When you recreate an issue in JIRA, please make sure
    you close the issue in Trac with the resolution: "migrated".


Trac will not be completely shut down, however. The following
functionality will stay open:


-  Wiki
-  Timeline
-  Browse Source

That is, the wiki and the subversion integration. We basically use
Trac as the subversion viewer that we coupled with JIRA. Any
changeset numbers or file references in JIRA issues will link to
the changesets/sources in Trac.

We're looking forward to working with JIRA and we think it is an
improvement for all users. We hope you enjoy all the new
functionality provided by JIRA, like voting for issues that are
important to you, tracking issues and much more.

So, head over to our
`JIRA instance <http://doctrine-project.org/jira>`_ , create an
account and start creating issues and explore the features.
