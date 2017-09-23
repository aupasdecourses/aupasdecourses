<?php

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . '/Cms/Wysiwyg/ImagesController.php';
class Apdc_Media_Adminhtml_ApdcImageBrowserController extends Mage_Adminhtml_Cms_Wysiwyg_ImagesController
{
    /**
     * Fire when select image
     */
    public function onInsertAction()
    {
        $helper = Mage::helper('cms/wysiwyg_images');

        $filename = $this->getRequest()->getParam('filename');
        $filename = $helper->idDecode($filename);

        $fileurl = $helper->getCurrentUrl() . $filename;
        $mediaPath = str_replace(Mage::getBaseUrl('media'), '', $fileurl);
        $this->getResponse()->setBody($mediaPath);
    }
}
