$(document).ready(function () {
	$('#exemple tbody').on('click', 'tr', $.clickLine);
	oST.sAjaxSource = "tasksusers/list?token=" + as_token;
	oST.fnDrawCallback = function (oSettings) {
		$('.pop').popup();
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);

	$('#openView').click(function () {
	    document.location.href = "frmtask?id=" + $.fnGetSelected(oTable).attr('id');
	});
	$("#exemple").on("change", "input[data-task_id]", function () {
		var elem = $(this);
		$.ajax({
			type : 'POST',
			url : "tasksusers",
			dataType : 'json',
			data : {
				op : 'taskassign',
				task_user_id : elem.data("task_user_id"),
				task_id : elem.data("task_id"),
				user_id : elem.data("user_id"),
				val : elem.prop("checked"),
				token : as_token
			},
			error : function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus : textStatus,
					errorThrown : errorThrown
				});
			},
			success: function (data) {
			    if (data.reponse === true) {
			        $.notify({
			            title: data.title,
			            message: data.msg,
			            timeout: 3,
			            callback: function (e) {
			                oTable.fnDraw();
			            }
			        });
			    }
			    $.fnMessage(data);
			}
		});
	});
});
