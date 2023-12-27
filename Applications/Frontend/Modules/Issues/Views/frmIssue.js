$(document).ready(function () {
	var myDropzone= null,
		cleanDropZone = false,
		countFile = 1;
	var myDropzoneOptions = {
			url: "issues/file",
			addRemoveLinks: true,
			init: function() {
				myDropzone = this;
				myDropzone.on("sending", function(file, xhr, formData) {
					formData.append("token", as_token);
					console.log(as_token);
				})
				.on("addedfile", function(file) {
					file.id = countFile;
					$("#frmIssue").append('<input id="file'+file.id+'" type="hidden" name="file['+file.id+']" value="'+file.name+'" />');
					$("#frmIssue").append('<input id="filesize'+file.id+'" type="hidden" name="filesize['+file.id+']" value="'+file.size+'" />');
				}).on("success", function(file, data) {
					if (data.reponse === true) {
						countFile = countFile + 1;
						cleanDropZone = false;
					} else {
						cleanDropZone = true;
						myDropzone.removeFile(file);
					}
					$.fnMessage(data);
				})
				.on("removedfile", function(file) {
					$("#frmIssue>#file" + file.id).remove();
					$("#frmIssue>#filesize" + file.id).remove();
					if (cleanDropZone)
						return;
					$.ajax({
						type : 'post',
						url : 'issues',
						dataType : 'json',
						data : {
							token : as_token,
							file : file.name,
							op : 'delete_file'
						},
						error : function (jqXHR, textStatus, errorThrown) {
							$.fnMessage({
								textStatus : textStatus,
								errorThrown : errorThrown
							});
						},
						success : function (data) {
							$.fnMessage(data);
						}
					});
				});
			} 
		};

	$.oDialog["frmIssue"].options = {
		id : {
			issue_id : $.fnGetSelected(oTable).prop('id')
		},
		form : '#frmIssue',
		add : {
			op : 'add',
			title : 'Add New Issue',
			beforeOpen : function (selector) {
				$("#upload").dropzone(myDropzoneOptions);		
			},
			beforeClose : function (selector) {
				cleanDropZone = true;
				myDropzone.destroy();
			}
		},
		edit : {
			op : 'edit',
			title : 'Edit Issue',
			beforeOpen : function (selector) {
				$("#upload").dropzone(myDropzoneOptions);		
			},
			beforeClose : function (selector) {
				cleanDropZone = true;
				myDropzone.destroy();
			}
		},
		del : {
			op : 'delete',
			title : 'Confirm',
			url : 'issues',
			data : {
				op : 'delete',
				token : as_token
			},
			anSelected : $.fnGetSelected(oTable)
		},
		oGetData : function (data) {
		    $("#frmIssue #issue_id").val(data[0]);
			$("#frmIssue #user_id").val(data[1]);
			$("#frmIssue #title").val(data[2]);
			$("#frmIssue #description").val(data[3]);
			$("#frmIssue #type_id").val(data[4]);
			$("#frmIssue #status").val(data[5]);
			try {
				if (typeof data['files'] !== 'undefined') {
					$.each(data['files'], function(key, row) {
						var mockFile = { name: row.filename, size: row.filesize };
						myDropzone.files.push(mockFile);
						myDropzone.emit("addedfile", mockFile);
					});
					
				}
			} catch (e) {
				console.log(e);
			}
		},
		validate : {
			rules : {
				title : {
					required : true
				}
			}
		}
	};
});