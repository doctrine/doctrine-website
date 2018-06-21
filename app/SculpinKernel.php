<?php

declare(strict_types=1);

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;

class SculpinKernel extends AbstractKernel
{
    /**
     * @return string[]
     */
    protected function getAdditionalSculpinBundles() : array
    {
        return ['Doctrine\Website\DoctrineSculpinBundle\DoctrineSculpinBundle'];
    }
}
