$(function () {
	var alert = $('.alert');
	var postForm = $('form.ajax').on('submit', function (e) {
		e.preventDefault();
		var $this = $(this);
		var $btn = $(this).find('button[type=submit]');
		var old = $btn.text();
		$btn.text('...');
		console.log(old);
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (ret.code === 0) {
				if (ret.data && ret.data.url) {
					location.href = ret.data.url;
					return;
				}
			}
			$btn.text(old);
			alert.removeClass('alert-hidden').text(ret.message);
		}, 'json');
	});
});
