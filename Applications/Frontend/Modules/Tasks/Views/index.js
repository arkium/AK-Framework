$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "tasks/list?token=" + as_token;
	oST.aaSorting = [[0, 'desc']];
	oST.fnPreDrawCallback = function (oSettings) {
		$('#status_filter').change(function (event) {
			var value = $('#status_filter').val();
			value = (value == '-1') ? '' : value;
			oTable.fnFilter(value, null, false, true, false, true);
		});
	};
	oST.fnDrawCallback = function (oSettings) {
		$("#div_status_filter").appendTo($("#filterDialog > .ui.form"));
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);
	$('#status_filter').change();

	$('#actionBtn .dropdown').dropdown();
	$('#openView').click(function () {
		document.location.href = "frmtask?id=" + $.fnGetSelected(oTable).attr('id');
	});
	$('#openAdd').click(function () {
		document.location.href = "frmtask";
	});
	$('#openEdit').click(function () {
		document.location.href = "frmtask?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
	});
	$('#delete').click(function () {
		$.DlgDelete({
			url: 'tasks',
			dialog: {
				title: 'Project',
				message: 'Are you sure to want to delete Project ? Please to confirm your choice.'
			},
			data: {
				op: 'delete',
				task_id: $.fnGetSelected(oTable).attr('id'),
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			}
		});
	});
	$("#openTimeline").on('click', function () {
		document.location.href = "timelines_index?task_id=" + $.fnGetSelected(oTable).attr('id');
	});
});