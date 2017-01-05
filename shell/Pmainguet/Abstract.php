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

class Pmainguet_Abstract extends Mage_Shell_Abstract
{
    
    public function run(){}

    public function get_contactentity($email){
        $contacts=Mage::getSingleton('apdc_commercant/contact')->getCollection();
        return $contacts->addFieldToFilter('email', $email)->getFirstItem();
    }

    //Création des entités contacts
    public function create_contactentity($lastname,$firstname,$email){
        if($email!=''){
            $contact=$this->get_contactentity($email);
            if (null == $contact->getId()) {
                $contact=Mage::getSingleton('apdc_commercant/contact');
                $data=[
                    'firstname'=>$firstname,
                    'lastname'=>$lastname,
                    'email'=>$email,
                    'role_id'=>array(1,2,3,4),
                ];
                $contact->setData($data)->save();
                $text = 'Contact %s %s CREATED!\n';
                echo sprintf($text, $firstname, $lastname);
            } else{
                $text = 'Contact %s %s already EXISTS. Next!\n';
                echo sprintf($text, $firstname, $lastname);
            }
        }else{
             $text = 'Pas de mail renseigné pour %f %l !\n';
            echo sprintf($txet, $firstname, $lastname);
        }
    }

    public function get_commercantentity($name){
        $commercants=Mage::getSingleton('apdc_commercant/commercant')->getCollection();
        return $commercants->addFieldToFilter('name', $name)->getFirstItem();
    }

    //Création des entités commerçants
    public function create_commercantentity($name,$id_contact_ceo,$id_contact_billing,$zipcode,$city){
        $commercant=$this->get_commercantentity($name);
        if (null == $commercant->getId()) {
            $commercant=Mage::getSingleton('apdc_commercant/commercant');
            $data=[
                'name'=>$name,
                'id_contact_ceo'=>$id_contact_ceo,
                'id_contact_billing'=>$id_contact_billing,
                'hq_postcode'=>$zipcode[0],
                'hq_city'=>$city,
                'hq_country'=>'FR',
            ];
            $commercant->setData($data)->save();
            $text = 'Commercant %s CREATED!\n';
            echo sprintf($text, $name);
        } else{
            $text = 'Commercant %s already EXISTS. Next!\n';
            echo sprintf($text, $name);
        }
    }

    //Création des entités magasins
    public function create_magasinentity($name,$namecommercant,$id_contact,$id_category,$id_attribut_commercant,$zipcode,$googlesheets){
        $shop=Mage::getModel('apdc_commercant/shop')->getCollection()->addFieldToFilter('name', $name)->getFirstItem();
        if (null == $shop->getId()) {
            $shop=Mage::getSingleton('apdc_commercant/shop');
            $data=[
                'enabled'=>true,
                'name'=>$name,
                'id_commercant'=>Mage::getSingleton('apdc_commercant/commercant')->getCollection()->addFieldToFilter('name', $namecommercant)->getFirstItem()->getId(),
                'id_contact_manager'=>$id_contact,
                'id_category'=>$id_category,
                'id_attribut_commercant'=>$id_attribut_commercant,
                'delivery_days'=>array(2,3,4,5),
                'city'=>'Paris',
                'postcode'=>$zipcode[0],
                'google_id'=>$googlesheets['google_id'],
                'google_key'=>$googlesheets['google_key'],
            ];
            $shop->setData($data)->save();
            $text = 'Magasin %s CREATED!\n';
            echo sprintf($text, $name);
        } else{
            $text = 'Magasin %s already EXISTS. Next!\n';
            echo sprintf($text, $name);
        }
    }
}