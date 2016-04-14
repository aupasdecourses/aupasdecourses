<?php
class Pmainguet_Validateaddress_Model_Observer
{
  public function validate_method($observer) { 
    $event = $observer->getEvent(); //Fetches the current event
    $eventmsg = "Current Event Triggered : " . $event->getName();
    Mage::log($eventmsg);
  }
}
?>