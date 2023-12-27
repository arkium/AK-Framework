$(document).ready(function() {
    $('#exemple tbody').on('click', 'tr', $.clickLine);
    oST.sAjaxSource = "contacts/list?token=" + as_token;
    oTable = $('#exemple').dataTable(oST);

    $("#div_contacttype_filter").appendTo($("#filterDialog .uk-form"));
    $('#contacttype_filter').change(function() {
	oTable.fnFilter($('#contacttype_filter').val(), 1, false, true, false, true);
    });
});
