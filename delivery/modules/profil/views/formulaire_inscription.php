<div class="page-header"><h1>Inscription</h1></div>
<div class="row">
  <div class="col-md-12 Zebra_Form">
    <?php
    
	if (!empty($erreurs_inscription)) {

	      echo '<div class="error"><div class="container">'."\n";
	      
	      foreach($erreurs_inscription as $e) {
	      
		      echo '	<span>'.$e.'</span>'."\n";
	      }
	      
	      echo '</div></div>';
      }
	// generate output using a custom template
	$form_inscription->render('templates/temp_form_inscription.php');
    ?>
  </div>
</div>