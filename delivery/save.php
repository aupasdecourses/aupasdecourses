<?php
  //init Magento
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  include 'global/init.php';
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

  function correctImageOrientation($filename) {
    if (function_exists('exif_read_data')) {
      $exif = exif_read_data($filename);
      if($exif && isset($exif['Orientation'])) {
        $orientation = $exif['Orientation'];
        if($orientation != 1){
          $img = imagecreatefromjpeg($filename);
          //Resize
          $tailleimg=getimagesize($filename);
          $newwidth=1100;
          $Reduction = ( ($newwidth * 100)/$tailleimg[0] );
          $newheight = ( ($tailleimg[1] * $Reduction)/100 );
          $newimg = imagecreatetruecolor($newwidth , $newheight) or die ("Erreur");
          imagecopyresampled($newimg , $img, 0, 0, 0, 0, $newwidth, $newheight, $tailleimg[0],$tailleimg[1]);
          imagedestroy($img);
          //rotation
          $deg = 0;
          switch ($orientation) {
            case 3:
              $deg = 180;
              break;
            case 6:
              $deg = 270;
              break;
            case 8:
              $deg = 90;
              break;
          }
          if ($deg) {
            $newimg = imagerotate($newimg, $deg, 0);        
          }
          // then rewrite the rotated image back to the disk as $filename 
          imagejpeg($newimg, $filename, 95);
        } // if there is some rotation necessary
      } // if have the exif orientation info
    } // if function exists      
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
      $commentcommande=$_POST['commentcommande'];
      $commentticket=$_POST['commentticket'];
      $commentfraislivraison=$_POST['commentfraislivraison'];
      if ($field->getData('commentaires_commande')!="" || $field->getData('remboursements')!="" || $field->getData('commentaires_ticket')!="" || $field->getData('commentaires_fraislivraison')!="") {
        if ($_POST['supcomments']!="false"){
          $field->setData('remboursements',$commentremboursement);
          $field->setData('commentaires_commande',$commentcommande);
          $field->setData('commentaires_ticket',$commentticket);
          $field->setData('commentaires_fraislivraison',$commentfraislivraison);
          echo "Commentaire(s) modifié(s).";                
        }else{
          echo "Des commentaire(s) existe(nt) déjà.";
        }
      } else {
        $field->setData('remboursements',$commentremboursement);
          $field->setData('commentaires_commande',$commentcommande);
          $field->setData('commentaires_ticket',$commentticket);
          $field->setData('commentaires_fraislivraison',$commentfraislivraison);
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
                correctImageOrientation(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
                $field->setData('ticket_commercant',$imagename);
                echo $imagename." modifié.";                
              }else{
                echo $imagename . " existe déjà. ";
              }
          } else {
              move_uploaded_file($_FILES["imageticket"]["tmp_name"],Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
              correctImageOrientation(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
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