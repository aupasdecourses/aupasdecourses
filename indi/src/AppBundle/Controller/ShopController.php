<?php

namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;

class ShopController extends AbstractController
{
    protected $entityName = 'Shop';

    /** @var array The ACL for each Action */
    protected $acl = [
        'default' => 'ROLE_ADMIN',
    ];

    /** @var array Default orderBy used in indexAction */
    protected $defaultOrder = ['name' => 'asc'];
}
