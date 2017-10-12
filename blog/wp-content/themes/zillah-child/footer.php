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

		<?php if ( is_active_sidebar( 'zillah-footer-widget-area-3' ) || is_active_sidebar( 'zillah-footer-widget-area' ) || is_active_sidebar( 'zillah-footer-widget-area-2' ) ) : ?>

			<div class="container container-footer">

				<div class="footer-inner">
					<div class="row">
						<div class="col-sm-4">
							<?php
							if ( is_active_sidebar( 'zillah-footer-widget-area' ) ) {
								dynamic_sidebar( 'zillah-footer-widget-area' );
							}?>
						</div>

						<div class="col-sm-4">
							<?php
							if ( is_active_sidebar( 'zillah-footer-widget-area-2' ) ) {
								dynamic_sidebar( 'zillah-footer-widget-area-2' );
							} ?>
						</div>

						<div class="col-sm-4">
							<?php
							if ( is_active_sidebar( 'zillah-footer-widget-area-3' ) ) {
								dynamic_sidebar( 'zillah-footer-widget-area-3' );
							} ?>
						</div>
					</div>
				</div>
			</div> <!-- .container-footer -->

		<?php endif; ?>

		<div class="site-info">
		    
			<div class="container-footer-info">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <img class="logo-blog" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/logo-blog-blanc-150x32.png" alt="Logo du Blog d'AuPasDeCourses"/>
                    </a>
                
                    <span id="footer-copyright-and-icons">
                       <span>
                           &copy;<?= getdate()[year]?> Le blog d'<a href="https://aupasdecourses.com/">Au Pas De Courses</a>, tous droits réservés
                       </span>
                       <span>
                           <?php dynamic_sidebar( 'footer-sidebar' ); ?>
                       </span>
                    </span>
                
                    <a href="https://www.aupasdecourses.com" target="_blank">
                        <img class="logo-site" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/logo-APDC.png" alt="Logo Au Pas de Courses">
                    </a>
                 
			</div>
		</div><!-- .site-info -->

	</footer>
</div><!-- #page -->


<script type="text/javascript">
    jQuery(document).ready(function($){
        
        /* ============ HORIZONTAL MENU SECTION ============ */
        /* resize horizontal menu bar in header when the user scrolls more than 50px down */
        function checkScrollOffset() {
            if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                document.getElementById("top-menu-fixed-wrapper").className = "opaque-menu-bg";
                document.getElementById("logo-apdc").className = "logo-apdc-small";
            } else {
                document.getElementById("top-menu-fixed-wrapper").className = "";
                document.getElementById("logo-apdc").className = "";
            }
        }
        
        /* ============ SINGLE POST SHARE BUTTONS SECTION ============ */
        /* The social icon share buttons will stay visible on the screen as the user scrolls
        down and back up over the article content. */
        
        /* check to see if the element #article-sharing-icon-wrapper exists on the page or not.
        This will only be true on the single post page. */
        if ( $("#article-sharing-icon-wrapper").length ){
            
            var offsetOfHorizontalMenu = $("#top-menu-fixed-wrapper").offset().top;
            var heightOfHorizontalMenu = $("#top-menu-fixed-wrapper").outerHeight();
            var bottomOfHorizontalMenu;
            
            var offsetOfIconWrapper = $("#article-sharing-icon-wrapper").offset().top;
            var heightOfIconWrapper = $("#article-sharing-icon-wrapper").outerHeight();
            var bottomOfIconWrapper = offsetOfIconWrapper + heightOfIconWrapper;

            var offsetOfArticleContent = $(".article-content").offset().top;
            var heightOfArticleContent = $(".article-content").outerHeight();
            var bottomOfArticleContent = offsetOfArticleContent + heightOfArticleContent;
            
            function scrollIconWrapper() {
                /* recalculate bottomOfHorizontalMenu */
                offsetOfHorizontalMenu = $("#top-menu-fixed-wrapper").offset().top;
                bottomOfHorizontalMenu = offsetOfHorizontalMenu + heightOfHorizontalMenu;
                
                /* if we have scrolled below the beginning of the article content */
                if ( offsetOfArticleContent < bottomOfHorizontalMenu ){
                
                    /* recalculate current bottomOfIconWrapper */
                    offsetOfIconWrapper = $("#article-sharing-icon-wrapper").offset().top;
                    
                    bottomOfIconWrapper = offsetOfIconWrapper + heightOfIconWrapper;
                    
                    /* if bottomOfIconWrapper is still above bottomOfArticleContent */
                    if (bottomOfIconWrapper <= bottomOfArticleContent){
                        $("#article-sharing-icon-wrapper").css("top",bottomOfHorizontalMenu - offsetOfArticleContent);
                    }else if ( bottomOfIconWrapper > bottomOfArticleContent && (bottomOfHorizontalMenu + heightOfIconWrapper) < bottomOfArticleContent ){
                        $("#article-sharing-icon-wrapper").css("top",bottomOfHorizontalMenu - offsetOfArticleContent);
                    }
                }
            } /* end function scrollIconWrapper */
        }/* end if #article-sharing-icon-wrapper exists */
        
        
        /* =========================================================================*/
        /* when page is loaded, if the page was scrolled before reload and returns
        to its previous scroll position without triggering the .onscroll() event
        we detect that here and execute the functions if need be. */
        checkScrollOffset();
        /* check to see if the element #article-sharing-icon-wrapper exists on the page or not.
        This will only be true on the single post page. */
        if ( $("#article-sharing-icon-wrapper").length ){
            scrollIconWrapper();
        }
        
        /* when there is a scroll event, check the offset and modify header if need be */
        window.onscroll = function() {
            checkScrollOffset();
            if ( $("#article-sharing-icon-wrapper").length ){
                scrollIconWrapper();
            }
        };
    })
</script>

<?php wp_footer(); ?>

</body>
</html>
