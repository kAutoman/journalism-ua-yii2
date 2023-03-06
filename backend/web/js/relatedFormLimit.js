$(function () {
    $('.limited-widget').each(function (index, obj) {
        var limitMax = $(obj).data('limit-max-related-forms');
        var limitMin = $(obj).data('limit-min-related-forms');

        if (limitMax > 0 || limitMin > 0) {
            var form = $('#main-form');
            var addBtn = $(obj).find('div.button-add button');
            var warningBlock = $(obj).find('h4.limit-warning');
            var showHideBtn = function () {
                if (limitMax > 0) {
                    $(addBtn).hide();
                    if (formCounter() < limitMax) {
                        $(addBtn).show();
                    }
                }
            };
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            var myObserver = new MutationObserver(showHideBtn);
            var obsConfig = {
                childList: true,
                attributes: false,
                characterData: false,
                subtree: false
            };

            var formCounter = function () {
                return $(obj).find('div.content-append').length;
            };

            showHideBtn();

            $(obj).find('.template-builder.ui-sortable').each(function() {
                myObserver.observe(this, obsConfig);
            });

            form.on('beforeValidate', function () {
                warningBlock.removeClass('red-error-block');

                var formsCount = formCounter();

                if ((limitMax > 0 && formsCount > limitMax) || (limitMin > 0 && formsCount < limitMin)) {
                    warningBlock.addClass('red-error-block');
                }

                return true;
            });

            form.on('afterValidate', function () {
                checkTabErrors();
            });

            form.on('beforeSubmit', function () {
                var formsCount = formCounter();
                return (limitMax === 0 || formsCount <= limitMax) && (limitMin === 0 || formsCount >= limitMin);
            });
        }
    })
});

