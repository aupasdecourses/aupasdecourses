<?php
if($_POST['action']=='optimiser'){
	shell_exec ("./photos_produits.sh");
	echo "Processing OK!";
}elseif($_POST['action']=='reindex'){
	shell_exec ("./apdc-reindex-all.sh");
	echo "Reindex OK!";
}else{
	echo "error";
}
?>
