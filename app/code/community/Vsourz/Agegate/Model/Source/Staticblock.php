<?php	
class Vsourz_Agegate_Model_Source_Staticblock
{
	protected $_options;
	public function toOptionArray()
	{
		if (!$this->_options) {
			$this->_options = Mage::getResourceModel('cms/block_collection')
			->load()
			->toOptionArray();
		}		
		return $this->_options;
	}
}
