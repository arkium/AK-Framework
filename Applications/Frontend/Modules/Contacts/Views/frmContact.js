$.oDialog["frmContact"].options = {
    jqrte : $("#frmContact #note"),
    id : {
	contact_id : function() {
	    return $.fnGetSelected(oTable).attr('id')
	}
    },
    form : '#frmContact',
    view : {
	op : 'view',
	title : 'View Contact'
    },
    add : {
	op : 'add',
	title : 'Add New Contact'
    },
    edit : {
	op : 'edit',
	title : 'Edit Contact'
    },
    del : {
	op : 'delete',
	title : 'Confirm',
	url : "contacts",
	data : {
	    op : 'delete',
	    token : as_token
	},
	anSelected : function() {
	    return $.fnGetSelected(oTable)
	}
    },
    oGetData : function(data) {
	$("#frmContact #contact_id").val(data[0]);
	$("#frmContact #entity_id").val(data[1]);
	$("#frmContact #contact_type_id").val(data[2]);
	$("#frmContact #first_name").val(data[3]);
	$("#frmContact #last_name").val(data[4]);
	$("#frmContact #email").val(data[5]);
	$("#frmContact #phone").val(data[6]);
	$("#frmContact #fax").val(data[7]);
	$("#frmContact #mobile").val(data[8]);
	$("#frmContact #note").val(data[9]);
	$("#frmContact #status").val(data[10]);
	$("#frmContact #update_time").val(data[11]);
	$("#frmContact #created_time").val(data[12]);
    },
    validate : {
	rules : {
	    last_name : {
		required : true
	    },
	    email : {
		email : true
	    }
	}
    }
};
