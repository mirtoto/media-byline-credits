(function( $ ) {
	'use strict';

	$(document).ready(function() {
		var choices = '';
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: '/wp-admin/admin-ajax.php',
			data: 'action=get_search_list',
			success: function(response) {
				choices = response;
			}
		});

		if (typeof wp != 'undefined' && wp.media) {

			// media library modal
			wp.media.view.Modal.prototype.on('open', function() {
				$('.compat-field-byline input').autoComplete({
					minChars: 1,
					menuClass: 'suggestions-modal',
					source: function(term, suggest){
						term = term.toLowerCase();
						var suggestions = [];
						for (var i=0;i<choices.length;i++) {
							if (~choices[i].toLowerCase().indexOf(term)) {
								suggestions.push(choices[i]);
							}
						}
						suggest(suggestions);
					}
				});
			});

		}

		// attachment page
		$('.compat-field-byline input').autoComplete({
			minChars: 1,
			source: function(term, suggest){
				term = term.toLowerCase();
				var suggestions = [];
				for (var i=0;i<choices.length;i++) {
					if (~choices[i].toLowerCase().indexOf(term)) {
						suggestions.push(choices[i]);
					}
				}
				suggest(suggestions);
			}
		});

	});

})( jQuery );
