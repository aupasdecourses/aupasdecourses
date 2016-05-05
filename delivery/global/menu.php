<div class="navbar-header">
  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
  <a class="navbar-brand" href="index.php"><i class="fa fa-bicycle fa-2x"></i><span> Au Pas De Courses</span> </a>
</div>
<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
 <?php if (!utilisateur_est_connecte()) :?>
<?php else : ?>
  <ul class="nav navbar-nav">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Commandes<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li><a href="index.php?module=commercant&action=view&option=order"><b>Liste par Commerçant</b></a></li>
          <li><a href="index.php?module=commande&action=view">Liste complète (détails)</a></li>         
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Livraison<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li><a href="index.php?module=dispatch&action=view&option=listing"><b>Listing picking</b></a></li>
          <li><a href="index.php?module=commande&action=view&option=client"><b>Listing livraison</b></a></li>
          <li><a href="index.php?module=dispatch&action=view&option=route">Créations tournées</a></li>
          <li><a href="index.php?module=dispatch&action=view&option=carte">Cartes</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Remb./Facturaton<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li><a href="index.php?module=remboursement&action=view">Remboursements</a></li>
          <li><a href="index.php?module=facturation&action=view">Facturation</a></li>
        </ul>
    </li>
    <li><a href="index.php?module=commercant&action=view&option=profile">Profils commerçant</a></li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Clients<span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li> <a href="index.php?module=clients&action=view&option=stat">Stats Clients</a></li>
          <li><a href="index.php?module=clients&action=view&option=fidelity">Fidelité Clients</a></li>
          <li><a href="index.php?module=clients&action=view&option=notation">Notes commande</a></li>
        </ul>
    </li>
    <li><a href="index.php?module=prepacommande&action=view">Process</a></li>
    <li><a href="index.php?module=profil&amp;action=deconnexion">Déconnexion</a></li>
  </ul>
    <?php endif; ?>
</div>