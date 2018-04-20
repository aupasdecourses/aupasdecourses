<?php

namespace Apdc\ApdcBundle\Services\Helpers;

//A mettre plutôt dans un repository comme l'a fait Frédéric pour l'appli (mais peut être pas nécessaire)

trait Order
{
    private function OrdersQuery($dfrom, $dto, $commercantId = -1, $orderId = -1)
    {
        $orders = \Mage::getModel('sales/order')->getCollection();
        $orders->getSelect()->join(
            'mwddate_store',
            'main_table.entity_id=mwddate_store.sales_order_id',
            array(
                'mwddate_store.ddate_id',
            )
        );
        $orders->getSelect()->join(
            'mwddate',
            'mwddate_store.ddate_id=mwddate.ddate_id',
            array(
                'ddate' => 'mwddate.ddate',
                'dtime' => 'mwddate.dtime',
            )
        );
        $orders->getSelect()->join(
            'mwdtime',
            'mwddate.dtime = mwdtime.dtime_id',
            array(
                'dtime' => 'mwdtime.interval',
            )
        );
        $orders->getSelect()->join(
            array(
                'order_attribute' => 'amasty_amorderattr_order_attribute',
            ),
            'order_attribute.order_id = main_table.entity_id',
            array(
                'contactvoisin' => 'order_attribute.contactvoisin',
                'codeporte1' => 'order_attribute.codeporte1',
                'codeporte2' => 'order_attribute.codeporte2',
                'batiment' => 'order_attribute.batiment',
                'etage' => 'order_attribute.etage',
                'telcontact' => 'order_attribute.telcontact',
                'info' => 'order_attribute.infoscomplementaires',
            )
        );
        $orders->getSelect()->joinLeft(
            array(
                'attachment' => 'amasty_amorderattach_order_field',
            ),
            'attachment.order_id = main_table.entity_id',
            array(
                'upload' => 'attachment.upload',
                'input' => 'attachment.input',
                'digest' => 'attachment.digest',
                'refund' => 'attachment.refund',
                'refund_shipping' => 'attachment.refund_shipping',
                'closure' => 'attachment.closure',
                'commentaire_commercant' => 'attachment.commentaires_commercant',
                'commentaire_client' => 'attachment.commentaires_client',
            )
        );
        $orders->addFilterToMap('ddate', 'mwddate.ddate');
        $orders->addFilterToMap('dtime', 'mwdtime.interval')
            ->addFieldToFilter('main_table.status', array('nin' => array('pending_payment', 'payment_review', 'holded', 'canceled')))
            ->addAttributeToSort('main_table.increment_id', 'dsc');
        if ($orderId != -1) {
            $orders->addFieldToFilter('main_table.increment_id', ['eq' => $orderId]);
        } else {
            $orders->addAttributeToFilter('ddate', array(
                'from' => $dfrom,
                'to' => $dto,
            ));
            if ($commercantId != -1) {
                $orders->getSelect()->join(
                    array(
                        'order_item' => \Mage::getSingleton('core/resource')->getTableName('sales/order_item'),
                    ),
                    'order_item.order_id = main_table.entity_id'
                )->where("order_item.commercant={$commercantId}")->group('order_item.order_id');
            }
        }

        return $orders;
    }

    private function OrderHeaderParsing($order)
    {
        $orderHeader = [];
        $shipping = $order->getShippingAddress();
        $orderHeader['mid'] = $order->getData('entity_id');
        $orderHeader['id'] = $order->getData('increment_id');
        $orderHeader['store'] = \Mage::app()->getStore($order->getData('store_id'))->getName();
        $orderHeader['store_id'] = \Mage::app()->getStore($order->getData('store_id'))->getId();
        $orderHeader['status'] = $order->getStatusLabel();
        $orderHeader['upload'] = $order->getData('upload');
        $orderHeader['input'] = $order->getData('input');
        $orderHeader['digest'] = $order->getData('digest');
        $orderHeader['refund'] = $order->getData('refund');
        $orderHeader['refund_shipping'] = $order->getData('refund_shipping');
        $orderHeader['closure'] = $order->getData('closure');
        $orderHeader['commentaire_commercant'] = $order->getData('commentaire_commercant');
        $orderHeader['commentaire_client'] = $order->getData('commentaire_client');
        $orderHeader['customer_id'] = $order->getData('customer_id');
        $orderHeader['first_name'] = $shipping->getData('firstname');
        $orderHeader['last_name'] = $shipping->getData('lastname');
        $orderHeader['address'] = $shipping->getStreet()[0].' '.$shipping->getPostcode().' '.$shipping->getCity();
        $orderHeader['phone'] = $shipping->getTelephone();
        $orderHeader['mail'] = $order->getData('customer_email');
        $orderHeader['codeporte1'] = $order->getData('codeporte1');
        $orderHeader['codeporte2'] = $order->getData('codeporte2');
        $orderHeader['batiment'] = $order->getData('batiment');
        $orderHeader['etage'] = $order->getData('etage');
        $orderHeader['info'] = $order->getData('info');
        $orderHeader['contact'] = $order->getData('contactvoisin');
        $orderHeader['contact_phone'] = $order->getData('telcontact');
        $orderHeader['order_date'] = $order->getData('created_at');
        $orderHeader['delivery_date'] = $order->getData('ddate');
        $orderHeader['delivery_time'] = $order->getData('dtime');
        $orderHeader['equivalent_replacement'] = $order->getData('produit_equivalent');
        $orderHeader['shipping_method'] = $order->getData('shipping_method');
        $orderHeader['total_quantite'] = 0;
        $orderHeader['total_prix'] = 0.0;
        $orderHeader['products'] = [];

        if ($order->getData('refund_shipping')) {
            $orderHeader['refund_shipping_amount'] = $order->getShippingAmount() + $order->getShippingTaxAmount();
        } else {
            $orderHeader['refund_shipping_amount'] = 0;
        }

        return $orderHeader;
    }

    private function ProductParsing($product, $order_id)
    {
        $prod_data = [
            'id' => $product->getItemId(),
            'nom' => $product->getName(),
            'order_id' => $order_id,
            'prix_kilo' => $product->getPrixKiloSite(),
            'quantite' => round($product->getQtyOrdered(), 0),
            'description' => $product->getShortDescription(),
            'prix_unitaire' => round($product->getPriceInclTax(), 2),
            'prix_total' => round($product->getRowTotalInclTax(), 2),
            'commercant_id' => $product->getCommercant(),
            'refund_comment' => $product->getRefundComment(),
            'produit_fragile' => $product->getProduitFragile(),
            ];
        $prod_data['comment'] = '';
        $options = isset($product->getProductOptions()['options']) ? $product->getProductOptions()['options'] : null;
        if (!is_null($options)) {
            foreach ($options as $option) {
                $prod_data['comment'] .= $option['label'].': '.$option['value'].' | ';
            }
        }
        $prod_data['comment'] .= html_entity_decode($product->getData('item_comment'));

        $prod_data['nom_commercant'] = $this->_attributeArraysLabels['commercant'][$prod_data['commercant_id']];

        return $prod_data;
    }
}
