$(document).ready(function () {
	$("#forgot_btn").click(function () {
		$("#forgot").css({
			display: "block"
		});
		$("#signup").css({
			display: "none"
		});
	});
	$("#return_btn").click(function () {
		$("#signup").css({
			display: "block"
		});
		$("#forgot").css({
			display: "none"
		});
	});
	var frmLogin = $('#login');
	var frmForgot = $('#forgot_form');
	var frmNewpass = $('#newpassword_form');
	frmLogin.submit(function () {
		return false;
	}).form({
		fields: {
			username: 'empty',
			password: 'empty'
		}
	});
	frmForgot.submit(function () {
		return false;
	}).form({
		fields: {
			username: 'empty'
		}
	});
	frmNewpass.submit(function () {
		return false;
	}).form({
		fields: {
			password: 'empty',
			password2: 'empty'
		}
	});

	$("#submit").click(function (e) {
		e.preventDefault();
		frmLogin.form('validate form');
		if (frmLogin.form('is valid') === true) {
			$.ajax({
				type: frmLogin.attr('method'),
				url: frmLogin.attr('action'),
				dataType: 'json',
				data: frmLogin.serialize(),
				error: function () {
					$.notify({
						title: 'Sign In',
						message: 'Please contact your administrator!',
						status: 'warning',
						timeout: 5
					});
				},
				success: function (data) {
					if (data.reponse == true) {
						document.location.href = data.url;
					} else if (data.reponse == 'change') {
						$("#newpassword").css({
							display: "block"
						});
						$("#signup").css({
							display: "none"
						});
						$("#id").val(data.user_id);
						$("#token_change").val(data.token);
					} else {
						$.notify({
							title: 'Sign In',
							message: data.reponse,
							status: data.status,
							timeout: 5,
							callback: function (e) {
								if (data.goLogout)
									document.location.href = "logout";
							}
						});
					}
				}
			});
		}
	});
	$("#submit_forgot").click(function (e) {
		e.preventDefault();
		frmForgot.form('validate form');
		if (frmForgot.form('is valid') === true) {
			$.ajax({
				type: frmForgot.attr('method'),
				url: frmForgot.attr('action'),
				dataType: 'json',
				data: frmForgot.serialize(),
				error: function () {
					$.notify({
						title: 'Sign In',
						message: 'Please contact your administrator!',
						status: 'warning',
						timeout: 5
					});
				},
				success: function (data) {
					var notify = $.notify({
						title: 'Sign In',
						message: data.reponse,
						status: data.status,
						timeout: 5,
						callback: function (e) {
							$("#return_btn").click();
						}
					});
				}
			});
		}
	});
	$("#submit_newpassword").click(function (e) {
		e.preventDefault();
		frmNewpass.form('validate form');
		if (frmNewpass.form('is valid') === true) {
			$.ajax({
				type: frmNewpass.attr('method'),
				url: frmNewpass.attr('action'),
				dataType: 'json',
				data: frmNewpass.serialize(),
				error: function () {
					$.notify({
						title: 'Sign In',
						message: 'Please contact your administrator!',
						status: 'warning',
						timeout: 5
					});
				},
				success: function (data) {
					if (data.reponse == true) {
						var notify = $.notify({
							title: 'Sign In',
							message: 'Your new password has been changed successfully.',
							status: 'success',
							timeout: 5,
							callback: function (e) {
								document.location.href = data.url;
							}
						});
					} else {
						$.notify({
							title: 'Sign In',
							message: data.reponse,
							status: data.status,
							timeout: 5
						});
					}
				}
			});
		}
	});
});