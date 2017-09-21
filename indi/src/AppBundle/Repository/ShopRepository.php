<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Shop;
use AutoBundle\Repository\AbstractRepository;

class ShopRepository extends AbstractRepository
{
    /** @var Shop */
    protected $entity;

    /**
     * @param int  $amount
     * @param Shop $entity
     *
     * @return $this
     */
    public function increment($amount = 1, $entity = null)
    {
        if (!$entity) {
            $entity = $this->entity;
        }

        $entity->setIncremental(($entity->getIncremental() + $amount));
        $this->save();

        return $this;
    }
}
