<?php
namespace AppBundle\Repository;

use AutoBundle\Repository\AbstractMageRepository;

class ProductRepository extends AbstractMageRepository
{
    protected $modelName = 'catalog/product';

    /**
     * @inheritdoc
     */
    protected function searchQuery($search, $qb)
    {
        // TODO: reimplement
        $qb->andWhere('magic.name LIKE \'%' . str_replace('\'', '\'\'', $search) . '%\'');
    }
}
