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

	<footer class="site-footer" role="contentinfo">

		<div class="site-info">
		    
			<div class="container-footer-info">
                    
                    <p>&copy; Au Pas De Courses <?= getdate()[year]?>. Tous droits réservés.</p>
                    <a href="https://www.aupasdecourses.com/batignolles/nos-engagements/" target="_blank">NOS ENGAGEMENTS</a>
                    <a href="https://www.aupasdecourses.com/batignolles/mentions-legales-cgv/" target="_blank">MENTIONS LÉGALES &amp; CGV</a>
                    <a href="https://www.aupasdecourses.com/batignolles/politique-confidentialite-restriction-cookie/" target="_blank">POLITIQUE DE CONFIDENTIALITÉ</a>
                    <a href="https://www.aupasdecourses.com">
                        AUPASDECOURSES.com
                    </a>
                    <a href="https://www.aupasdecourses.com/blog">BLOG</a>
                 
			</div><!-- end .container-footer-info-->
		</div><!-- end .site-info -->

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
