var o = {
	form: 'frmTimesheethours',
	data: {
		hour_id: $('#hour_id').prop('value')
	},
	fields: {
		start: 'empty',
		end: 'empty'
	},
	GetData: function (ele, data) {
		ele.form('set values', {
			hour_id: data[0],
			time_id: data[1],
			start: data[2],
			end: data[3],
			duration: moment(moment(data[3], 'HH:mm').subtract(moment(data[2], 'HH:mm'))).format("HH:mm")
		});
		$('#btnCancel').data('return', 'timesheetshours_index?id=' + data[1])
	}
};
$.initForm(o)

var timeS, timeE, timeD;

$("#start").focus(function (e) {
	e.stopImmediatePropagation();
	$(this).data('oldValue', this.value);
	$(this).data('oldValueEnd', $('#end').value);
	$(this).data('oldValueDuration', $('#duration').value);
}).blur(function (e) {
	e.stopImmediatePropagation();
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
				$('#end').val(that.data('oldValueEnd'));
				$('#duration').val(that.data('oldValueDuration'));
			}
		});
	} else {
		if ($(this).data('oldValue') != $(this).val()) {
			$('#end').val("");
			$('#duration').val("");
		}
	}
});

$('#end').focus(function (e) {
	e.stopImmediatePropagation();
	$(this).data('oldValue', this.value);
}).blur(function (e) {
	e.stopImmediatePropagation();
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
	} else {
		if ($(this).data('oldValue') != $(this).val()) {
			var timeE = moment($('#end').val(), 'HH:mm');
			var timeD = moment(timeE.subtract(moment($('#start').val(), 'HH:mm'))).format("HH:mm");
			$('#duration').val(timeD);
		}
	}
});

/*
$("#frmTimesheet #duration").focus(function (e) {
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
*/