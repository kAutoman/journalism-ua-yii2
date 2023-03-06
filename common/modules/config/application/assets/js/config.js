$(function () {
    //openActiveTab();

    // reload window after file upload
    $('#config-entity-form input:file').on('fileuploaded', function (event, file, previewId, index) {
        //fileActionHandler();
        var form = $('#' + previewId).closest('.form-group.has-error');
        if (form.length) {
            form.removeClass('has-error');
            form.find('.help-block').empty();
        }
    });

    // fix file upload error after delete file
    $(document).on('click', '#config-entity-form .kv-file-remove', function () {
        var self = $(this);
        self.closest('.file-preview-thumbnails').closest('.file-preview').find('.kv-fileinput-error').hide();
        self.closest('.file-preview-frame').remove();
        //fileActionHandler();
    });

    // check model rules and pick out tab with errors
    $(document).on("afterValidate", "#config-entity-form", function (event, messages, errorAttributes) {
        var tabs = 'ul.nav-tabs',
            tabsObj = $(tabs + ' a');

        if (tabsObj.length > 0) {
            tabsObj.removeClass('tab-error');
        }

        $(this).addClass('error');
        for (k in errorAttributes) {
            var tabId = $(errorAttributes[k].input).closest('div.tab-pane').attr('id');
            if (tabId !== '' || tabId !== undefined) {
                var errorTab = $(tabs).find('a[href="#' + tabId + '"]');
                if (errorTab.length > 0) {
                    errorTab.addClass('tab-error');
                }
            }
        }
        if (errorAttributes.length < 1) {
            $(this).removeClass('error');
        }

    });
});
$(document).on('submit', '#config-entity-form', function (e) {

    if ($(this).hasClass('error')) {
        return false;
    }
});
/**
 * Redirect  to active tab after file handlers
 */
//function fileActionHandler() {
//    var activeTabHash = $('div.tab-pane.active').attr('id');
//    if (activeTabHash !== undefined) {
//        window.location.href = window.location.origin + window.location.pathname + '#' + activeTabHash;
//        window.location.reload(true);
//    }
//}

///**
// * Open active tan
// */
//function openActiveTab() {
//    var pageHash = window.location.hash,
//        activeTab;
//
//    if (pageHash) {
//        activeTab = $('a[href="' + pageHash + '"]');
//        if (activeTab.length > 0) {
//            activeTab.trigger('click');
//        }
//    }
//}
