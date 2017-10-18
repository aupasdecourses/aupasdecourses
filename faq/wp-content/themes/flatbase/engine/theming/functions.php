<?php
/**
 * Table of Contents (functions.php)
 *
 *	- nice_logo()
 *	- nice_copyright()
 *	- nice_breadcrumbs()
 */

/**
 * nice_logo()
 *
 * Function to create the logo.
 *
 * @since 1.1.2
 *
 */

if ( ! function_exists( 'nice_logo' ) ) :

	function nice_logo( $args = array() ){

		$defaults = apply_filters( 'nice_logo_default_args', array(
								'echo'			=> true,
								'link'			=> "https://staging.aupasdecourses.com/",
								'alt'			=> get_bloginfo( 'name' ),
								'title'			=> get_bloginfo( 'name' ),
								'text_title'	=> false,
								'logo'			=> '',
								'logo_retina'	=> '',
								'width'			=> '',
								'height'		=> '',
								'before'		=> '',
								'after'			=> '',
								'before_title'	=> '<h1>',
								'after_title'	=> '</h1>')
				);


		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'nice_logo_args', $args );

		do_action( 'nice_logo_before', $args );

		$output = '';

		$output .= $args['before'];

		$output .= $args['before_title'];

		if ( apply_filters( 'nice_logo_enable_link', true ) ){
			$output .= '<a href="' . $args['link'] . '" title="' . $args['title'] . '">';
		}

		if ( $args['text_title'] ) :

			$output .= '<span class="text-logo">' . $args['title'] . '</span>';

		elseif ( $args['logo'] <> '' ) :

			$output .= '<img id="default-logo" src="' . $args['logo'] . '" alt="' . $args['alt'] . '" />';

			if ( $args['logo_retina'] <> '' ){
				$output .= '<img id="retina-logo" src="' . $args['logo_retina'] . '" alt="' . $args['alt'] . '" />';
			} else {
				$output .= '<img id="retina-logo" src="' . $args['logo'] . '" alt="' . $args['alt'] . '" />';
			}

		else:
			$output .= '<img id="default-logo" src="' . get_stylesheet_directory_uri() . '/images/logo.png" alt="' . $args['alt'] . '" />';
			$output .= '<img id="retina-logo" src="' . get_stylesheet_directory_uri() . '/images/logo@2x.png" alt="' . $args['alt'] . '" />';
		endif;

		if ( apply_filters( 'nice_logo_enable_link', true ) ){
			$output .=	'</a>';
		}

		$output .= $args['after_title'];

		$output .= $args['after'];

		$output = apply_filters( 'nice_logo_html', $output, $args );

		if ( $args['echo'] == true ) echo $output;
		else return $output;

		do_action( 'nice_logo_after', $args );

	}

endif;

/**
 * nice_copyright()
 *
 * The copyright function.
 *
 * @since 1.1.2
 *
 */

if ( ! function_exists( 'nice_copyright' ) ) :

	function nice_copyright( $args = array() ){

		$defaults = apply_filters( 'nice_copyright_default_args', array(
								'echo'			=> true,
								'before'		=> '<p>',
								'after'			=> '</p>',
								'text'			=> '')
				);

		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'nice_copyright_args', $args );

		do_action( 'nice_copyright_before', $args );

		$output = $args['before'] . $args['text'] . $args['after'];

		$output = apply_filters( 'nice_copyright_html', $output, $args );

		if ( $args['echo'] == true ) echo $output;
		else return $output;

		do_action( 'nice_copyright_after', $args );

	}

endif;

/**
 * nice_breadcrumbs()
 *
 * Breadcrumbs, nicely displayed.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_breadcrumbs' ) ) :

	function nice_breadcrumbs( $args = array() ) {

		global $wp_rewrite;

		$defaults = apply_filters( 'nice_breadcrumbs_default_args', array(
							'separator' 	=> '/',
							'before' 		=> '',
							'after' 		=> false,
							'front_page' 	=> true,
							'show_home' 	=> __( 'Home', 'nicethemes' ),
							'echo' 			=> true
						)
					);

		if ( is_singular() ) {
			$post = get_queried_object();
			$defaults["singular_{$post->post_type}_taxonomy"] = false;
		}

		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'nice_breadcrumbs_args', $args );

		do_action( 'nice_breadcrumbs_before', $args );

		$trail = array();
		$path = '';

		if ( ! is_front_page() && $args['show_home'] )
			$trail[] = '<a href="' . home_url() . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $args['show_home'] . '</a>';

		if ( is_singular() ) {

			$post = get_queried_object();
			$post_id = absint( get_queried_object_id() );
			$post_type = $post->post_type;
			$parent = absint( $post->post_parent );

			$post_type_object = get_post_type_object( $post_type );

			if ( $post_type == 'post'  ) {

				/* If $front has been set, add it to the $path. */
				$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a path, check for parents. */
				if ( ! empty( $path ) )
					$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

				/* Map the permalink structure tags to actual links. */
				//$trail = array_merge( $trail, breadcrumb_trail_map_rewrite_tags( $post_id, get_option( 'permalink_structure' ), $args ) );
			}
			elseif ( 'page' !== $post_type ) {

				/* If $front has been set, add it to the $path. */
				if ( $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a slug, add it to the $path. */
				if ( !empty( $post_type_object->rewrite['slug'] ) )
					$path .= $post_type_object->rewrite['slug'];

				/* If there's a path, check for parents. */
				if ( !empty( $path ) )
					$trail = array_merge( $trail, breadcrumb_trail_get_parents( '', $path ) );

				/* If there's an archive page, add it to the trail. */
				if ( !empty( $post_type_object->has_archive ) )
					$trail[] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $post_type_object->labels->name ) . '">' . $post_type_object->labels->name . '</a>';
			}

			/* If the post type path returns nothing and there is a parent, get its parents. */
			if ( ( empty( $path ) && 0 !== $parent ) || ( 'attachment' == $post_type ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( $parent, '' ) );

			/* Or, if the post type is hierarchical and there's a parent, get its parents. */
			elseif ( 0 !== $parent && is_post_type_hierarchical( $post_type ) )
				$trail = array_merge( $trail, breadcrumb_trail_get_parents( $parent, '' ) );

			/* Display terms for specific post type taxonomy if requested. */
			if ( !empty( $args["singular_{$post_type}_taxonomy"] ) && $terms = get_the_term_list( $post_id, $args["singular_{$post_type}_taxonomy"], '', ', ', '' ) )
				$trail[] = $terms;

			/* End with the post title. */
			$post_title = get_the_title();
			if ( !empty( $post_title ) )
				$trail['trail_end'] = $post_title;

		}

		if ( ! empty( $trail ) && is_array( $trail ) ) {

			/* Open the breadcrumb trail containers. */
			$nice_breadcrumbs = '<div class="breadcrumb breadcrumbs nice-breadcrumb"><div class="breadcrumb-trail">';

			/* If $before was set, wrap it in a container. */
			$nice_breadcrumbs .= ( ! empty( $args['before'] ) ? '<span class="trail-before">' . $args['before'] . '</span> ' : '' );

			/* Wrap the $trail['trail_end'] value in a container. */
			if ( ! empty( $trail['trail_end'] ) )
				$trail['trail_end'] = '<span class="trail-end">' . $trail['trail_end'] . '</span>';

			/* Format the separator. */
			$separator = ( ! empty( $args['separator'] ) ? '<span class="sep">' . $args['separator'] . '</span>' : '<span class="sep">/</span>' );

			/* Join the individual trail items into a single string. */
			$nice_breadcrumbs .= join( " {$separator} ", $trail );

			/* If $after was set, wrap it in a container. */
			$nice_breadcrumbs .= ( !empty( $args['after'] ) ? ' <span class="trail-after">' . $args['after'] . '</span>' : '' );

			/* Close the breadcrumb trail containers. */
			$nice_breadcrumbs .= '</div></div>';
		}

		/* Allow developers to filter the breadcrumb trail HTML. */
		$nice_breadcrumbs = apply_filters( 'breadcrumb_trail', $nice_breadcrumbs, $args );

		/* Output the breadcrumb. */
		if ( $args['echo'] )
			echo $nice_breadcrumbs;
		else
			return $nice_breadcrumbs;

	}

endif;

/**
 * breadcrumb_trail_get_parents()
 *
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'breadcrumb_trail_get_parents' ) ) :

	function breadcrumb_trail_get_parents( $post_id = '', $path = '' ) {

		/* Set up an empty trail array. */
		$trail = array();

		/* Trim '/' off $path in case we just got a simple '/' instead of a real path. */
		$path = trim( $path, '/' );

		/* If neither a post ID nor path set, return an empty array. */
		if ( empty( $post_id ) && empty( $path ) )
			return $trail;

		/* If the post ID is empty, use the path to get the ID. */
		if ( empty( $post_id ) ) {

			/* Get parent post by the path. */
			$parent_page = get_page_by_path( $path );

			/* If a parent post is found, set the $post_id variable to it. */
			if ( ! empty( $parent_page ) )
				$post_id = $parent_page->ID;
		}

		/* If a post ID and path is set, search for a post by the given path. */
		if ( $post_id == 0 && !empty( $path ) ) {

			/* Separate post names into separate paths by '/'. */
			$path = trim( $path, '/' );
			preg_match_all( "/\/.*?\z/", $path, $matches );

			/* If matches are found for the path. */
			if ( isset( $matches ) ) {

				/* Reverse the array of matches to search for posts in the proper order. */
				$matches = array_reverse( $matches );

				/* Loop through each of the path matches. */
				foreach ( $matches as $match ) {

					/* If a match is found. */
					if ( isset( $match[0] ) ) {

						/* Get the parent post by the given path. */
						$path = str_replace( $match[0], '', $path );
						$parent_page = get_page_by_path( trim( $path, '/' ) );

						/* If a parent post is found, set the $post_id and break out of the loop. */
						if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
							$post_id = $parent_page->ID;
							break;
						}
					}
				}
			}
		}

		/* While there's a post ID, add the post link to the $parents array. */
		while ( $post_id ) {

			/* Get the post by ID. */
			$page = get_page( $post_id );

			/* Add the formatted post link to the array of parents. */
			$parents[]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . get_the_title( $post_id ) . '</a>';

			/* Set the parent post's parent to the post ID. */
			$post_id = $page->post_parent;
		}

		/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
		if ( isset( $parents ) )
			$trail = array_reverse( $parents );

		/* Return the trail of parent posts. */
		return $trail;
	}

endif;
