$(document).ready(function () {
    $('#exemple tbody').on('click', 'tr', $.clickLine);
    oST.sAjaxSource = "periods/list?token=" + as_token;
    oST.aaSorting = [[0, 'desc']];
    oST.fnPreDrawCallback = function (oSettings) {
    	$('#status_filter').change(function (event) {
    		var value = $('#status_filter').val();
    		value = (value == '-1') ? '' : value;
    		oTable.fnFilter(value, null, false, true, false, true);
    	});
    };
    oST.fnDrawCallback = function (oSettings) {
    	$("#div_status_filter").appendTo($("#filterDialog > .ui.form"));
    	$('#filterDialog select').dropdown();
    };
    oTable = $('#exemple').dataTable(oST);
    $('#status_filter').change();

    $('#actionBtn .dropdown').dropdown();
    $('#openView').click(function () {
        document.location.href = "frmperiod?id=" + $.fnGetSelected(oTable).attr('id');
    });
    $('#openAdd').click(function () {
        document.location.href = "frmperiod";
    });
    $('#openEdit').click(function () {
        document.location.href = "frmperiod?id=" + $.fnGetSelected(oTable).attr('id') + "&op=edit";
    });
    $('#delete').click(function () {
        $.DlgDelete({
            url: 'periods',
            dialog: {
                title: 'Period',
                message: 'Are you sure to want to delete Period ? Please to confirm your choice.'
            },
            data: {
                op: 'delete',
                period_id: $.fnGetSelected(oTable).attr('id'),
                token: as_token
            },
            anSelected: function () {
                return $.fnGetSelected(oTable);
            }
        });
    });

    $('#frmAddPeriods').form({
        fields: {
            yearAdd: 'empty'
        }
    });
    $('#modalAddPeriods').modal({
        closable: false,
        onShow: function () {
            $.ajax({
                url: $('#frmAddPeriods').attr('action'),
                type: $('#frmAddPeriods').attr('method'),
                data: {
                    op: 'year_button',
                    token: as_token
                },
                success: function (data) {
                    $("#frmAddPeriods #yearAdd").empty().append(data.reponse);
                    $('#yearAdd').dropdown();
                    $('#frmAddPeriods').form('set values', { yearAdd: data['selected'] });
                }
            });
        },
        onDeny: function () {
            return true;
        },
        onApprove: function () {
            $('#frmAddPeriods').form('validate form');
            if ($('#frmAddPeriods').form('is valid') === true) {
                $.ajax({
                    type: $('#frmAddPeriods').prop('method'),
                    url: $('#frmAddPeriods').prop('action'),
                    dataType: 'json',
                    data: $('#frmAddPeriods').serialize(),
                    error: function (jqXHR, textStatus, errorThrown) {
                        $.fnMessage({
                            textStatus: textStatus,
                            errorThrown: errorThrown
                        });
                    },
                    success: function (data) {
                        if (data.reponse === true) {
                            $.notify({
                                title: 'Data saved successfully!',
                                message: 'The table will be updated some time.',
                                timeout: 3
                            });
                        }
                        $.fnMessage(data);
                        oTable.fnDraw();
                    }
                });
            }
            return true;
        }
    });
    $('#openAdds').click(function () {
        $('#modalAddPeriods').modal('show');
    });

    $('#frmDeletePeriods').form({
        fields: {
            yearDelete: 'empty'
        }
    });
    $('#modalDeletePeriods').modal({
        closable: false,
        onShow: function () {
            $.ajax({
                url: $('#frmDeletePeriods').attr('action'),
                type: $('#frmDeletePeriods').attr('method'),
                data: {
                    op: 'year_button',
                    list: 1,
                    token: as_token
                },
                success: function (data) {
                    $("#frmDeletePeriods #yearDelete").empty().append(data.reponse);
                    $('#yearDelete').dropdown();
                    $('#frmDeletePeriods').form('set values', { yearDelete: data['selected'] });
                }
            });
        },
        onDeny: function () {
            return true;
        },
        onApprove: function () {
            $('#frmDeletePeriods').form('validate form');
            if ($('#frmDeletePeriods').form('is valid') === true) {
                $.ajax({
                    type: $('#frmDeletePeriods').prop('method'),
                    url: $('#frmDeletePeriods').prop('action'),
                    dataType: 'json',
                    data: $('#frmDeletePeriods').serialize(),
                    error: function (jqXHR, textStatus, errorThrown) {
                        $.fnMessage({
                            textStatus: textStatus,
                            errorThrown: errorThrown
                        });
                    },
                    success: function (data) {
                        if (data.reponse === true) {
                            $.notify({
                                title: 'Data deleted successfully!',
                                message: 'The table will be updated some time.',
                                timeout: 3
                            });
                        }
                        $.fnMessage(data);
                        oTable.fnDraw();
                    }
                });
            }
            return true;
        }
    });
    $('#openDeletes').click(function () {
        $('#modalDeletePeriods').modal('show');
    });
});
