$(document).ready(function () {
	delete oST.bProcessing;
	delete oST.bServerSide;
	delete oST.sAjaxSource;
	oST.bSort = false;
	oTable = $('#exemple').dataTable(oST);

	$("#div_filter").appendTo($("#dataTables_header"));
	$("<div class='col-sm-4 col-xs-6'></div>").prependTo($("#dataTables_footer"));
	
	$('#milestone_type_id').change(function () {
		document.location.href = "milestones_edit?milestone_type_id=" + $('#milestone_type_id').val();
		return false;
	});

	$("[data-dashboard='myproject']").each(function () {
		var self = $(this);
		self.loadDialog('frmTask', 'view', {
			task_id : self.data('id')
		});
	});
	
	function save(oInput, self) {
		if ($.TestDate(self)) {
			$.ajax({
				type : 'post',
				url : 'milestones',
				dataType : 'json',
				data : {
					op : "save_date",
					token : as_token,
					task_id : oInput.attr('data-task_id'),
					milestone_field_id : oInput.attr('data-milestone_field_id'),
					date : oInput.val(),
					milestone_id : oInput.attr('data-milestone_id'),
					direct : true
				},
				error : function (jqXHR, textStatus, errorThrown) {
					$.fnMessage({
						textStatus : textStatus,
						errorThrown : errorThrown
					});
				},
				success : function (data) {
					$.ambiance({
						message : data.msg,
						title : data.title,
						type : "info"
					});
					return;
				}
			});
			oValue = false;
		} else {
			var text = 'The date format is invalid.<br /><br />Please enter a valid date less than 10 years and format:<br />Year-Month-Day : YYYY-MM-DD';
			$.MsgInvalid(oInput, oInput.data('oldValue'), text);
		}
	}

	var oValue = false;
	var oInput = null;
	var as_period_id = $("#timesheet #period_id").val();
	var as_user_id = $("#timesheet #user_id").val();

	$("#exemple input")
	.focus(function () {
		$(this).data('oldValue', this.value);
	})
	.keyup(function (e) {
		e.preventDefault();
		var keyCodes = {
			'up' : 38,
			'down' : 40,
			'left' : 37,
			'right' : 39,
			'esc' : 27
		};
		var notReadonly = ":input:not([readonly])",
		input,
		reverse = false,
		col = 'td:eq(' + $(this).closest('td').index() + ')';
		if (e.keyCode == keyCodes.right)
			input = $(this).closest('td').nextAll();
		else if (e.keyCode == keyCodes.left) {
			input = $(this).closest('td').prevAll();
			reverse = true;
		} else if (e.keyCode == keyCodes.down)
			input = $(this).closest('tr').nextAll().find(col);
		else if (e.keyCode == keyCodes.up) {
			input = $(this).closest('tr').prevAll().find(col);
			reverse = true;
		}
		if (input) {
			if (e.ctrlKey)
				reverse = !reverse;
			if (!reverse)
				input.find(notReadonly).first().focus();
			else
				input.find(notReadonly).last().focus();
		}
	})
	.change(function (e){
		e.stopImmediatePropagation();
		if (this.value == $(this).data('oldValue'))
			return true;
		oInput = $(this);
		save(oInput, this);
		return false;
	})
	.datetimepicker({
		pickTime: false
	});
});
