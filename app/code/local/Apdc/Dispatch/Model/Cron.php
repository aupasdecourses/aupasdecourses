<?php

//Previous issue with this function inside Export.php. Maybe because the latter extends a custom class ?

class Apdc_Dispatch_Model_Cron
{
	public function processCronFtp(){
		Mage::log("Model Export - start processCrontFtp",null,"disaptch.log");
		Mage::getModel("apdcdispatch/export")->processRequest($params);
	}
}