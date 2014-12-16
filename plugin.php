<?php
/**
 * Plugin Name: SG-60 - Style Guide Creator
 * Plugin URI: http://arcctrl.com/plugins/sg-60
 * Description: This plugin will allow you to easily create style guide for your clients
 * Version: 1.5
 * Author: ARC(CTRL)
 * Author URI: http://www.arcctrl.com
 * License: GPL2
 */

define( 'SG60_PLUGINPATH', plugin_dir_path( __FILE__ ) );
define( 'SG60_PLUGINURL', plugins_url( '', __FILE__ ) );

require('includes/admin/settings.php');
require('includes/admin/meta.php');
require('includes/admin/shortcodes.php');

class StyleGuideCreator {
	
	function __construct(){
		global $wpdb;
		add_action( 'init', array( $this, 'styleGuideCPT' ) );

		add_action('admin_init', array( $this, 'seoCheck' ) );

		new styleAdmin();
		new styleGuideShortcodes();
		
		// SINGLE 
		add_filter( 'the_content', array( $this, 'singleTemplate' ), 20 );
	}
		
	function seoCheck(){
		if( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ){
			add_action( 'add_meta_boxes', array( $this, 'remove_wp_seo_box' ), 100000 );
		}
	}

	function remove_wp_seo_box() {
		remove_meta_box( 'wpseo_meta', 'style-guides', 'normal' );
	}

	function styleGuideCPT() {
		$labels = array(
			'name'               => _x( 'Style Guides', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name'      => _x( 'Style Guide', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name'          => _x( 'Style Guides', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar'     => _x( 'Style Guide', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new'            => _x( 'New Style Guide', 'style guide', 'your-plugin-textdomain' ),
			'add_new_item'       => __( 'New Style Guide', 'your-plugin-textdomain' ),
			'new_item'           => __( 'New Style Guide', 'your-plugin-textdomain' ),
			'edit_item'          => __( 'Edit Style Guide', 'your-plugin-textdomain' ),
			'view_item'          => __( 'View Style Guide', 'your-plugin-textdomain' ),
			'all_items'          => __( 'All Style Guides', 'your-plugin-textdomain' ),
			'search_items'       => __( 'Search Style Guides', 'your-plugin-textdomain' ),
			'parent_item_colon'  => __( 'Parent Style Guides:', 'your-plugin-textdomain' ),
			'not_found'          => __( 'No style guides found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No style guides found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'style-guides' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);

		register_post_type( 'style-guides', $args );
		new styleGuideMeta();
	}
	
	function singleTemplate( $content ) {
		if( 'style-guides' === get_post_type() ) {
			global $post;
			$content = do_shortcode( '[sg-60 id="'.$post->ID.'"]' );
		}
		
		return $content;
	}
}

new StyleGuideCreator();

function get_attach_id( $url ) {
	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
 
	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}
	
	global $wpdb;
 
	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
	return $attachment[0];
}

?>