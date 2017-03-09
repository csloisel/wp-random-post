<?php
/**
 * Plugin Name: CSLoisel Random Post
 */

class CSLoisel_Random_Post {

	const COOKIE_KEY = 'CSLSeenRandomPosts';

	public static function init() {
		if ( self::is_random_post_page() ) {
			add_action( 'pre_get_posts', array( __CLASS__, 'query_random_post' ) );
			add_action( 'wp', array( __CLASS__, 'store_seen_post' ) );
		}
	}

	public static function is_random_post_page() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$request_uri = str_replace( '/', '', $request_uri );
		return 'random' === $request_uri;
	}

	public static function query_random_post( $query ) {
		if ( $query->is_main_query() ) {
			$query->is_single = true;
			$query->is_singular = true;
			$query->is_home = false;
			$query->set( 'orderby', 'rand' );
			$query->set( 'post_type', 'post' );
			$query->set( 'posts_per_page', 1 );
			$query->set( 'exclude', self::get_seen_posts() );
		}
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