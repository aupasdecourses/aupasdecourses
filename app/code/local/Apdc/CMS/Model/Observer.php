<?php

class Apdc_CMS_Model_Observer
{
    public function cmsField($observer)
    {
        //get CMS model with data
        $model = Mage::registry('cms_page');
        //get form instance
        $form = $observer->getForm();
        //create new custom fieldset 'atwix_content_fieldset'
        $fieldset = $form->addFieldset('apdc_content_fieldset', array('legend'=>Mage::helper('cms')->__('Bannière'),'class'=>'fieldset-wide'));
        //add new field
		
		if(is_array($model->getImgBanner())) {
			$value = $model->getImgBanner()['value'];
		}
		else {
			$value = $model->getImgBanner();
		}
		
        $fieldset->addField('img_banner', 'image', array(
            'name'      => 'img_banner',
            'label'     => Mage::helper('cms')->__('Bannière'),
            'title'     => Mage::helper('cms')->__('Bannière'),
            'disabled'  => false,
            'value'     => $value
        ));
		
    }
	
	public function addFormEnctype($observer) {
		try {
			$block = $observer->getEvent()->getBlock();
			if ($block instanceof Mage_Adminhtml_Block_Cms_Page_Edit_Form){
				$form = $block->getForm();
				$form->setData('enctype', 'multipart/form-data');
				$form->setUseContainer(true);
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
		}
	}
	
	public function savePage($observer) {
		try {
			// Initialisations
			$model = $observer->getEvent()->getPage();
			$request = $observer->getEvent()->getRequest();
			$data = $request->getPost();
			$media_dir = 'cms_page'.DS;
			$media_path = Mage::getBaseDir('media').DS.$media_dir;

			// Looping on images attributes
			$imagesAttributesNames = array('img_banner');
			foreach($imagesAttributesNames as $imageAttributeName) {
				$uploadedImage = $_FILES[$imageAttributeName];
				if (isset($uploadedImage['name']) && $uploadedImage['name'] != '') {
					// Upload the image
					$uploader = new Varien_File_Uploader($imageAttributeName);
					$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'bmp'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					
					$file_name = 'cms_';  
					$rand_file_name = $file_name . $this->get_rand_filename($uploadedImage['name']);  
					
					$uploader->save($media_path, $rand_file_name);
					// Adjusting model
					$model->setData($imageAttributeName, $media_dir.$rand_file_name);
				} else {
					if(isset($data[$imageAttributeName]['delete']) && $data[$imageAttributeName]['delete'] == 1) {
						$model->setData($imageAttributeName, '');
					} else {
						$model->setData($imageAttributeName, $data[$imageAttributeName]['value']);
						unset($data[$imageAttributeName]);
					}
				}
			}
		}
		catch(Exception $e) {
			Mage::logException($e);
		}
	}
	
	public function get_rand_filename($base_file_name) {  
        $ext = pathinfo($base_file_name, PATHINFO_EXTENSION);  
        return Mage::getModel('core/date')->timestamp(time()) . '.' . $ext;  
    }  
	
}