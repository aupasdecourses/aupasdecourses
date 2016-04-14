<?php
/**
* @author Amasty Team
* @copyright Copyright (c) Amasty (http://www.amasty.com)
* @package Amasty_Deliverydate
*/
class Varien_Data_Form_Element_Deliverydate extends Varien_Data_Form_Element_Date
{
    protected $_what = false;
    
    public function getSqlValue()
    {
        if (empty($this->_value)) {
            return '';
        }
        return date('Y-m-d', $this->_value->getTimestamp());
    }
    
    public function getConfigFormat()
    {
        return Mage::getStoreConfig('amdeliverydate/date_field/format', $this->_getCurrentStore());
    }
    
    public function getElementHtml()
    {
        $this->addClass('input-text');
        
        $html = sprintf(
            '<input name="%s" id="%s" value="%s" %s style="width:110px !important;" />'
            .' <img src="%s" alt="" class="v-middle" id="%s_trig" title="%s" style="%s" />',
            $this->getName(), $this->getHtmlId(), $this->_escape($this->getValue()), $this->serialize($this->getHtmlAttributes()),
            $this->getImage(), $this->getHtmlId(), 'Select Date', ($this->getDisabled() ? 'display:none;' : '')
        );
        
        $html .= ' <input name="' . $this->getHtmlId() . '_hidden" type="hidden" id="' . $this->getHtmlId() . '_hidden" value="' . $this->getSqlValue() . '">';
        
        $outputFormat = $this->getFormat();
        if (empty($outputFormat)) {
            $outputFormat = $this->getConfigFormat();
        }
        $displayFormat = Varien_Date::convertZendToStrFtime($outputFormat, true, (bool)$this->getTime());
        
        // Disable
        $daysOfWeek = '';
        if (Mage::getStoreConfig('amdeliverydate/general/disabled_days', $this->_getCurrentStore())) {
            $daysOfWeek = 'if ([' . Mage::getStoreConfig('amdeliverydate/general/disabled_days', $this->_getCurrentStore()) . '].include(date.getDay()+1)) return true;';
        }
        $fixedHolidays = $this->_getHolidaysJs();
        $eachYearHolidays = $this->_getHolidaysJs('eq', 'neq', 'EachYear');
        $eachMonthHolidays = $this->_getHolidaysJs('neq', 'eq', 'EachMonth');
        $eachMYHolidays = $this->_getHolidaysJs('eq', 'eq', 'EachMY');
        $dateIntervals = $this->_getDateIntervalsJs();
        $minMaxDateInterval = $this->_getMinMaxDateIntervalJs();
        
        $html .= sprintf('
            <script type="text/javascript">
            //<![CDATA[
                Calendar.setup({
                    inputField: "%s",
                    ifFormat: "%s",
                    showsTime: %s,
                    button: "%s_trig",
                    align: "Bl",
                    singleClick : true,
                    disableFunc: function(date) {
                        %s
                        %s
                        %s
                        %s
                        %s
                        %s
                        %s
                        return false;
                    }
                });
            //]]>
            </script>',
            $this->getHtmlId(), $displayFormat,
            $this->getTime() ? 'true' : 'false', $this->getHtmlId(),
            // Disable
            $daysOfWeek,
            $fixedHolidays,
            $eachYearHolidays,
            $eachMonthHolidays,
            $eachMYHolidays,
            $dateIntervals,
            $minMaxDateInterval
        );

        $html .= $this->getAfterElementHtml();

        return $html;
    }
    
    private function _getCurrentStore()
    {
        return Mage::app()->getStore()->getId();
    }
    
    private function _getStringDate($holiday, $year, $month)
    {
        $stringDate = '"';
        if ('neq' == $year) {
            $stringDate .= $holiday->getYear() . '-';
        }
        if ('neq' == $month) {
            $stringDate .= $holiday->getMonth() . '-';
        }
        $stringDate .= $holiday->getDay() . '",';
        return $stringDate;
    }
    
    private function _getJsVar($name, $year, $month)
    {
        $jsVar = 'var ' . $name . ' = ';
        if ('neq' == $year) {
            $jsVar .= 'String(date.getFullYear()) + - +';
        }
        if ('neq' == $month) {
            $jsVar .= 'String(date.getMonth()+1) + - +';
        }
        $jsVar .= 'String(date.getDate());';
        return $jsVar;
    }
    
    private function _includeSameNextDayAndShippingQuota($year, $month)
    {
        $stringDate = '';
        if (('neq' == $year) && ('neq' == $month)) {
            $currentStore = $this->_getCurrentStore();
            $now = date('U') + 3600 * Mage::getStoreConfig('amdeliverydate/general/offset', $currentStore); // 60 min. * 60 sec. = 3600 sec.
            if (Mage::getStoreConfig('amdeliverydate/general/enabled_same_day', $currentStore)) {
                list($h, $m, $s) = explode(',', Mage::getStoreConfig('amdeliverydate/general/same_day', $currentStore));
                $disableAfterSrc = date('Y', $now) . '-' . date('m', $now) . '-' . date('d', $now) . ' ' . $h . ':' . $m . ':' . $s;
                $disableAfter = strtotime($disableAfterSrc);
                if ($disableAfter <= $now) {
                    $stringDate = '"' . date('Y-n-j', $now) . '"';
                }
            }
            
            if (Mage::getStoreConfig('amdeliverydate/general/enabled_next_day', $currentStore)) {
                list($h, $m, $s) = explode(',', Mage::getStoreConfig('amdeliverydate/general/next_day', $currentStore));
                $disableAfterSrc = date('Y', $now) . '-' . date('m', $now) . '-' . date('d', $now) . ' ' . $h . ':' . $m . ':' . $s;
                $disableAfter = strtotime($disableAfterSrc);
                if ($disableAfter <= $now) {
                    $nextSrc = date('Y', $now) . '-' . date('n', $now) . '-' . (date('j', $now)+1);
                    $next = strtotime($nextSrc);
                    if ($stringDate) {
                        $stringDate .= ',';
                    }
                    $stringDate .= '"' . date('Y-n-j', $next) . '"';
                }
            }
            
            if ($quota = Mage::getStoreConfig('amdeliverydate/general/shipping_quota')) {
                $days = Mage::getStoreConfig('amdeliverydate/general/min_days', $currentStore);
                $min = $now + 86400 * ((int)$days-1); // 24 h. * 60 min. * 60 sec. = 86400 sec.
                $collection = Mage::getModel('amdeliverydate/deliverydate')->getCollection();
                $collection->getOlderThan(date('Y-m-d', $min));
                if (0 < $collection->getSize()) {
                    $dates = array();
                    foreach ($collection as $delivery) {
                        $dates[] = $delivery->getDate();
                    }
                    $deliveries = array_count_values($dates);
                    foreach ($deliveries as $date => $count) {
                        if ($count >= $quota) {
                            $disable = strtotime($date);
                            if ($stringDate) {
                                $stringDate .= ',';
                            }
                            $stringDate .= '"' . date('Y-n-j', $disable) . '"';
                        }
                    }
                }
            }
        }
        return $stringDate;
    }
    
    private function _getHolidaysJs($year = 'neq', $month = 'neq', $jsVar = 'fixedDate')
    {
        $holidays = Mage::getModel('amdeliverydate/holidays')->getCollection();
        $holidays->addFieldToFilter('year', array($year => 0));
        $holidays->addFieldToFilter('month', array($month => 0));
        $sameNextDayAndQuota = $this->_includeSameNextDayAndShippingQuota($year, $month);
        $arr = '[' . $sameNextDayAndQuota . ']';
        if (0 < $holidays->getSize()) {
            $currentStore = $this->_getCurrentStore();
            $arr = '[';
            foreach ($holidays as $holiday) {
                $storeIds = trim($holiday->getStoreIds(), ',');
                $storeIds = explode(',', $storeIds);
                if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds)) {
                    continue;
                }
                $arr .= $this->_getStringDate($holiday, $year, $month);
            }
            $arr .= $sameNextDayAndQuota . ',';
            if (1 != strlen($arr)) {
                $arr = substr($arr, 0, -1); // remove last comma
            }
            $arr .= ']';
        }
        if (2 != strlen($arr)) {
            $js = $this->_getJsVar($jsVar, $year, $month) . ' if (' . $arr . '.include(' . $jsVar . ')) return true;';
        } else {
            $js = '';
        }
        return $js;
    }
    
    private function _getJsDateForInterval($year, $month, $day)
    {
        if (0 == $year) {
            $year = 'date.getFullYear()';
        }
        if (0 == $month) {
            $month = 'date.getMonth()';
        } else {
            $month = $month-1;
        }
        return ' new Date(' . join(',', array($year, $month, $day)) . ');';
    }
    
    private function _getDateIntervalsJs()
    {
        $collection = Mage::getModel('amdeliverydate/dinterval')->getCollection();
        $js = '';
        if (0 < $collection->getSize()) {
            $currentStore = $this->_getCurrentStore();
            foreach ($collection as $interval) {
                $storeIds = trim($interval->getStoreIds(), ',');
                $storeIds = explode(',', $storeIds);
                if (!in_array($currentStore, $storeIds) && !in_array(0, $storeIds)) {
                    continue;
                }
                $from = 'FromInterval' . $interval->getId();
                $to = 'ToInterval' . $interval->getId();
                $js .= 'var ' . $from . ' =' . $this->_getJsDateForInterval($interval->getFromYear(), $interval->getFromMonth(), $interval->getFromDay());
                $js .= 'var ' . $to . ' =' . $this->_getJsDateForInterval($interval->getToYear(), $interval->getToMonth(), $interval->getToDay());
                $js .= 'var What = new Date(date.getFullYear(), date.getMonth(), date.getDate());';
                $this->_what = true;
                $js .= 'if ((' . $from . ' <= What) && (What <= ' . $to . ')) return true;';
            }
        }
        return $js;
    }
    
    private function _getMinMaxDateIntervalJs()
    {
        $js = '';
        $currentStore = $this->_getCurrentStore();
        $now = date('U') + 3600 * Mage::getStoreConfig('amdeliverydate/general/offset', $currentStore);
        // Minimal Delivery Interval
        $days = Mage::getStoreConfig('amdeliverydate/general/min_days', $currentStore);
        $min = $now + 86400 * ((int)$days-1); // 24 h. * 60 min. * 60 sec. = 86400 sec.
        if (!$this->_what) {
            $js .= 'var What = new Date(date.getFullYear(), date.getMonth(), date.getDate());';
            $this->_what = true;
        }
        $js .= 'var Min =' . $this->_getJsDateForInterval((int)date('Y', $min), (int)date('n', $min), (int)date('j', $min));
        $js .= 'if (Min >= What) return true;';
        // Maximum Delivery Interval
        if ($days = Mage::getStoreConfig('amdeliverydate/general/max_days', $currentStore)) {
            $max = $now + 86400 * ((int)$days-1);
            if (!$this->_what) {
                $js .= 'var What = new Date(date.getFullYear(), date.getMonth(), date.getDate());';
                $this->_what = true;
            }
            $js .= 'var Max =' . $this->_getJsDateForInterval((int)date('Y', $max), (int)date('n', $max), (int)date('j', $max));
            $js .= 'if (Max < What) return true;';
        }
        
        return $js;
    }
}