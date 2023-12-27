var o = {
    form: 'frmClient',
    data: {
        entity_id: $('#entity_id').prop('value')
    },
    fields: {
        code: 'empty',
        organisation: 'empty',
    	entity_group_id: 'empty',
        http_url: 'url'
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            entity_id: data[0],
            entity_type_id: data[1],
            code: data[2],
            organisation: data[3],
            entity_group_id: data[4],
            address1: data[5],
            address2: data[6],
            postal_code: data[7],
            city: data[8],
            state: data[9],
            country: data[10],
            http_url: data[11],
            inception_date: data[12],
            legal_form: data[13],
            juridiction: data[14],
            opportunity_client: data[15],
            dateLastRiskAssessment: data[16],
            note: data[19],
            status: data[20],
            update_time: data[21],
            created_time: data[22]
        });
        if (data[17] == '1') {
            $("#frmClient #direct").prop("checked", true).val("1");
            $("#frmClient #indirect").prop("checked", false).val("0");
        } else {
            $("#frmClient #direct").prop("checked", false).val("1");
            $("#frmClient #indirect").prop("checked", true).val("0");
        }
    }
};
$.initForm(o)