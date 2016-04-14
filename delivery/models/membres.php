<?php

function hashage_mdp($nom_utilisateur,$mdp){
	return $hash = sha1('helloworld'.$nom_utilisateur.'foxtrotbravo'.$mdp.'biloubilou');
}

 function ajouter_membre_dans_bdd($login,$nom_utilisateur,$prenom_utilisateur,$mdp, $email, $telephone,$hash_validation,$role) {

	if(isset($lastinsertid)){unset($lastinsertid);}
	if(isset($erreur)){unset($erreur);}
	
	$pdo = PDO2::getInstance();

	//Création dans la table membres
	$requete = $pdo->prepare("INSERT INTO membres SET
		login=:login,
		nom_utilisateur = :nom_utilisateur,
		prenom_utilisateur = :prenom_utilisateur,
		email = :email,
		telephone=:telephone,
		hash_validation = :hash_validation,
		date_inscription = NOW(),
		mdp = :mdp,
		role = :role");
	
	$requete->bindValue(':login', $login);
	$requete->bindValue(':nom_utilisateur', $nom_utilisateur);
	$requete->bindValue(':prenom_utilisateur', $prenom_utilisateur);
	$requete->bindValue(':mdp',    $mdp);
	$requete->bindValue(':email',   $email);
	$requete->bindValue(':telephone',   $telephone);
	$requete->bindValue(':hash_validation', $hash_validation);
	$requete->bindValue(':role',   $role);

	if ($requete->execute()) {
	
		$lastinsertid= $pdo->lastInsertId();
	}
	
	$erreur['membres'] = $requete->errorInfo();
	
	//Création dans la table livreur ou commerçant
	if(isset($lastinsertid)){
	    $text='INSERT INTO '.$role.' SET id_membre=:id_membre';
	    $requete=$pdo->prepare($text);
	    $requete->bindValue(':id_membre',$lastinsertid);	
	    if ($requete->execute()) {
	    }else{
	      $erreur['dispo']=$requete->errorInfo();
	    }
	  return $lastinsertid;
	}else{
	  return $erreur;
	}
	
    }

function maj_avatar_membre($id_utilisateur , $avatarURL) {

	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("UPDATE membres SET
		avatarURL = :avatarURL
		WHERE
		id = :id_utilisateur");

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->bindValue(':avatarURL',         $avatarURL);

	return $requete->execute();
}

function maj_mot_de_passe_membre($id_utilisateur,$new_mdp){
	
	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("UPDATE membres SET
		mdp = :mdp
		WHERE
		id = :id_utilisateur");

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->bindValue(':mdp', sha1($new_mdp));

	
	return $requete->execute();
}


function maj_adresse_email_membre($id_utilisateur , $email) {

	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("UPDATE membres SET
		email = :email
		WHERE
		id = :id_utilisateur");

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->bindValue(':email', $email);

	return $requete->execute();
}

function maj_infos_utilisateur($type, $id_utilisateur , $info) {

	$pdo = PDO2::getInstance();
	
	$string="UPDATE membres SET ".$type." = :".$type." WHERE id = :id_utilisateur";

	$requete = $pdo->prepare($string);

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	
	$value=':'.$type;
	$requete->bindValue($value, $info);

	return $requete->execute();
}

function combinaison_connexion_valide($login, $mdp) {

	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("SELECT id FROM membres
		WHERE
		login = :login AND 
		mdp = :mdp AND
		hash_validation = ''");

	$requete->bindValue(':login', $login);
	$requete->bindValue(':mdp', $mdp);
	$requete->execute();
	
	if ($result = $requete->fetch(PDO::FETCH_ASSOC)) {
	
		$requete->closeCursor();
		return $result['id'];
	}
	return false;
}

function lire_infos_utilisateur($id_utilisateur) {

	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("SELECT login, nom_utilisateur, prenom_utilisateur, mdp, email, telephone, avatarURL, date_inscription, hash_validation, role
		FROM membres
		WHERE
		id = :id_utilisateur");

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->execute();
	
	if ($result = $requete->fetch(PDO::FETCH_ASSOC)) {
	
		$requete->closeCursor();
		return $result;
	}
	return false;
}

function valider_compte_avec_hash($hash_validation) {

	$pdo = PDO2::getInstance();

	$requete = $pdo->prepare("UPDATE membres SET
		hash_validation = ''
		WHERE
		hash_validation = :hash_validation");

	$requete->bindValue(':hash_validation', $hash_validation);
	
	$requete->execute();

	return ($requete->rowCount() == 1);
}

// function url_exists($url) {
// 	$headers = get_headers($url);
// 	$httpcode = substr($headers[0], 9, 3);
// 	return in_array($httpcode, array(200, 301));
// }

/*HORAIRES MAGASIN*/

function recup_horaire_magasin($id_utilisateur){
	
	global $nom_jour;
	$pdo = PDO2::getInstance();
	
	$string1="SELECT ";
	$string2=" FROM commercant WHERE id_membre=:id_utilisateur";
	foreach ($nom_jour as$j){
	  $string1=$string1.'horaire_'.lcfirst($j).',';
	}
	$string1=substr($string1, 0, strlen($string1)-1);
	$string=$string1.$string2;
		
	$requete = $pdo->prepare($string);

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->execute();
	
	if ($result = $requete->fetch(PDO::FETCH_ASSOC)) {
	
		$requete->closeCursor();
		return $result;
	}
	return false;
}


function afficher_horaire_magasin($id){
		
	global $nom_jour;
	
	//tableau des heures sélectionnables
	$heure[0]='Fermé';
	for($i=0;$i<24;$i++){
	  $heure[2*$i+1]=$i.'h00';
	  $heure[2*$i+2]=$i.'h30';
	}
	
	//Récupération des ouvertures du magasin et stockage dans tableau
	$horaire=recup_horaire_magasin($id);
	foreach($nom_jour as $j){
	  $horaire_split[$j]=explode("|",$horaire['horaire_'.lcfirst($j)]);
	}
	
	//Tableau intitulé des noms des select
	$nom_select=['-matinouv','-matinfer','-apresouv','-apresfer'];
	
	echo '<form action"" method="post"><table class="table table-striped table-condensed"><thead><tr>';
	echo '<th rowspan="2"></th><th colspan="2">Matin</th><th colspan="2">Après-midi</th></tr>';
	echo '<tr><th class="th_small">Ouverture</th><th class="th_small">Fermeture</th><th class="th_small">Ouverture</th><th class="th_small">Fermeture</th></tr>';
	echo '</tr></thead><tbody>';
	foreach ($nom_jour as $j){
	  echo '<tr><td>'.$j.'</td>';
	  foreach($nom_select as $knom=>$kvalue){
	    echo '<td><select name="'.$j.$kvalue.'">';
	    foreach($heure as $key=>$value){
	      $check=($horaire_split[$j][$knom]==$value)?'selected="selected"':'';
	      echo '<option value="'.$value.'" '.$check.'>'.$value.'</option>';
	    }
	    echo '</select></td>';
	  }
	}	
	echo '</tbody></table>';
	echo '<input type="hidden" name="modif_horaire_magasin" value="modif_horaire_magasin">';
	echo '<div class="btn_modif_dispo"><a href=""><button class="submit">Modifier mes horaires</button></a></div></form>';

}

function update_horaire_magasin($id_utilisateur,$horaire){

	//A faire: ajouter la vérification de la cohérence des horaires renseignés (fermeture > ouverture par ex)

	$pdo = PDO2::getInstance();
	
	//génération des strings pour dispo_xx
	global $nom_jour;
	$horaire_str=array();
	$flag=['-matinouv','-matinfer','-apresouv','-apresfer'];
	
	foreach($nom_jour as $j){
	  $str="";
	  foreach($flag as $key=>$value){
	    $str=$str.$_POST[$j.$value].'|';
	  }
	  $str=substr($str,0,strlen($str)-1);
	  $dispo_str['horaire_'.lcfirst($j)]=$str;
	}
	
	//Préparation requête
	$string1="UPDATE commercant SET ";
	$string2=" WHERE id_membre=:id_utilisateur";
	foreach($dispo_str as $key=>$value){
	  $string1=$string1.$key.'=:'.$key.',';
	}
	$string1=substr($string1, 0, strlen($string1)-1);
	$string=$string1.$string2;

	$requete = $pdo->prepare($string);

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	foreach($dispo_str as $key=>$value){
	  $requete->bindValue(':'.$key,$value);
	}

	if($requete->execute()){
	}else{
	  return $erreur;
	}

}

/*DISPOS LIVREURS*/

function recup_dispo_livreur($id_utilisateur){
	
	global $nom_jour;
	
	$pdo = PDO2::getInstance();
	
	$string1="SELECT ";
	$string2=" FROM livreur WHERE id_membre=:id_utilisateur";
	foreach ($nom_jour as$j){
	  $string1=$string1.'dispo_'.lcfirst($j).',';
	}
	$string1=substr($string1, 0, strlen($string1)-1);
	$string=$string1.$string2;
	
	$requete = $pdo->prepare($string);

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	$requete->execute();
	
	if ($result = $requete->fetch(PDO::FETCH_ASSOC)) {
	
		$requete->closeCursor();
		return $result;
	}
	return false;
}

function afficher_dispo_livreur($id){
	
	global $nom_jour;
	$plage_horaire=[HEURE_DEBUT,HEURE_FIN];
	$creneau_horaire=array();
	
	$dispo=recup_dispo_livreur($id);
	foreach($nom_jour as $j){
	  $dispo_split[$j]=str_split($dispo['dispo_'.lcfirst($j)]);
	}
	
	for($i=$plage_horaire[0];$i<$plage_horaire[1];$i++){
	  $creneau_horaire[$i-HEURE_DEBUT]=$i.'h-'.($i+1).'h';
	}
	
	echo '<form action"" method="post"><table class="table table-striped table-condensed"><thead><tr><th></th>';
	foreach($nom_jour as $j){
	  echo '<th>'.$j.'</th>';
	}
	echo '</tr></thead><tbody>';
	
	foreach ($creneau_horaire as $key=>$h){
	  echo '<tr><td>'.$h.'</td>';
	  foreach($nom_jour as $j){
	    if($dispo_split[$j][$key]==1){
	      	echo '<td><input type="checkbox" name="'.$j.'-'.$key.'" value="'.$j.'-'.$key.'" checked></td>';
	    }	else{
	      echo '<td><input type="checkbox" name="'.$j.'-'.$key.'" value="'.$j.'-'.$key.'"></td>';
	    }
	  }
	  echo '</tr>';
	}	
	echo '</tbody></table>';
	echo '<input type="hidden" name="modif_dispo_livreur" value="modif_dispo_livreur">';
	echo '<div class="btn_modif_dispo"><a href=""><button class="submit">Modifier mes horaires</button></a></div></form>';

}

function update_dispo_livreur($id_utilisateur,$dispo){

	$pdo = PDO2::getInstance();
	
	//génération des strings pour dispo_xx
	global $nom_jour;
	$dispo_str=array();
	
	foreach($nom_jour as $j){
	  $str="";
	  for($i=0;$i<HEURE_FIN-HEURE_DEBUT;$i++){
	    $post=(isset($_POST[$j.'-'.$i]))?1:0;
	    $str=$str.$post;
	  }
	  $dispo_str['dispo_'.lcfirst($j)]=$str;
	}
	
	//Préparation requête
	$string1="UPDATE livreur SET ";
	$string2=" WHERE id_membre=:id_utilisateur";
	foreach($dispo_str as $key=>$value){
	  $string1=$string1.$key.'=:'.$key.',';
	}
	$string1=substr($string1, 0, strlen($string1)-1);
	$string=$string1.$string2;

	$requete = $pdo->prepare($string);

	$requete->bindValue(':id_utilisateur', $id_utilisateur);
	foreach($dispo_str as $key=>$value){
	  $requete->bindValue(':'.$key,$value);
	}

	$requete->execute();

}

function get_idcommercant($id_membre,$table_commercants){

	$pdo = PDO2::getInstance();
	
	$string="SELECT id_commercant FROM commercant WHERE id_membre=:id_utilisateur";
	$requete = $pdo->prepare($string);
	$requete->bindValue(':id_utilisateur', $id_membre);
	$requete->execute();
	
	if ($result = $requete->fetch(PDO::FETCH_ASSOC)) {
		$requete->closeCursor();
		$result=urlencode($table_commercants[$result['id_commercant']]);
		return $result;
	}
	return false;
}

?>