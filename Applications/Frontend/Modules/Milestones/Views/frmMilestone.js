$('#lst_milestones').slimScroll({
	position : 'right',
	height : '210px',
	railVisible : true,
	alwaysVisible : false
});

$.oDialog["frmMilestone"].options = {
	jqrte : $("#frmMilestone #note"),
	id : {
		task_id : $.fnGetSelected(oTable).attr('id')
	},
	form : '#frmMilestone',
	view : {
		op : 'view',
		title : 'Milestones'
	},
	edit : {
		op : 'edit',
		title : 'Milestones'
	},
	oGetData : function (data) {
		$("#frmMilestone #task_id").val(data[0]);
		$("#frmMilestone #code").html(data[1]);
		$("#frmMilestone #closing").html(data[6]);
		$("#frmMilestone #lst_milestones tbody").html(data['list_fields']);
		if (data['list_fields'][0] == "") 
			$("#frmMilestone").parents().find('button:contains("Ok")').hide();
		$("#frmMilestone .jdate")
		.focus(function (e) {
			e.preventDefault();
			$(this).data('oldValue', this.value);
		})
		.blur(function (e) {
			e.stopImmediatePropagation();
			if (this.value == $(this).data('oldValue')) {
				return true;
			}
			var oInput = $(this);
			if (!$.TestDate(this)) {
				var text = 'The date format is invalid.<br /><br />Please enter a valid date less than 10 years and format:<br />Year-Month-Day : YYYY-MM-DD';
				$.MsgInvalid(oInput, oInput.data('oldValue'), text);
			}
		})
		.datepicker({
			showOn : "button",
			buttonImageOnly : true,
			dateFormat : "yy-mm-dd",
			defaultDate : "+1w",
			changeMonth : true,
			changeYear : true
		});
	},
	validate : {
		rules : {}
	}
};
