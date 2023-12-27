$(document).ready(function () {
    $('#exemple tbody').on('click', 'tr', $.clickLine);
    oST.sAjaxSource = "usersroles/list?token=" + as_token;
    oST.fnDrawCallback = function (oSettings) {
    	$('#filterDialog select').dropdown();
    };
    oTable = $('#exemple').dataTable(oST);

    $('#actionBtn .dropdown').dropdown();
    $('#openView').click(function () {
        document.location.href = "frmuserrole?id=" + $.fnGetSelected(oTable).attr('id');
    });
    $('#openAdd').click(function () {
        document.location.href = "frmuserrole";
    });
    $('#openEdit').click(function () {
        document.location.href = "frmuserrole?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
    });
    $('#delete').click(function () {
        $.DlgDelete({
            url: 'usersroles',
            dialog: {
                title: 'User Role',
                message: 'Are you sure to want to delete User Role ? Please to confirm your choice.'
            },
            data: {
                op: 'delete',
                role_id: $.fnGetSelected(oTable).attr('id'),
                token: as_token
            },
            anSelected: function () {
                return $.fnGetSelected(oTable);
            }
        });
    });
});