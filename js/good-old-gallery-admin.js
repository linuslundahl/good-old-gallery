var $j = jQuery.noConflict();

(function ($) {
	var $this, showHide, getValues, value = "", link, confirm = 1;

	// Show/Hide div
	showHide = function (div, title, alt, id) {
		div.hide();
		link = $('<a />').attr('href', '#').addClass(id).text(title).insertBefore(div);
		link.click(function (e) {
			div.slideToggle().toggleClass('open');
			if (div.hasClass('open')) {
				link.text(alt);
			} else {
				link.text(title);
			}
			e.preventDefault();
		});
	};

	// Shortcode generator
	getValues = function () {
		$('input[type="text"], select').each(function () {
			var $this = $(this);
			if ($this.val().length) {
				value += ' ' + $this.attr('id') + '="' + $this.val() + '"';
			}
		});

		$('input[type="checkbox"]').each(function () {
			var $this = $(this);
			if ($this.is(':checked')) {
				value += ' ' + $this.attr('id') + '="' + $this.val() + '"';
			}
		});

		$('#good-old-gallery-shortcode code').text('[good-old-gallery' + value + ']');
		value = "";
	};

	$('#go-gallery-generator input[type="checkbox"], #go-gallery-generator select').each(function () {
		$this = $(this);
		$this.change(getValues);
	});

	$('#go-gallery-generator input[type="text"]').each(function () {
		$this = $(this);
		$this.keyup(getValues);
	});

	// Settings page
	showHide($('.goodoldgallery_page_gog_themes .themes-available'), 'View installed themes', 'Hide installed themes', 'themes-link');
	$('#order').sortable({
		update : function (event, ui) {
			var order = $(this).sortable('toArray');
			$.each(order, function(index) {
				console.log($('#' + order[index]));
				$('#order_' + order[index]).val(index+1);
			});
			console.log(order);
		}
	}).next('table').hide();
}($j));

// Add Flattr button
(function() {
	var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
	s.type = 'text/javascript';
	s.async = true;
	s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
	t.parentNode.insertBefore(s, t);
})();