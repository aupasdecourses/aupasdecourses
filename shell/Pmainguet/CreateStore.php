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

    protected $_rootcategory;
    protected $_rootcaturlkey;
    protected $_codewebsite;
    protected $_namewebsite;
    protected $_namestoregroup;
    protected $_codeboutique;
    protected $_nameboutique;
    protected $_city;
    protected $_zipcode;
    protected $_country;
    protected $_listmailchimp;
    protected $_contacts;
    protected $_commercant;
    protected $_magasin;
    protected $_googlesheets;

    //PARAMETRES FIXES

    //Modèles
    private $_codemodel = 'batignolles';
    private $_shippingruletoduplicate = 'Restriction 17e';
    private $_shippinglivraisonpro = 'Livraison pour pro';
    private $_couponnames = array('Première livraison gratuite',"10EUROSPARRAINAGE","Coupon de 10€ après 50€ de commande");

    //identifiers éléments à updater/modifier
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
             'autoriser-cookies' => 'autoriser-cookies',
             'besoin-d-aide' => 'besoin-d-aide',
             'no-route' => 'no-route',
             'mentions-legales-cgv' => 'mentions-legales-cgv',
             'nos-engagements' => 'nos-engagements',
             'politique-confidentialite-restriction-cookie' => 'politique-confidentialite-restriction-cookie',
             'tarifs-livraison'=> 'tarifs-livraison',
             'zone-et-horaire' => 'zone-et-horaire',
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

    //Super Menu - Top Categories
    private $_menutextcolor='#ffffff';
    private $_menubgcolors = [
        'Boucher' => '#f3606f',
        'Boulanger' => '#f57320',
        'Caviste' => "#c62753",
        'Primeur' => '#3ab64b',
        'Fromager' => '#faae37',
        'Poissonnier' => "#5496d7",
        'Epicerie' => '#2f4da8',
        'Traiteur' => '#272b32',
        'Bio' => '#00595E',
    ];

    public function __construct(){
        //set value of zone horaire page
        foreach($this->_pagetoupdate as $model => $new){
            if($model!=$new){
                $this->_pagetoupdate[$model]=$new.$this->_codeboutique;
            }
        }
        parent::__construct();
    }

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
                 'title' => 'Au Pas De Courses - Livraison de courses '.$this->_city.' '.$this->_nameboutique,
                 'root_template' => $page->getRootTemplate(),
                 'meta_description' => 'Les livraisons de courses à '.$this->_city.' '.$this->_nameboutique.' avec Au Pas De Courses, pour bien manger avec les meilleurs commerçants du quartier. Essayez ce soir, c\'est facile!',
                 'identifier' => $page->getIdentifier(),
                 'content_heading' => 'Au Pas De Courses, votre livraison de produits frais dans le '.$this->_nameboutique,
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
        // echo "//DUPLICATE CARROUSSEL//\n";
        // $b = Mage::getModel('cms/block');
        // $check = $b->getCollection()->addStoreFilter($newstoreid)->addFieldToFilter('identifier', 'carroussel-home-page');
        // if (sizeof($check->getData()) == 0) {
        //     $data = $b->load('carroussel-home-page')->setStoreId($modelid)->getData();
        //     unset($data['block_id']);
        //     unset($data['creation_time']);
        //     $data['title'] = 'Caroussel Home Page '.$this->_nameboutique;
        //     $data['store_id'] = $newstoreid;
        //     $data['stores'] = array($newstoreid);
        //     $b->setData($data);
        //     $b->save();
        //     echo "Carroussel has been duplicated\n";
        // } else {
        //     echo "Carroussel already exist. Next!\n";
        // }

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
        foreach ($pages as $model => $new) {
            $b = Mage::getModel('cms/page')->setStoreId($modelid)->load($model);
            $arraystore = $b->getStoreId();
            if (!in_array($newstoreid, $arraystore) && $model==$new) {
                array_push($arraystore, $newstoreid);
                $b->setStores($arraystore)->save();
                echo 'Page '.$new." updated!\n";
            } elseif(!in_array($newstoreid, $arraystore) && $model!=$new) {
                $PageData = [
                    'identifier' => $new,
                    'stores' => array($newstoreid),
                    'title' => $b->getTitle(),
                    'root_template' => $b->getRootTemplate(),
                    'meta_description' => $b->getMetaDescription(),
                    'content_heading' => $b->getContentHeading(),
                    'content'=>$b->getContent(),
            ];
            Mage::getModel('cms/page')->setData($PageData)->save();
                echo 'Page '.$new." created!\n";
            } else{
                echo 'Page '.$new." already OK.Next!\n";
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

            //rewrite of conditions
            $modelcondition=unserialize($data['conditions_serialized']);
            $modelcondition['conditions']=array();
            $temp="";

            foreach($this->_zipcode as $zip){
                $temp=array(
                    'type' => "amshiprestriction/rule_condition_address",
                    'attribute' => "postcode",
                    'operator' => "==",
                    'value' => $zip,
                    'is_value_processed' => false
                );
                array_push($modelcondition['conditions'], $temp);
            }
            $data['conditions_serialized']=serialize($modelcondition);
            $data['stores'] = $newstoreid;
            $data['name'] = $namenewrule;
            $data['is_active'] = true;
            $data['message'] = 'Désolé, mais votre code postal n\'est pas dans la zone livrée.';
            $data['days'] = ',7,1,2,3,4,5,6,';
            $data['cust_groups']=',1,';

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
                $check=Mage::getModel('shipping/carrier_tablerate'); 
                $check->setData($data);
                $check->save(); //Cette méthode n'est pas disponible pour ce modèle => à implémenter ?
            //} catch (Exception $e) {
                //print_r($e);
            //}
        }
        echo 'Shipping rates duplicated!';*/

        //Update Coupon
        echo "//EXTEND BIENVENUE & PARRAINAGE COUPON TO NEW BOUTIQUE//\n";
        foreach($this->_couponnames as $coupon){
            $salesRule = Mage::getModel('salesrule/rule')->getCollection()->addFieldToFilter('name', $coupon)->getFirstItem();
            try {
                $data = $salesRule->getData();
                $websiteids = $salesRule->getWebsiteIds();
                array_push($websiteids, $newstoreid);
                $salesRule->setData($data);
                $salesRule->setWebsiteIds($websiteids);
                $salesRule->setCouponCode('BIENVENUE');
                $salesRule->save();
                echo 'Coupon '.$coupon." extended to new boutique!\n";
            } catch (Exception $e) {
                // display error message
                echo $e->getMessage()."\n";
            }
        }

    }

    //Création des catégories générales Commerçants
    public function creategeneralcat()
    {
        echo "//// Création de l\’arborescence initiale (catégories commercants) ////\n\n";

        $catnames = array_keys($this->_magasin);

        $storeid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});

        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();

        if ($rootCategoryId==0){
            echo "Please create rootcat before creating general cat\n";
            return ;
        }

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
                $category->setMenuBgColor($this->_menubgcolors[$name]);
                $category->setMenuTextColor($this->_menutextcolor);
                $category->save();
                echo 'Category '.$name." created!\n";
            } else {
                echo 'Category '.$name." already exist. Next!\n";
            }
        }
    }

    //Création des entités contacts pour script quartier
    // $data @array (firstname, lastname, email)
    public function create_contactentity($data){
        if($data['email']!=''){
            $check=Mage::getSingleton('apdc_commercant/contact')->getCollection()->addFieldToFilter('email', $data['email'])->getFirstItem();
            if (null == $check->getId()) {
                Mage::getSingleton('apdc_commercant/contact')->setData($data)->save();
                $text = "Contact %s %s CREATED!\n";
            } else{
                $text = "Contact %s %s already EXISTS. Next!\n";
            }
        }else{
             $text = "Pas de mail renseigné pour %f %l !\n";
        }
        return sprintf($text, $data['firstname'], $data['lastname']);
    }

    //Création des entités commerçants pour script quartier
    //$data @array (name,$id_contact_ceo,$id_contact_billing,$zipcode,$city)
    public function create_commercantentity($data){
        $check=Mage::getSingleton('apdc_commercant/commercant')->getCollection()->addFieldToFilter('name', $data['name'])->getFirstItem();
        if (null == $check->getId()) {
            Mage::getSingleton('apdc_commercant/commercant')->setData($data)->save();
            $text = "Commercant %s CREATED!\n";
        } else{
            $text = "Commercant %s already EXISTS. Next!\n";
        }
        return sprintf($text, $data['name']);
    }

    //Création des entités magasins pour script quartier
    // $data @array ($name,$namecommercant,$id_contact,$id_category,$id_attribut_commercant,$zipcode,$googlesheets)
    public function create_magasinentity($data){
        $check=Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldToFilter('name', $data['name'])->getFirstItem();
        if (null == $check->getId()) {
            Mage::getModel('apdc_commercant/shop')->setData($data)->save();
            $text = 'Magasin %s CREATED!\n';
            return sprintf($text, $data['name']);
        } else{
            $text = 'Magasin %s already EXISTS. Next!\n';
            return sprintf($text, $data['name']);
        }
    }

    //Create commercants catégories and product attributes
    public function createcommercantcat()
    {
        echo "//// Création des catégories et attributs commerçants sous Magento ////\n\n";

        $catnames = $this->_magasin;
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
 
        foreach ($catnames as $parentcat => $childcat_array) {
            foreach($childcat_array as $childcat){
                if (!in_array($childcat, $options)) {
                    echo 'Attribute option '.$childcat." CREATED!\n";
                    $option['attribute_id'] = $attr_id;
                    $option['value'][$childcat][0] = $childcat;
                    $setup_check = true;
                } else {
                    echo 'Attribute option '.$childcat." already EXISTS.Next!\n";
                }
            }
        }

        if ($setup_check) {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($option);
        }

        //Create contacts
        echo "//CREATE CONTACT//\n";
        foreach($this->_contacts as $magasin => $data_contact){
            $data_contact['role_id']=array(1,2,3,4);
            $this->create_contactentity($data_contact);
        }

        //Create Commercants (legal entity)
        echo "//CREATE COMMERCANTS//\n";
        foreach($this->_commercant as $magasin => $data){
            $mail=$this->_contacts[$magasin]['email'];
            $data['id_contact_ceo']=$data['id_contact_billing']=Mage::getSingleton('apdc_commercant/contact')->getCollection()->addFieldToFilter('email', $mail)->getFirstItem()->getId();
            $data['hq_postcode']=$this->_zipcode[0];
            $data['city']=$this->_city;
            $data['hq_country']=$this->_country;
            $this->create_commercantentity($data);
        }


        //Create categories and attributes option
        echo "//CREATE MAGASIN CATEGORIES//\n";
        $storeid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});
        $rootCategoryId = Mage::app()->getStore($storeid)->getRootCategoryId();

        if ($rootCategoryId==0){
            echo "Please create rootcat before creating general cats and commercants cat\n";
            return ;
        }

        foreach ($catnames as $parentcat => $childcat_array) {
            foreach($childcat_array as $childcat){
                if($childcat != '' ){
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

                        $meta_description=$childcat.', votre '.strtolower($parentCategory->getName()).' de '.$this->_city.' '.$this->_codeboutique.' vous livre à domicile grâce à Au Pas De Courses. Profitez-en, faites vous livrer ce soir!';

                        //id of parent category
                        $parentId = $parentCategory->getId();
                        $category = Mage::getModel('catalog/category');
                        $category->setName($childcat);
                        $category->setIsActive(1);
                        $category->setIsAnchor(1);
                        $category->setIsClickable(1);
                        $category->setData('estcom_commercant', 70);
                        $category->setStoreId($storeid);
                        $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                        $category->setPath($parentCategory->getPath());
                        $category->setMenuTemplate('template2');
                        $category->setMetaDescription($meta_description);
                        $category->save();
                        $parentCatUrlKey=$parentCategory->getUrlKey();

                        //Create Content Block
                        $check=null;
                        if($check == null){
                            $contentblock='<ul class="main-cats"><li class="item-main-block"><a class="level2" href="{{store url=""}}'.$parentCatUrlKey.'/';
                            $contentblock.=$category->getUrlKey();
                            $contentblock.='/tous-les-produits.html"><div class="cat-thumbnail"><img src="{{config path="web/secure/base_url"}}media/catalog/category/tous.jpg"></div><span class="cat-name">Tous les produits</span></a></li></ul>';
                            $contentblock.='<ul><li class="item-main-block info-commercant fa fa-info-circle" aria-hidden="true"> <a href="{{store url=""}}';
                            $contentblock.=$parentCategory->getUrlKey();
                            $contentblock.='/'.$category->getUrlKey().'.html">En savoir plus sur '.$childcat.'</a></li>
                                </ul>';

                            $datablock=[
                                'title'=>"Main Block ".$childcat." - ".$this->_city." ".$this->_nameboutique,
                                'identifier'=>'main-block-'.$category->getUrlKey(),
                                'stores'=>array($storeid),
                                'is_active'=>true,
                                'content'=>$contentblock,
                            ];

                            $mainblockmenu=Mage::getModel('cms/block')->setData($datablock)->save();

                            $category->setMenuMainStaticBlock($mainblockmenu->getIdentifier());
                        }else{
                            $text="CMS block %s already exists. Setup category with existing blocks";
                            echo sprintf($text,'main-block-'.$category->getUrlKey());
                            $category->setMenuMainStaticBlock('main-block-'.$category->getUrlKey());
                        }
                        $category->save();

                        //Create Shop Entity
                        $namecommercant=Mage::getSingleton('apdc_commercant/commercant')->getCollection()->addFieldToFilter('name', $this->_commercant[$childcat])->getFirstItem()->getName();
                        $mail=$this->_contacts[$childcat]['email'];
                        $id_contact=Mage::getSingleton('apdc_commercant/contact')->getCollection()->addFieldToFilter('email', $mail)->getFirstItem()->getId();
                        $id_attribut_commercant = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter('commercant')->getFirstItem()->getSource()->getOptionId($childcat);

                        $S = Mage::helper('apdc_commercant')->getStoresArray();

                        $data=[
                            'enabled'=>true,
                            'name'=>$childcat,
                            'id_commercant'=>Mage::getSingleton('apdc_commercant/commercant')->getCollection()->addFieldToFilter('name', $namecommercant)->getFirstItem()->getId(),
                            'id_contact_manager'=>$id_contact,
                            'id_category'=>array($category->getId()),
                            'stores'=>array($S[explode('/', $category->getPath())[1]]['store_id']),
                            'id_attribut_commercant'=>$id_attribut_commercant,
                            'delivery_days'=>array(2,3,4,5),
                            'city'=>'Paris',
                            'postcode'=>$this->_zipcode[0],
                            'google_id'=>$this->_googlesheets[$childcat]['google_id'],
                            'google_key'=>$this->_googlesheets[$childcat]['google_key'],
                        ];

                        echo $this->create_magasinentity($data);

                        echo 'Category, contact, commercants, shop, main static block '.$childcat." CREATED!\n";
                    } else {
                        echo 'Category '.$childcat." already EXISTS. Next!\n";
                    }
                }
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
            if (in_array($cat->getName(), array_keys($this->_magasin))) {
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

    public function setupMailchimp()
    {
        $newstoreid = intval(Mage::getConfig()->getNode('stores')->{$this->_codeboutique}->{'system'}->{'store'}->{'id'});
        $listid=Mage::getSingleton('monkey/api')->lists(['list_name'=>$this->_listmailchimp], null, 100)['data'][0]['id'];
        if(!is_null($listid) && $newstoreid<>0){
            Mage::getConfig()->saveConfig('monkey/general/list', $listid, 'stores', $newstoreid);
            echo "Mailchimp list set!\n";
        } else {
            echo "Cannot find Mailchimp list or store id.\n";
        }
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
        $steps = ['createrootcat', 'createstore', 'cmspage', 'attributes', 'creategeneralcat', 'createcommercantcat', 'activatecat', 'setupMailchimp', 'createsitemap'];
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