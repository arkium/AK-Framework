var o = {
    form: 'frmUserRole',
    data: {
        role_id: $('#role_id').prop('value')
    },
    fields: {
        code: 'empty',
        name: 'empty'
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            role_id: data[0],
            code: data[1],
            name: data[2],
			modules: data[4],
            status: data[5],
            update_time: data[6],
            created_time: data[7]
        });
        value = ((data[3] & Math.pow(2, 0)) != 0) ? true : false;
        $("#frmUserRole #view").prop("checked", value).val("1");
        value = ((data[3] & Math.pow(2, 1)) != 0) ? true : false;
        $("#frmUserRole #add").prop("checked", value).val("1");
        value = ((data[3] & Math.pow(2, 2)) != 0) ? true : false;
        $("#frmUserRole #edit").prop("checked", value).val("1");
        value = ((data[3] & Math.pow(2, 3)) != 0) ? true : false;
        $("#frmUserRole #delete").prop("checked", value).val("1");
        value = ((data[3] & Math.pow(2, 4)) != 0) ? true : false;
        $("#frmUserRole #approval").prop("checked", value).val("1");
        value = ((data[3] & Math.pow(2, 5)) != 0) ? true : false;
        $("#frmUserRole #admin").prop("checked", value).val("1");
        $("#frmUserRole #noadmin").prop("checked", !value).val("0");
    }
};
$.initForm(o)

$('#modules').dropdown({
    allowAdditions: true
});