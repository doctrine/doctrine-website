<?php

declare(strict_types=1);

namespace Doctrine\Website\Model;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Website\Model\Entity\EventParticipant;

/** @template-extends AbstractLazyCollection<int, EventParticipant> */
final class EventParticipants extends AbstractLazyCollection
{
    protected function doInitialize(): void
    {
        $this->collection = new ArrayCollection();
    }
}
