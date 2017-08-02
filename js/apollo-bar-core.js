jQuery(document).ready(function($) {

	/*-----------------------------------------------------------------------------------*/
	/*	All Frontend Scripts
	/*-----------------------------------------------------------------------------------*/
	
	if($('#apollo-bar').length) {

		$('body').prepend($('#apollo-bar')); // Add Apollo Bar to body
		$('body').prepend('<div class="apb-pusher"></div>'); // Add Apollo Bar to body
	
		if($.cookie('apb_active') == 'false') {
			$('#apollo-bar, .apb-pusher').hide();
		};
		
		// Close Button
		$('#close').click(function() {
			$('#apollo-bar, .apb-pusher').slideUp('slow', function() {
				// After complate
			});
			$.cookie('apb_active', 'false', { expires: null, path: '/'});
			return false;
		});

		// Cycle Effect
		$('.apb-message div:first-child').show(); // Set first div to show

		if ($('.apb-message div').length > 1) {
			// Begin the loop, fade out, find next div, fade that div in, end the process and append back to main div.
			setInterval(function() {
				$('.apb-message div:first-child').fadeOut(300, function() {
					$(this).next('div').fadeIn(300).end().appendTo('.apb-message');
				});
			}, 10000);
		}

	}

});