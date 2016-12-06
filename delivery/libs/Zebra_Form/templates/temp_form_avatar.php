<?php
    // don't forget about this for custom templates, or errors will not show for server-side validation
    // $zf_error is automatically created by the library and it holds messages about SPAM or CSRF errors
    // $error is the name of the variable used with the set_rule method
    echo (isset($zf_error) ? $zf_error : (isset($error) ? $error : ''));

    //Form modif infos
if (!empty($msg_confirm)) {

	echo '<ul>'."\n";

	foreach($msg_confirm as $m) {

		echo '	<li>'.$m.'</li>'."\n";
	}

	echo '</ul>';
}

if (!empty($erreurs_form_modif_avatar)) {

	echo '<ul>'."\n";

	foreach($erreurs_form_modif_avatar as $e) {

		echo '	<li>'.$e.'</li>'."\n";
	}

	echo '</ul>';
}
  
?>
    <div class="form-group"><?php echo $label_suppr_avatar_choice1 . $suppr_avatar_choice1?></div>
    <div class="form-group"><?php echo $avatar?></div>
    <div class="form-group"><?php echo $label_avatarURL . $avatarURL?></div>
    <div class="form-group"><?php echo $btnsubmit?></div>