<?php
  //init Magento
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  include '../../global/init.php';
  include CHEMIN_MODELE.'magento.php';
  connect_magento();
  
  function getextension($filename){
    $temp = explode("/", $filename);
    $extension = end($temp);
    return $extension;
  }

  function checkextension($extension){
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    return in_array($extension, $allowedExts);
  }

  // Get the data
  $TypeData=$_POST['type'];
  $incrementid=$_POST['orderid'];

  $order = Mage::getModel('sales/order')->loadByIncrementId($incrementid);
  $orderid=$order->getId();
  $field=Mage::getModel('amorderattach/order_field');

  //Check if entity exists in database
  $check=$field->getCollection()->addFieldToFilter('order_id', $orderid)->getFirstItem()->getId();
  if($check==NULL){
    $field->setData('order_id',intval($orderid));
  }else{
    $field->load($orderid, 'order_id');
  }

  //Cases
  switch ($TypeData){
    case 'comment':
      $commentremboursement=$_POST['commentremboursement'];
      $commentticket=$_POST['commentticket'];
      if ($field->getData('commentaires_commande')!="" || $field->getData('remboursements')!="") {
        if ($_POST['supcomments']!="false"){
          $field->setData('commentaires_commande',$commentremboursement);
          $field->setData('remboursements',$commentticket);
          echo "Commentaire(s) modifié(s).";                
        }else{
          echo "Des commentaire(s) existe(nt) déjà.";
        }
      } else {
        $field->setData('commentaires_commande',$commentremboursement);
        $field->setData('remboursements',$commentticket);
        echo "Commentaire(s) enregistré(s).";                
      } 
      break;
    case 'ticket':
      $type=$_FILES["imageticket"]["type"];
      $temp=$_FILES["imageticket"]["tmp_name"];
      if (checkextension(getextension($type))){
        $imageData=$_POST['image'];
        $imagename=$incrementid."_ticket.".getextension($type);
        if (file_exists(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename) || $field->getData('ticket_commercant')!="") {
              if (isset($_POST['supimageticket'])){
                move_uploaded_file($_FILES["imageticket"]["tmp_name"],Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
                $field->setData('ticket_commercant',$imagename);
                echo $imagename." modifié.";                
              }else{
                echo $imagename . " existe déjà. ";
              }
          } else {
              move_uploaded_file($_FILES["imageticket"]["tmp_name"],Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
              $field->setData('ticket_commercant',$imagename);
              echo $imagename." enregistré";
          }
      }else{
        echo "Fichier invalide. Merci de sélectionner une image jpg,jpeg,png ou gif.";
      }
      break;
    case 'screenshot':
      $imageData=$_POST['image'];
      $imagename=$incrementid."_remb.png";
      if (file_exists(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename) || $field->getData('screenshot')!="") {
        if ($_POST['supscreenshot']!="false"){
          $unencodedData=base64_decode($imageData);
          file_put_contents(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename,$unencodedData);
          $field->setData('screenshot',$imagename);
          echo $imagename." modifié.";                
        }else{
          echo $imagename. " existe déjà. ";
        }
      } else {
        $unencodedData=base64_decode($imageData);
        file_put_contents(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename,$unencodedData);
        $field->setData('screenshot',$imagename);
        echo $imagename." enregistré";
      } 
      break;
  }

$field->save();

?>