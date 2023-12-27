$(document).ready(function () {
	var period_id = $("#period_id").val();

	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "timesheetsapprove/list?period_id=" + period_id + "&token=" + as_token;
	//oST.aaSorting = [[5, 'desc'], [1, 'asc']];
	oST.aaSorting = [];
	oST.fnDrawCallback = function (oSettings) {
		$('#div_period_filter').appendTo($('#title_table'));
		$('#period_id').change(function () {
			document.location.href = "timesheetsapprove_index?period_id=" + $('#period_id').val();
			return false;
		}).dropdown();
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);

	$('#actionBtn .dropdown').dropdown();

	$('#previous').click(function (e) {
		e.stopImmediatePropagation();
		$('#period_id option:selected').prev().attr('selected', 'selected').change();
	});
	$('#next').click(function (e) {
		e.stopImmediatePropagation();
		$('#period_id option:selected').next().attr('selected', 'selected').change();
	});

	$('#viewtimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		var anSelected = $.fnGetSelected(oTable);
		document.location.href = "timesheets_view?period_id=" + period_id + "&user_id=" + anSelected.attr('id') + "&p=1";
	});
	$('#edittimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		var anSelected = $.fnGetSelected(oTable);
		document.location.href = "timesheets_edit?period_id=" + period_id + "&user_id=" + anSelected.attr('id') + "&p=1";
	});
	$('#approvaltimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		if ($("tr.row_selected>td:eq(6)").text() == "Approved") {
			$.notify({
				title: 'Information',
				message: 'Timesheet already approved.',
				timeout: 3,
				status: 'info'
			});
			return;
		}
		if ($("tr.row_selected>td:eq(6)").text() == "") {
			$.notify({
				title: 'Information',
				message: 'Timesheet was not submitted.',
				timeout: 3,
				status: 'info'
			});
			return;
		}
		var anSelected = $.fnGetSelected(oTable);
		if (anSelected.length === 0)
			return;
		var text = "Are you sure you want to approve this timesheet?<br/>" +
			"<div class=\"checkbox\">" +
			"<label>" +
			"<input type=\"checkbox\" id=\"dialog-email\" value=\"1\" checked>" +
			"Do you want to inform by email your approver?" +
			"</label>" +
			"</div>";
		var o = {
			url: 'timesheetsapprove/json',
			dialog: {
				title: 'Approval the timesheet',
				message: text
			},
			data: {
				op: 'approval',
				user_id: anSelected.attr('id'),
				period_id: period_id,
				email: $('#dialog-email:checkbox:checked').val(),
				token: as_token
			},
			anSelected: function () {
				return anSelected;
			},
			callback: null
		};
		$.fnModal(o);
	});
	$('#opentimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		if ($("tr.row_selected>td:eq(6)").text() == "") {
			$.notify({
				title: 'Information',
				message: 'Timesheet already open.',
				timeout: 3,
				status: 'info'
			});
			return;
		}
		var anSelected = $.fnGetSelected(oTable);
		if (anSelected.length === 0)
			return;
		var o = {
			url: 'timesheetsapprove/json',
			dialog: {
				title: 'Openning the timesheet',
				message: 'Are you sure you want to open this timesheet?'
			},
			data: {
				op: 'open',
				user_id: anSelected.attr('id'),
				period_id: period_id,
				token: as_token
			},
			anSelected: function () {
				return anSelected;
			},
			callback: null
		};
		$.fnModal(o);
	});

});
