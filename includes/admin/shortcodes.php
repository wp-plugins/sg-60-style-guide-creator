<?php

include( PLUGINPATH.'includes/frontend/ajax.php' );
include( 'googleFonts.php' );

class styleGuideShortcodes {
	
	function __construct() {
		add_shortcode( 'sg-60', array( $this, 'sgShortcode' ) );
	}
	
	function sgShortcode( $atts ) {

		$a = shortcode_atts( array(
			'id' => ''
		), $atts );
		
		if( empty( $a['id'] ) ) return '<p>Please define a SG-60 Style Guide ID in your shortcode <code>[sg60 id="..."]</code></p>';
		
		$sg = get_post( intval( $a['id'] ) );
		
		if( $sg && $sg->post_type == 'style-guides' ) :

			// enqueue
			wp_enqueue_style( 'sg60Styles', PLUGINURL.'/includes/frontend/css/sg60styles.css', null, '0.1', 'all' );
			wp_enqueue_script( 'sg60FEscript', PLUGINURL.'/includes/frontend/js/sg60_fedScripts.js', array('jquery'), '1.0', false );
			wp_enqueue_script( 'BSModal', PLUGINURL.'/includes/bootstrap/modal.js', array('jquery'), '1.0', false );
			

			$postFonts = get_post_meta( $sg->ID, '_fonts', true );
			if( !empty( $postFonts ) ) {
				$this->GoogleFontSetup( $sg->ID );
				
				$GoogleFonts = GoogleFonts();
				echo '<style>';
				foreach( $postFonts as $font ) {
					if( $font['type'] == 'font' ) {
						foreach( $GoogleFonts as $gfont ) {
							$fontClass = str_replace( ' ', '', $gfont['name'] );
							if( $font['value'] == $gfont['name'] ) {
								echo '.'.$fontClass.' { font-family: "'.$gfont['name'].'"; }';
							}
						}
					}
				}
				echo '</style>';
			}

			include(PLUGINPATH.'includes/frontend/sg60Template.php');
			
			return $template;
			
		else:
			
			return '<p>Cannot find a Style Guide with that ID</p>';
		
		endif;
		
	}


	function GoogleFontSetup( $post_id ) {

		$postFonts = get_post_meta( $post_id, '_fonts', true );
		$GoogleFonts = GoogleFonts();
		$string = '//fonts.googleapis.com/css?family=';

		foreach( $postFonts as $font ) {
			if( $font['type'] == 'font' ) {
				foreach( $GoogleFonts as $gfont ) {
					if( $font['value'] == $gfont['name'] ) {
						$string = $string.$gfont['script'].'|';
					}
				}
			}
		}

		wp_enqueue_style( 'gfonts', $string, null, '0.1', 'all' );

	}
}

?>