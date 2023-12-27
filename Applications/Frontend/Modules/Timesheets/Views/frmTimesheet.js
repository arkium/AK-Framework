var o = {
	form: 'frmTimesheet',
	data: {
		time_id: $('#time_id').prop('value')
	},
	fields: {
		task_id: 'empty',
		date: ['empty', 'dateISO', 'dateValide'],
		duration: 'empty'
	},
	GetData: function (ele, data) {
		console.log(moment(data[4], "YYYY-MM-DD"));
		ele.form('set values', {
			time_id: data[0],
			user_id: data[1],
			period_id: data[2],
			date: moment(data[4]).format("YYYY-MM-DD"),
			duration: data[5],
			task_id: data[6],
			comment: data[8]
		});

		//as_period_id = data[2];
		//as_user_id = data[1];
		//updateSelect('all', $('#frmTimesheet #task_id'));
		//$('#btnCancel').data('return', 'timesheets_edit?period_id=' + data[2])
	}
};
$.initForm(o)

//var as_period_id = $("#period_id").val();
//var as_user_id = $("#user_id").val();

//function updateSelect(type, destination) {
//	if (sessionStorage.period == as_period_id) {
//		if (sessionStorage.getItem(type) != null) {
//			return $.when(destination.html(sessionStorage.getItem(type)));
//		}
//	} else {
//		sessionStorage.clear();
//	}
//	$.ajax({
//		type: 'post',
//		url: 'timesheets',
//		dataType: 'json',
//		data: {
//			op: "displayCodes",
//			token: as_token,
//			type: type,
//			period_id: as_period_id
//		},
//		error: function (jqXHR, textStatus, errorThrown) {
//			$.fnMessage({
//				textStatus: textStatus,
//				errorThrown: errorThrown
//			});
//		},
//		success: function (data) {
//			destination.dropdown('restore defaults')
//			destination.html(data['option']);
//			sessionStorage.setItem('period', as_period_id);
//			sessionStorage.setItem(type, data['option']);
//		}
//	//}).done(function (ele, data) {
//	//	o.form.form('set value', 'task_id', data[6]);
//	//	console.log(data[6]);
//	});
//}

//$('#frmTimesheet #lastWeekCodes').on('click', function (e) {
//	e.stopImmediatePropagation();
//	updateSelect('last', $('#frmTimesheet #task_id'));
//});

//$('#frmTimesheet #allCodes').on('click', function (e) {
//	e.stopImmediatePropagation();
//	updateSelect('all', $('#frmTimesheet #task_id'));
//});

$("#duration").focus(function (e) {
	e.stopImmediatePropagation();
	$(this).data('oldValue', this.value);
}).blur(function (e) {
	e.stopImmediatePropagation();
	if (this.value == $(this).data('oldValue'))
		return true;
	if (!$.TestTime(this)) {
		var text = 'The time format is invalid.<br /><br />Please enter the time in the format :<br />00:00 or 00.00 or 00,00 in 1/100 hours.';
		var that = $(this);
		$.notify({
			title: 'Information!',
			message: text,
			timeout: 3,
			status: 'info',
			callback: function (e) {
				that.val(that.data('oldValue'));
			}
		});
	}
});

$('#btnHours').click(function () {
	var as_time_id = $('#time_id').prop('value');
	if (as_time_id == '') {
		var as_period_id = $("#period_id").val();
		var as_user_id = $("#user_id").val();
		var as_task_id = $("#task_id").val();
		var as_date = $("#date").val();
		document.location.href = "frmtimesheetshours?period_id=" + as_period_id + "&user_id=" + as_user_id + "&date=" + as_date + "&task_id=" + as_task_id;
	} else {
		document.location.href = "timesheetshours_index?id=" + $('#time_id').prop('value') + "&p=2";
	}
	return false;
});