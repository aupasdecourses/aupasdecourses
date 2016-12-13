<?php
class CleanEav extends Magmi_UtilityPlugin
{
	public function getPluginInfo()
	{
		return array("name"=>"Clean EAV tables",
					 "author"=>"Dweeves",
					 "version"=>"1.0.0");
	}
	
	public function runUtility()
	{
		$types=array("datetime","decimal","varchar","int");
		foreach($types as $type)
		{
			$tname=$this->tablename("catalog_product_entity_$type");
			$sql="DELETE FROM $tname WHERE value IS NULL";
			$this->delete($sql);
		}
			
		echo "EAV Cleaned";
	}
	
	public function getStatistics()
	{
		$this->connectToMagento();
		$types=array("datetime","decimal","varchar","int");
		$stats=array();
		foreach($types as $type)
		{
			$tname=$this->tablename("catalog_product_entity_$type");
			$sql="SELECT COUNT(t1.value_id) as total,COUNT(t2.value_id) as empty,ROUND(COUNT(t2.value_id)*100/COUNT(t1.value_id),3) as pc
				  FROM `$tname`as t1 
				  LEFT JOIN `$tname`as t2 ON t2.value_id=t1.value_id AND t2.value IS NULL";
			$result=$this->selectAll($sql);
			$result=$result[0];
			$stats[$type]=$result;
		}
		$this->disconnectFromMagento();
		return $stats;
	}
	
	
	public function getShortDescription()
	{
		return "This Utility cleans the eav tables from magento from NULL values generated by Magento ORM";
	}
	
}