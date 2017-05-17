<?php
namespace AppBundle\Repository;

use AutoBundle\Repository\AbstractMageRepository;

class OrderRepository extends AbstractMageRepository
{
    protected $modelName = 'sales/order';

    /**
     * @inheritdoc
     *
     * @see http://devdocs.magento.com/guides/m1x/magefordev/mage-for-dev-8.html#other-comparison-operators
     */
    protected function searchQuery($search, $qb)
    {
         $qb->addFieldToFilter('shipping_description', ['like' => $search]);
    }
}
