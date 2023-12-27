$(document).ready(function () {

	var time = moment();

	var o = {
		form: 'frmAddTime',
		fields: {
			task_id: 'empty',
			user_id: 'empty',
			date: 'empty',
			start: 'empty',
			end: 'empty',
			duration: 'empty'
		}
	};
	$.initForm(o);

	var timestart = null, timeend = null, timetotal;

	$('#start').blur(function (e) {
		e.preventDefault();
		timestart = moment.utc(e.target.value, "H:m");
		$('#start').val(timestart.format("HH:mm"));
		$('#end').val("");
		$('#duration').val("");
	});

	$('#end').blur(function (e) {
		e.preventDefault();
		if (e.target.value != "") {
			timeend = moment.utc(e.target.value, "H:m");
			$('#end').val(timeend.format("HH:mm"));
			time = timeend;
			timetotal = time.subtract(timestart);
			$('#duration').val(timetotal.format("HH:mm"));
		}
	});
})
