<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package zillah
 */

?>

		</div><!-- .container -->
	</div><!-- #content -->

	<footer>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="social-icons">
                        <li><a href="https://www.facebook.com/aupasdecourses" title="Facebook" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://twitter.com/aupasdecourses" title="Twitter" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="https://instagram.com/aupasdecourses/" title="Instagram" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <p class="copyright text-center">
                        ©<?= getdate()[year]?> Au Pas De Courses, tous droits réservés.
                    </p>
                    <ul class="apdc-liens text-center" role="navigation">
                        <li><a href="https://www.aupasdecourses.com/accueil/../faq" title="Mentions légales">Mentions légales</a></li>
                        <li><a href="https://www.aupasdecourses.com/accueil/../faq" title="Conditions générales d'utilisation">Conditions générales d'utilisation</a></li>
                        <li><a href="https://www.aupasdecourses.com/accueil/../faq" title="Tarifs">Tarifs</a></li>
                        <li><a href="https://www.aupasdecourses.com/accueil/../blog" title="Blog">Blog</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<script type="text/javascript">
    jQuery(document).ready(function($){
        /* Replace the search bar placeholder text on small screens */
        if ($(window).width() <= 460){
            $(".proinput input").attr("placeholder", "Rechercher...");
        }
    })
</script>

<?php wp_footer(); ?>

</body>
</html>
