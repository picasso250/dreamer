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
		var old = $btn.text();
		$btn.text('...');
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (false === callback(ret)) {
				return
			}
			$btn.text(old);
			postForm.find('.form-msg').text(ret.msg);
		}, 'json');
	});
}
