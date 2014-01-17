$(function() {
	resize_puff();
});

$(window).resize(function() {
	resize_puff();
});

function resize_puff() {
	$('.puffar').each(function () {
		$(this).height('auto');
		var max_height = 0;
		$(this).children().filter('.puff').each(function () {
			var h = $(this).height() + $(this).children().filter('.wrapper').children().filter('.link').height() + 10;
			max_height = (h > max_height ? h : max_height);
		});

		$(this).height(max_height);
	});
}
