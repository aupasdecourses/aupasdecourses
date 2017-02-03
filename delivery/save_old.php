<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
  include 'global/init.php';
  include CHEMIN_MODELE.'magento.php';
  connect_magento();
  // Get the data
  $imageData=$_POST['image'];
  $imagename=$_POST['name'];
  $incrementid=$_POST['orderid'];
  $commentremboursement=$_POST['commentremboursement'];
  $commentticket=$_POST['commentticket'];
  // Remove the headers (data:,) part.
  // A real application should use them according to needs such as to check image type
  //$filteredData=substr($imageData, strpos($imageData, ",")+1);

  // Need to decode before saving since the data we received is already base64 encoded
  $unencodedData=base64_decode($imageData);
  // Save file. This example uses a hard coded filename for testing,
  // but a real application can specify filename in POST variable
  file_put_contents(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename,$unencodedData); 
  // $fp = fopen( 'test.png', 'wb' );
  // fwrite( $fp, $unencodedData);
  // fclose( $fp );

//Save to amasty Order Attachment tables
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

$field->setData('remboursements',$commentremboursement);
$field->setData('commentaires_ticket',$commentticket);
$field->setData('ticket_commercant',$field->getData('ticket_commercant').';'.$imagename);

$field->save();

?>