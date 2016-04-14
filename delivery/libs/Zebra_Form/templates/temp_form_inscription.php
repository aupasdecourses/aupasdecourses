<?php
    // don't forget about this for custom templates, or errors will not show for server-side validation
    // $zf_error is automatically created by the library and it holds messages about SPAM or CSRF errors
    // $error is the name of the variable used with the set_rule method
    echo (isset($zf_error) ? $zf_error : (isset($error) ? $error : ''));
?>
  
<div class="form col-md-4" role="form">
    <div class="form-group"><?php echo $label_role . $role?></div>
    <div class="form-group"><?php echo $label_login . $login?></div>
    <div class="form-group"><?php echo $label_nom . $nom_utilisateur?></div>
    <div class="form-group"><?php echo $label_prenom . $prenom_utilisateur?></div>
    <div class="form-group"><?php echo $label_email . $email.$note_email?></div>
    <div class="form-group"><?php echo $label_telephone . $telephone?></div>
</div>
<div class="form col-md-4" role="form">
    <div class="form-group"><?php echo $label_avatarURL.$avatarURL?></div>
    <div class="form-group"><?php echo $avatar?></div>
    <div class="form-group"><?php echo $label_password . $mdp.$note_password?></div>
    <div class="form-group"><?php echo $label_confirm_password . $mdp_verif?></div>
</div>
<div class="form col-md-4" role="form">
    <div class="form-group"><?php echo $captcha_image.$label_captcha_code.$captcha_code.$note_captcha?></div>
    <div class="form-group"><?php echo $btnsubmit?></div>
</div>