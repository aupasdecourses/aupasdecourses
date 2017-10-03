<?php

//Previous issue with this function inside Export.php. Maybe because the latter extends a custom class ?

class Apdc_Dispatch_Model_Cron
{
	public function processCronFtp(){
		Mage::log("Model Export - start processCrontFtp",null,"export.log");
		try{
			Mage::getModel("apdcdispatch/export")->processRequest($params);
		}catch(Exception $e){
			Mage::log("Model Export - error",null,"export.log");
			Mage::log("Model Export - ".$e->getMessage(),null,"export.log");
		}
	}
}