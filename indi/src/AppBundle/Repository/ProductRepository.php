<?php
namespace AppBundle\Repository;

use AutoBundle\Repository\AbstractMageRepository;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ProductRepository extends AbstractMageRepository
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    protected function searchQuery($search, $qb)
    {
        $qb->andWhere('magic.name LIKE \'%' . str_replace('\'', '\'\'', $search) . '%\'');
    }
}
