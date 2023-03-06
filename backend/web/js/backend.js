function parseResponse(response) {
    if (response.replaces instanceof Array) {
        for (var i = 0, ilen = response.replaces.length; i < ilen; i++) {
            $(response.replaces[i].what).replaceWith(response.replaces[i].data);
        }
    }
    if (response.append instanceof Array) {
        for (i = 0, ilen = response.append.length; i < ilen; i++) {
            $(response.append[i].what).append(response.append[i].data);
        }
    }
    if (response.content instanceof Array) {
        for (i = 0, ilen = response.content.length; i < ilen; i++) {
            $(response.content[i].what).html(response.content[i].data);
        }
    }
    if (response.js) {
        $("body").append(response.js);
    }
    if (response.refresh) {
        window.location.reload(true);
    }
    if (response.redirect) {
        window.location.href = response.redirect;
    }
}

$(document).on('click', '#scroll-to-top', function (e) {
    e.preventDefault();
    $('html,body').animate({scrollTop: 0}, 1000);
});
// Add confirmation to change edit language on main form
$(document).on('afterValidateAttribute', '#main-form', function () {
    var langInputs = $('.edit-langs a');
    $.each(langInputs, function (index, item) {
        if (!item.hasAttribute('data-confirm')) {
            $(item).attr('data-confirm', $('.edit-langs').data('msg'));
        }
    });
    checkTabErrors();
});

//Multi-upload widget
$(function () {
    $(document).on('ready', function () {
        initImageSorting();
        fixMultiUploadImageCropUrl();
        checkTabErrors();
        metaButtonInit();
    });

    $(document).on('click', '.save-cropped', function (event) {
        event.preventDefault();
        var that = this;
        var url = $(that).attr('href');
        var data = {
            startX: $('#dataX').val(),
            startY: $('#dataY').val(),
            width: $('#dataWidth').val(),
            height: $('#dataHeight').val(),
            fileId: $('#fileId').val()
        };

        jQuery.ajax({
            'cache': false,
            'type': 'POST',
            'dataType': 'json',
            'data': 'data=' + JSON.stringify(data),
            'success':
                function (response) {
                    $('#preloader').fadeOut();
                    parseResponse(response);
                }, 'error': function (response) {
                $('#preloader').fadeOut();
                alert(response.responseText);
            }, 'beforeSend': function () {
                $('#preloader').fadeIn()
            }, 'complete': function () {
            }, 'url': url
        });

    });

    $(document).on('click', '.cancel-crop', function (event) {
        event.preventDefault();

        hideModal('.bs-cropper-modal');
    });

    $(document).on('hidden.bs.modal', '.bs-cropper-modal', function (e) {
        $(this).removeData('bs.modal');
        $('.modal-dialog .modal-content').empty();
        // hideModal('.modal');
    });

    /*$(document).on('shown.bs.modal', '.bs-cropper-modal', function (e) {
        var $dataX = $("#dataX"),
            $dataY = $("#dataY"),
            $dataHeight = $("#dataHeight"),
            $dataWidth = $("#dataWidth");

        $(".img-container > img").cropper({
            viewMode: 1,
            aspectRatio: $('.actual-aspect-ratio').val(),
            preview: ".img-preview",
            crop: function(data) {
                $dataX.val(Math.round(data.x));
                $dataY.val(Math.round(data.y));
                $dataHeight.val(Math.round(data.height));
                $dataWidth.val(Math.round(data.width));
            }
        });
    });*/

    $(document).on('click', '.crop-link', function () {
        var aspectRatio = $('.actual-aspect-ratio');
        var value = $(this).parents('div.form-group').find('.aspect-ratio').val();
        if (!aspectRatio.length) {
            $('.container').append('<input type="hidden" name="aspectRatio" class="actual-aspect-ratio" value="' + value + '">');
        } else {
            aspectRatio.val(value);
        }

    });

    $('.template-builder').sortable({
        handle: ".btn-template-mover",
        items: ".content-append",
        cancel: ''
    });

    //fix file delete after uploading
    $(document).on('click', '.kv-file-remove.new-uploaded-image', function () {
        var that = $(this);
        jQuery.ajax({
            'cache': false,
            'type': 'GET',
            'url': that.data('url')
        });
    });

    //fix file delete after uploading
    $(document).on('click', '.file-input .btn-file input', function(e){
        var multiple = $(this).closest('.form-group').find('.is-multiple-upload').val();
        if (!multiple) {
            var filePreview = $(this).parents('.file-input');
            var hasFiles = filePreview.find('.file-preview-frame').length;
            if (hasFiles) {
                alert('Only 1 file can be uploaded. Delete the previous one first');
                return false;
            }
        }
    });

    $(document).on('click', '.file-input.has-error .kv-file-remove', function (event) {
        // var thumbs = $(this).closest('.file-preview-thumbnails');
        // thumbs.empty();
        // thumbs.parent().find('.file-error-message').hide();
        $(this).fileinput('clear');
    });

    // Close meta data form after saving
    $(document).on('pjax:complete', '#meta-data-form-container', function () {
        var $closePopup = $(this).find('[data-action="close-popup"]');
        if ($closePopup.length) {
            var targetPopup = $closePopup.data('target');
            setTimeout(function () {
                hideModal(targetPopup);
            }, 500);
        }
    });
});
$("#modalMeta").on("show.bs.modal", function (event) {
    $(this).find(".modal-content").load(event.relatedTarget.href);
});

function metaButtonInit() {
    $('.meta-btn').each(function () {
        var href = $(this).attr('href');
        var key = $(this).data('key');
        var lang = $(this).data('lang');
        if (key) {
            href = href + key;
            if (lang) {
                href = href + '?' + lang;
            }
            $(this).attr('href', href);
        }
    });
    $('.meta-btn-ar').each(function (e) {
        var href = $(this).attr('href');
        var key = $(this).closest('.file-preview-frame').find('img').data('id');
        var lang = $(this).data('lang');
        if (key) {
            href = href + key;
            if (lang) {
                href = href + '?' + lang;
            }
            $(this).attr('href', href);
        }
    });
}


function hideModal(elem) {
    $(elem).modal('hide');
}

function initImageSorting() {
    if ($('.file-preview-thumbnails').length) {
        $('.file-preview-thumbnails').sortable({
            update: function (event, ui) {
                saveSort();
            }
        });
    }
}

function saveSort() {
    if ($('a#urlForSorting').length > 0) {
        var url = $('a#urlForSorting').attr('href');
    } else {
        var url = $('#urlForSorting').val();
    }

    var data = $(".kv-file-remove.btn").map(
        function () {
            return $(this).data('key');
        }
    ).get().join(",");


    jQuery.ajax({
        'cache': false,
        'type': 'POST',
        'dataType': 'json',
        'data': 'sort=' + data,
        'success':
            function (response) {
                parseResponse(response);
            }, 'error': function (response) {
            alert(response.responseText);
        }, 'beforeSend': function () {
        }, 'complete': function () {
        }, 'url': url
    });
}

function fixMultiUploadImageCropUrl() {
    $('.crop-link, .meta-data-link').each(function () {
        var href = $(this).attr('href');
        var key = $(this).data('key');
        var isKeyAdded = parseInt(href.match(/\d+/));

        if (key && isNaN(isKeyAdded)) {
            $(this).attr('href', href + key);
        }
    });
}

$(function () {
    //For checkbox in gridView (index page)
    $(document).on('click', '.ajax-checkbox', function () {
        var that = $(this);
        jQuery.ajax({
            'cache': false,
            'type': 'POST',
            'data': {
                'modelId': that.data('id'),
                'modelName': that.data('modelname'),
                'attribute': that.data('attribute')
            },
            'url': '/site/ajax-checkbox'
        });
    });

    //For file deleting in forms
    $(document).on('click', '.delete-file', function () {
        var that = $(this);
        jQuery.ajax({
            'cache': false,
            'type': 'POST',
            'data': {
                'modelId': that.data('modelid'),
                'modelName': that.data('modelname'),
                'attribute': that.data('attribute'),
                'language': that.data('language')
            },
            'success':
                function (response) {
                    if (response.error) {
                        alert('Не удалось удалить файл');
                    } else {
                        that.parent('.file-name').remove();
                    }
                }, 'error': function (response) {
                alert(response.responseText);
            },
            'url': '/site/delete-file'
        });
    });

    //For changing configuration form
    $(document).on('change', '.config-type', function (event) {
        event.preventDefault();
        var that = this;
        var url = $(that).data('url');
        var form = $(this).parents('form');
        var action = form.attr('action');

        jQuery.ajax({
            'cache': false,
            'type': 'POST',
            'dataType': 'json',
            'data': form.serialize() + '&action=' + action,
            'success':
                function (response) {
                    parseResponse(response);
                }, 'error': function (response) {
                alert(response.responseText);
            }, 'beforeSend': function () {
            }, 'complete': function () {
            }, 'url': url
        });
    });
});

function checkTabErrors() {
    var tabs = $('.tab-content .tab-pane');
    $('ul.nav.nav-tabs li a').removeClass('tab-error');

    if (tabs.length) {
        tabs.each(function (index, el) {
            var that = $(el);
            that.children().each(function () {
                if ($(this).hasClass('has-error, .red-error-block')) {
                    var id = that.attr('id');
                    $('a[href="#' + id + '"]').addClass('tab-error');
                    $(this).parents('.builder-item').addClass('error');
                    console.log($(this).parents('.builder-item'));
                }
            });
            if (that.find('.has-error, .red-error-block').length) {
                var id = that.attr('id');
                $('a[href="#' + id + '"]').addClass('tab-error');
                that.find('.has-error, .red-error-block').each(function () {
                    $(this).parents('.builder-item').addClass('error');
                    console.log($(this));
                })


            }
        });
    }
}

$(document).on('click', '.language_tabs  .swiper', function (e) {
    var $this = $(this),
        status = $this.parent().hasClass('active'),
        parent = $this.parents('.language_tabs'),
        tabs = parent.next('.tab-content');
    if (!status) {
        tabs.children().each(function () {
            var _that = $(this);
            _that.addClass('active');
            _that.children().children('.hidden_label').show();
        })
    } else {
        tabs.children().each(function (index) {
            var _that = $(this);
            if (index != 0) {
                _that.removeClass('active');
            }
            _that.children().children('.hidden_label').hide();
        })
    }
});


$(document).on('click', '.language_tabs > li', function () {
    var $this = $(this),
        parent = $this.parent(),
        tabs = parent.next(),
        indexMain = $this.index() - 1,
        swiper = parent.children('.checkbox');
    swiper.children('label').removeClass('active');
    swiper.children('label').children('input').prop('checked', false);


    tabs.children().each(function (index) {
        var _that = $(this);
        _that.removeClass('active');
        if (index == indexMain) {
            _that.addClass('active');
        }
        _that.children().children('.hidden_label').hide();
    })
});

$(function () {
    $(document).on('ready', function () {
        pagesInitBlocks();
    });
    $(document).on('change', '#page-type', function () {
        pagesInitBlocks();
    });

    function pagesInitBlocks() {
        let selected = parseInt($('#page-type option:selected').val());
        let initField = $('#builder-init');

        if (selected === 1) { //selected type === static builder
            initField.parent('.form-group').show();
        } else {
            initField.parent('.form-group').hide();
        }
    }
});
