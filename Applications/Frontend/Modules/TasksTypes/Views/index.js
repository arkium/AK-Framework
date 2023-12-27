$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "taskstypes/list?token=" + as_token;
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

	$('#actionBtn .dropdown').dropdown();
	$('#openView').click(function () {
		document.location.href = "frmtasktype?id=" + $.fnGetSelected(oTable).attr('id');
	});
	$('#openAdd').click(function () {
		document.location.href = "frmtasktype";
	});
	$('#openEdit').click(function () {
		document.location.href = "frmtasktype?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
	});
	$('#delete').click(function () {
		$.DlgDelete({
			url: 'taskstypes',
			dialog: {
				title: 'Activity Type',
				message: 'Are you sure to want to delete Activity Type ? Please to confirm your choice.'
			},
			data: {
				op: 'delete',
				task_type_id: $.fnGetSelected(oTable).attr('id'),
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			}
		});
	});
});