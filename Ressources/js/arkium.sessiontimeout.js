(function ($) {

	"use strict";

	$.sessionTimeout = function (options) {

		var defaults = {
			message: 'Your session is about to expire in a few minutes.',
			keepAliveUrl: 'keepalive',
			redirUrl: 'logout',
			logoutUrl: 'logout',
			TimeRemainingUrl: 'timeremaining',
			warnAfter: 0,
			redirAfter: 0
		};

		defaults = $.extend({}, defaults, options);

		var sessionDialog = $('<div id="sessionTimeout-dialog" class="ui small modal"></div>').appendTo("body");
		var sessionHeader = $('<i class="close icon"></i><div class="header">Session Time</div>').appendTo(sessionDialog);
		var sessionContent = $('<div class="content"></div>').appendTo(sessionDialog);
		var sessionActions = $('<div class="actions"><div class="ui cancel button">Log Out Now</div><div class="ui ok button">Stay Connected</div></div></div>').appendTo(sessionDialog);

		sessionContent.append(defaults.message);

		var opt = {
			closable: false,
			onDeny: function () {
				window.location = defaults.logoutUrl
			},
			onApprove: function () {
				sessionDialog.modal("hide");
				$.ajax({
					type: 'POST',
					url: defaults.keepAliveUrl,
					success: function (data) {
						if (data.reponse == true) {
							controlRedirTimer('stop');
							controlDialogTimer('start');
						}
					}
				});
			}
		};

		var dialog = sessionDialog.modal(opt);

		var dialogTimer, redirTimer;

		function controlDialogTimer(action) {
			switch (action) {
				case 'start':
					$.ajax({
						type: 'POST',
						url: defaults.TimeRemainingUrl,
						success: function (data) {
							if (data.reponse === true) {
								defaults.warnAfter = data.warnafter;
								defaults.redirAfter = data.redirafter;
								dialogTimer = setTimeout(function () {
									dialog.modal("show");
									controlRedirTimer('start');
								}, defaults.warnAfter);
							}
						}
					});
					break;
				case 'stop':
					clearTimeout(dialogTimer);
					break;
			}
		}
		function controlRedirTimer(action) {
			switch (action) {
				case 'start':
					redirTimer = setTimeout(function () {
						window.location = defaults.redirUrl
					}, defaults.redirAfter - defaults.warnAfter);
					break;
				case 'stop':
					clearTimeout(redirTimer);
					break;
			}
		}
		controlDialogTimer('start');
	};

	$(document).ready(function () {
		$.sessionTimeout();
	});

})(jQuery);
