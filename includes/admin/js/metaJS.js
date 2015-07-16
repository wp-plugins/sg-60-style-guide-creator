var $ = jQuery;

function fontTemp( count ) {
	var tmpl = '<span><select name="_fonts[font][]" class="fontDrop">' + $($('select.fontDrop')[0]).html() + '</select>';
	tmpl += '<select name="_fonts[weight][]" class="fontWeights hidden"><option value="-1">Select Font Weight</option></select>';
	tmpl += '<input type="number" name="_fonts[size][]" placeholder="Font size in pixels (i.e 12 for 12px)" class="fontSize" />';
	tmpl += '<span class="or">OR</span>';
	tmpl += '<input type="text" name="_fonts[images][]" id="_font_' + count + '_image" /><button class="button media" name="_font_' + count + '_image_button" id="_font_' + count + '_image_button">Upload Image</button>';
	tmpl += '<a href="#" class="removeFont">x</a> </span>';
	return tmpl
}

function colorTemp() {
	var clrtmp = '<div><input type="text" class="colorTitle" name="_colors[colorTitle][]" placeholder="Color Title" />';
	clrtmp += '<span class="colorCMYK">';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Cyan (C)" name="_colors[colorC][]" />';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Magenta (M)" name="_colors[colorM][]" />';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Yellow (Y)" name="_colors[colorY][]" />';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Key (K)" name="_colors[colorK][]" />';
	clrtmp += '</span>';
	clrtmp += '<span class="colorRGB">';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Red (R)" name="_colors[colorR][]" />';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Green (G)" name="_colors[colorG][]" />';
		clrtmp += '<input type="text" class="colorRGB" placeholder="Blue (B)" name="_colors[colorB][]" />';
	clrtmp += '</span>';
	clrtmp += '<input type="text" class="color" placeholder="Hex Value" name="_colors[colorHex][]" />';
	clrtmp += '<a href="#" class="removeColor">x</a>';
	clrtmp += '</div>';
	
	return clrtmp;
}

$(document).ready(function($){

	var colorCount = 1,
	influenceCount = 1,
	logoCount = 1,
	fontCount = 1,
	_custom_media = true,
	_orig_send_attachment = wp.media.editor.send.attachment;

	// COLOR ADD & REMOVE
	$('.addColor').on('click', function(e){
		e.preventDefault();
		$('div.colors').append(colorTemp());
	});

	$('body').on('click', 'a.removeFont', function(e){
		e.preventDefault();
		$(this).parent('span').remove();
	});
	
	$('a.removeColor').on('click', function(e){
		e.preventDefault();
		$(this).parent('div').remove();
	})

	// FONTS


	// MEDIA UPLOADER
	$('body').on('click', '.uploader .button.media', function(e) {
		if($(this).attr('class'))
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		var id = button.attr('id').replace('_button', '');
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				$("#"+id).val(attachment.url);
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}

		wp.media.editor.open(button);
		return false;
	});

	$('.add_media').on('click', function(){
		_custom_media = false;
	});	

	// INFLUENCE, LOGO AND FONT ADD
	$('.addInfluence').on('click', function(e){
		e.preventDefault();
		influenceCount = influenceCount + 1;
		$('.uploader.influences').append('<span><input type="text" name="_influences[]" id="_influence_' + influenceCount + '" /><button class="button media" name="_influence_' + influenceCount + '_button" id="_influence_' + influenceCount + '_button">Upload Image</button></span>');	
	});

	$('.addLogo').on('click', function(e){
		e.preventDefault();
		logoCount = logoCount + 1;
		$('.logos').append('<span><input type="text" name="_logos[]" id="_logo_' + logoCount + '" /><button class="button media" name="_logo_' + logoCount + '_button" id="_logo_' + logoCount + '_button">Upload Image</button></span>');
	});

	$('.addFont').on('click', function(e){
		e.preventDefault();
		fontCount = fontCount + 1;

		$('.uploader.fonts').append(fontTemp(fontCount));	
	});

	$('body').on('change', 'select.fontDrop', function(){
		var par = $(this),
		val = par.val();

		if( par.siblings('input.fontWeight').length > 0 ) { 
			par.siblings('input.fontWeight').remove();  
			$('<select name="_fonts[weight][]" class="fontWeights hidden"></select>').insertAfter(par);
		}

		par.siblings('select.fontWeights').html('<option disabled>loading</option>');

		$.ajax({
			type: 'POST',
			url: sg60_ajax.url,
			data: { action: 'get_font_weights', font: val }
		}).done(function(res){
			res = JSON.parse(res);

			if(res.weights) {
				par.siblings('select.fontWeights').html('<option value="-1">Select Font Weight</option>');
				$.each( res.weights, function( key, value ){
					par.siblings('select.fontWeights').append('<option value="' + value + '">' + value + '</option>').removeClass('hidden');
				})
			} else {
				par.siblings('select.fontWeights').html('<option  value="-1" selected="selected">No Weights Available</option>').removeClass('hidden');
			}
		})
	})

});