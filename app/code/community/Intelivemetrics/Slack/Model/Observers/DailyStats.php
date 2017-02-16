<?php

/**
 * Copyright (c) 2015, Intelive Metrics SRL
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
 * @copyright Copyright 2015, Intelive Metrics SRL (http://mageslack.com/)
 */
class Intelivemetrics_Slack_Model_Observers_DailyStats extends Intelivemetrics_Slack_Model_Observers_Abstract {

    protected function _getOrderStats($date) {
        $stats = array(
            'orders' => 0,
            'gt' => 0,
            'items' => 0
        );
        $collection = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToSelect('*');
        $collection->getSelect()
                ->where("DATE(created_at) = '{$date}'")
                ->where("state <> '" . Mage_Sales_Model_Order::STATE_CANCELED . "'")
        ;
        foreach ($collection as $order) {
            $stats['orders']++;
            $stats['gt']+=$order->getGrandTotal();
            $stats['items']+=$order->getTotalQtyOrdered();
        }
        
        return $stats;
    }
    
    protected function _getCustomerStats($date) {
        $stats = array(
            'customers' => 0,
        );
        $collection = Mage::getModel('customer/customer')
                ->getCollection();
        $collection->getSelect()
                ->where("DATE(created_at) = '{$date}'")
        ;
        foreach ($collection as $customer) {
            $stats['customers']++;
        }
        
        return $stats;
    }
    
    protected function _padToLength($text, $length=40){
        $formated=$text;
        for ($i = strlen($text); $i < $length; $i++) {
            $formated = $formated.' ';
        }
        
        return $formated;
    }
    
    protected function _makeSpace($length=10){
        $space='';
        for ($i = 0; $i < $length; $i++) {
            $space .= ' ';
        }
        return $space;
    }

    /**
     * Send a daily notification with yesterday's stats
     * @param $observer
     */
    public function notify($observer) {
        if ($this->_getConfig(Intelivemetrics_Slack_Model_Notification::DAILY_STATS_PATH)) {
            $_yesterday = strtotime("-1 day",  strtotime("12:00:00"));
            $yesterday = date('Y-m-d', $_yesterday);
            $orderStats = $this->_getOrderStats($yesterday);
            $customerStats = $this->_getCustomerStats($yesterday);
            
            echo $message = '*Orders:* '.$orderStats['orders']. $this->_makeSpace().
                        '*Products:* '.$orderStats['items'].$this->_makeSpace().
                        '*New Customers:* '.$customerStats['customers'].$this->_makeSpace().
                        '*Revenue:* '.number_format($orderStats['gt'],2,',','.') .$this->_makeSpace().
                        '*AOV:* '.number_format($orderStats['gt']/$orderStats['orders'],2,',','.').$this->_makeSpace().
                        '*AVG Items/Order:* '.round($orderStats['items']/$orderStats['orders'],2)
                    ;
            $this->_notificationModel
                    ->setMessage($message)
                    ->setIcon(':date:')
                    ->setPretext("Unlock critical eCommerce KPI's with <http://www.unityreports.com/?utm_source=slackgento&utm_medium=slack&utm_content=pretext%20link%20&utm_campaign=slackgento|UnityReports>")
                    ->setUsername('Stats for: '.date('l, M jS, Y', $_yesterday))
                    ->send();
        }
    }

}
