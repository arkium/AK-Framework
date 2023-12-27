var o = {
    form: 'frmPeriod',
    data: {
        period_id: $('#period_id').prop('value')
    },
    fields: {
        start_date: ['empty', 'dateISO', 'dateValide'],
        end_date: ['empty', 'dateISO', 'dateValide']
    },
    GetData: function (ele, data) {
        ele.form('set values', {
            period_id: data[0],
            start_date: data[1],
            end_date: data[2],
            status: data[3],
            update_time: data[4],
            created_time: data[5]
        })
    }
};
$.initForm(o)