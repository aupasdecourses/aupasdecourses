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

    public function getMainRole()
    {
        if ($this->hasRole('ROLE_ADMIN') || $this->isSuperAdmin()) {
            return 'ROLE_ADMIN';
        }

        return 'ROLE_USER';
    }
}
