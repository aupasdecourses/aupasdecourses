<?php

class Apdc_Referentiel_Model_Observer
{

    //ajouter logique pour remplir champs permettant d'identifier la personne qui modifie + timestamp de la modification

    protected $_product;
    protected $_columns;

    public function getProduct($observer){
        if(!isset($this->_product)){
            $this->_product= $observer->getProduct();
        } 
        return $this->_product;
    }

    public function getColumns(){
        if(!isset($this->_columns)){
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');  
            $tableName = $resource->getTableName('apdc_referentiel/backupmodif');
            $saleafield=$readConnection->describeTable($tableName);
            unset($saleafield['entity_id']);
            unset($saleafield['updated_at']);
            $this->_columns=array_keys($saleafield);
        } 
        return $this->_columns;
    }


    /**
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    //N'est plus utilisé pour le moment - Attention! Format obsolète
    public function saveChanges(Varien_Event_Observer $observer)
    {

        $product = $this->getProduct($observer);
        $origData=$product->getOrigData();
        $newData=$product->getData();

        $columns = $this->getColumns();
        
        $data=array(
            "OLD" => array(
                "data_type"=>"OLD",
                "product_id"=>$product->getEntityId()
            ),
            "NEW" => array(
                "data_type"=>"NEW",
                "product_id"=>$product->getEntityId()
            )
        );
        $identity=true;
        foreach($columns as $col){
            $data["OLD"][$col]=$origData[$col];
            $data["NEW"][$col]=$newData[$col];
            if($origData[$col]!=$newData[$col]){
                $identity=false;
            }
        }

        if(!$identity){
            $model=Mage::getModel("apdc_referentiel/backupmodif");
            $model->setData($data["OLD"])->save();
            $model->setData($data["NEW"])->save();  
        }

    }

    //a bouger dans le modèle Backupmodif
    public function saveEntry($data){
        
        $entry=Mage::getModel("apdc_referentiel/backupmodif");
        foreach($data as $key => $d){
            if(in_array($key, $this->getColumns())){
                $entry->setData($key,$d);
            }
        }
        $entry->setData("updated_at",Mage::getModel('core/date')->date("Y-m-d H:i:s", now()));
        $entry->save();
    }

    public function formatCurrentData($data){
        unset($data['stock_item']);
        $data['product_id']=$data['entity_id'];
        unset($data['entity_id']);
        foreach($this->getColumns() as $att){
            if(!in_array($att,array_keys($data))){
                $data[$att]=NULL;
            }
        }
        return $data;
    }

    public function cronCheckCatalogChanges(){

        $attributes=$this->getColumns();

        //Get all products with specific columns
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array('entity_id','sku'));
        foreach ($attributes as $a){
                $products->addAttributeToSelect($a);            
        }

        foreach ($products as $p){
            //Load last item modification if exists
            $tp=Mage::getModel("apdc_referentiel/backupmodif")->getCollection()->addFieldToFilter('product_id', $p->getId())->setOrder('updated_at', 'DSC')->getFirstItem()->getData();

            //Transform entity_id in product_id and format array (otherwise troubles!)
            $data_p=$this->formatCurrentData($p->getData());
            if(sizeof($tp)>0){
                //If previous entry, comparison of data
                $diff=array_diff($tp, $data_p);
                unset($diff['entity_id']);
                unset($diff['updated_at']);
                if(sizeof($diff)>0){

                    $this->saveEntry($data_p);
                }
            }else{
                //If no entry, save current value
                $this->saveEntry($data_p);
            }
        }
    }

    public function fixCats($observer){
        if(Mage::getSingleton('admin/session', array('name' => 'adminhtml'))->isLoggedIn()){
            $cat=$observer->getEvent()->getCategory()->getId();
            Mage::getModel('apdc_referentiel/categoriesbase')->fixCats(Mage::getModel('catalog/category')->load($cat));
        }
    }

}
