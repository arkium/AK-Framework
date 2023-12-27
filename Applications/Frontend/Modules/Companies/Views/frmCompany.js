var o = {
    form: 'frmCompany',
    data: {
        entity_id: $('#entity_id').prop('value')
    },
    fields: {
        code: 'empty',
        organisation: 'empty'
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            entity_id: data[0],
            code: data[2],
            organisation: data[3],
            address1: data[5],
            address2: data[6],
            postal_code: data[7],
            city: data[8],
            state: data[9],
            country: data[10],
            http_url: data[11],
            note: data[19],
            status: data[20],
            update_time: data[21],
            created_time: data[22]
        })
    }
};
$.initForm(o)