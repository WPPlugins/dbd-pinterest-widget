  	(function($) {
  		$(document).ready(function() {
  			var containerWidth = $('a.pin-img').width();
  			var containerHeight = $('a.pin-img').height();
  			$('a.pin-img').find('img').each(function(i, img) {
  				var imgXDiff = (containerWidth - $(img).width()) / 2;
  				var imgYDiff = (containerHeight - $(img).height()) / 2;
  				$(img).css({
  					'margin-left': imgXDiff + 'px',
  					'margin-top': imgYDiff + 'px'
  				});
  			});
  		});
  	})(jQuery);