<?php

/**
 * Class Apdc_Commercant_Block_Adminhtml_Form_Element_Closing
 */
class Apdc_Commercant_Block_Adminhtml_Form_Element_Closing extends Varien_Data_Form_Element_Abstract
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<div id="period_container">';

        $rowStr = json_encode($this->_getRowHtml());
        $script = "
        <script type=\"text/javascript\">
        var row = $rowStr;
        function addPeriod() {
            var e = document.createElement('div');
            e.innerHTML = row;
            document.getElementById('period_container').appendChild(e);
        }
        function removePeriod(btn) {
            btn.parentNode.remove();
        }
        </script>";

        $value = is_array($this->getValue()) ? $this->getValue() : [];
        foreach ($value as $rowValue) {
            $html .= '<div>'.$this->_getRowHtml($rowValue).'</div>';
        }
        $html .= '</div>';
        $html .= '<button type="button" onclick="addPeriod()">Add a period</button>';
        $html .= $script;
        return $html;
    }

    /**
     * @param array $rowValue
     *
     * @return string
     */
    protected function _getRowHtml($rowValue = null)
    {
        $row = '
<label>From : <input type="date" name="'.$this->getName().'[start][]" value="%s" placeholder="yyyy-mm-dd"/></label>
<label>to :  <input type="date" name="'.$this->getName().'[end][]" value="%s" placeholder="yyyy-mm-dd"/></label>
<button type="button" onclick="removePeriod(this)">Remove</button>';

        if ($rowValue === null) {
            $start = $end = '';
        } else {
            $start = $rowValue['start'];
            $end = $rowValue['end'];
        }
        $row = sprintf($row, $start, $end);
        return $row;
    }
}
