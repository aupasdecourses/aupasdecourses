<div class="col-sm-12">
	<div class="row">
		<?php if($check!=NULL){
	  		foreach($collection as $c){
	  			echo "<h1><small>Ticket(s) uploadé(s)</small></h1>";
	  			$array=explode(";",$c->getData('ticket_commercant'));
  				foreach($array as $a){
	  				if($a!=""){
	  					echo '<a target="_blank" href="'.Mage::getBaseUrl('media').DS.'attachments'.DS.$a.'"><img class="img-responsive center-block" src="'.Mage::getBaseUrl('media').DS.'attachments'.DS.$a.'"></a>';
	  				}else{
	  					echo "Aucun";
	  				}
	  			}
	  			echo "<h1><small>Calcul remboursement (image)</small></h1>";
	  			$a=$c->getData('screenshot');
	  			if($a!=""){
	  					echo '<a target="_blank" href="'.Mage::getBaseUrl('media').DS.'attachments'.DS.$a.'"><img class="img-responsive center-block" src="'.Mage::getBaseUrl('media').DS.'attachments'.DS.$a.'"></a>';
	  				}else{
	  					echo "Aucun";
	  				}
	  		}}
	  	?>
	</div>
</div>