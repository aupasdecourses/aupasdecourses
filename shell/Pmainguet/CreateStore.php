<?php

/* To start we need to include abscract.php, which is located 
 * in /shell/abstract.php which contains Magento's Mage_Shell_Abstract 
 * class. 
 *
 * Since this .php is in /shell/Namespace/ we
 * need to include ../ in our require statement which means the
 * file we are including is up one directory from the current file location.
 */
require_once '../abstract.php';

class Pmainguet_CreateStore extends Mage_Shell_Abstract
{
    private $_codemodel = 'batignolles';

    private $_rootcategory = 'Commercants Commerce';
    private $_rootcaturlkey = 'commercants-commerce';
    private $_codewebsite = 'apdc_commerce';
    private $_namewebsite = 'Au Pas De Courses Commerce';
    private $_namestoregroup = 'apdc_commerce';
    private $_codeboutique = 'commerce';
    private $_nameboutique = 'Commerce';

    private $_commercant = [
            'Boucher' => 'Boucherie Franck Lebrec',
            'Primeur' => 'Les Halles Felix Faure',
            'Fromager' => 'Thomas Artisan Fromager',
            'Poissonnier' => 'Poissonnier Vent d\'Ouest',
            'Caviste' => 'Le goût des vignes',
            'Boulanger' => 'Boulangerie Moisan Fondary',
            ];

    private $_amasty = [
             'batiment',
             'codeporte1',
             'codeporte2',
             'contactvoisin',
             'etage',
             'infoscomplementaires',
             'produit_equivalent',
             'telcontact',
         ];

    private $_pagetoupdate = [
            'annonce-livreurs',
            'autoriser-cookies',
            'besoin-d-aide',
            'no-route',
            'mentions-legales-cgv',
            'nos-engagements',
            'politique-confidentialite-restriction-cookie',
            'tarifs-livraison',
            //'zone-et-horaire',
        ];

    private $_blocktoupdate = [
            'footer_links_middle',
            'footer_links_left',
            'cookie_restriction_notice_block',
            'footer_links_right',
            'footer_links_middle_2',
            'footer-social',
            'footer-cards',
        ];

    private $_titlehomepage = 'Au Pas De Courses - Livraison de courses Commerce Paris 15e';
    private $_metadescription = 'Les livraisons de courses dans le 15e arrondissement avec Au Pas De Courses, pour bien manger avec les meilleurs commerçants de Paris. Essayez ce soir, c\'est facile!';
    private $_content_heading = 'Au Pas De Courses Commerce, votre livraison de produits frais dans le 15e';

    private $_shippingruletoduplicate = 'Restriction 15e';
    private $_shippinglivraisonpro = 'Livraison pour pro';
    private $_couponname = 'Première livraison gratuite';

    //Creation de la Root Category
    public function createrootcat()
    {
        echo "//Création d’une catégorie racine pour le magasin ////\n\n";

        $storeId = 0;
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        $check = $category->getCollection()->addAttributeToFilter('is_active', true)
            ->addAttributeToFilter('name', $this->_rootcategory)
            ->getFirstItem()->getId();

        if ($check == null) {
            $category->setName($this->_rootcategory);
            $category->setUrlKey($this->_rootcaturlkey);
            $category->setIsActive(1);
            $category->setDisplayMode('PRODUCTS');
            $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            $parentCategory = Mage::getModel('catalog/category')->load($parentId);
            $category->setPath($parentCategory->getPath());
            $category->save();
            echo 'Category '.$this->_rootcategory." created!\n";
        } else {
            echo 'Category '.$this->_rootcategory." already exist. Next!\n";
        }
    }

    //Creation de la Boutique, du Group Store et du Store
    public function createstore()
    {
        echo "//Création du Website, Group Store et Store ////\n\n";

        //To create from the frontend
        Mage::registry('isSecureArea');

        //#addWebsite
        /** @var $website Mage_Core_Model_Website */
        $website = Mage::getModel('core/website');
        if (is_null($website->load($this->_codewebsite)->getWebsiteId())) {
            $website->setCode($this->_codewebsite)
            ->setName($this->_namewebsite)
            ->save();
            echo 'Website '.$this->_namewebsite." created!\n";
        } else {
            echo 'Website '.$this->_namewebsite." already exist. Stopped!\n";
        }

        //get root category id
        $category = Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('name', $this->_rootcategory)
            ->getFirstItem();
        $categoryId = $category->getId();

        //#addStoreGroup
        /** @var $storeGroup Mage_Core_Model_Store_Group */
        $storeGroup = Mage::getModel('core/store_group');
        $check = $storeGroup->getCollection()->addFieldToFilter('name', $this->_namestoregroup)->getFirstItem()->getGroupId();
        if (is_null($check)) {
            $storeGroup->setWebsiteId($website->getId())
            ->setName($this->_namestoregroup)
            ->setRootCategoryId($categoryId)
            ->save();
            echo 'Store Group '.$this->_namestoregroup." created!\n";
        } else {
            echo 'Store Group '.$this->_namestoregroup." already exist. Stopped!\n";
        }

        //#addStore
        /** @var $store Mage_Core_Model_Store */
        $store = Mage::getModel('core/store');
        $check = $store->getCollection()->addFieldToFilter('code', $this->_codeboutique)->getFirstItem()->getStoreId();
        if (is_null($check)) {
            $store->setCode($this->_codeboutique)
            ->setWebsiteId($storeGroup->getWebsiteId())
            ->setGroupId($storeGroup->getId())
            ->setName($this->_nameboutique)
            ->setIsActive(1)
            ->save();
            echo 'Store '.$this->_nameboutique." created!\n";
        } else {
            echo 'Store '.$this->_nameboutique." already exist. Stopped!\n";
        }
    }

    //Change Scope of existing CMS Home Page
    public function cmspage()
    {
        echo "//Création et mise à jour contenu nouvelle boutique ////\n\n";

        $modelid = intval(Mage::getConfig()->getNode('stores')->{$this->_codemodel}->{'system'}->{'store'}->{'id'});
        $newstoreid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

        //DUPLICATE HOMEPAGE
        echo "//DUPLICATE HOMEPAGE//\n";
        $page = Mage::getModel('cms/page')->setStoreId($modelid)->load('home');
        $check = sizeof(Mage::getModel('cms/page')->setStoreId($newstoreid)->load('home')->getPageId());
        if ($check == 0) {
            $cmsPageData = [
                 'title' => $this->_titlehomepage,
                 'root_template' => $page->getRootTemplate(),
                 'meta_description' => $this->_metadescription,
                 'identifier' => $page->getIdentifier(),
                 'content_heading' => $this->_content_heading,
                 //'content'=>$page->getContent(),
                 'store_id' => [$newstoreid],
            ];
            Mage::getModel('cms/page')->setData($cmsPageData)->save();

            echo 'Homepage for '.$this->_nameboutique." created!\n";
        } else {
            echo 'Homepage for '.$this->_nameboutique." already exists. Next!\n";
        }

        Mage::getConfig()->saveConfig('design/head/default_description', $this->_metadescription, 'stores', $newstoreid);

        //DUPLICATE CAROUSSEL
        echo "//DUPLICATE CARROUSSEL//\n";
        $b = Mage::getModel('cms/block');
        $check = $b->getCollection()->addStoreFilter($newstoreid)->addFieldToFilter('identifier', 'carroussel-home-page');
        if (sizeof($check->getData()) == 0) {
            $data = $b->load('carroussel-home-page')->setStoreId($modelid)->getData();
            unset($data['block_id']);
            unset($data['creation_time']);
            $data['title'] = 'Caroussel Home Page '.$this->_nameboutique;
            $data['store_id'] = $newstoreid;
            $data['stores'] = array($newstoreid);
            $b->setData($data);
            $b->save();
            echo "Carroussel has been duplicated\n";
        } else {
            echo "Carroussel already exist. Next!\n";
        }

        //UPDATE FOOTER BLOCKS
        echo "//UPDATE FOOTER BLOCKS//\n";
        $blocks = $this->_blocktoupdate;
        foreach ($blocks as $block) {
            $b = Mage::getModel('cms/block')->setStoreId($modelid)->load($block);
            $arraystore = $b->getStoreId();
            if (!in_array($newstoreid, $arraystore)) {
                array_push($arraystore, $newstoreid);
                $b->setStores($arraystore);
                $b->setStoreId($arraystore);
                $b->save();
                echo 'Block '.$block." updated!\n";
            }
            echo 'Block '.$block." already OK.Next!\n";
        }

        //UPDATE EXISTING PAGES
        echo "//UPDATE EXISTING PAGES//\n";
        $pages = $this->_pagetoupdate;
        foreach ($pages as $page) {
            $b = Mage::getModel('cms/page')->setStoreId($modelid)->load($page);
            $arraystore = $b->getStoreId();
            if (!in_array($newstoreid, $arraystore)) {
                array_push($arraystore, $newstoreid);
                $b->setStoreId($arraystore);
                $b->save();
                echo 'Page '.$page." updated!\n";
            } else {
                echo 'Page '.$page." already OK.Next!\n";
            }
        }
    }

    //Change Amasty Order Attributes
    public function attributes()
    {
        echo "//// Setup Order Attributes, Restriction Shipping & Delivery Time ////\n\n";

        $modelid = intval(Mage::getConfig()->getNode('stores')->{$this->_codemodel}->{'system'}->{'store'}->{'id'});
        $newstoreid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

        //Amasty Order Attributes
        echo "//UPDATE AMASTY ORDER ATTRIBUTES//\n";
        $amasty = $this->_amasty;

        foreach ($amasty as $am) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(5, $am);
            if (!in_array($newstoreid, explode(',', $attribute->getStoreIds()))) {
                $attribute->setStoreIds($attribute->getStoreIds().','.$newstoreid);
                $attribute->save();
                echo 'Attribut '.$am." updated!\n";
            } else {
                echo 'Attribut '.$am." already OK.Next!\n";
            }
        }

        //Livraison pro update
        echo "//LIVRAISON PRO SHIPPING RESTRICTION//\n";
        $livpro = $this->_shippinglivraisonpro;

        $check = Mage::getModel('amshiprestriction/rule')->getCollection()
           ->addFieldToFilter('is_active', true)
           ->addFieldToFilter('name', $livpro)->getFirstItem();

        if (!is_null($check->getRuleId())) {
            $rules = Mage::getModel('amshiprestriction/rule')->getCollection()
               ->addFieldToFilter('is_active', true)
               ->addFieldToFilter('name', $livpro);
            foreach ($rules as $rule) {
                $rule->setStores($rule->getStores().','.$newstoreid);
                $rule->save();
            }
            echo 'Shipping restriction '.$livpro." updated!\n";
        } else {
            echo 'Shipping restriction '.$livpro." does not exist.Pass!\n";
        }

        //Duplicate Shipping restriction
        echo "//DUPLICATE SHIPPING RESTRICTION//\n";
        $namerule = $this->_shippingruletoduplicate;
        $namenewrule = 'Restriction '.$this->_nameboutique;

        $check = Mage::getModel('amshiprestriction/rule')->getCollection()
           ->addFieldToFilter('is_active', true)
           ->addFieldToFilter('name', $namenewrule)->getFirstItem();

        if (is_null($check->getRuleId())) {
            $data = Mage::getModel('amshiprestriction/rule')->getCollection()
               ->addFieldToFilter('is_active', true)
               ->addFieldToFilter('name', $namerule)->getData()[0];
            unset($data['rule_id']);
            $data['stores'] = $newstoreid;
            $data['name'] = $namenewrule;
            $newrule = Mage::getModel('amshiprestriction/rule')->setData($data);
            $newrule->save();
            echo 'Shipping restriction '.$namenewrule." created!\n";
        } else {
            echo 'Shipping restriction '.$namenewrule." already exist.Pass!\n";
        }

        //Duplicate Delivery Slot
        echo "//DUPLICATE DELIVERY SLOT//\n";
        $collection = Mage::getModel('ddate/dtime')->getCollection()
            ->addFieldToFilter('store_id', array('eq' => $modelid));

        foreach ($collection as $col) {
            $check = Mage::getModel('ddate/dtime')->getCollection()
                ->addFieldToFilter('dtime', $col->getDtime())
                ->addFieldToFilter('store_id', array('eq' => $newstoreid))->getFirstItem();
            if (is_null($check->getData('dtime_id'))) {
                $data = $col->getData();
                unset($data['dtime_id']);
                unset($data['special_day']);
                unset($data['store_id']);
                $data['dtime_stores'] = array($newstoreid);
                $check->setData($data);
                $check->save();
                echo 'Delivery Slot '.$col->getDtime()." created!\n";
            } else {
                echo 'Delivery Slot '.$col->getDtime()." already exist.Pass!\n";
            }
        }

        //Duplicate entries for shipping rates
        echo "//DUPLICATE ENTRIES FOR SHIPPING RATE --------------->>>>>>> DO IT BY HAND FOR THE MOMENT (IMPORTANT)!//\n";
        /*$rates = Mage::getResourceModel('shipping/carrier_tablerate_collection')
            ->addFieldToFilter('website_id', $modelid);

        foreach ($rates as $rat) {
            $data = $rat->getData();
            unset($data['pk']);
            $data['website_id'] = $newstoreid;
            //try {
                $check=Mage::getModel('shipping/carrier_tablerate'); //Not functioning
                $check->setData($data);
                $check->save();
            //} catch (Exception $e) {
                //print_r($e);
            //}
        }
        echo 'Shipping rates duplicated!';*/

        //Update BIENVENUE Coupon
        echo "//EXTEND BIENVENUE COUPON TO NEW BOUTIQUE//\n";
        $salesRule = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('name', $this->_couponname)->getFirstItem();
        try {
            $data = $salesRule->getData();
            $websiteids = $salesRule->getWebsiteIds();
            array_push($websiteids, $newstoreid);
            $salesRule->setData($data);
            $salesRule->setWebsiteIds($websiteids);
            $salesRule->setCouponCode('BIENVENUE');
            $salesRule->save();
            echo "Livraison gratuite extended to new boutique!\n";
        } catch (Exception $e) {
            // display error message
            echo $e->getMessage()."\n";
        }
    }

    //Création des catégories générales Commerçants
    public function creategeneralcat()
    {
        echo "//// Création de l\’arborescence initiale (catégories commercants) ////\n\n";

        $catnames = array_keys($this->_commercant);

        $storeid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

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
                echo 'Category '.$name." created!\n";
            } else {
                echo 'Category '.$name." already exist. Next!\n";
            }
        }
    }

    //Create commercants catégories and product attributes
    public function createcommercantcat()
    {
        echo "//// Création des catégories et attributs commerçants sous Magento ////\n\n";

        $catnames = $this->_commercant;
        //create attribute option if do not exist
        echo "//CREATE PRODUCT ATTRIBUTES FOR EACH COMMERCANTS//\n";
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
                echo 'Attribute option '.$childcat." CREATED!\n";
                $option['attribute_id'] = $attr_id;
                $option['value'][$childcat][0] = $childcat;
                $setup_check = true;
            } else {
                echo 'Attribute option '.$childcat." already EXISTS.Next!\n";
            }
        }

        if ($setup_check) {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($option);
        }

        //Create categories and attributes option
        echo "//CREATE COMMERCANTS CATEGORIES//\n";
        $storeid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});
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
                //id of parent category
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
                echo 'Category '.$childcat." CREATED!\n";
            } else {
                echo 'Category '.$childcat." already EXISTS. Next!\n";
            }
        }
    }

    //Activation des catégories produits commerçants
    public function activatecat()
    {
        echo "//// Activer toutes les catégories en cliquable ////\n\n";

        $storeid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});
        $categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addFieldToFilter('is_active', array('eq' => 1))
                ->addAttributeToSelect('*');
        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();
        $categories->addAttributeToFilter('path', array('like' => '1/'.$rootCategoryId.'/%'));
        foreach ($categories as $cat) {
            if (in_array($cat->getName(), array_keys($this->_commercant))) {
                $cat->setIsClickable('Non');
                echo 'Catégorie '.$cat->getName()." non cliquable.\n";
            } else {
                $cat->setIsClickable(true);
                echo 'Catégorie '.$cat->getName()." cliquable.\n";
            }
            $cat->save();
        }
        echo 'Catégories de '.$this->_codeboutique." activées!\n";
    }

    public function createsitemap()
    {
        $newstoreid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

        // init model and set data
        $model = Mage::getModel('sitemap/sitemap');

        $sitemap_filename = 'sitemap.xml';
        $sitemap_path = '/sitemap/sitemap_'.$this->_codeboutique.'/';
        $server_path = glob($_SERVER['DOCUMENT_ROOT']);

        if (!file_exists($server_path[0].$sitemap_path)) {
            mkdir($server_path[0].$sitemap_path, 0777, true);
            echo $server_path[0].$sitemap_path." folder has been created!\n";
        } else {
            echo $server_path[0].$sitemap_path." folder already exists. Next!\n";
        }

        $path = $sitemap_path.$sitemap_filename;

        if (is_null(Mage::getSingleton('sitemap/sitemap')->getCollection()->addFieldToFilter('sitemap_filename', $sitemap_filename.'fdf')->getFirstItem()->getId())) {
            $data = array(
                'sitemap_filename' => $sitemap_filename,
                'sitemap_path' => $sitemap_path,
                'store_id' => $newstoreid,
                );

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                echo "The sitemap has been saved.\n";
            } catch (Exception $e) {
                // display error message
                echo $e->getMessage()."\n";
            }

            // if sitemap record exists
            if ($model->getId()) {
                try {
                    $model->generateXml();
                    echo "The sitemap has been generated.\n";
                } catch (Mage_Core_Exception $e) {
                    echo $e->getMessage()."\n";
                } catch (Exception $e) {
                    echo "Unable to generate the sitemap.\n";
                }
            } else {
                echo "Unable to find a sitemap to generate.\n";
            }
        } else {
            echo "Sitemap seems to already exists. Check Magento for additionnal info. We stop here!\n";
        }
    }

    // Implement abstract function Mage_Shell_Abstract::run();
    public function run()
    {
        $steps = ['createrootcat', 'createstore', 'cmspage', 'attributes', 'creategeneralcat', 'createcommercantcat', 'activatecat', 'createsitemap'];
        //get argument passed to shell script
        $step = $this->getArg('step');
        if (in_array($step, $steps)) {
            $this->$step();
        } else {
            echo "STEP MUST BE ONE OF THESE:\n";
            foreach ($steps as $s) {
                echo $s.",\n";
            }
        }
    }
}

// Create a new instance of our class and run it.
$shell = new Pmainguet_CreateStore();
$shell->run();
