<?php

class Apdc_Referentiel_Model_Categoriesposition extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_referentiel/categoriesposition');
    }

    public function getPositionRef(){
        $imagecat_collection=$this->getCollection();
        $ics = $imagecat_collection->toArray(array('name', 'position'))['items'];
        $result=[];
        foreach($ics as $i){
            if(!isset($result[$i['name']])){
                $result[$i['name']]=array(0=>$i['position']);
            }else{
                array_push($result[$i['name']], $i['position']);
            }
        }
        return $result;
    }

}