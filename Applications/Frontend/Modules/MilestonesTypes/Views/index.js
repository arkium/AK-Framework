$(document).ready(function () {
	$('#openView').loadDialog('frmMilestoneType', 'view');
	$('#openAdd').loadDialog('frmMilestoneType', 'add');
	$('#openEdit').loadDialog('frmMilestoneType', 'edit');
	$('#delete').loadDialog('frmMilestoneType', 'delete');
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "milestonestypes/list?token=" + as_token;
	oTable = $('#exemple').dataTable(oST);
});
