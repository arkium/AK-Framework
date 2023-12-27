$(document).ready(function() {
    $('#exemple tbody').on('click', 'tr', $.clickLine);
    oST.sAjaxSource = "companies/list?token=" + as_token;
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
        document.location.href = "frmcompany?id=" + $.fnGetSelected(oTable).attr('id');
    });
    $('#openAdd').click(function () {
        document.location.href = "frmcompany";
    });
    $('#openEdit').click(function () {
        document.location.href = "frmcompany?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
    });
    $('#delete').click(function () {
        $.DlgDelete({
            url: 'companies',
            dialog: {
                title: 'Company',
                message: 'Are you sure to want to delete Company ? Please to confirm your choice.'
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
