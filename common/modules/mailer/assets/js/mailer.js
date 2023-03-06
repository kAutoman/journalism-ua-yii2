$(function () {
    const fieldClassPrefix = '#mailersetting-';
    const defaultCheckbox = '#mailer-default';

    const changedFields = [
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'auth',
        'smtp_username',
        'smtp_password',
    ];

    $(document).on('click', defaultCheckbox, function () {
        let isChecked = $(this).prop('checked');
        $.ajax({
            method: 'post',
            url: $(this).data('url'),
            success: function (response) {
                fill(response, isChecked);
            }
        });
    });
    
    function fill(response, populate) {
        $.each(changedFields, function (index, item) {
            let input = $(fieldClassPrefix + item);
            let value = response[item] || null;
            if (input.attr('type') === 'checkbox') {
                input.prop('checked', value)
            }
            input.prop('disabled', populate);
            input.val(value);
        })
    }
});
