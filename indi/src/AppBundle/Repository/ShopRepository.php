<?php
namespace AppBundle\Repository;

use AutoBundle\Repository\AbstractRepository;

class ShopRepository extends AbstractRepository
{
    /** @var \AppBundle\Entity\Shop */
    protected $entity;

    public function increment($amount = 1)
    {
        $this->entity->setIncremental(($this->entity->getIncremental() + $amount));
        $this->save();

        return $this;
    }
}
