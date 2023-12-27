$(document).ready(function () {
	$('#openView').on('click', function(){
		document.location = 'issues_comments?id=' + $.fnGetSelected(oTable).prop('id');
	});
	$('#openAdd').loadDialog('frmIssue', 'add');
	$('#openEdit').loadDialog('frmIssue', 'edit');
	$('#delete').loadDialog('frmIssue', 'delete');
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "issues/list?token=" + as_token;
	oTable = $('#exemple').dataTable(oST);
});