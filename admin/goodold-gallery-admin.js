var $j = jQuery.noConflict();

(function ($) {
	var getValues, value = "";

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

		$('#goodold-gallery-shortcode code').text('[goodold-gallery' + value + ']')
		value = "";
	};

	$('input[type="checkbox"], select').each(function () {
		var $this = $(this);
		$this.change(getValues);
	});

	$('input[type="text"]').each(function () {
		var $this = $(this);
		$this.keyup(getValues);
	});
}($j));