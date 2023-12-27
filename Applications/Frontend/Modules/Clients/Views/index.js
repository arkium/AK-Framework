$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "clients/list?token=" + as_token;
	oST.fnPreDrawCallback = function (oSettings) {
		$('#opportunity_filter').change(function (event) {
			var value = $('#opportunity_filter').val();
			value = (value == '-1') ? '' : value;
			oTable.fnFilter(value, null, false, true, false, true);
		});
	};
	oST.fnDrawCallback = function (oSettings) {
		$("#div_opportunity_filter").appendTo($("#filterDialog > .ui.form"));
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);
	$('#opportunity_filter').change();

	$('#actionBtn .dropdown').dropdown();
	$('#openView').click(function () {
		document.location.href = "frmclient?id=" + $.fnGetSelected(oTable).attr('id');
	});
	$('#openAdd').click(function () {
		document.location.href = "frmclient";
	});
	$('#openEdit').click(function () {
		document.location.href = "frmclient?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
	});
	$('#delete').click(function () {
		$.DlgDelete({
			url: 'clients',
			dialog: {
				title: 'Clients',
				message: 'Êtes-vous sûr de vouloir supprimer le client  ? Veuillez confirmer votre choix.'
			},
			data: {
				op: 'delete',
				entity_id: $.fnGetSelected(oTable).attr('id'),
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			}
		});
	});
});