var $j = jQuery.noConflict();

(function ($) {
	var $this, showHide, showHideSelect, getValues, value = "";

	// Show/Hide div
	showHide = function ($div, title, alt, id) {
		var link;

		$div.hide();
		link = $('<a />').attr('href', '#').addClass(id).text(title).insertBefore($div);
		link.click(function (e) {
			$div.slideToggle().toggleClass('open');
			if ($div.hasClass('open')) {
				link.text(alt);
			} else {
				link.text(title);
			}
			e.preventDefault();
		});
	};

	// Show/Hide info via dropdown
	showHideSelect = function ($select) {
		var val = $select.val();

		if (val.length) {
			$('.plugin-info.' + val).show();
		}

		$select.change(function () {
			val = $select.val();
			console.log(1, val);
			$('.plugin-info').slideUp();
			if (val.length) {
				$('.plugin-info.' + val).slideDown();
			}
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
			} else {
				value += ' ' + $this.attr('id') + '="' + $this.prev().val() + '"';
			}
		});

		$('#good-old-gallery-shortcode code').text('[good-old-gallery' + value + ']');
		value = "";
	};

	$(function () {
		var body = $('body');

		getValues();

		// Shortcode generator
		$('#go-gallery-generator input[type="checkbox"], #go-gallery-generator select').each(function () {
			$this = $(this);
			$this.change(getValues);
		});

		$('#go-gallery-generator input[type="text"]').each(function () {
			$this = $(this);
			$this.keyup(getValues);
		});

		// Themes page
		if (body.hasClass('goodoldgallery_page_gog_themes')) {
			showHide($('.goodoldgallery_page_gog_themes .themes-available'), 'View installed themes', 'Hide installed themes', 'themes-link');
		}

		// Settings page
		if (body.hasClass('goodoldgallery_page_gog_settings')) {
			showHideSelect($('select#plugin'));

			// Make fields sortable
			if ($.isFunction($.fn.sortable)) {
				$('#order').sortable({
					update : function (event, ui) {
						var order = $(this).sortable('toArray');
						$.each(order, function(index) {
							$('#order_' + order[index]).val(index+1);
						});
					}
				}).next('table').hide();
			}
		}
	});
}($j));

// Add Flattr button
(function() {
	var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
	s.type = 'text/javascript';
	s.async = true;
	s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
	t.parentNode.insertBefore(s, t);
})();