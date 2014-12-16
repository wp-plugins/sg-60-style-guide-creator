<?php
	// GET DATA
	global $wpdb;
	$post = $sg;
	$logoMain = get_post_meta( $post->ID, '_logo_main', true ); 
	$logos = get_post_meta( $post->ID, '_logos', true );
	$colors = get_post_meta( $post->ID, '_colors', true );
	$fonts = get_post_meta( $post->ID, '_fonts', true );
	$influences = get_post_meta( $post->ID, '_influences', true );
	$template = '<div class="sg60Container">';
	$template .= '<div id="sg60Modal" class="modal fade " aria-labelledby="modal" >';
		$template .= '<div class="modal-dialog modal-lg">';
			$template .= '<div class="modal-content"><div class="modal-body text-center">';
				$template .= '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>';
				$template .= '<img src="" class="img-responsive"/></div>';
			$template .= '</div></div>';
	$template .= '</div>';
	
	// MAIN LOGO
	if( $logoMain ):
		$template .= '<div class="row sg60_mainLogo"><div class="col-md-12 text-center">';
			$template .= '<img data-img-url="'.$logoMain.'" src="'.$logoMain.'" alt="'.$post->post_title.'" />';
		$template .= '</div></div>';
	endif;
	
	// OTHER LOGOS
	if( $logos ):
		$template .= '<div class="row sg60_otherLogos">';
		$i = 1;
		foreach( $logos as $logo ) {
			if( !empty( $logo ) ):
				$template .= '<div class="col-md-3 text-center">';
					$template .= '<img class="img-responsive" data-img-url="'.$logo.'" src="'.$logo.'" alt="'.$post->post_title.'" />';
				$template .= '</div>';
				if( $i%4 == 0 ) { $template .= '</div><div class="row sg60_otherLogos">'; }
				$i++;
			endif;
		}
		$template .= '</div>';
		//$template .= '<div class="row sg60_logoDownload"><div class="col-md-12 text-center"><a href="#" class="downloadLogos btn btn-primary" data-id="'.$post->ID.'">Download Logos</a></div></div>';
	endif;
	
	// COLORS
	if( $colors ):
		$template .= '<div class="row"><div class="col-md-12"><h2 class="text-center">COLORS</h2></div></div>';
		$template .= '<div class="row sg60_colors">';
		$i = 1;
			foreach( $colors as $color ){
				$template .= '';
				$template .='<div class="col-md-4"><div class="sg60_color">';
					$template .= '<span style="background:'.$color['colorHex'].'"></span>';
					if( isset( $color['colorTitle'] ) )						
					$template .= '<div class="row colorDefs"><div class="col-xs-12">';
						$template .= '<p><strong>'.$color['colorTitle'].'</strong></p>';
						$template .= '<p>HEX: '.$color['colorHex'].'</p>';
						$template .= '<p>CMYK: ';
							if( isset( $color['colorCMYK']['c'] ) ) $template .= $color['colorCMYK']['c'].' / ';
							if( isset( $color['colorCMYK']['m'] ) ) $template .= $color['colorCMYK']['m'].' / ';
							if( isset( $color['colorCMYK']['y'] ) ) $template .= $color['colorCMYK']['y'].' / ';
							if( isset( $color['colorCMYK']['k'] ) ) $template .= $color['colorCMYK']['k'];
						$template .= '</p>';
							
						$template .= '<p>RGB: ';
							if( isset( $color['colorRGB']['r'] ) ) $template .= $color['colorRGB']['r'].' / ';
							if( isset( $color['colorRGB']['g'] ) ) $template .= $color['colorRGB']['g'].' / ';
							if( isset( $color['colorRGB']['b'] ) ) $template .= $color['colorRGB']['b'];
						$template .= '</p>';
					$template .= '</div></div>';
						
				$template .= '</div></div>';
				if( $i%3 == 0 ) { $template .= '</div><div class="row sg60_colors">'; }
				$i++;
			}
		$template .= '</div>';
	endif;
	
	// FONTS
	if( $fonts ):
		$template .= '<div class="row sg60_fonts"><div class="col-md-12 sg60_fontWrapper">';
			$template .= '<h2 class="text-center">FONTS</h2>';
			$i = 1;
			foreach( $fonts as $font ) {
				$fontClass = str_replace( ' ', '', $font['value'] );
				$fontStyle = 'style="';
					if( isset( $font['weight'] ) && intVal( $font['weight'] ) > 90 ) { 
						if( strpos( $font['weight'], 'italic') ) {
							$weight = str_replace( 'italic', '', $font['weight'] );
							$fontStyle .= 'font-weight: '.$weight.'; font-style: italic;';
						} else {
							$fontStyle .= 'font-weight: '.$font['weight'].';';
						}
					}
					if( isset( $font['size'] ) && intVal( $font['size'] ) > 1 ) { $fontStyle .= 'font-size: '.$font['size'].'px;'; }
				$fontStyle .= '"';
				$template .= '<div class="fontWrapper">';
					$template .= '<span class="title">';
						if( $i <= 3 ) { $template.= 'h'.$i; } else { $template .= 'Body'; }
					$template .= '</span>';
					
					if( $i <= 3 ) { $template.= '<h'.$i.'>'; } else { $template .= '<p>'; }
						// FONT TEXT
						if( $font['type'] == 'font' ) { $template .= '<span class="'.$fontClass.'" '.$fontStyle.'>'.$font['value'].'</span>';; }
						// FONT IMAGE
						if( $font['type'] == 'image' ) { $template .= '<img alt="'.$post->post_title.'" src="'.$font['value'].'" /> '; }
					if( $i <= 3 ) { $template .= '</h'.$i.'>'; } else { $template .= '</p>'; }
				$template .= '</div>';
				$i++;
			}
		$template .= '</div></div>';
	endif;
	// INFLUENCES
	if( $influences ):
		$template .= '<div class="row sg60_influences">';
		$template .= '<div class="row"><div class="col-md-12"><h2 class="text-center">INFLUENCES</h2></div></div>';
		$i = 1;
		foreach( $influences as $influence ) {
			if( !empty( $influence ) ):
				$id = get_attach_id( $influence );
				$smImg = wp_get_attachment_image_src( $id, 'thumbnail' );
				$lgImg = wp_get_attachment_image_src( $id, 'full' );
			
				$template .= '<div class="col-md-3 col-xs-6 text-center">';
					$template .= '<img class="img-responsive openModal" data-img-url="'.$lgImg[0].'" src="'.$smImg[0].'" alt="'.$post->post_title.'" />';
				$template .= '</div>';
				if( $i%4 == 0 ) { $template .= '</div><div class="row sg60_influences">'; }
				$i++;
			endif;
		}
		$template .= '</div>';
	endif;
	$template .= '</div> <!-- end SG-60 Container -->';
?>