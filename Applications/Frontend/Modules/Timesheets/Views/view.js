$(document).ready(function () {
	$('#returnoverview').click(function () {
		document.location.href = $(this).data('return');
		return false;
	});

	$('.pop').popup();
	$('.comment').popup();
});
