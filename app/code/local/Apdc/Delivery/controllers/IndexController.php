<?php
/*
* @author Pierre Mainguet
*/
class Apdc_Delivery_IndexController extends Mage_Core_Controller_Front_Action {

	public function indexAction(){}

    public function getextension($filename){
	    $temp = explode("/", $filename);
	    $extension = end($temp);
	    return $extension;
	  }

	 public function checkextension($extension){
	    $allowedExts = array("gif", "jpeg", "jpg", "png");
	    return in_array($extension, $allowedExts);
	  }

	public function getcheckext($filename){
		$extension=$this->getextension($filename);
		return $this->checkextension($extension);
	}

	/** Fonction similaire dans le trait Credimemo dans Indi **/
	public function setCloseStatus($order){
        $shipment = $order->prepareShipment();
        $shipment->register();
        $order->setIsInProcess(true);
        $order->addStatusHistoryComment('Automatically Shipped by APDC Delivery.', false);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();
	}

	 public function correctImageOrientation($filename) {
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

	public function processdataAction($TypeData,$params,$files){
	
	  if($TypeData!='ddb'){
		  $incrementid=$params['orderid'];
		  $order = Mage::getModel('sales/order')->loadByIncrementId($incrementid);
		  $orderid=$order->getId();
		  $field=Mage::helper('pmainguet_delivery')->check_amorderattach($orderid);
	  }

	//Cases
	switch ($TypeData){
	 	case 'ticket':
	 	  $incrementid=$params['orderid'];
	      $type=$files["imageticket"]["type"];
	      $temp=$files["imageticket"]["tmp_name"];
	      if ($this->getcheckext($type)){
	        $imagename=$incrementid."_ticket.".$this->getextension($type);
	        if (file_exists(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename) || $field->getData('ticket_commercant')!="") {
	              if (isset($params['supimageticket'])){
	                move_uploaded_file($files["imageticket"]["tmp_name"],Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
	                $this->correctImageOrientation(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
	                $field->setData('ticket_commercant',$imagename);
	                $field->save();
	                echo true;                
	              }else{
	                echo false;
	              }
	          } else {
	              move_uploaded_file($files["imageticket"]["tmp_name"],Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
	              $this->correctImageOrientation(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename);
	              $field->setData('ticket_commercant',$imagename);
	              $field->save();
	              echo true;
	          }
	      }else{
	      	//fichier invalide
	        echo false;
	      }
	      break;
	 	case 'ddb':
		    $check=false;    
	    	$datatable=$params['data'];
	        $sup=$params['supscreenshot'];

	        $order_item_ids=array();
	        foreach($datatable as $k=>$d){
	        	array_push($order_item_ids,$d['order_item_id']);
	        }

	        //ARRAY_COLUMN NE FONCTIONNE PAS SOUS OVH
    //     	//$order_item_ids=array_column($datatable, 'order_item_id');
	        try{
	        	//Check already existing record in database
	        	$items = Mage::getModel('pmainguet_delivery/refund_items');
	        	$presentIds=$items->getCollection()
	    		->addFieldToFilter('order_item_id', array('in' => $order_item_ids))
	    		->addFieldToSelect('order_item_id')->getColumnValues('order_item_id');
				$newIds = array_diff($order_item_ids, $presentIds);
				
				//Function for non existing Ids
				if(count($newIds)>0){
					foreach($newIds as $id){
						$key=array_search($id, $order_item_ids);
						$data=$datatable[$key];
						$items->setData($data);
						$items->save();
						echo "Item ".$id." créé.";
					}
					$check=true;
				}

				if($sup<>'false'){
					foreach($presentIds as $id){
						$key=array_search($id, $order_item_ids);
						$data=$datatable[$key];
						$item=$items->load($data['order_item_id'], 'order_item_id');
						$item->addData($data);
						$item->save();
						//echo "Item ".$item->getItemName()." mis à jour.";
					}
					$check=true;
				}
				if($check){
					echo true;
				}else{
					echo false;
				}

	        }catch(Exception $e){
	        	echo $e->getMessage();
	        }
	    	break;
	    case 'screenshot':
	      $imageData=$params['image'];
	      $imagename=$incrementid."_remb.png";
	      if (file_exists(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename) || $field->getData('screenshot')!="") {
	        if ($params['supscreenshot']!="false"){
	          $unencodedData=base64_decode($imageData);
	          file_put_contents(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename,$unencodedData);
	          $field->setData('screenshot',$imagename);
	          $field->save(); 
	          echo true;                
	        }else{
	          echo false;
	        }
	      } else {
	        $unencodedData=base64_decode($imageData);
	        file_put_contents(Mage::getBaseDir('media') . DS . 'attachments'. DS . $imagename,$unencodedData);
	        $field->setData('screenshot',$imagename);
	        $field->save(); 
	        echo true;
	      }
	      break;
	    case 'comment':
	      $commentremboursement=$params['commentremboursement'];
	      $commentticket=$params['commentticket'];
	      $commentfraislivraison=$params['commentfraislivraison'];
	      if ($field->getData('remboursements')!="" || $field->getData('commentaires_ticket')!="" || $field->getData('commentaires_fraislivraison')!="") {
	        if ($params['supcomments']!="false"){
	          $field->setData('remboursements',$commentremboursement);
	          $field->setData('commentaires_ticket',$commentticket);
	          $field->setData('commentaires_fraislivraison',$commentfraislivraison);
	          $field->save(); 
	          echo true;                
	        }else{
	          echo false;
	        }
	      } else {
	        $field->setData('remboursements',$commentremboursement);
	          $field->setData('commentaires_ticket',$commentticket);
	          $field->setData('commentaires_fraislivraison',$commentfraislivraison);
	          $field->save(); 
	        echo true;                
	      } 
	      break;
	    case 'close':
	    	try{
	    		$this->setCloseStatus($order);
	    		echo true;
	    	}catch(Exception $e){
	    		echo false;
	    	}
	    	break;
	}
}

//entry point
public function processajaxAction(){
	$params = $this->getRequest()->getParams();
 	$TypeData=$params['type'];
	try{
 		$this->processdataAction($TypeData,$params,$_FILES);
 	}catch(Exception $e){
      	echo $e->getMessage();
    }
}

}