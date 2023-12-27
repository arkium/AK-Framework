$("#frmMilestoneType #tabs").tabs();

$.oDialog["frmMilestoneType"].options = {
	jqrte : $("#frmMilestoneType #note"),
	id : {
		milestone_type_id : $.fnGetSelected(oTable).attr('id')
	},
	form : '#frmMilestoneType',
	view : {
		op : 'view',
		title : 'View Milestone List'
	},
	add : {
		op : 'add',
		title : 'Add New Milestone List'
	},
	edit : {
		op : 'edit',
		title : 'Edit Milestone List'
	},
	del : {
		op : 'delete',
		title : 'Confirm',
		url : 'milestonestypes',
		data : {
			op : 'delete',
			token : as_token
		},
		anSelected : $.fnGetSelected(oTable)
	},
	oGetData : function (data) {
		$("#frmMilestoneType #milestone_type_id").val(data[0]);
		$("#frmMilestoneType #code").val(data[1]);
		$("#frmMilestoneType #name").val(data[2]);
		$("#frmMilestoneType #note").val(data[3]);
		$("#frmMilestoneType #status").val(data[4]);
		$("#frmMilestoneType #update_time").val(data[5]);
		$("#frmMilestoneType #created_time").val(data[6]);
		$("#frmMilestoneType #tabs-1 tbody").html(data['field']);
	},
	validate : {
		rules : {
			name : {
				required : true
			},
			code : {
				required : true
			}
		}
	}
};

$("#frmMilestoneType #tabs-1").on('click', '#add-more', function (e) {
	e.stopImmediatePropagation();
	var next = parseInt($("#frmMilestoneType #count").val());
	var previousInput = "#f" + next;
	var previousSpan = "#s" + next;
	var previousButton = "#b" + next;
	var newButton = '<button id="b' + next + '" class="btn btn-danger" type="button">-</button>';
	next = next + 1;
	var newInput = '<tr id="f' + next + '"><td>' + next + '</td><td><div class="input-group"><input autocomplete="off" class="form-control" id="field' + next + '" name="field[' + next + ']" type="text" placeholder="Description" /><span id="s' + next + '" class="input-group-btn"></span></div></td><td><input type="checkbox" name="show[' + next + ']" value="1"> Show</td></tr>';
	var newInput = $(newInput);
	$(previousInput).after(newInput);
	$('#add-more').appendTo("#s" + next);
	$(newButton).appendTo(previousSpan);
	$(previousButton).click(function (e) {
		e.preventDefault();
		$(previousInput).remove();
	});
	$("#frmMilestoneType #count").val(next);
	return false;
});

$("#frmMilestoneType #tabs-1").on('click', '.remove-me', function (e) {
	e.stopImmediatePropagation();
	var fieldNum = this.id.charAt(this.id.length - 1);
	
	var text = "Are you sure you want to delete the field?<br>" +
	"All dates go to be delete.";
	var dialog = $("<div id='dialog-confirm'></div>");
	dialog.appendTo(document.body);
	dialog.html(text);
	dialog.dialog({
		autoOpen : false,
		resizable : false,
		modal : true,
		title : "Confirm",
		width : '500',
		open : function (event, ui) {
			$(this).parent('div').find('button:contains("No")').focus();
		},
		close : function (event, ui) {
			$('#dialog-confirm').remove();
		},
		buttons : {
			Yes : function () {
				$("#f" + fieldNum).remove();
				$(this).dialog("close");
			},
			No : function () {
				$(this).dialog("close");
			}
		}
	}).dialog('open');
});

$("#frmMilestoneType #tabs-1 tbody").sortable({
	cursor : "move",
	width : "100%"
});

$('#frmMilestoneType #tabs-1').slimScroll({
	position : 'right',
	railVisible : true,
	alwaysVisible : false,
	allowPageScroll : true
});
