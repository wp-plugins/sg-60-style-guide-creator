<?php

include( 'googleFonts.php' );
include( 'metaSave.php');

class styleGuideMeta {
	function __construct(){
		add_action( 'add_meta_boxes', array( $this, 'metaInit' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'metaScripts' ) );
		add_action( 'save_post', 'metaSave' );
		add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
		
		add_filter( 'get_sample_permalink_html', array( $this, 'remove_permalink' ) );
		
		add_filter( 'pre_get_shortlink', function( $false, $post_id ) {
			return 'style-guides' === get_post_type( $post_id ) ? '' : $false;
		}, 10, 2 );
		
		add_action( 'admin_head-post-new.php', array( $this, 'posttype_admin_css' ) );
		add_action( 'admin_head-post.php', array( $this, 'posttype_admin_css' ) );

		add_action('wp_ajax_get_font_weights', array( $this, 'getFontWeights' ) );
	}

	function metaScripts() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_style( 'wp-color-picker' );
	    wp_enqueue_media();

		wp_enqueue_script( 'metaJS', plugin_dir_url( __FILE__ ).'js/metaJS.js', array( 'jquery' ), '1.0', false );
		wp_localize_script( 'metaJS', 'sg60_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'metaCSS', plugin_dir_url( __FILE__ ).'css/metaCSS.css', null, '1.0', 'all' );

	}

	function metaInit() {
		add_meta_box( 'styleguidelogos', __( 'Logos', 'myplugin_textdomain' ), array( $this, 'styleGuideLogos' ), 'style-guides', 'normal', 'high' );
		add_meta_box( 'styleguidecolors', __( 'Colors', 'myplugin_textdomain' ), array( $this, 'styleGuideColors' ), 'style-guides', 'normal', 'low' );
		add_meta_box( 'styleguidefonts', __( 'Fonts', 'myplugin_textdomain' ), array( $this, 'styleGuideFonts' ), 'style-guides', 'normal', 'high' );
		add_meta_box( 'styleguideinfluences', __( 'Influences', 'myplugin_textdomain' ), array( $this, 'styleGuideInfluences' ), 'style-guides', 'normal', 'low' );
		add_meta_box( 'styleguidemeta', __( 'Meta Data', 'myplugin_textdomain' ), array( $this, 'styleGuideMeta' ), 'style-guides', 'normal', 'low' );
	}

	function styleGuideColors( $post ) {
		wp_nonce_field( 'myplugin_meta_box', 'guide_nonce' );
		echo '<p><em>Color box in guide will be based on the hex value</em></p>';
		echo '<div class="colors">';
			if( !get_post_meta( $post->ID, '_colors', true ) ) { 
				echo '<div>';
					echo '<input type="text" class="colorTitle" name="_colors[colorTitle][]" placeholder="Color Title" />';
					echo '<span class="colorCMYK">';
						echo '<input type="text" class="colorRGB" placeholder="Cyan (C)" name="_colors[colorC][]" />';
						echo '<input type="text" class="colorRGB" placeholder="Magenta (M)" name="_colors[colorM][]" />';
						echo '<input type="text" class="colorRGB" placeholder="Yellow (Y)" name="_colors[colorY][]" />';
						echo '<input type="text" class="colorRGB" placeholder="Key (K)" name="_colors[colorK][]" />';
					echo '</span>';
					echo '<span class="colorRGB">';
						echo '<input type="text" class="colorRGB" placeholder="Red (R)" name="_colors[colorR][]" />';
						echo '<input type="text" class="colorRGB" placeholder="Green (G)" name="_colors[colorG][]" />';
						echo '<input type="text" class="colorRGB" placeholder="Blue (B)" name="_colors[colorB][]" />';
					echo '</span>';
					echo '<input type="text" class="color" placeholder="Hex Value" name="_colors[colorHex][]" />';
				echo '</div>';
			} else {
				$colorCount = 100;
				foreach( get_post_meta( $post->ID, '_colors', true ) as $color ) {
					if( !empty( $color ) ):
					
						echo '<div>';
						
						echo '<input type="text" class="colorTitle" name="_colors[colorTitle][]" placeholder="Color Title" value="';
							if( isset( $color['colorTitle'] ) ) echo $color['colorTitle'];
						echo '" />';
						
						echo '<span class="colorCMYK">';
							echo '<input type="text" class="colorRGB" placeholder="Cyan (C)" name="_colors[colorC][]" value="';
								if( isset( $color['colorCMYK']['c'] ) ) echo $color['colorCMYK']['c'];
							echo '" />';
							echo '<input type="text" class="colorRGB" placeholder="Magenta (M)" name="_colors[colorM][]" value="';
								if( isset( $color['colorCMYK']['m'] ) ) echo $color['colorCMYK']['m'];
							echo '" />';
							echo '<input type="text" class="colorRGB" placeholder="Yellow (Y)" name="_colors[colorY][]" value="';
								if( isset( $color['colorCMYK']['y'] ) ) echo $color['colorCMYK']['y'];
							echo '" />';
							echo '<input type="text" class="colorRGB" placeholder="Key (K)" name="_colors[colorK][]" value="';
								if( isset( $color['colorCMYK']['k'] ) ) echo $color['colorCMYK']['k'];
							echo '" />';
							
						echo '</span>';

						echo '<span class="colorRGB">';
							echo '<input type="text" class="colorRGB" placeholder="Red (R)" name="_colors[colorR][]" value="';
								if( isset ( $color['colorRGB']['r'] ) ) echo $color['colorRGB']['r'];
							echo '" />';
							echo '<input type="text" class="colorRGB" placeholder="Green (G)" name="_colors[colorG][]" value="';
								if( isset ( $color['colorRGB']['g'] ) ) echo $color['colorRGB']['g'];
							echo '" />';
							echo '<input type="text" class="colorRGB" placeholder="Blue (B)" name="_colors[colorB][]" value="';
								if( isset ( $color['colorRGB']['b'] ) ) echo $color['colorRGB']['b'];
							echo '" />';
						echo '</span>';
							echo '<input type="text" class="color" placeholder="Hex Value" value="'.$color['colorHex'].'" name="_colors[colorHex][]" />';
							echo '<a href="#" class="removeColor">x</a>';
						echo '</div>';
						$colorCount++;
					endif;
				}
			}
		echo '</div>';
		echo '<button class="button button-primary addColor">Add Color</button>';

	}

	function styleGuideLogos( $post ) {
		$logoMain = '';
		if( get_post_meta( $post->ID, '_logo_main', true ) ) { 
			$logoMain = get_post_meta( $post->ID, '_logo_main', true ); 
		}

		echo '<div class="uploader"><div class="logos">';
			echo '<h3>Main Logo</h3>';
			echo '<span><input type="text" name="_logo_main" id="_logo_main" value="'.$logoMain.'" />';
			echo '<button class="button media" name="_logo_main_button" id="_logo_main_button">Upload Image</button></span>';

			echo '<h3>Additional Logos</h3>';
			if( !get_post_meta( $post->ID, '_logos', true ) ) { 
				echo '<span><input type="text" name="_logos[]" id="_logo_1" />';
				echo '<button class="button media" name="_logo_1_button" id="_logo_1_button">Upload Image</button></span>';
			} else {
				$logoCount = 100;
				foreach( get_post_meta( $post->ID, '_logos', true ) as $logo ) {
					if( !empty( $logo ) ):
						echo '<span><input type="text" name="_logos[]" id="_logo_'.$logoCount.'" value="'.$logo.'" />';
						echo '<button class="button media" name="_logo_'.$logoCount.'_button" id="_logo_'.$logoCount.'_button">Upload Image</button></span>';
						$logoCount++;
					endif;
				}
			}
			echo '</div>';
			echo '<button class="button button-primary addLogo">Add Logo</button>'; 

		echo '</div>';
	}

	function styleGuideInfluences( $post ) {
		echo '<div class="uploader influences">';
			echo '<h3>Influence Imagery</h3>';
			if( !get_post_meta( $post->ID, '_influences', true ) ) {
				echo '<span><input type="text" name="_influences[]" id="_influence_1" />';
				echo '<button class="button media" name="_influence_1_button" id="_influence_1_button">Upload Image</button> </span>';
			} else {
				$counter = 100;
				foreach( get_post_meta( $post->ID, '_influences', true) as $influence ) {
					echo '<span><input type="text" name="_influences[]" id="_influence_'.$counter.'" value="'.$influence.'" />';
					echo '<button class="button media" name="_influence_'.$counter.'_button" id="_influence_'.$counter.'_button">Upload Image</button> </span>';	
					$counter++;
				}
			}
		echo '</div>';
		echo '<button class="button button-primary addInfluence">Add Influence</button>'; 
	}

	function styleGuideFonts( $post ) {
		echo '<div class="uploader fonts">';
		$allFonts = GoogleFonts();
			echo '<h3>Fonts</h3> <em>First 3 fonts will be wrapped in heading tags (h1, h2, h3) follows by paragraph tags (p)';
			if( !get_post_meta( $post->ID, '_fonts', true ) ) {
				echo '<span><select name="_fonts[font][]" class="fontDrop" >';
					echo '<option value="-1">Select One or Upload Image</option>';
					foreach( $allFonts as $font ) {
						echo '<option value="'.$font['name'].'">'.$font['name'].'</option>';
					}

				echo '</select>';
				echo '<select name="_fonts[weight][]" class="fontWeights hidden">'; 
					echo '<option value="-1">Select Font Weight</option>';
				echo '</select>';
				echo '<input type="number" name="_fonts[size][]" placeholder="Font size in pixels (i.e 12 for 12px)" class="fontSize" />';
				echo '<span class="or">OR</span>';
				echo '<input type="text" name="_fonts[images][]" id="_font_1_image" />';
				echo '<button class="button media" name="_font_1∑_image_button" id="_font_1_image_button">Upload Image</button> </span>';
			} else {
				$counter = 100;
				foreach( get_post_meta( $post->ID, '_fonts', true) as $font ) {
				
					echo '<span><select name="_fonts[font][]" class="fontDrop">';
						echo '<option value="-1" ';
							if( $font['type'] == 'image' ) echo 'selected="selected"';
						echo '>Select One or Upload Image</option>';
						
						foreach( $allFonts as $fontSel ) {
							echo '<option value="'.$fontSel['name'].'"';
								if( $font['type'] == 'font' && $font['value'] == $fontSel['name'] ) echo 'selected="selected"';
							echo '>'.$fontSel['name'].'</option>';
						}
					echo '<input type="text" readonly="readonly" name="_fonts[weight][]" value="'.$font['weight'].'" class="fontWeight" />';
					echo '<input type="number" name="_fonts[size][]" placeholder="Font size in pixels (i.e 12 for 12px)" class="fontSize" value="';
						if( isset( $font['size'] ) ) echo $font['size'];
					echo '" />';	
					echo '</select> <span class="or">OR</span>';
					
					echo '<input type="text" name="_fonts[images][]" id="_font_'.$counter.'_image" ';
						if( $font['type'] == 'image' ) echo 'value="'.$font['value'].'"';
					echo ' />';
					echo '<button class="button media" name="_font_'.$counter.'_image_button" id="_font_'.$counter.'_image_button">Upload Image</button>';
					echo '<a href="#" class="removeFont">x</a>';
					echo '</span>';
					$counter++;
				}
			}
		echo '</div>';
		echo '<button class="button button-primary addFont">Add Font</button>'; 
	}

	// SAVE POST DATA
	

	function styleGuideMeta ( $post ) {
		var_dump( get_post_meta( $post->ID, '_logo_main' ) );
		echo '<br/><br/>';
		var_dump( get_post_meta( $post->ID, '_logos' ) );
		echo '<br/><br/>';
		var_dump( get_post_meta( $post->ID, '_colors' ) );
		echo '<br/><br/>';
		var_dump( get_post_meta( $post->ID, '_fonts' ) );
		echo '<br/><br/>';
		var_dump( get_post_meta( $post->ID, '_influences' ) );
	}
	
	function remove_row_actions( $actions ) {
		if( get_post_type() === 'style-guides' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['trash'] );
			unset( $actions['view'] );
		}
		
		return $actions;
		
	}
	
	function remove_permalink( $return ) {
		if( get_post_type() === 'style-guides' ) {
			$return = '<a class="button button-small" style="float:right" href="'.admin_url( 'admin.php?page=style-guide-main' ).'">Get Shortcode</a>';
		}
		return $return;
	
	}
	
	function previewBut() {
		$return = '<a class="button button-small" style="float:right" href="'.admin_url( 'admin.php?page=style-guide-main' ).'">Get Shortcode</a>';
		return $return;
	}
	
	function posttype_admin_css() {
		global $post_type;
		if( 'style-guides' == $post_type )
			echo '<style type="text/css">#minor-publishing-actions,#misc-publishing-actions{display: none;}</style>';
	}

	function getFontWeights() {
		$font = $_POST['font'];
		$allFonts = GoogleFonts();
		foreach( $allFonts as $fonts ) {
			if( $fonts['name'] == $font ) {
				if( isset( $fonts['weights'] ) ) {
					$return['weights'] = $fonts['weights'];
				} else {
					$return['weights'] = false;
				}
				break;
			}
		}

		echo json_encode( $return );
		
		die();
	}
}

?>