<?php
if($_POST['action']=='optimiser'){
	shell_exec ("./photos_produits.sh");
	echo "Processing OK!";
}
elseif($_POST['action']=='reindex'){
	shell_exec ("./apdc-reindex-all.sh");
	echo "Reindex OK!";
}
elseif($_POST['action'] =='commercants'){
	shell_exec ("./photos_commercants.sh".$_POST['image']);
	echo "Optimisation and thumbnail creation for merchants done!";
}
elseif($_POST['action'] =='categories'){
	shell_exec ("./photos_categories.sh".$_POST['image']);
	echo "Optimisation and thumbnail creation for categories done!";
}
else{
	echo "error";
}
?>
