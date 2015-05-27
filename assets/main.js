$(function () {
	$.fn.extend({button: function(state) {
		var $button = $(this);
		if (state === 'loading') {
			$button.attr('disable', true);
			var old = $button.text('loading');
			$button.data('old', old)
		}
		if (state === 'reset') {
			$button.attr('disable', true);
			$button.text($button.data('old'));
		}
	}})
	var alert = $('.alert');
	var postForm = $('form[method="post"][role=post]').on('submit', function (e) {
		e.preventDefault();
		var $this = $(this);
		var $btn = $(this).find('button[type=submit]');
		$btn.button('loading');
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (ret.code === 0) {
				if (ret.data && ret.data.url) {
					location.href = ret.data.url;
					return;
				}
			}
			$btn.button('reset');
			alert.removeClass('alert-hidden').text(ret.message);
		}, 'json');
	});
});
