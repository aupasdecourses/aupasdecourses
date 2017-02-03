<div class="page-header"><h1>Votre profil</h1></div>
<div class="row">
  <div class="col-md-4">
    <label>Login:</label>
    <p><?php echo $_SESSION['login'];?></p>
    <label>Role:</label>
    <p><?php echo $_SESSION['role'];?></p>
    <?php  $form_modif_infos->render('templates/temp_form_infos.php'); ?>
    <br>
    <?php  $form_modif_avatar->render('templates/temp_form_avatar.php');?>
  </div>
  <div class="col-md-4">
      <?php $form_modif_mdp->render('templates/temp_form_mdp.php');?>
  </div>
  <div class="col-md-4">
      <?php if($_SESSION['role']=='livreur'){
	      if(isset($_POST['modif_dispo_livreur'])){
		update_dispo_livreur($_SESSION['id'],$_POST);
	      }
      ?>
	      <label>Mes disponibilités</label>
		<div class="horaires_dispos dispo_livreur">
		  <?php afficher_dispo_livreur($_SESSION['id']); ?>
		</div>
      <?php }elseif($_SESSION['role']=='commercant'){
	      if(isset($_POST['modif_horaire_magasin'])){
		update_horaire_magasin($_SESSION['id'],$_POST);
	      }
      ?>     
	      <label>Horaires magasins</label>
	      <div class="horaires_dispos horaires_magasin">
		  <?php afficher_horaire_magasin($_SESSION['id']); ?>
	      </div>
      <?php } ?>
  </div>