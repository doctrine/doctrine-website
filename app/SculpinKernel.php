<?php

declare(strict_types=1);

use Sculpin\Bundle\SculpinBundle\HttpKernel\AbstractKernel;

class SculpinKernel extends AbstractKernel
{
    protected function getAdditionalSculpinBundles()
    {
        return ['Doctrine\Website\DoctrineSculpinBundle\DoctrineSculpinBundle'];
    }
}
