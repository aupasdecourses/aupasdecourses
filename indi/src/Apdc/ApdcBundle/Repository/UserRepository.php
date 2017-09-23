<?php

namespace Apdc\ApdcBundle\Repository;

use AutoBundle\Repository\AbstractRepository;

/**
 * UserRepository
 */
class UserRepository extends AbstractRepository
{
    /**
     * @inheritdoc
     */
    protected function filtersQuery($filters, $qb)
    {
        if (isset($filters['type'])) {
            $qb->andWhere('magic.shop IS NOT NULL');

            unset($filters['type']);
        }

        parent::filtersQuery($filters, $qb);
    }
}
