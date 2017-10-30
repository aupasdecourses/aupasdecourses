<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Invoice view  comments form
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Apdc_Sales_Block_Order_Info extends Mage_Core_Block_Template
{
    protected $_links = array();
    protected $websiteId = null;
    protected $neighborhood = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/info.phtml');
    }

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Order # %s', $this->getOrder()->getRealOrderId()));
        }
        $this->setChild(
            'payment_info',
            $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment())
        );
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function addLink($name, $path, $label)
    {
        $this->_links[$name] = new Varien_Object(array(
            'name' => $name,
            'label' => $label,
            'url' => empty($path) ? '' : Mage::getUrl($path, array('order_id' => $this->getOrder()->getId()))
        ));
        return $this;
    }

    public function getLinks()
    {
        $this->checkLinks();
        return $this->_links;
    }

    private function checkLinks()
    {
        $order = $this->getOrder();
        if (!$order->hasInvoices()) {
            unset($this->_links['invoice']);
        }
        if (!$order->hasShipments()) {
            unset($this->_links['shipment']);
        }
        if (!$order->hasCreditmemos()) {
            unset($this->_links['creditmemo']);
        }
    }

    /**
     * Get url for reorder action
     *
     * @deprecated after 1.6.0.0, logic moved to new block
     * @param Mage_Sales_Order $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        $params = [
            'order_id' => $order->getId()
        ];
        if (!$this->isSameNeighborhoodAsCurrent($order)) {
            $params['neighborhood_id'] = $this->getNeighborhood($order)->getId();
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/reorder', $params);
        }
        return $this->getUrl('sales/order/reorder', $params);
    }

    /**
     * Get url for printing order
     *
     * @deprecated after 1.6.0.0, logic moved to new block
     * @param Mage_Sales_Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
        }
        return $this->getUrl('sales/order/print', array('order_id' => $order->getId()));
    }

    /**
     * getNeighborhood 
     * 
     * @param Mage_Sales_Model_Order $order order 
     * 
     * @return string
     */
    public function getNeighborhoodName($order)
    {
        $neighborhood = $this->getNeighborhood($order);
        return $neighborhood->getName();
    }

    /**
     * getNeighborhood 
     * 
     * @param Mage_Sales_Model_Order $order order 
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood
     */
    public function getNeighborhood($order)
    {
        $websiteId = $this->getWebsiteIdByStoreId($order->getStoreId());
        if (is_null($this->neighborhood)) {
            $neighborhoods = Mage::helper('apdc_neighborhood')->getNeighborhoodsByWebsiteId($websiteId);
            $this->neighborhood = $neighborhoods->getFirstItem();
        }
        return $this->neighborhood;
    }

    /**
     * getWebsiteIdByStoreId 
     * 
     * @param int $storeId storeId 
     * 
     * @return int
     */
    protected function getWebsiteIdByStoreId($storeId)
    {
        if (is_null($this->websiteId)) {
            $this->websiteId = Mage::getModel('core/store')->load($storeId)->getWebsite()->getId();
        }
        return $this->websiteId;
    }

    /**
     * isSameNeighborhoodAsCurrent 
     * 
     * @param Mage_Sales_Model_Order $order order 
     * 
     * @return boolean
     */
    public function isSameNeighborhoodAsCurrent($order)
    {
        if (Mage::helper('apdc_neighborhood')->getCurrentNeighborhood() && 
            $this->getNeighborhood($order)->getId() == Mage::helper('apdc_neighborhood')->getCurrentNeighborhood()->getId()
        ) {
            return true;
        }
        return false;
    }
}
