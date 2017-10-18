<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package zillah
 */

get_header(); ?>

	<div class="content-wrap">

		<div id="primary" class="col-sm-12 content-area">
			<main id="main" class="site-main" role="main">

				<section class="error-404 not-found">
					<header class="page-header">
						<h1 class="page-title"><?php esc_html_e( 'Aiee! Cette page est introuvable.', 'zillah' ); ?></h1>
					</header><!-- .page-header -->

					<div class="page-content">
						<p><?php esc_html_e( 'Désolé, il semble que cette page n&rsquo;existe pas. Retournez à la page d&rsquo;accueil ou la page précédente.', 'zillah' ); ?></p>
					</div><!-- .page-content -->
				</section><!-- .error-404 -->

			</main><!-- #main -->
		</div><!-- #primary -->

	</div><!-- .content-wrap -->

<?php
get_footer();

