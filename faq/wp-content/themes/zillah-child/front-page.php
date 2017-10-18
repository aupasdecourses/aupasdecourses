<?php
/*
@package zillah
*/

get_header(); 
?>

<main>
    <div class="row">
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>les-commercants/">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/favicon.png" alt="Qui sommes-nous ?"/>
                    <h2>Qui sommes-nous&nbsp;?</h2>
                    <p>
                        Le concept, nos valeurs, la sélection des commerçants
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>prix-codes-promos/">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/money.png" alt="Prix"/>
                    <h2>Prix</h2>
                    <p>
                        Prix des produits, utilisation des bons de réduction, prix du service
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>paiement">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/credit-card.png" alt="Paiement"/>
                    <h2>Paiement</h2>
                    <p>
                        Moyens de paiement, coordonnées bancaires, remboursement
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
    </div><!-- end .row -->
    
    <div class="row">
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>mes-commandes">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/groceries.png" alt="Qui sommes-nous ?"/>
                    <h2>Mes Commandes</h2>
                    <p>
                        Statut des commandes, modification ou annulation d'une commande
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>les-livraisons">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/bicycle.png" alt="Prix"/>
                    <h2>Livraison</h2>
                    <p>
                        Horaires, conservation des produits, réception de la commande
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>compte-client">
                    <img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2016/10/pencil.png" alt="Paiement"/>
                    <h2>Mon Compte</h2>
                    <p>
                        Réinitialiser mon mot de passe, changement d'adresse, inscription newsletter
                    </p>
                </a>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
    </div><!-- end .row -->
    
    <div class="row">
        <div class="col-sm-4">
            <div class="link-wrapper">
                <h2>CONTACTEZ NOUS</h2>
                <p>
                    Nous sommes à votre service. Contactez un membre dAu Pas de Courses
                </p>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <h2>APPELEZ NOUS</h2>
                <p>
                    Nous sommes disponibles du lundi au vendredi, de 9h à 18h, au 09.72.50.90.69
                </p>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
        <div class="col-sm-4">
            <div class="link-wrapper">
                <h2>SUIVEZ-NOUS</h2>
                <p id="suivez-nous">
                    <a href="https://www.facebook.com/aupasdecourses/" target="_blank"><img class="social-icons" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/facebook.png" title="Facebook" alt="Facebook"/></a>
                    <a href="https://twitter.com/aupasdecourses" target="_blank"><img class="social-icons" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/twitter.png" title="Twitter" alt="Twitter"/></a>
                    <a href="https://www.instagram.com/aupasdecourses/" target="_blank"><img class="social-icons" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/instagram.png" title="Instagram" alt="Instagram"/></a>
                </p>
            </div> <!-- end .link-wrapper -->
        </div> <!-- end column -->
    </div><!-- end .row -->
</main> <!-- end main .container -->

<?php
get_footer();
