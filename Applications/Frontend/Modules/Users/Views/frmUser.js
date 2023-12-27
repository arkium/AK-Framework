var o = {
    form: 'frmUser',
    data: {
        user_id: $('#user_id').prop('value')
    },
    fields: {
        code: 'empty',
        first_name: 'empty',
        last_name: 'empty',
        username: 'empty',
        password: 'empty',
        invoicing_entity_id: 'empty',
        email_address: ['empty', 'email']
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            user_id: data[0],
            code: data[1],
            first_name: data[2],
            last_name: data[3],
            email_address: data[4],
            level: data[5],
            username: data[6],
            password: data[7],
            invoicing_entity_id: data[8],
            contract: data[9],
            status: data[10],
            codeupdate_time: data[11],
            created_time: data[12],
            typetimesheet: data[14]
        })
    }
};
$.initForm(o)