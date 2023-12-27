$(document).ready(function () {
	oST.sAjaxSource = "tasks/list_approval?token=" + as_token;
	oST.fnDrawCallback = function (oSettings) {
		$('#filterDialog select').dropdown();
	};
	oTable = $('#exemple').dataTable(oST);

	var form = $('#approval_form').submit(function () {
		return false;
	});

	$('#approval').click(function () {
		$.ajax({
			type: form.attr('method'),
			url: form.attr('action'),
			dataType: 'json',
			data: form.serialize(),
			error: function (jqXHR, textStatus, errorThrown) {
				$.fnMessage({
					textStatus: textStatus,
					errorThrown: errorThrown
				});
			},
			success: function (data) {
				if (data.reponse === true) {
					$.notify({
						title: data.title,
						message: data.msg,
						timeout: 3,
						status: "info",
						callback: function (e) {
							if (oTable)
								oTable.fnDraw();
						}
					});
				}
				$.fnMessage(data);
			}
		});
	});
});