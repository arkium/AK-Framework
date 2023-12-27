$(document).ready(function () {
	$("input[name=token]").val(as_token);
	
	$("#type_id").val($("#type").data('type'));
	$('#type_id').change(function() {
		$.ajax({
			type : 'post',
			url : 'issues',
			dataType : 'json',
			data : {
				token : as_token,
				issue_id : $('#issue_id').val(),
				op : 'type',
				type : $('#type_id option:selected').val()
			},
			error : function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus : textStatus,
					errorThrown : errorThrown
				});
			},
			success : function (data) {
				if (data.reponse === true) {
					$.ambiance({
						message : "Change type",
						title : "Information",
						type : "success"
					});
					$('#type').html($('#type_id').val());
					return;
				}
				$.fnMessage(data);
			}
		});
	});
	
	var comments = null;

	$.ajax({
		type : 'post',
		url : 'issues',
		dataType : 'json',
		data : {
			token : as_token,
			issue_id : $('#issue_id').val(),
			op : 'lst_comment',
		},
		error : function (jqXHR, textStatus, errorThrown) {
			$.fnMessage({
				textStatus : textStatus,
				errorThrown : errorThrown
			});
		},
		success : function (data) {
			if (data.reponse === true) {
				comments = data.result;
				ini_tmpl();
				return;
			}
			$.fnMessage(data);
		}
	});
	
	function countComments(){
		$("#count").html(comments.length + " comment(s)");
	}
	
	function ini_tmpl() {
		countComments();
		$.templates("#commentTmpl").link("#chat", comments)
		.on("click", ".remove", function() {
			var view = $.view(this);
			var self = $(this);
			$.ajax({
				token : as_token,
				type : 'post',
				url : 'issues',
				dataType : 'json',
				data : {
					token : as_token,
					comment_id : self.data('id'),
					op : 'delete_comment',
				},
				error : function (jqXHR, textStatus, errorThrown) {
					$.fnMessage({
						textStatus : textStatus,
						errorThrown : errorThrown
					});
				},
				success : function (data) {
					if (data.reponse === true) {
						$.ambiance({
							message : "Comment deleted",
							title : "Information",
							type : "success"
						});
						$.observable(comments).remove(view.index);
						return;
					}
					$.fnMessage(data);
				}
			});
		});
		$("#chat .remove").hide();
		$([comments]).on("arrayChange", countComments); 
	}
	
	$('#frmaddcomment').submit(function () {
		return false;
	});
	
	$('#returnoverview').on('click', function () {
		document.location.href = "issues_index";
		return false;
	});
	
	$('#radio').buttonset();
	
	$('#close, #open').change(function() {
		$.ajax({
			type : 'post',
			url : 'issues',
			dataType : 'json',
			data : {
				token : as_token,
				issue_id : $('#issue_id').val(),
				op : 'status',
				status : $('input[name=status]:checked').val()
			},
			error : function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus : textStatus,
					errorThrown : errorThrown
				});
			},
			success : function (data) {
				if (data.reponse === true) {
					$.ambiance({
						message : "Change status",
						title : "Information",
						type : "success"
					});
					if ($('input[name=status]:checked').val() == 1){
						$("#status").removeClass("label-warning");
						$("#status").addClass("label-success");
					} else {
						$("#status").removeClass("label-success");
						$("#status").addClass("label-warning");
					} 
					return;
				}
				$.fnMessage(data);
			}
		});
	});
	
	$('#btn_submit').on('click', function(){
		var form = $('#frmaddcomment');
		if ($('#comment').val() == '')
			return;
		$.ajax({
			type : form.attr('method'),
			url : form.attr('action'),
			dataType : 'json',
			data : form.serialize(),
			error : function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus : textStatus,
					errorThrown : errorThrown
				});
			},
			success : function (data) {
				if (data.reponse === true) {
					$.ambiance({
						message : "Comment added",
						title : "Information",
						type : "success"
					});
					$.observable(comments).insert(data.result);
					$("#frmAddComment").slideUp("slow");
					$("#chat .remove").hide();
					frmaddcomment.reset();
					return;
				}
				$.fnMessage(data);
			}
		});
	});

	$('#delete_issue').on('click', function(){
		$.ajax({
			type : 'post',
			url : 'issues',
			dataType : 'json',
			data : {
				token : as_token,
				issue_id : $('#issue_id').val(),
				op : 'delete_issue'
			},
			error : function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus : textStatus,
					errorThrown : errorThrown
				});
			},
			success : function (data) {
				if (data.reponse === true) {
					document.location.href = "issues_index";
					return false;
				}
				$.fnMessage(data);
			}
		});
	});
	
	$('#btn_cancel').on('click', function(){
		frmaddcomment.reset();
	});
	
	$("#frmAddComment").hide();
	$("#btnAddComment, #btnClosefrmAddComment").click(function (event) {
		if (event.isPropagationStopped())
			return;
		if ($("#frmAddComment").css('display') !== 'none') {
			$("#frmAddComment").slideUp("slow");
		} else {
			$("#frmAddComment").slideDown("slow");
		}
		event.stopPropagation();
	});
	
	$("#modifyPanel").hide();
	$("#modify").click(function (event) {
		if (event.isPropagationStopped())
			return;
		if ($("#modifyPanel").css('display') !== 'none') {
			$("#modifyPanel").slideUp("slow");
//			$("#chat .remove").effect("fade", {direction: "left"}, "slow");
			$("#chat .remove").slideUp("slow");
		} else {
			$("#modifyPanel").slideDown("slow");
			$("#chat .remove").slideDown("slow");
		}
		event.stopPropagation();
	});
	
});
