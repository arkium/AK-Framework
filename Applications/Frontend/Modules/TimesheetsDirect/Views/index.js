var time = moment();

var o = {
	form: 'frmTimeDirect',
	data: {
		hour_id: $('#hour_id').prop('value')
	},
	fields: {
		task_id: 'empty',
		date: 'empty'
	},
	GetData: function (ele, data) {
		var timeS = moment(data[5], 'HH:mm:ss');
		var timeE = (data[6] == '00:00:00') ? time : moment(data[6], 'HH:mm:ss');
		var timeD = timeE.clone().subtract(timeS);

		ele.form('set values', {
			date: moment(data[4]).format("X"),
			start: timeS.format("HH:mm"),
			end: timeE.format("HH:mm"),
			duration: timeD.format("HH:mm"),
			comment: data[10],
			task_id: data[8]
		});

		$('#txtDate').html(moment(data[4]).format("YYYY-MM-DD"));
		$('#txtStart').html(timeS.format("HH:mm"));
		$('#txtEnd').html(timeE.format("HH:mm"));
		$('#txtDuration').html(timeD.format("HH:mm"));
	}
};
$.initForm(o);

switch ($('#op').val()) {
	case "edit":
		$('#frmTimeDirect .ui.dropdown').addClass("disabled");
		break;
	case "add":
		$('#date').val(time.format("X"));
		$('#txtDate').html(time.format("YYYY-MM-DD"));
		$('#start').val(time.format("HH:mm"));
		$('#txtStart').html(time.format("HH:mm"));
		$('#end').val('');
		$('#txtEnd').html("--:--");
		$('#duration').val('');
		$('#txtDuration').html("--:--");
		break;
}

$('#task_id').change(function () {
	$.ajax({
		type: 'post',
		url: 'timeclock/json',
		dataType: 'json',
		data: {
			op: "note",
			token: as_token,
			task_id: $("#task_id option:selected").val(),
			date: $("#date").val()
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$.fnMessage({
				textStatus: textStatus,
				errorThrown: errorThrown
			});
		},
		success: function (data) {
			$("#note").val(data.note);
			$("#comment").val(data.comment);
		}
	});
});