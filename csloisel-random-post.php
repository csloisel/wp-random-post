<?php
/**
 * Plugin Name: CSLoisel Random Post
 */

class CSLoisel_Random_Post {

	const COOKIE_KEY = 'CSLSeenRandomPosts';

	public static function init() {
		add_filter( 'theme_page_templates', function( $templates ) {
			$templates['random-post.php'] = 'Random Post';
			return $templates;
		} );

		add_filter( 'wp', function( $template ) {
			$template_slug = get_page_template_slug();
			if ( 'random-post.php' === $template_slug ) {
				self::setup_random_post();
			}
		} );
	}

	public static function get_random_post() {
		$args = array(
			'orderby' => 'rand',
			'post_type' => 'post',
			'posts_per_page' => 1,
			'post__not_in' => self::get_seen_posts(),
			'post_status' => 'publish'
		);
		$query = new WP_Query( $args );
		return $query->posts[0];
	}

	public static function setup_random_post() {
		$random_post = self::get_random_post();
		$args = array(
			'p' => $random_post->ID
		);
		query_posts( $args );
		$GLOBALS['post'] = $random_post;
	}

	public static function get_seen_posts() {
		$seen_ids = !empty( $_COOKIE[ self::COOKIE_KEY ] ) ? json_decode( $_COOKIE[ self::COOKIE_KEY ] ) : array();
		$seen_ids = array_map( 'intval' , $seen_ids );
		$seen_ids = array_filter( $seen_ids );

		return $seen_ids;
	}

	public static function store_seen_post( $wp ) {
		$post_id = get_queried_object_id();
		$seen_ids = self::get_seen_posts();

		$seen_ids[] = $post_id;

		setcookie( self::COOKIE_KEY, json_encode( $seen_ids ), time() + MONTH_IN_SECONDS );
	}

}

add_action( 'init', array( 'CSLoisel_Random_Post', 'init' ) );