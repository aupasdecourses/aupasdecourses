<?php 

/*  @author Pierre Mainguet
/   Functions used in headercommercant (page commerçant)
*/

class Apdc_Catalog_Block_Category_CategoryCommercant extends Mage_Adminhtml_Block_Catalog_Category_Abstract{

    /**
     * Retrieve thumbnail URL - made by Pierre Mainguet
     *
     * @return string
     */
    public function getThumbnailUrl($category)
    {
        $cur_category=Mage::getModel('catalog/category')->load($category->getId());
        $layer = Mage::getSingleton('catalog/layer');
        $layer->setCurrentCategory($cur_category);
        $image = $layer->getCurrentCategory()->getThumbnail();
        if (preg_match('/^wysiwyg\//', $image) || preg_match('/^catalog\/category\//', $image)) {
            return Mage::getBaseUrl('media') . DS . $image;
        } else {
            return Mage::getBaseUrl('media') . 'catalog/category/' . $image;
        }
    }
}
