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

    /** @var  \AppBundle\Entity\Shop */
    protected $shop;

    public function getMainRole()
    {
        if ($this->hasRole('ROLE_ADMIN') || $this->isSuperAdmin()) {
            return 'ROLE_ADMIN';
        }

        return 'ROLE_USER';
    }

    /**
     * Set shop
     *
     * @param \AppBundle\Entity\Shop $shop
     *
     * @return User
     */
    public function setShop(\AppBundle\Entity\Shop $shop = null)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return \AppBundle\Entity\Shop
     */
    public function getShop()
    {
        return $this->shop;
    }
}
