<?php
/**
 * Copyright (c) 2015, Marcel Hauri
 * All rights reserved.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright 2015, Marcel Hauri (https://github.com/mhauri/magento-slack/)
 *
 * @category Notification
 * @package mhauri-slack
 * @author Marcel Hauri <marcel@hauri.me>, Sander Mangel <https://github.com/sandermangel>
 */

class Intelivemetrics_Slack_Model_Observers_NewOrder
 extends Intelivemetrics_Slack_Model_Observers_Abstract
{
    /**
     * Send a notification when a new order was placed
     * @param $observer
     */
    public function notify($observer)
    {
        if($this->_getConfig(Intelivemetrics_Slack_Model_Notification::NEW_ORDER_PATH)) {
            $order = $observer->getOrder();
            $url1 = Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId()));
            $url2 = Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit', array('id' => $order->getCustomer()->getId()));
            $message = $this->_helper->__("*Order ID:* <%s|%s>, *Customer:* <%s|%s %s>, *Amount:* %s %s",
                $url1,
                $order->getIncrementId(),
                $url2,
                $order->getCustomer()->getFirstname(),
                $order->getCustomer()->getLastname(),
                $order->getQuoteBaseGrandTotal(),
                $order->getOrderCurrencyCode()
            );

            $this->_notificationModel
                ->setMessage($message)
                ->setIcon(':package:')
                ->setUsername($this->_helper->__('New Order'))
                ->send();
        }
    }
}
