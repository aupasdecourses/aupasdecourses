<?php

namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\View as ViewTemplate;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    protected $entityName = 'Order';

    /** @var array The ACL for each Action */
    protected $acl = [
        'default' => 'ROLE_SUPER_ADMIN',
        'list'    => 'ROLE_USER',
        'get'     => 'ROLE_USER',
    ];

    /** @var array Default orderBy used in indexAction */
    protected $defaultOrder = ['entity_id' => 'desc'];

    /** @var array  */
    protected $orderable = ['increment_id', 'ddate'];

    /** @var array  */
    protected $filterable = ['commercant'];

    /**
     * @inheritdoc
     */
    public function getModel($name = null, $setForm = true)
    {
        return $this->get('apdc_apdc.repository.orders');
    }

    /**
     * @inheritdoc
     */
    protected function getFilterBy(Request $request)
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return parent::getFilterBy($request);
        }

        $filters = parent::getFilterBy($request);
        $filters['commercant'] = $this->getUser()->getShop()->getProductMerchant();

        return $filters;
    }

    /**
     * @inheritdoc
     *
     * @ViewTemplate()
     * @ApiDoc(output={})
     */
    public function getAction($id, Request $request)
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            return parent::getAction($id, $request);
        }

        $entity = parent::getAction($id, $request);

        $items = [];

        foreach ($entity['items'] as $item) {
            if ($item['commercant'] == $this->getUser()->getShop()->getProductMerchant()) {
                $items[] = $item;
            }
        }

        $entity['items'] = $items;

        return $entity;
    }
}
