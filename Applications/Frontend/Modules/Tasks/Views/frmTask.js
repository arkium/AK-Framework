var o = {
    form: 'frmTask',
    data: {
        task_id: $('#task_id').prop('value')
    },
    fields: {
        code: 'empty',
        name: 'empty',
        invoicing_entity_id : 'empty',
        customer_id : 'empty',
        task_type_id : 'empty',
        closing_date: ['empty', 'dateISO', 'dateValide'],
        intermediate_id : 'empty',
        start_date: ['empty', 'dateISO', 'dateValide'],
        end_date: ['empty', 'dateISO', 'dateValide'],
        status: 'empty',
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            task_id: data[0],
            code: data[1],
            name: data[2],
            invoicing_entity_id: data[3],
            customer_id: data[4],
            task_type_id: data[5],
            closing_date: data[6],
            intermediate_id: data[7],
            num_proj: data[8],
            milestone_type_id: data[9],
            start_date: data[10],
            end_date: data[11],
            project_proposal: data[12],
            note: data[13],
            status: data[14],
            update_time: data[15],
            created_time: data[16],
            staff: data['user_id']
        });
    }
};
$.initForm(o)