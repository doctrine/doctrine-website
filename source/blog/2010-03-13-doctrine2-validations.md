---
title: "Validation of Doctrine 2 Entities"
menuSlug: blog
layout: blog-post
controller: ['Doctrine\Website\Controllers\BlogController', 'view']
authorName: beberlei
authorEmail:
categories: []
permalink: /2010/03/13/doctrine2-validations.html
---
While Doctrine 1 had validation nested inside the `Doctrine_Record`
instance this is not the case in Doctrine 2 anymore. We won't ship
Doctrine 2 with any validators, the reason being that we think all the
frameworks out there already ship with quite decents ones that can be
integrated into your Domain easily. Besides us being ORM experts not
wanting to maintain yet another validation library, moving the
responsibility of validation into the domain layer also allows you to
integrate it much easier into frameworks form libraries for example.

What we do offer are hooks to execute any kind of validation inside the
Doctrine ORM.

Entities can register lifecycle event methods with Doctrine that are
called on different occasions. For validation we would need to hook into
the events called before persisting and updating. Even though we don't
support validation out of the box, the implementation is even simpler
than in Doctrine 1 and you will get the additional benefit of being able
to re-use your validation in any other part of your domain.

Say we have an `Order` with several `OrderLine` instances. We never want
to allow any customer to order for a larger sum than he is allowed to:

~~~~ {.sourceCode .php}
<?php
class Order
{
    public function assertCustomerAllowedBuying()
    {
        $orderLimit = $this->customer->getOrderLimit();

        $amount = 0;
        foreach ($this->orderLines AS $line) {
            $amount += $line->getAmount();
        }

        if ($amount > $orderLimit) {
            throw new CustomerOrderLimitExceededException();
        }
    }
}
~~~~

Now this is some pretty important piece of business logic in your code,
enforcing it at any time is important so that customers with a unknown
reputation don't owe your business too much money.

We can enforce this constraint in any of the metadata drivers. First
Annotations:

~~~~ {.sourceCode .php}
<?php
/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Order
{
    /**
     * @PrePersist @PreUpdate
     */
    public function assertCustomerAllowedBuying() {}
}
~~~~

In XML Mappings:

    [xml]
    <doctrine-mapping>
        <entity name="Order">
            <lifecycle-callbacks>
                <lifecycle-callback type="prePersist" method="assertCustomerAllowedBuying" />
                <lifecycle-callback type="preUpdate" method="assertCustomerAllowedBuying" />
            </lifecycle-callbacks>
        </entity>
    </doctirne-mapping>

YAML needs some little change yet, to allow multiple lifecycle events
for one method, this will happen before Beta 1 though.

Now validation is performed whenever you call
`EntityManager#persist($order)` or when you call `EntityManager#flush()`
and an order is about to be updated. Any Exception that happens in the
lifecycle callbacks will be catched by the EntityManager and the current
transaction is rolled back.

Of course you can do any type of primitive checks, not null,
email-validation, string size, integer and date ranges in your
validation callbacks.

~~~~ {.sourceCode .php}
<?php
class Order
{
    /**
     * @PrePersist @PreUpdate
     */
    public function validate()
    {
        if (!($this->plannedShipDate instanceof DateTime)) {
            throw new ValidateException();
        }

        if ($this->plannedShipDate->format('U') < time()) {
            throw new ValidateException();
        }

        if ($this->customer == null) {
            throw new OrderRequiresCustomerException();
        }
    }
}
~~~~

What is nice about lifecycle events is, you can also re-use the methods
at other places in your domain, for example in combination with your
form library. Additionally there is no limitation in the number of
methods you register on one particular event, i.e. you can register
multiple methods for validation in "PrePersist" or "PreUpdate" or mix
and share them in any combinations between those two events.

There is no limit to what you can and can't validate in "PrePersist" and
"PreUpdate" aslong as you don't create new entity instances. This was
already discussed in the previous blog post on the Versionable
extension, which requires another type of event called "onFlush".

Also read:

-   [Doctrine 2 Manual:
    Events](http://www.doctrine-project.org/documentation/manual/2_0/en/events#lifecycle-events)
-   [Doctrine 2 Blog: A reusable Versionable
    Behaviour](http://www.doctrine-project.org/blog/doctrine2-versionable)

