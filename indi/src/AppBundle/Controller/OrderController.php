<?php
namespace AppBundle\Controller;

use AutoBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    protected $entityName = 'Order';

    /** @var array The ACL for each Action */
    protected $acl = [
        'default' => 'ROLE_ADMIN',
        'list'    => 'ROLE_USER',
        'get'     => 'ROLE_USER',
    ];

    /** @var array Default orderBy used in indexAction */
    protected $defaultOrder = ['entity_id' => 'desc'];

    /** @var array  */
    protected $orderable = [];

    /** @var array  */
    protected $filterable = ['customer_id'];

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
        if ($this->isGranted('ROLE_ADMIN')) {
            return parent::getFilterBy($request);
        }

        $filters = parent::getFilterBy($request);
        $filters['customer_id'] = $this->getUser()->getId();

        return $filters;
    }
}
