$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "users/list?token=" + as_token;
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
		document.location.href = "frmuser?id=" + $.fnGetSelected(oTable).attr('id');
	});
	$('#openAdd').click(function () {
		document.location.href = "frmuser";
	});
	$('#openEdit').click(function () {
		document.location.href = "frmuser?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
	});
	$('#delete').click(function () {
		$.DlgDelete({
			url: 'users',
			dialog: {
				title: 'User',
				message: 'Are you sure to want to delete User ? Please to confirm your choice.'
			},
			data: {
				op: 'delete',
				user_id: $.fnGetSelected(oTable).attr('id'),
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			}
		});
	});
});
