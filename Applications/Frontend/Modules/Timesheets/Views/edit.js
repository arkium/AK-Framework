$(document).ready(function () {

	var as_period_id = $("#period_id").val();
	var as_user_id = $("#user_id").val();
	var as_task_id = null;

	$.clickCell = function () {
		var self = $(this);
		if (self.hasClass('row_selected')) {
			self.removeClass('row_selected');
			self.children("span").removeClass("ui red horizontal label");
			as_task_id = null;
			$.afficheButtons(true);
		} else {
			$('.row_selected').children("span").removeClass("ui red horizontal label");
			$('.row_selected').removeClass('row_selected');
			self.addClass('row_selected');
			self.children("span").addClass("ui red horizontal label");
			as_task_id = self.parent().prop('id');
			$.afficheButtons(false);
		}
	};
	$('#exemple tbody').on('click', '.codeName', $.clickCell);
	$.afficheButtons(true);

	$('.pop').popup();
	$('.comment').popup();

	$('#actionBtn .dropdown').dropdown();
	$('#viewcode').click(function () {
		document.location.href = "frmtask?id=" + as_task_id;
	});
	$('#addcode').click(function () {
		document.location.href = "frmtimesheet?period_id=" + as_period_id + "&user_id=" + as_user_id;
	});
	$('#deletecode').click(function () {
		$.DlgDelete({
			url: 'timesheets',
			dialog: {
				title: 'Timesheet',
				message: 'Are you sure to want to delete this code project of the timesheet? Please to confirm your choice.'
			},
			data: {
				op: 'delete',
				period_id: as_period_id,
				user_id: as_user_id,
				id: as_task_id,
				token: as_token
			},
			callback: function () {
				document.location.href = "timesheets_edit?period_id=" + as_period_id;
			}
		});
	});
	$('#returnoverview').click(function () {
		document.location.href = $(this).data('return');
		return false;
	});

	$("#timesheet").submit(function () {
		return false;
	});

	//$.fn.btnTime = function (as_period_id) {
	//	$(this).each(function () {
	//		$(this).on('click', function (e) {
	//			e.stopImmediatePropagation();
	//			var that = $('.focus');
	//			var p = "&p=1";
	//			if ($.hasData(that) && typeof that.data('p') !== 'undefined') {
	//				p = "";
	//			}
	//			if ($.hasData(that) && typeof that.data('time_id') !== 'undefined') {
	//				//document.location.href = p+"frmtimesheet?id=" + that.data('time_id') + "&op=edit" + p;
	//			} else {
	//				//document.location.href = p+"frmtimesheet?period_id=" + as_period_id + "&date=" + that.data('date') + p;
	//			}
	//		});
	//	});
	//	return this;
	//}
	//$('#editTime').btnTime(as_period_id).hide();

	$("input[data-date]")
	.focus(function (e) {
		e.preventDefault();
		$(this).data('oldValue', this.value);
		//$('#editTime').show();
		//$('.focus').removeClass('focus');
		//$(this).addClass('focus');
	})
	.keyup(function (e) {
		e.preventDefault();
		var keyCodes = {
			'up': 38,
			'down': 40,
			'left': 37,
			'right': 39,
			'esc': 27
		};
		var notReadonly = ":input:not([readonly])",
		input,
		reverse = false,
		col = 'td:eq(' + $(this).closest('td').index() + ')';
		if (e.keyCode == keyCodes.right)
			input = $(this).closest('td').nextAll();
		else if (e.keyCode == keyCodes.left) {
			input = $(this).closest('td').prevAll();
			reverse = true;
		} else if (e.keyCode == keyCodes.down)
			input = $(this).closest('tr').nextAll().find(col);
		else if (e.keyCode == keyCodes.up) {
			input = $(this).closest('tr').prevAll().find(col);
			reverse = true;
		}
		if (input) {
			if (e.ctrlKey)
				reverse = !reverse;
			if (!reverse)
				input.find(notReadonly).first().focus();
			else
				input.find(notReadonly).last().focus();
		}
	})
	.blur(function (e) {
		e.preventDefault();
		if (this.value == $(this).data('oldValue')) {
			return true;
		}
		var oInput = $(this);
		if ($.TestTime(this)) {
			$.ajax({
				type: 'post',
				url: 'timesheets',
				dataType: 'json',
				data: {
					op: "save_time",
					token: as_token,
					task_id: oInput.data('task_id'),
					date: oInput.data('date'),
					period_id: as_period_id,
					user_id: as_user_id,
					time_id: oInput.data('time_id'),
					duration: oInput.val(),
					direct: true
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$.fnMessage({
						textStatus: textStatus,
						errorThrown: errorThrown
					});
					$(this).removeData('oldValue');
				},
				success: function (oData) {
					oInput.data('time_id', oData.lastID);
					$.UpTimeTotal(oInput.attr('data-date'), oInput.val(), oInput.data('oldValue'));
					oInput.removeData('oldValue');
				}
			});
		} else {
			var text = 'The time format is invalid.<br /><br />Please enter the time in the format :<br />00:00 or 00.00 or 00,00 in 1/100 hours.';
			var that = $(this);
			$.notify({
				title: 'Information',
				message: text,
				timeout: 3,
				status: 'info',
				callback: function (e) {
					that.val(that.data('oldValue'));
				}
			});
		}
	})
	.clickTime(as_period_id, as_user_id);
});
