<?php

namespace Apdc\ApdcBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\Accessor;

/**
 * User
 */
class User extends BaseUser
{
    /**
     * @var int
     */
    protected $id;

    /** @Accessor(getter="getMainRole") */
    protected $role;

    /**
     * User first name
     *
     * @var integer
     */
    private $shopId;

    public function getMainRole()
    {
        if ($this->hasRole('ROLE_ADMIN') || $this->isSuperAdmin()) {
            return 'ROLE_ADMIN';
        }

        return 'ROLE_USER';
    }

    /**
     * Set shopId
     *
     * @param integer $shopId
     *
     * @return User
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * Get shopId
     *
     * @return integer
     */
    public function getShopId()
    {
        return $this->shopId;
    }
}
