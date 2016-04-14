<?php 
include(dirname(__FILE__).'/../Model/model.php');
session_start();
validate_item($_POST['order_id'],$_POST['comment']);
echo 'Order '.$_POST['order_id'].' updated';
?>