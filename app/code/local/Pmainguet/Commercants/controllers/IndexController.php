<?php
	class Pmainguet_Commercants_IndexController extends Mage_Core_Controller_Front_Action
	{  
	  public function indexAction(){
	  	$this->loadLayout();
	  	$this->renderLayout();
	  }

	  public function activatecatAction(){
			$code='saintmartin';
	  		$storeid=intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});
			$categories = Mage::getModel('catalog/category')
					->getCollection()
			    	->addFieldToFilter('is_active', array('eq' => 1))
			        ->addAttributeToSelect('*');
			$rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();
			$categories->addAttributeToFilter('path', array('like' => "1/".$rootCategoryId."/%"));
			foreach($categories as $cat){
				$cat->setIsClickable(true);
				$cat->save();
			}
			echo "Done!";
			
	  }

	  public function creategeneralcatAction(){
			$code='saintmartin';
			$catnames=[
				"Boucher",
				"Primeur",
				"Fromager",
				"Poissonnier",
				"Caviste",
				//"Boulanger",
				//"Epicier"
			];

	  		$storeid=intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});

			$rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();
			foreach($catnames as $name){
				$parentCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
				$childCategory = Mage::getModel('catalog/category')->getCollection()
				    ->addAttributeToFilter('is_active', true)
				    ->addIdFilter($parentCategory->getChildren())
				    ->addAttributeToFilter('name', $name)
				    ->getFirstItem();
				if (null == $childCategory->getId()) {
			    	$parentId = $rootCategoryId;// id of parent category 
				    $category = Mage::getModel('catalog/category');
				    $category->setName($name);
				    $category->setIsActive(1); // to make active
				    $category->setIsAnchor(0); // This is for active anchor
				    $category->setStoreId($storeid);
				    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
				    $category->setPath($parentCategory->getPath());
				    $category->save();
		    		Echo "Category created!";
				} else {
				    echo "Category ".$name." already exist. Next!</br>";
				}
			}
			
	  }

	  public function createcommercantcatAction(){
	  		$code='saintmartin';
			$catnames=[
				"Boucher"=>"Boucherie Lévêque",
				"Primeur"=>"Verger Saint-Martin",
				"Fromager"=>"Fromagerie Bouvet",
				"Poissonnier"=>"Les Viviers de Noirmoutier",
				"Caviste"=>"La cave du marché Saint-Martin",
				];  		

	  		//create attribute option if do not exist
	  		$arg_attribute = 'commercant';
	  		$attr_model = Mage::getModel('catalog/resource_eav_attribute');
			$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
			$attr_id = $attr->getAttributeId();
			$attropt=$attr->getSource()->getAllOptions();
			$options=[];
			$setup_check=false;
			foreach($attropt as $opt){
				array_push($options,$opt['label']);
			}

			foreach($catnames as $parentcat => $childcat){
		    	if(!in_array($childcat,$options)){
		    		echo "Attribute option ".$childcat." CREATED!</br>";
		      		$option['attribute_id'] = $attr_id;
					$option['value'][$childcat][0] = $childcat;
					$setup_check=true;
		    	}else{
		    		echo "Attribute option ".$childcat." already EXISTS.Next!</br>";
		    	}
			}

			if($setup_check){
				$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
				$setup->addAttributeOption($option);
			}

			//Create categories and attributes option
	  		$storeid=intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});
	  		$rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();

	  		$oAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'att_com_id')->getSource()->getAllOptions();
			$options_comid=[];
			foreach($oAttribute as $opt){
				$options_comid[$opt['label']]=$opt['value'];
			}

			foreach($catnames as $parentcat => $childcat){
				$parentCategory = Mage::getModel('catalog/category')->getCollection()
				    ->addAttributeToFilter('is_active', true)
				    ->addAttributeToFilter('path', array('like' => "1/".$rootCategoryId."/%"))
				    ->addAttributeToFilter('name', $parentcat)
				    ->getFirstItem();
				$childCategory = Mage::getModel('catalog/category')->getCollection()
				    ->addAttributeToFilter('is_active', true)
				    ->addIdFilter($parentCategory->getChildren())
				    ->addAttributeToFilter('name', $childcat)
				    ->getFirstItem();
				if (null == $childCategory->getId()) {
			    	$parentId = $parentCategory->getId();// id of parent category 
				    $category = Mage::getModel('catalog/category');
				    $category->setName($childcat);
				    $category->setIsActive(1); // to make active
				    $category->setIsAnchor(1); // This is for active anchor
				    $category->setData("estcom_commercant",70);
				    $category->setData("att_com_id",$options_comid[$childcat]);
				    $category->setStoreId($storeid);
				    $parentCategory = Mage::getModel('catalog/category')->load($parentId);
				    $category->setPath($parentCategory->getPath());
				    $category->save();
		    		Echo "Category ".$childcat." CREATED!</br>";

				} else {
				    echo "Category ".$childcat." already EXISTS. Next!</br>";
				}
			}
	  }

	}

?>