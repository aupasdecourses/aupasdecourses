<?php
namespace AppBundle\Repository;

use Apdc\ApdcBundle\Services\Magento;
use AutoBundle\Repository\AbstractMageRepository;

class OrderRepository extends AbstractMageRepository
{
    protected $modelName = 'sales/order';

    /**
     * @inheritdoc
     */
    public function __construct(Magento $mage)
    {
        parent::__construct($mage);

        // TODO - Note: All of that should be added only if we has for the field

        $this->model->getSelect()->join(
            'mwddate_store',
            'main_table.entity_id=mwddate_store.sales_order_id',
            [
                'mwddate_store.ddate_id',
            ]
        )->join(
            'mwddate',
            'mwddate_store.ddate_id=mwddate.ddate_id',
            [
                'ddate' => 'mwddate.ddate',
            ]
        )->join(
            'mwdtime',
            'mwddate.dtime = mwdtime.dtime_id',
            [
                'dtime' => 'mwdtime.interval',
            ]
        )->join(
            [
                'order_attribute' => 'amasty_amorderattr_order_attribute',
            ],
            'order_attribute.order_id = main_table.entity_id',
            [
                'order_attribute.codeporte1',
                'order_attribute.codeporte2',
                'order_attribute.batiment',
                'order_attribute.etage',
                'order_attribute.telcontact',
                'order_attribute.infoscomplementaires',
            ]
        )->join(
            'sales_flat_order_item',
            '`sales_flat_order_item`.order_id=`main_table`.entity_id',
            [
                'commercant' => 'sales_flat_order_item.commercant'
            ]
        )->joinLeft(
            ['shipping_o_a' => $this->model->getTable('sales/order_address')],
            '(main_table.entity_id = shipping_o_a.parent_id AND shipping_o_a.address_type = "shipping")',
            [
                'shipping_o_a.firstname',
                'shipping_o_a.middlename',
                'shipping_o_a.lastname',
                'shipping_o_a.telephone',
                'shipping_o_a.street',
                'shipping_o_a.postcode',
                'shipping_o_a.city'
            ]
        )->group('main_table.entity_id')
        ;

        $this->model->addFieldToFilter('main_table.status', array('nin' => array('complete', 'pending_payment', 'payment_review', 'holded', 'closed', 'canceled')));
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        $result = parent::find($id);
        
        $collection = $this->entity->getItemsCollection();
        $collection->getSelect()->join(
            'eav_attribute_option_value',
            'eav_attribute_option_value.option_id=main_table.commercant',
            [
                'commercant_name' => 'eav_attribute_option_value.value'
            ]
        );
        $collection->addFilterToMap('commercant_name', 'eav_attribute_option_value.value');

        \Mage::log($collection,null,"find.log");

        $result['items']=$collection->toArray()['items'];

        return $result;
    }

    /**
     * @inheritdoc
     *
     * @see http://devdocs.magento.com/guides/m1x/magefordev/mage-for-dev-8.html#other-comparison-operators
     */
    protected function searchQuery($search, $qb)
    {
         $qb->addFieldToFilter('shipping_description', ['like' => $search]);
    }
}
