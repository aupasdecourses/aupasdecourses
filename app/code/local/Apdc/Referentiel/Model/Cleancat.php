<?php

class Apdc_Referentiel_Model_Cleancat extends Mage_Core_Model_Abstract
{
    private $_cat;
    private $_haschanged=false;
    private $_isdeleted=false;
    private $_message="";

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/cleancat');
    }

    public function process($cat){
        $this->_cat=$cat;
        if (!$this->eraseerrorcat()) {
            $this->setimagecat();
            $this->setinfocat();
            if (!$this->setsmallcat()) {
                $this->disableshop();
                $this->deactivatesubcat();
                $this->sortcat();
                $this->fixlevel2();
            }
        }
        if ($this->_haschanged && !$this->_isdeleted) {
            $this->_cat->save();
            echo $this->_message;
        }
    }

    private function _setData($att, $value)
    {
        if ($this->_cat->getData($att) <> $value) {
            $this->_cat->setData($att, $value);
            $this->_haschanged = true;
            $this->_message .='Fix ' . $att . ' for Level ' . $this->_cat->getLevel() . ' cat: ' . $this->_cat->getId() . ' / ' . $this->_cat->getName() . ' (new value = ' . $value . ")\n";
            return true;
        }else{
            return false;
        }
    }

    /**
     * eraseerrorcat()
     * 
     * Delete categories that are listed in $forbidden array
     */
    public function eraseerrorcat()
    {
        if (in_array($this->_cat->getName(), Mage::getSingleton('apdc_referentiel/categoriesbase')->forbidden)) {
            $this->_cat->delete();
            echo 'Delete Forbidden Cat: ' . $this->_cat->getId() . ' / ' . $this->_cat->getName() . "\n";
            $this->_isdeleted=true;
            return true;
        }else{
            return false;
        }
    }

    /**
     * setimagecat()
     * 
     * Apdc_Referentiel_Model_Categoriesbase
     * 
     * Set Image for current category based on infos from Categoriesbase model
     */
    public function setimagecat()
    {
        $ics = Mage::getModel('apdc_referentiel/categoriesbase')->getCollection()->getImageRef();
        $url = '';
        if ($this->_cat->getLevel() > 2 && $this->_cat->getLevel()<5) {
            if ($this->_cat->getThumbnail() == null) {
                if (isset($ics[$this->_cat->getName()])) {
                    $rand = rand(0, sizeof($ics[$this->_cat->getName()]) - 1);
                    $url = $ics[$this->_cat->getName()][$rand];
                    $this->_setData('image', $url);
                    $this->_setData('thumbnail', $url);
                } elseif ($this->_cat->getLevel() == 3) {
                    $shops = Mage::getModel('apdc_commercant/shop')->getCollection();
                    $shops->addFieldToFilter('name', $this->_cat->getName());
                    $i = $shops->getFirstItem();
                    $img = $i->getData('category_image');
                    if ($img <> null && $img <> '') {
                        $this->setData('image', $img);
                    }
                    $th = $i->getData('category_thumbnail');
                    if ($th <> null && $th <> '') {
                        $this->_setData('thumbnail', $th);
                    }
                } else {
                    $url = $ics['Default'][0];
                    $this->_setData('image', $url);
                    $this->_setData('thumbnail', $url);
                }
            } elseif ($this->_cat->getThumbnail() == $ics['Default'][0]) {
                if (isset($ics[$this->_cat->getName()])) {
                    $rand = rand(0, sizeof($ics[$this->_cat->getName()]) - 1);
                    $url = $ics[$this->_cat->getName()][$rand];
                    $this->setData('image', $url);
                    $this->setData('thumbnail', $url);
                }
            }
        }
    }

    public function setinfocat()
    {
        $url = '';
        if ($this->_cat->getLevel() == 2) {
            $this->_setData('name', trim($this->_cat->getName()));
            if ($this->_cat->getIsClickable() == 1) {
                $this->_setData('is_clickable',0);
            }
        }
        if ($this->_cat->getLevel() == 3) {
            if ($this->_cat->getParentCategory()->getName() == 'Envies') {
                $this->_setData('is_clickable', 0);
            } else {
                if (!$this->hasChildren() && $this->_cat->getIsClickable() == 1) {
                    $this->_setData('is_clickage',0);
                } elseif ($this->hasChildren() && $this->_cat->getIsClickable() == 0) {
                    $this->_setData('is_clickable',1);
                }
            }
        }
        if ($this->_cat->getLevel() > 3 && $this->_cat->getIsActive() == 1 && $this->_cat->getIsClickable() == 0) {
            $this->_setData('is_clickable',1);
        }
        //A désactiver en temps normal
        // $this->_setData('show_in_navigation',1);
        if ($this->_cat->getLevel() == 2 && $this->_cat->getName() == 'Caviste') {
            if ($this->_cat->getShowAgePopup() == 0) {
                $this->_setData('show_age_popup',1);
            }
            $subcats = $this->_cat->getChildrenCategories();
            foreach ($subcats as $subcat) {
                if ($subcat->getData('show_age_popup') <> 0) {
                    $subcat->_setData('show_age_popup', 0);
                    $this->_message = 'Fix show age popup of subcat of ' . $this->_cat->getName() . ' => ' . $subcat->getId() . ' - ' . $subcat->getName();
                }
            }
        }
        $this->_setData('display_mode', 'PRODUCTS');
        if ($this->_cat->getMetaTitle() == '' || $this->_cat->getMetaTitle() == null) {
            $parent_name = $this->_cat->getParentCategory()->getName();
            $this->_setData('metaTitle',$parent_name . ' - ' . $this->_cat->getName());
        }
    }

    public function setsmallcat()
    {
        if ($this->_cat->getLevel() <= 2) {
            return;
        } else {
            $count = $this->_cat->getProductCollection()->count();
            if ($count == 0) {
                $this->_cat->delete();
                echo 'Delete Cat with 0 products: ' . $this->_cat->getId() . ' / ' . $this->_cat->getName() . "\n";
                $this->_isdeleted = true;
                return true;
            }
            if ($count <= Mage::getSingleton('apdc_referentiel/categoriesbase')->limitsmall && $this->_cat->getIsActive() == 1) {
                $this->_setData('is_active',0);
                return false;
            } elseif ($count > Mage::getSingleton('apdc_referentiel/categoriesbase')->limitsmall && $this->_cat->getIsActive() == 0 && $this->_cat->getParentCategory()->getIsActive() == 1 && $this->_cat->getParentCategory()->getName() <> 'Envies') {
                $this->_setData('is_active', 1);
                return false;
            }
        }
    }

    public function disableshop()
    {
        if ($this->_cat->getLevel() == 3) {
            $shopid = Mage::getResourceModel('apdc_commercant/shop')->getShopIdByCategoryId($this->_cat->getId());
            if ($shopid <> false) {
                $shops = Mage::getModel('apdc_commercant/shop')->getCollection();
                $shops->addFieldToFilter('id_shop', [$shopid]);
                $i = $shops->getFirstItem();
                $status = $i->getData('enabled');
                if ($status == 0 && $this->_cat->getIsActive() == 1) {
                    $this->_setData('is_active',0);
                }
                //A réactiver une fois qu'il n'y a plus de catégories fantomes de commerçants
                // elseif($status && $this->_cat->getIsActive()==0){
                //     $this->_setData('is_active',1);
                // }
            } else {
                $this->_setData('is_active', 1);
            }
        }
    }

    public function deactivatesubcat()
    {
        if ($this->_cat->getLevel() <= 3) {
            return;
        } else {
            if ($this->_cat->getParentCategory()->getIsActive() == 0 && $this->_cat->getIsActive() == 1) {
                $this->_setData('is_active',0);
            }
        }
    }

    public function sortcat()
    {
        $ics = Mage::getModel('apdc_referentiel/categoriesposition')->getPositionRef();
        if ($this->_cat->getLevel() <= 1) {
            return;
        } elseif ($this->_cat->getLevel() == 3) {
            $counter_l3 = 10;
            if (in_array(explode(' ', $this->_cat->getName())[0], Mage::getSingleton('apdc_referentiel/categoriesbase')->maincats_l3)) {
                $this->_setData('position', 0);
            } else {
                $result = $this->_setData('position', $counter_l3);
                if ($result) {
                    $counter_l3 += 10;
                }
            }
        } else {
            if (isset($ics[$this->_cat->getName()])) {
                $this->_setData('position', $ics[$this->_cat->getName()][0]);
            }
        }
    }

    public function fixlevel2()
    {
        if ($this->_cat->getLevel() == 2) {
            if ($this->_cat->getIsActive() == 0 && $this->hasChildren()) {
                $this->_setData('is_active',1);
            }
            if (!isset(Mage::getSingleton('apdc_referentiel/categoriesbase')->bgcolors_l1[$this->_cat->getName()])) {
                echo 'WARNING ' . $this->_cat->getName() . ' (' . $this->_cat->getId() . ") DOESN'T EXIST IN THE TABLE OF LEVEL 2 CATS !!!!\n";
            }
            $this->_setData('menu_bg_color', Mage::getSingleton('apdc_referentiel/categoriesbase')->bgcolors_l1[$this->_cat->getName()]);
            $this->_setData('menu_text_color', Mage::getSingleton('apdc_referentiel/categoriesbase')->textcolor_l1);
        }
    }

    public static function setcorrectchildrennumber()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('catalog/category');
        $query_read = "SELECT p.entity_id, p.children_count, COUNT(c.entity_id) AS correct_children_count, COUNT(c.entity_id) - p.children_count AS child_diff FROM catalog_category_entity p LEFT JOIN catalog_category_entity c ON c.path LIKE CONCAT(p.path,'/%') WHERE 1 GROUP BY p.entity_id HAVING correct_children_count != p.children_count";
        $query_write = "UPDATE catalog_category_entity c SET children_count = (SELECT COUNT(*) FROM (SELECT * FROM catalog_category_entity) cc WHERE cc.path LIKE CONCAT(c.path, '/%') AND cc.path NOT LIKE CONCAT(c.path, '/%/%'));";
        $result = $readConnection->fetchAll($query_read);
        if ((int) $result[0]['children_count'] < 0) {
            $writeConnection->exec($query_write);
        }
    }

    public static function clearcache()
    {
        Mage::app('admin')->setUseSessionInUrl(false);
        Mage::getConfig()->init();
        $types = Mage::app()->getCacheInstance()->getTypes();
        try {
            echo "Cleaning data cache... \n";
            flush();
            Mage::app()->cleanCache();
            // foreach ($types as $type => $data) {
            //     echo "Removing $type ... ";
            //     echo Mage::app()->getCacheInstance()->clean($data["tags"]) ? "Cache cleared!" : "There is some error!";
            //     echo "\n";
            // }
        } catch (exception $e) {
            die('[ERROR:' . $e->getMessage() . ']');
        }
        try {
            flush();
            Mage::getModel('catalog/product_image')->clearCache();
            // echo Mage::app()->getCacheInstance()->clean() ? "Cache cleared!" : "There is some error!";
            // echo "\n\n";
        } catch (exception $e) {
            die('[ERROR:' . $e->getMessage() . ']');
        }
    }

}
