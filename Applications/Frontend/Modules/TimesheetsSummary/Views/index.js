$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "timesheetssummary/list?token=" + as_token;
	oST.aaSorting = [[0, 'desc']];
	oST.fnDrawCallback = function (oSettings) {
		$("#div_status_filter").appendTo($("#filterDialog > .ui.form"));
		$('#status_filter').change(function () {
			var value = $('#status_filter').val();
			value = (value == '-1') ? '' : value;
			oTable.fnFilter(value, null, false, true, false, true);
		});
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);
	oTable.fnFilter('Open', null, false, true, false, true);

	$('#actionBtn .dropdown').dropdown();
	$('#viewtimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		var anSelected = $.fnGetSelected(oTable);
		document.location.href = "timesheets_view?period_id=" + anSelected.attr('id');
	});
	$('#edittimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		if ($("tr.row_selected td:eq(5)").text() == "Submitted") {
			$.notify({
				title: 'Information',
				message: 'Period of the timesheet submitted.',
				status: 'info',
				timeout: 3
			});
			return;
		}
		var anSelected = $.fnGetSelected(oTable);
		document.location.href = "timesheets_edit?period_id=" + anSelected.attr('id');
	});
	$('#submittimesheet').click(function () {
		if ($(this).hasClass("disabled"))
			return;
		if ($("tr.row_selected>td:eq(5)").text() == "Submitted") {
			$.notify({
				title: 'Information',
				message: 'Period of the timesheet already submitted.',
				status: 'info',
				timeout: 3
			});
			return;
		}
		var anSelected = $.fnGetSelected(oTable);
		if (anSelected.length === 0)
			return;
		var o = {
			url: 'timesheetssummary/json',
			dialog: {
				title: 'Submitting the timesheet',
				message: "Are you sure you want to submit this period?<br/>" + "<div class=\"checkbox\">" + "<label>" + "<input type=\"checkbox\" id=\"dialog-email\" value=\"1\" checked>" + "Do you want to inform by email your staff?" + "</label>" + "</div>"
			},
			data: {
				op: 'submit',
				period_id: anSelected.attr('id'),
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
});
