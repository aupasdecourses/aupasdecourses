<?php

class Apdc_Catalog_Model_Resource_Adminhtml_Request_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
	protected $_sql;
    protected $_pluralarrays;

    public function __construct($sql=null)
    {
        parent::__construct();
        $this->_sql=$sql;
        $this->_pluralarrays=array('Tournedos','Souris','Anchois','Dos','Charolais','Epoisses','Maïs','Abymes','Accras','Algues','Amandes','Amatéus','Ananas','Antipastis','Après-soleil','Arbois','Arbois','Assortiments','Avis','Ballotins','Bas','Beaujolais','Beaujolais','Belles','Belons','Berlingots','Béssous','Bières','Bigotes','Biscuits','Blancs','Blinis','Bonbons','Bourgognes','Boutons','Brebis','Bricelets','Brisures','Cacahuetes','Cahors','Cahors','Calamars','Capres','Câpres','Capsules','Caramels','Cassis','Cassis/groseille','Cepes','Cèpes','Céréales','Cervelas','Chablis','Chablis','Champignons','Chanterelles','Chataignes','Châtaignes','Chenas','Chèvres','Chips','Chirinoyas','Chiroubles','Chouquettes','Chutneys','Clos','Coeurs','Cookies','Coquillettes','Corbières','Cornas','Cornichons','Costes','Costières','Couhins','Coulis','Coulomiers','Coulommiers','Cours','Cous','Couscous','Coustous','Crackers','Croquants','Croutons','Croûtons','Crozes','Crozes-Hermitage','Crozes-Hermitage','Crozets','Cyrus','Divers','Dziugas','Ecosses','Ehauyattes','Epices','Faugères','Faugères','fdsfds','Feuilles','Feves','Fèves','Fêves','Fines','Flageolets','Fleurs','Flocons','Flutes','Gambas','Gigandas','Gigondas','Girolles','Gours','Graines','Grains','Grandes','Grands','Gras','Graves','Gros','Grosses','Hautes','Hautes-côtes','Herbes','Hors','Houmous','Huitres','Huîtres','Jeunes','Julienas','Jus','Kakos','Kippers','Langres','Lardons','Lentilles','Lentillons','Les','Lingots','Lyonnais','Macarons','Maconnais','Mâconnais','Manchons','Maranges','Maroilles','Marquis','Marrons','MAS','Matjas','Matjes','Maucazais','Mavromatis','Mavrommatis','Mineolas','Minervois','Minervois','Minis','Montgomerys','Montlouis','Morilles','Mothais','Moules','Moulis','Mousserons','Mouthes','Murols','Nonetes','Nonettes','Nuits','Oeufs','Œufs','Olives','Ollières','Ossalois','P’tites','Palets','Panais','Parpadelles','Parpelettes','Pates','Pâtes','Pattes','Pavés','Pays','Pétales','Petites','Petits','Physalis','Picholines','Pickles','Pieds','Pignons','Piments','Pistaches','Pithiviers','Pleurotes','Pleurottes','Pointes','Pois','Pomelos','Pousses','Praires','Prémices','Printemps','Produits','Profiteroles','Pyrénées','Radis','Ravioles','Raviolis','Ribs','Rilettes','Rilletes','Rilletetes','Rillettes','Rillettes','Rillons','Ris','Roches','Roiboos','Rollmops','Roves','Rozelieures','Sables','Sablés','Salers','Salsifis','Sans','Sauternes','Saveurs','Schweppes','Selles','Selles-sur-cher','Selles/Cher','Sens','Sous','Speculos','Sprats','Tagliatelles','Tartines','Terres','Tielles','Torsades','Tranches','Travers','Très','Tripes','Trompettes','Truffes','Tuiles','Tulipes','Vacherin Fribourgeois','Vacqueyras','Vacqueyras','Vendanges','Venetes','Vénètes','Viennois','Vins','Vinsobres');
 }

    protected function _construct($sql=null)
    {
        $this->_init('apdc_catalog/adminhtml_request');
    }

    protected function selectIllegalCharactersActive(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),
            array('table1.entity_id', 'table1.sku'))
        ->joinLeft(array('table2'=>'catalog_product_entity_int'),'table1.entity_id = table2.entity_id')
        ->joinLeft(array('table3'=>'eav_attribute'),'table2.attribute_id = table3.attribute_id')
        ->where('table3.attribute_code = ?', 'status')
        ->where('value = ?', 1)
        ->where("sku <> CONVERT(sku USING ASCII) OR sku LIKE '% %' OR sku REGEXP '\r\n' OR sku REGEXP '\n' OR sku NOT REGEXP '[[:<:]]([[:alnum:]]{1,}-[[:alnum:]]{1,}-[[:alnum:]]{1,})[[:>:]]'");
    }

    protected function selectWithoutSku(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),
            array('table1.sku'))
        ->where('table1.sku = ?','');
    }

    protected function selectDoublonSku(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),
            array('COUNT(table1.sku)', 'table1.sku'))
        ->group('table1.sku')
        ->having('COUNT(sku) > 1');
    }

    protected function selectNoImages(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),
            array('table1.entity_id', 'table1.sku'))
        ->join(array('table2'=>'eav_attribute'),'table2.attribute_code="image" and table2.frontend_input = "media_image"')
        ->joinLeft(array('table3'=>'catalog_product_entity_varchar'),'table1.entity_id = table3.entity_id AND table2.attribute_id = table3.attribute_id')
        ->join(array('table4'=>'eav_attribute'),'table4.attribute_code =  "status"')
        ->joinLeft(array('table5'=>'catalog_product_entity_int'),'table1.entity_id = table5.entity_id AND table5.attribute_id = table4.attribute_id')
        ->where('table5.value=1')
        ->where('type_id="simple"')
        ->where('table3.value =  "" OR table3.value IS NULL  OR table3.value =  "no_selection"');
    }

    protected function selectPricesWithEuros(){
        $this->_select=$this->getConnection()->select()
        ->distinct()
        ->from(array('table1' => 'catalog_product_entity'),'table1.entity_id')
        ->join(array('table2'=>'eav_attribute'),'table2.attribute_code="prix_public"',array())
        ->joinLeft(array('table3'=>'catalog_product_entity_varchar'),'table1.entity_id = table3.entity_id AND table2.attribute_id = table3.attribute_id',array('table3.value'))
        ->columns('table1.sku')
        ->where('table3.value LIKE "%€%"');
    }

    protected function selectPricesZero(){
        $this->_select=$this->getConnection()->select()
        ->distinct()
        ->from(array('a' => 'catalog_product_entity'),'a.entity_id')
        ->join(array('attribute'=>'eav_attribute'),'attribute.attribute_code="price"',array())
        ->joinLeft(array('b'=>'catalog_product_entity_decimal'),'a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id',array('b.value'))
        ->join(array('attribute2'=>'eav_attribute'),'attribute2.attribute_code="status"',array())
        ->joinLeft(array('c'=>'catalog_product_entity_int'),'b.entity_id = c.entity_id AND c.attribute_id = attribute2.attribute_id',array('c.value'))
        ->columns(array('a.sku', 'b.value', 'c.value'))
        ->where('c.value=1 AND (b.value=0 OR b.value IS NULL) AND attribute.entity_type_id=4');
    }

    protected function selectNoRefCode(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'),'table1.entity_id')
        ->join(array('table2'=>'eav_attribute'),'table2.attribute_code="code_ref_apdc"')
        ->joinLeft(array('table3'=>'catalog_product_entity_varchar'),'table1.entity_id = table3.entity_id AND table2.attribute_id = table3.attribute_id')
        ->join(array('table4'=>'eav_attribute'),'table4.attribute_code="status"')
        ->joinLeft(array('table5'=>'catalog_product_entity_int'),'table1.entity_id = table5.entity_id AND table4.attribute_id = table5.attribute_id')
        ->join(array('table6'=>'eav_attribute'),'table6.attribute_code="commercant"')
        ->joinLeft(array('table7'=>'catalog_product_entity_int'),'table1.entity_id = table7.entity_id AND table6.attribute_id = table7.attribute_id')
        ->joinLeft(array('table8'=>'eav_attribute_option_value'),'table7.value = table8.option_id')
        ->where('table3.value IS NULL AND table5.value=1')
        ->group('table1.entity_id')
        ->order('table1.sku');
    }

    protected function selectNoShops(){
        $this->_select=$this->getConnection()->select()
        ->from(array('table1' => 'catalog_product_entity'))
        ->join(array('table2'=>'eav_attribute'),'table2.attribute_code="commercant"')
        ->joinLeft(array('table3'=>'catalog_product_entity_int'),'table1.entity_id = table3.entity_id AND table3.attribute_id = table2.attribute_id')
        ->joinLeft(array('table4'=>'eav_attribute_option_value'),'table3.value = table4.option_id')
        ->where("table1.type_id='simple' AND table4.value IS NULL");
    }

    protected function selectOrphanProducts(){
        $this->_select=$this->getConnection()->select()
        ->from(array('a' => 'catalog_product_entity'),array('a.entity_id','a.type_id','a.sku'))
        ->joinLeft(array('cp'=>'catalog_category_product'),'cp.product_id = a.entity_id')
        ->joinLeft(array('cpr'=>'catalog_product_relation'),'cpr.child_id = a.entity_id')
        ->where("cp.product_id is null and cpr.parent_id is null and a.type_id != 'configurable'")
        ;
    }

    protected function selectBundles(){
         $this->_select=$this->getConnection()->select()
        ->from(array('a' => 'catalog_product_entity'),'a.entity_id')
        ->join(array('child'=>'catalog_product_bundle_selection'),'child.parent_product_id= a.entity_id',array())
        ->join(array('b'=>'catalog_product_entity'),'child.product_id = b.entity_id',array('b.sku'))
        ->join(array('attribute'=>'eav_attribute'),"attribute.attribute_code =  'status'",array())
        ->joinLeft(array('c'=>'catalog_product_entity_int'),"b.entity_id = c.entity_id AND c.attribute_id = attribute.attribute_id",array('c.value'))
        ->where("a.type_id='bundle' AND attribute.entity_type_id=4")
        ->columns(array('nbe produits total'=>'count(b.sku)','a.sku','nbe produits desactives'=>'sum(case when c.value = 2 then 1 else 0 end)','percent'=>'sum(case when c.value = 2 then 1 else 0 end)/count(b.sku)'))
        ->group('a.sku')
        ->order('percent DESC');
    }

    protected function selectProductsByCategory(){
        $this->_select=$this->getConnection()->select()
        ->from(array('a' => 'catalog_product_entity'),array('cat.category_id','nb_produits'=>'COUNT(a.entity_id)'))
        ->join(array('cat'=>'catalog_category_product'),'a.entity_id=cat.product_id',array('cat.category_id'))
        ->join(array('attribute'=>'eav_attribute'),'attribute.attribute_code =  "is_active"',array())
        ->joinLeft(array('b'=>'catalog_category_entity_int'),"cat.category_id = b.entity_id AND b.attribute_id = attribute.attribute_id",array())
        ->where('b.store_id=0 AND b.value=1')
        ->group('cat.category_id')
        ->order('nb_produits');
    }

    protected function selectPluralProducts(){
        $this->_select=$this->getConnection()->select()
        ->from(array('a' => 'catalog_product_entity'),array('a.entity_id','a.sku','name'=>'b.value'))
        ->join(array('attribute'=>'eav_attribute'),'attribute.attribute_code =  "name" AND attribute.entity_type_id = a.entity_type_id',array())
        ->joinLeft(array('b'=>'catalog_product_entity_varchar'),"a.entity_id = b.entity_id AND b.attribute_id = attribute.attribute_id",array('b.value'))
        ->where("(b.value REGEXP '^[[:alnum:]]*[s][[:>:]]' AND SUBSTRING_INDEX(SUBSTRING_INDEX(sku,'-',2),'-',-1) NOT IN ('VIN') AND SUBSTRING_INDEX(b.value,' ',1) NOT IN ('".implode("','",$this->_pluralarrays)."'))");
    }

    public function getData($select=null)
    {
        if ($this->_data === null) {
            $this->_renderFilters()
                 ->_renderOrders()
                 ->_renderLimit();

            if(!is_null($select)){
                switch($this->_sql){
                    case 'without_sku':
                        $this->selectWithoutSku();
                        break;
                    case 'doublon_sku':
                        $this->selectDoublonSku();
                        break;
                    case 'illegal_characters_active':
                        $this->selectIllegalCharactersActive();
                        break;
                    case 'no_images':
                        $this->selectNoImages();
                        break;
                    case 'prices_with_euros':
                        $this->selectPricesWithEuros();
                        break;
                    case 'prices_zero':
                        $this->selectPricesZero();
                        break;
                    case 'no_ref_code':
                        $this->selectNoRefCode();
                        break;
                    case 'no_shops':
                        $this->selectNoShops();
                        break;
                    case 'orphan_products':
                        $this->selectOrphanProducts();
                        break;
                    case 'bundles':
                        $this->selectBundles();
                        break;
                    case 'cats_n_products':
                        $this->selectProductsByCategory();
                        break;
                    case 'plural_products':
                        $this->selectPluralProducts();
                        break;
                    default:
                        break;
                }
            }

            if ($this->_pageSize) {
                $this->getSelect()->limitPage($this->getCurPage(), $this->_pageSize);
            }

            $query = $this->_prepareSelect($this->getSelect());
            $this->_data = $this->_fetchAll($query);
            $this->_afterLoadData();
        }
        return $this->_data;
    }

    public function limitPage(){
    	$page     = ($this->getCurPage() > 0)     ? $this->getCurPage()     : 1;
        $rowCount = ($this->getCurPage() > 0) ? $this->_pageSize : 1;
        return ' LIMIT '.(int) $rowCount * ($page - 1).','.(int) $rowCount;
    }

        /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        $sql = $this->getSelectCountSql();
        $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        return intval($this->_totalRecords);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_beforeLoad();

        $this->_renderFilters();
        $this->_renderOrders();

        $this->printLogQuery($printQuery, $logQuery);

        $data = $this->getData($this->_sql);
        $this->resetData();

        foreach ($data as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            } else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

}