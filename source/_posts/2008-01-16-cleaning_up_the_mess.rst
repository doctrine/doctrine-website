---
title: Cleaning up the mess
menuSlug: blog
authorName: guilhermeblanco 
authorEmail: 
categories: []
permalink: /:year/:month/:day/:basename.html
---
Starting from last blog post, development team went under some
general discussions to reach a commom sense regarding the project
versions. We are finally organizing our project from a
non-versioned stage to a versionable stage, which was causing a lot
troubles to everyone. Currently we are under heavy development to
reach a stable version of 1.0 release. Until here, we are under
some internal refactorings and we decided to create a branch which
we named 1.0beta2. Since then, we got a feature freeze version and
without big refactorings. The trunk went under big changes and we
decided to not keep the 1.0beta2 as the branch name; we renamed it
0.10. We know it is a big impact for those that checked-out the
branch, but we think it is safer to do it now instead of have to
deal with versions confusion in a near future. It's the best
organization we can reach and it's the last one (thanks God!). Now
that we have the branches 0.9 and 0.10, it is time to define minor
versions. We caught from our history what was the first release of
0.9 and we tagged it as 0.9.0. We also got the old 1.0beta2 release
and tagged it as 0.10-beta2. So currently we have 2 commit-free
tagged versions and 2 branches with feature freeze. We highly
discourage users from using trunk, since it is a very unstable
environment and your code may not work. Please update your
repository locations. These are the changes we did: tags/1.0beta1
=> tags/0.9.0 tags/1.0beta2 => tags/0.10-beta2 branches/1.0beta2 =>
branches/0.10 branches/0.9 => no change We also schedule new tagged
releases in a near future. The next releases will be 0.9.1 and
0.10-rc1. You can join #doctrine channel (at irc.freenode.net) or
group list to follow discussions regarding this subject. We
apologize the mess we caused to users until now and we hope to not
have this problem again, specially because we didn't have much
release management knowledge. Thanks.
