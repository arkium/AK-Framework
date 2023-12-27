$(document).ready(function () {
    $('#exemple tbody').on('click', 'tr', $.clickLine);
    oST.sAjaxSource = "timesheetshours/list?time_id=" + $("#exemple").data('timeid') + "&token=" + as_token;
    oST.fnDrawCallback = function (oSettings) {
    	$("#div_status_filter").appendTo($("#filterDialog > .ui.form"));
    	$('#filterDialog select').dropdown();
    };
    oTable = $('#exemple').dataTable(oST);

    $('#actionBtn .dropdown').dropdown();
    $('#openView').click(function () {
    	document.location.href = "frmtimesheetshours?hour_id=" + $.fnGetSelected(oTable).attr('id');
    });
    $('#openAdd').click(function () {
    	document.location.href = "frmtimesheetshours?id=" + $('#exemple').data('timeid');
    });
    $('#openEdit').click(function () {
    	document.location.href = "frmtimesheetshours?hour_id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
    });
    $('#delete').click(function () {
        $.DlgDelete({
        	url: 'timesheetshours',
            dialog: {
                title: 'Pointage Atelier',
                message: 'Voulez-vous supprimer le pointage ? Veuillez confirmer votre choix.'
            },
            data: {
                op: 'delete',
                hour_id: $.fnGetSelected(oTable).attr('id'),
                time_id: $('#exemple').data('timeid'),
                token: as_token
            },
            anSelected: function () {
                return $.fnGetSelected(oTable);
            }
        });
    });
    $('#returnoverview').click(function () {
    	document.location.href = $(this).data('return');
    	return false;
    });
});
