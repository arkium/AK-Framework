var o = {
    form: 'frmTaskType',
    data: {
        task_type_id: $('#task_type_id').prop('value')
    },
    fields: {
        code: 'empty',
        name: 'empty',
        task_family_id: 'empty',
        chargeable: 'empty'
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            task_type_id: data[0],
            code: data[1],
            name: data[2],
            task_family_id: data[3],
            chargeable: data[4],
            note: data[5],
            color: data[6],
            status: data[7],
            update_time: data[8],
            created_time: data[9]
        })
    }
};
$.initForm(o)

//$('#frmTaskType #color').colorpicker({});

$('#task_family_id').dropdown({ allowAdditions: true });

/*
$("#frmTaskType #tabs-1").listSettings();
$('#frmTaskType #color').colorpicker({});

$.oDialog["frmTaskType"].options = {
	jqrte : $("#frmTaskType #note"),
	id : {
		task_type_id : $.fnGetSelected(oTable).attr('id')
	},
	form : '#frmTaskType',
	view : {
		op : 'view',
		title : 'View Activity Type'
	},
	add : {
		op : 'add',
		title : 'Add New Activity Type'
	},
	edit : {
		op : 'edit',
		title : 'Edit Activity Type'
	},
	del : {
		op : 'delete',
		title : 'Confirm',
		url : 'taskstypes',
		data : {
			op : 'delete',
			token : as_token
		},
		anSelected : $.fnGetSelected(oTable)
	},
	oGetData : function (data) {
		$("#frmTaskType #task_type_id").val(data[0]);
		$("#frmTaskType #code").val(data[1]);
		$("#frmTaskType #name").val(data[2]);
		$("#frmTaskType #task_family_id").val(data[3]);
		$("#frmTaskType #chargeable").val(data[4]);
		$("#frmTaskType #note").val(data[5]);
		$("#frmTaskType #color").val(data[6]).change();
		$("#frmTaskType #status").val(data[7]);
		$("#frmTaskType #update_time").val(data[8]);
		$("#frmTaskType #created_time").val(data[9]);
	},
	validate : {
		rules : {
			name : {
				required : true
			},
			code : {
				required : true
			},
			task_family_id : {
				required : true
			},
			chargeable : {
				required : true
			},
			color : {
				required : true
			}
		}
	}
};
*/