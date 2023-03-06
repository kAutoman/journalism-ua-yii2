$(document).on('ready', function () {
    initSortable();
    hljs.initHighlightingOnLoad();
});

$(document).on('click', '.add-content-builder', function (e) {

    e.preventDefault();

    var link = $(this);
    var builderModel = $('.add-content-builder-list').find('option:selected').val();
    var key = 0;
    link.closest('form').find('.builder-item').each(function (indx, e) {

        if(key < parseInt($(e).attr('data-key-idx'))){
            key = parseInt($(e).attr('data-key-idx'));
        }
    });

    key = ++key;

    $.ajax({
        url: link.data('href'),
        dataType: 'html',
        type: 'POST',
        data: $.extend(link.data('params'), {builderModel: builderModel}, {key: key}),
        success: function (html) {

            $('.builder-container').append(html);
            link.data('key', key + 1);

            var last = $('.builder-item').last();
            $('html, body').animate({
                scrollTop: $(last).offset().top - 60 + 'px'
            }, 'fast');

            initSortable();
        },
        error: function (a, text) {
            console.log(text);
        }
    });
});




$(document).on('click', '.js-collapse', function (event) {
    event.preventDefault();
    $(this).parents('.builder-item').find('.collapse-content').toggleClass('active');
});

$(document).on('click', '.content-row-trash', function (event) {
    event.preventDefault();
    if ($(this).hasClass('delete-form-builder')) {
        var el = $(this);
        $.ajax({
            url: '/builder/builder/delete-builder?id=' + $(this).attr('data-id'),
            dataType: 'json',
            type: 'GET',
            success: function (json) {

                var row = el.closest('.builder-item');
                row.remove();
            },
            error: function (a, text) {
                console.log(text);
            }
        });

    } else {
        var row = $(this).closest('.builder-item');
        row.remove();
    }


});

$(document).on('click', '.content-row-trash', function (event) {
    event.preventDefault();
    var row = $(this).closest('.builder-item');
    row.remove();
});

$(document).on('click', '.content-row-sort', function (e) {
    e.preventDefault();

    var link = $(this);
    link.parent().parent().find('.content-form').toggle();
});

function initSortable() {
    $(".builder-container").sortable({
        items: '> .builder-item',
        handle: ".content-row-trigger-sort",
        update: function (event, ui) {
            var _sorter = $(this);

            _sorter.find('.builder-item').each(function (k, v) {
                var _row = $(v);

                var inp = $(_row).find('input');
                inp.each(function (j, input) {
                    input.name = input.name.replace(/(\[\d+\])/, '[' + k + ']');
                });

                var textarea = $(_row).find('textarea');
                textarea.each(function (j, input) {

                    if ($(input).prev().hasClass('mce-tinymce')) {
                        //reinit every tinyMCE instance
                        tinymce.EditorManager.execCommand('mceRemoveEditor', true, input.id);
                        tinymce.EditorManager.execCommand('mceAddEditor', true, input.id);
                    }

                    input.name = input.name.replace(/(\[\d+\])/, '[' + k + ']');
                });

                var select = $(_row).find('select');
                select.each(function (j, input) {
                    input.name = input.name.replace(/(\[\d+\])/, '[' + k + ']');
                });
            });
        }
    });
}

//$(document).on('click', '.block-settings', function (event) {
//    event.preventDefault();
//    let that = $(this);
//    $.ajax({
//        url: '/builder/builder/settings?id=' + that.data('id'),
//        dataType: 'json',
//        type: 'GET',
//        success: function (json) {
//           hideModal('.modal')
//        },
//        error: function (a, text) {
//            console.log(text);
//        }
//    });
//});

// $(document).on('beforeSubmit', '#main-form', function(e) {
//     e.preventDefault();
//     var alert = $('.alert.alert-danger.hide');
//     var form = $(this);
//     $.post(
//         form.attr("action"),
//         form.serialize()
//     ).done(function(result) {
//         console.log(result);
//         alert.removeClass('hide');
//         var errorMsg = '<ul>';
//         $.each(result, function (index, value) {
//             var field = $('[name="'+index+'"], [type="hidden"][name="Page['+index+']"]');
//             field.parent('.form-group').addClass('has-error');
//             field.closest('.form-group').find('.help-block').html(value);
//             errorMsg += '<li>' + value + "</li>";
//             checkTabErrors();
//         });
//         alert.append(errorMsg + '</ul>');
//     });
//     return false;
// });
