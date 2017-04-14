<?php
namespace AppBundle\Repository;

use AutoBundle\Repository\AbstractMageRepository;

class ProductRepository extends AbstractMageRepository
{
    protected $modelName = 'catalog/product';

    /**
     * @inheritdoc
     *
     * @see http://devdocs.magento.com/guides/m1x/magefordev/mage-for-dev-8.html#other-comparison-operators
     */
    protected function searchQuery($search, $qb)
    {
        $qb->addFieldToFilter('name', ['like' => $search]);
    }
}
