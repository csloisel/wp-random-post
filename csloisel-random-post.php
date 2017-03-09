<?php
/**
 * Plugin Name: CSLoisel Random Post
 */

class CSLoisel_Random_Post {

	public static function init() {
		if ( self::is_random_post_page() ) {
			add_action( 'pre_get_posts', array( __CLASS__, 'query_random_post' ) );
		}
	}

	public static function is_random_post_page() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$request_uri = str_replace( '/', '', $request_uri );
		return 'random' === $request_uri;
	}

	public static function query_random_post( $query ) {
		if ( $query->is_main_query() ) {
			$query->set( 'orderby', 'rand' );
			$query->set( 'post_type', 'post' );
			$query->set( 'posts_per_page', 1 );
		}
	}

}

add_action( 'init', array( 'CSLoisel_Random_Post', 'init' ) );