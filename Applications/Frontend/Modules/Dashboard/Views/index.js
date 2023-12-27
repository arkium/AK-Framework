$(document).ready(function () {
	var oDefault = {
		"bJQueryUI": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bSort": true,
		"bInfo": false,
		"bAutoWidth": false,
		"aaSorting": [[0, 'desc']]
	};

	$('.pop').popup();

	// My Projects
	oTable = $('#exemple').dataTable(oDefault);
	
	$("[data-dashboard='myproject']").each(function () {
		$(this).on('click', function () {
			document.location.href = "timelines_index?task_id=" + $(this).prop('id');
		});
	});
	$('#nbre_ligne_project').appendTo('#title_project');
	$('#scroll_myprojects').slimScroll({
		position: 'right',
		height: '310px',
		railVisible: true,
		alwaysVisible: false
	});

	// My Timsheets
	var oDefault_2 = {
		"aaSorting": [[0, 'desc']]
	};
	var oST_page = $.extend(oDefault, oDefault_2);
	$('#exemple1').dataTable(oST_page);
	$("[data-dashboard='mytimesheet']").each(function () {
		$(this).on('click', function () {
			document.location.href = "timesheets_view?period_id=" + $(this).prop('id');
		});
	});
	$('#nbre_ligne_timesheet').appendTo('#title_timesheet');
	$('#scroll_mytimesheets').slimScroll({
		position: 'right',
		height: '310px',
		railVisible: true,
		alwaysVisible: false
	});

});