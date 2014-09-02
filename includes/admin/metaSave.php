<?php

function metaSave( $post_id ) {
	if( 'style-guides' == get_post_type( $post_id ) && !empty( $_POST ) ):
		
		// MAIN LOGO
		if( !empty( $_POST['_logo_main'] ) )
			update_post_meta( $post_id, '_logo_main', $_POST['_logo_main'] );
		
		// LOGOS
		if( !empty( $_POST['_logos'] ) )
			$logosMetas = $_POST['_logos'];
			foreach( $logosMetas as $logo ) {
				if( !empty( $logo ) ) {
					$logosMeta[] = $logo;
				}
			}
			if( isset( $logosMeta ) ) update_post_meta( $post_id, '_logos', $logosMeta );
		
		// COLORS
		if( !empty( $_POST['_colors'] ) && isset( $_POST['_colors'] ) ) {
			$colorPost = $_POST['_colors'];
			$colorCMYK = array();
			$colorRGB = array();
			
			$i = 0;
			//var_dump( $colorPost ); die();
			
			if( !empty( $_POST['_colors']['colorHex'] ) ) {
				foreach( $_POST['_colors']['colorHex'] as $color ) {
					//var_dump( $colorPost['colorTitle'][$i] );
					// HEX
					if( isset( $colorPost['colorHex'][$i] ) ) $colorHex = $colorPost['colorHex'][$i];
					
					// CMYK
					if( isset( $colorPost['colorC'][$i] ) ) 
						$colorCMYK['c'] = $colorPost['colorC'][$i];
					if( isset( $colorPost['colorM'][$i] ) ) 
						$colorCMYK['m'] = $colorPost['colorM'][$i];
					if( isset( $colorPost['colorY'][$i] ) ) 
						$colorCMYK['y'] = $colorPost['colorY'][$i];
					if( isset( $colorPost['colorK'][$i] ) ) 
						$colorCMYK['k'] = $colorPost['colorK'][$i];
					
					// RGB
					if( isset( $colorPost['colorR'][$i] ) ) 
						$colorRGB['r'] = $colorPost['colorR'][$i];
					if( isset( $colorPost['colorG'][$i] ) )
						$colorRGB['g'] = $colorPost['colorG'][$i];
					if( isset( $colorPost['colorB'][$i] ) )
						$colorRGB['b'] = $colorPost['colorB'][$i];
					
					if( isset( $colorPost['colorTitle'][$i] ) )
						$colorTitle = $colorPost['colorTitle'][$i];	
					
					$colorMeta[] = array( 'colorHex' => $colorHex, 'colorCMYK' => $colorCMYK, 'colorRGB' => $colorRGB, 'colorTitle' => $colorTitle );
					
					$i++;
					
				}
				
				// UPDATE META
				//echo '<br/>'; var_dump( $colorMeta ); die();
				if( isset( $colorMeta ) && !empty( $colorMeta ) ) update_post_meta( $post_id, '_colors', $colorMeta );
			
			}
		} else { delete_post_meta( $post_id, '_colors' ); }
		// FONTS
		if( !empty( $_POST['_fonts'] ) )
			$fontsMeta = $_POST['_fonts'];
			
			$i = 0;
			
			//var_dump(  isset($fontsMeta['images'][3]) ); die();
			
			foreach( $fontsMeta['font'] as $font ) {
				if( !empty( $font ) ) {
					if( !strpos( $fontsMeta['images'][$i], '//' ) ) {							
						
						$fontWeight = '400';
						$fontSize = '';
						
						// GET WEIGHT
						if( isset( $fontsMeta['weight'] ) ):
							//var_dump( $fontsMeta['weight'] ); die();
							if( $fontsMeta['weight'][$i] == '-1' || !isset( $fontsMeta['weight'][$i] ) || $fontWeight == 'No Font Weight Available' ) { 
								$fontWeight = 'No Font Weight Available'; 
							} else {
								$fontWeight = $fontsMeta['weight'][$i];	
							}
						endif;
						
						// GET SIZE
						if( isset($fontsMeta['size'] ) ):
							$fontSize = $fontsMeta['size'][$i];
						endif;
			 			
			 			$fontMeta[] = array( 'type' => 'font', 'value' => $font, 'weight' => $fontWeight, 'size' => $fontSize  );
			 		} else {
			 			$fontMeta[] = array( 'type' => 'image', 'value' => $fontsMeta['images'][$i] );
			 		}
				}
				$i++;
			}
			//var_dump( $fontMeta ); die();
			if( isset( $fontMeta ) ) update_post_meta( $post_id, '_fonts', $fontMeta );
		
		// INFLUENCES
		if( !empty( $_POST['_influences'] ) )
			$infMetas = $_POST['_influences'];
			foreach( $infMetas as $inf ) {
				if( !empty( $inf ) ) {
					$infMeta[] = $inf;
				}
			}
			if( isset( $infMeta ) ) update_post_meta( $post_id, '_influences', $infMeta );
	endif;
}
?>