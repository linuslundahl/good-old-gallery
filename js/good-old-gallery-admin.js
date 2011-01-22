var $j = jQuery.noConflict();

(function ($) {
	var showHide, getValues, value = "", link, confirm = 1;

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
		var $this = $(this);
		$this.change(getValues);
	});

	$('#go-gallery-generator input[type="text"]').each(function () {
		var $this = $(this);
		$this.keyup(getValues);
	});

	// Settings page
	showHide($('.goodoldgallery_page_goodoldgallery .available-themes'), 'View installed themes', 'Hide installed themes', 'themes-link');

  // Restore settings confirm
  $('input[name="reset"]').click(function (e) {
    var $this = $(this);
    if (confirm === 1) {
      confirm++;
      $this.val('Are you sure?').addClass('confirm');
      e.preventDefault();
    }
  });
}($j));