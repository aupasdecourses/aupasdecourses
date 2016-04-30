<?php

class Pmainguet_Commercants_IndexController extends Mage_Core_Controller_Front_Action
{
    private $_codemodel = 'batignolles';

    private $_rootcategory = 'Commercants Saint Martin';
    private $_rootcaturlkey = 'commercants-saint-martin';
    private $_codewebsite = 'apdc_saintmartin';
    private $_namewebsite = 'Au Pas De Courses Saint Martin';
    private $_namestoregroup = 'apdc_saintmartin';
    private $_codeboutique = 'saintmartin';
    private $_nameboutique = 'Saint Martin';

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    //Creation de la Root Category
    public function createrootcatAction()
    {
        $storeId = 0;
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);
        $category->setName($this->_rootcategory);
        $category->setUrlKey($this->_rootcaturlkey);
        $category->setIsActive(1);
        $category->setDisplayMode('PRODUCTS');
        $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        $parentCategory = Mage::getModel('catalog/category')->load($parentId);
        $category->setPath($parentCategory->getPath());
        $category->save();
        echo $this->_rootcategory.' created!';
    }

    //Creation de la Boutique, du Group Store et du Store
    public function createstoreAction()
    {
        //To create from the frontend
        Mage::registry('isSecureArea');
        //#addWebsite
        /** @var $website Mage_Core_Model_Website */
        $website = Mage::getModel('core/website');
        $website->setCode($this->_codewebsite)
            ->setName($this->_namewebsite)
            ->save();
        echo 'Website '.$this->_namewebsite.' created!</br>';

        //get root category id
        $category = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('name', $this->_rootcategory)
            ->getFirstItem();
        $categoryId = $category->getId();

        //#addStoreGroup
        /** @var $storeGroup Mage_Core_Model_Store_Group */
        $storeGroup = Mage::getModel('core/store_group');
        $storeGroup->setWebsiteId($website->getId())
            ->setName($this->_namestoregroup)
            ->setRootCategoryId($categoryId)
            ->save();
        echo 'Store Group '.$this->_namestoregroup.' created!</br>';

        //#addStore
        /** @var $store Mage_Core_Model_Store */
        $store = Mage::getModel('core/store');
        $store->setCode($this->_codeboutique)
            ->setWebsiteId($storeGroup->getWebsiteId())
            ->setGroupId($storeGroup->getId())
            ->setName($this->_nameboutique)
            ->setIsActive(1)
            ->save();
        echo 'Store '.$this->_nameboutique.' created!</br>';
    }

    //Change Scope of existing CMS Home Page
    public function cmspageAction()
    {
        $modelid = intval(Mage::getConfig()->getNode('stores')->{$this->_codemodel}->{'system'}->{'store'}->{'id'});
        $newstoreid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

        // //DUPLICATE HOMEPAGE

        // //Get home page of Model
        //$page= Mage::getModel('cms/page')->setStoreId($modelid)->load('home');

        // // $arraystore=$page->getStoreId();
        // // if(!in_array($newstoreid,$arraystore){
        // //     array_push($newstoreid,$arraystore);
        // // }
        // $cmsPageData=[
        //     'title'=>'Au Pas De Courses - Livraison de courses Saint Martin Paris 10e et 3e',
        //     'root_template'=>$page->getRootTemplate(),
        //     'meta_description'=>'Les livraisons de courses dans le 10e et 3e arrondissements avec Au Pas De Courses, pour bien manger avec les meilleurs commerçants de Paris. Essayez ce soir, c\'est facile!',
        //     'identifier'=>$page->getIdentifier(),
        //     'content_heading'=>$page->getContentHeading(),
        //     //'content'=>$page->getContent(),
        //     'custom_root_template'=>$page->getCustomRootTemplate(),
        //     'store_id'=>[$newstoreid],
        // ];
        // Mage::getModel('cms/page')->setData($cmsPageData)->save();
        // echo 'Homepage for '.$this->_storecode.' created!';

        //UPDATE CAROUSSEL & FOOTER BLOCKS
        $blocks = [
            'carroussel-home-page',
            'footer_links_middle',
            'footer_links_left',
            'cookie_restriction_notice_block',
            'footer_links_right',
            'footer_links_middle_2',
            'footer-social',
            'footer-cards',
        ];
        foreach ($blocks as $block) {
            $b = Mage::getModel('cms/block')->setStoreId($modelid)->load($block);
            $arraystore = $b->getStoreId();
            if (!in_array($newstoreid, $arraystore)) {
                array_push($arraystore, $newstoreid);
                $b->setStores($arraystore);
                $b->setStoreId($arraystore);
                $b->save();
                echo 'Block '.$block.' updated!</br>';
            }
            echo 'Block '.$block.' already OK.Next!</br>';
        }

        //UPDATE EXISTING PAGES
        $pages = [
            'annonce-livreurs',
            'autoriser-cookies',
            'besoin-d-aide',
            'no-route',
            'mentions-legales-cgv',
            'nos-engagements',
            'politique-confidentialite-restriction-cookie',
            'tarifs-livraison',
            'zone-et-horaire',
        ];
        foreach ($pages as $page) {
            $b = Mage::getModel('cms/page')->setStoreId($modelid)->load($page);
            $arraystore = $b->getStoreId();
            if (!in_array($newstoreid, $arraystore)) {
                array_push($arraystore, $newstoreid);
                $b->setStoreId($arraystore);
                $b->save();
                echo 'Page '.$page.' updated!</br>';
            }
            echo 'Page '.$page.' already OK.Next!</br>';
        }
    }

    //Création des catégories générales Commerçants
    public function creategeneralcatAction()
    {
        $code = 'saintmartin';
        $catnames = [
            'Boucher',
            'Primeur',
            'Fromager',
            'Poissonnier',
            'Caviste',
            'Boulanger',
            //"Epicier"
        ];

        $storeid = intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});

        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();
        foreach ($catnames as $name) {
            $parentCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
            $childCategory = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToFilter('is_active', true)
                ->addIdFilter($parentCategory->getChildren())
                ->addAttributeToFilter('name', $name)
                ->getFirstItem();
            if (null == $childCategory->getId()) {
                $parentId = $rootCategoryId;
                $category = Mage::getModel('catalog/category');
                $category->setName($name);
                $category->setIsActive(1);
                $category->setIsAnchor(0);
                $category->setIsClickable(false);
                $category->setStoreId($storeid);
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
                $category->save();
                echo 'Category created!</br>';
            } else {
                echo 'Category '.$name.' already exist. Next!</br>';
            }
        }
    }

    //Create commercants catégories and product attributes
    public function createcommercantcatAction()
    {
        $code = 'saintmartin';
        $catnames = [
            'Boucher' => 'Boucherie Lévêque',
            'Primeur' => 'Verger Saint-Martin',
            'Fromager' => 'Fromagerie Bouvet',
            'Poissonnier' => 'Les Viviers de Noirmoutier',
            'Caviste' => 'La cave du marché Saint-Martin',
            'Boulanger' => 'La cave du marché Saint-Martin',
            ];
          //create attribute option if do not exist
          $arg_attribute = 'commercant';
        $attr_model = Mage::getModel('catalog/resource_eav_attribute');
        $attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
        $attr_id = $attr->getAttributeId();
        $attropt = $attr->getSource()->getAllOptions();
        $options = [];
        $setup_check = false;
        foreach ($attropt as $opt) {
            array_push($options, $opt['label']);
        }

        foreach ($catnames as $parentcat => $childcat) {
            if (!in_array($childcat, $options)) {
                echo 'Attribute option '.$childcat.' CREATED!</br>';
                $option['attribute_id'] = $attr_id;
                $option['value'][$childcat][0] = $childcat;
                $setup_check = true;
            } else {
                echo 'Attribute option '.$childcat.' already EXISTS.Next!</br>';
            }
        }

        if ($setup_check) {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($option);
        }

        //Create categories and attributes option
          $storeid = intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});
        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();

        $oAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'att_com_id')->getSource()->getAllOptions();
        $options_comid = [];
        foreach ($oAttribute as $opt) {
            $options_comid[$opt['label']] = $opt['value'];
        }

        foreach ($catnames as $parentcat => $childcat) {
            $parentCategory = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToFilter('is_active', true)
                ->addAttributeToFilter('path', array('like' => '1/'.$rootCategoryId.'/%'))
                ->addAttributeToFilter('name', $parentcat)
                ->getFirstItem();
            $childCategory = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToFilter('is_active', true)
                ->addIdFilter($parentCategory->getChildren())
                ->addAttributeToFilter('name', $childcat)
                ->getFirstItem();
            if (null == $childCategory->getId()) {
                // id of parent category
                $parentId = $parentCategory->getId();
                $category = Mage::getModel('catalog/category');
                $category->setName($childcat);
                $category->setIsActive(1);
                $category->setIsAnchor(1);
                $category->setData('estcom_commercant', 70);
                $category->setData('att_com_id', $options_comid[$childcat]);
                $category->setStoreId($storeid);
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
                $category->save();
                echo 'Category '.$childcat.' CREATED!</br>';
            } else {
                echo 'Category '.$childcat.' already EXISTS. Next!</br>';
            }
        }
    }

    //Activation des catégories produits commerçants
    public function activatecatAction()
    {
        $code = $this->_codeboutique;
        $storeid = intval(Mage::getConfig()->getNode('stores')->{$code}->{'system'}->{'store'}->{'id'});
        $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addAttributeToSelect('*');
        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $categories->addAttributeToFilter('path', array('like' => '1/'.$rootCategoryId.'/%'));
        foreach ($categories as $cat) {
            $cat->setIsClickable(true);
            $cat->save();
        }
        echo 'Catégories de '.$code.' activées!</br>';
    }
}
