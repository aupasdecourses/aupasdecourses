<?php

/* @author Pierre Mainguet
/  Rewrite Customer Account Navigation
/  Method: In layout
/	<customer_account>
/       <reference name="customer_account_navigation">
/           <action method="removeLinkByName">
/           	<name>recurring_profiles</name>
/           </action>
*/

class Apdc_Customer_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    /*Remove links in account navigation*/
    public function removeLinkByName($name)
    {
        unset($this->_links[$name]);

        return $this;
    }
}
