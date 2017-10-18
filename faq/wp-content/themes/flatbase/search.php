<?php
$i=0;$Booleen=0;$nb_de_page=0;$lien=site_url();
		$last_result = $wpdb->get_results("SELECT post_content, ID, post_title from wp_posts where post_type='page' and post_status='publish' and not ID=502", ARRAY_A);
		foreach ($last_result as $key => $value) {
			$nb_de_page++;
		}

// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

if ( ! empty( $_GET['ajax'] ) ? $_GET['ajax'] : null ) : // Is Live Search

if ( have_posts() ) : ?>

<ul id="search-result">

<?php while ( have_posts() && $i < 6) : the_post();

		$titre=get_the_title();

		for ($compteur=0; $compteur < $nb_de_page ; $compteur++) { 
			if (preg_match("#{$titre}#i", $last_result[$compteur]['post_content']) || preg_match("#{$titre}#i", $last_result[$compteur]['post_title'])) {
			$lien=' '.$lien.'/?page_id='.$last_result[$compteur]['ID'];
			break;
			}
		}


		if ( has_post_format( 'video' ) ) {
			$li_class = 'format-video';
			$nice_icon = '<i class="fa fa-youtube-play"></i>';
		} elseif ( 'faq' == get_post_type() ) {
			$li_class = 'format-faq';
			$nice_icon = '<i class="fa fa-question-circle"></i>';
		} elseif ( 'page' == get_post_type() ) {
			$li_class = 'format-page';
			$nice_icon = '<i class="fa fa-question-circle"></i>';
			next();
		}elseif ( 'post' == get_post_type() ) {
			$li_class = 'format-post';
			$nice_icon = '<i class="fa fa-question-circle"></i>';
		} else {
			$li_class = 'format-article';
			$nice_icon = '<i class="fa fa-question-circle"></i>';
		} 
		?>

		<li class="<?php echo $li_class; ?>">
			<a href="<?php echo $lien; ?>"><?php echo $nice_icon; print($titre);?></a>
		</li>

	<?php $i++;$lien=site_url(); endwhile; ?>
</ul>

<?php else : ?>

<ul id="search-result">
	<li class="no-results"><i class="fa fa-exclamation-circle"></i><?php _e( 'Désolé, nous n\'avons pas trouver ce que vous recherchiez.', 'nicethemes' ); ?></li>
</ul>

<?php endif;

else : // Is Normal Search

get_header(); ?>

<!-- BEGIN #content -->
<div id="content" class="<?php echo $post->post_name; ?>">

<header>
	<p><a href="/">Retour à la page d'accueil</a></p>
	<h1 class="archive-header"><?php _e( 'Résultat de la recherche', 'nicethemes' ); ?>: <?php the_search_query(); ?></h1>
</header>

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); 

		$titre=get_the_title();

		for ($compteur=0; $compteur < $nb_de_page ; $compteur++) { 
			if (preg_match("#{$titre}#i", $last_result[$compteur]['post_content']) || preg_match("#{$titre}#i", $last_result[$compteur]['post_title'])) {
			$lien=' '.$lien.'/?page_id='.$last_result[$compteur]['ID'];
			break;
			}
		}

		?>

				<article class="post clearfix">

					<header>
						<h2><a href="<?php echo $lien; ?>" rel="bookmark" ><?php the_title(); ?></a></h2>
						<?php nice_post_meta(); ?>
					</header>

					<?php if ( has_post_thumbnail() ) :?>

						<figure class="featured-image">
							<?php nice_image( array( 'width' => 620, 'height' => 285, 'class' => 'wp-post-image' ) ); ?>
						</figure>

					<?php endif; ?>

					<?php nice_excerpt(); ?>

				</article>

		<?php $i++;$lien=site_url(); endwhile; ?>

		<?php nice_pagenavi(); ?>

<?php else : ?>

	<?php _e ( 'Désolé, nous n\'avons pas trouver ce que vous recherchiez.', 'nicethemes' ); ?>

<?php endif; ?>

<!-- END #content -->
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
<?php endif;
