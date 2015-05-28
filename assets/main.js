$.fn.extend({
	button: function(state) {
		var $button = $(this);
		if (state === 'loading') {
			$button.attr('disable', true);
			var old = $button.text('...');
			$button.data('old', old)
		}
		if (state === 'reset') {
			$button.attr('disable', true);
			$button.text($button.data('old'));
		}
	}
})
$(function () {
	ajax_form('form.ajax', function (ret) {
		if (ret.code === 0) {
			if (ret.data && ret.data.url) {
				location.href = ret.data.url;
				return;
			}
		}
	})
});
function ajax_form(form, callback)
{
	var postForm = $(form).on('submit', function (e) {
		e.preventDefault();
		var $this = $(this);
		var $btn = $(this).find('button[type=submit]');
		$btn.button('loading');
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (false === callback(ret)) {
				return
			}
			$btn.button('reset');
			postForm.find('.form-msg').text(ret.msg);
		}, 'json');
	});
}
