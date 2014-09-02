var $ = jQuery;

$(document).ready(function(){
	$('.downloadLogos').on('click', function(e){
		e.preventDefault();
		var data = {
			'action': 'download_logos',
			'id': $(this).data('id')
		}
		$.post(sg60_ajax.url, data, function(res){
			console.log(res);
		});
		
	});
	if($('.openModal').length > 0){
		$('.openModal').on('click', function(e){
			e.preventDefault();
			var src = $(this).data('img-url');
			$('div#sg60Modal img').attr('src', src);
			setTimeout(function(){
				$('#sg60Modal').modal('show');	
			}, 100);
		});
		
		$('#sg60Modal').on('shown.bs.modal', function() {
			var ht = $(window).outerHeight();
			ht = ht - $('#sg60Modal .modal-dialog').outerHeight();
			$('#sg60Modal').children('.modal-dialog').css({
				'margin-top': ht / 2
			});
		});
	}
});