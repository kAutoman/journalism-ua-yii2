yii.giiAdvanced = (function ($) {
    return {
        init: function () {
            // model generator: hide class name input when table name input contains *
            $('#generator-tablename').change(function () {
                $('.field-generator-modelclass').toggle($(this).val().indexOf('*') == -1);
            });

            // model generator: translate table name to model class
            $('#generator-tablename, #generator-moduleid').on('blur', function () {
                var tableName = $(this).val();
                if (tableName && tableName.indexOf('*') === -1) {
                    var modelClass='';
                    $.each(tableName.split('_'), function() {
                        if(this.length>0)
                            modelClass+=this.substring(0,1).toUpperCase()+this.substring(1);
                    });
                    $('#generator-modelclass').val(modelClass);

                    var baseModelClass = $('#generator-basemodelclass');

                    if (baseModelClass.length && baseModelClass.val() == '') {
                        baseModelClass.val('common\\models\\' + modelClass);
                    }
                }
                var ns = $('#generator-ns');
                var moduleId = $('#generator-moduleid');
                var modelClassName = $('#generator-modelclass').val();
                if (moduleId.length && moduleId.val() && ns.length && modelClassName) {
                    ns.val('backend\\modules\\' + moduleId.val() + '\\models');
                    $('#generator-controllerclass').val('backend\\modules\\'
                        + moduleId.val() + '\\controllers\\' + modelClassName + 'Controller');
                }
            });

            // model generator: toggle query fields
            $('form #generator-createbasemodel').change(function () {
                $('form .field-generator-hideexistingbasemodel').toggle($(this).is(':checked'));
            });

            // hack
            var tableName = $('#generator-tablename').val();
            if (tableName) {
	            $('.field-generator-modelclass').toggle(tableName.indexOf('*') == -1);
            }
            $('form .field-generator-hideexistingbasemodel').toggle($('form #generator-createbasemodel').is(':checked'));

	        $('#generator-moduleid, #static-page-model-generator #generator-modelclassname').on('blur', function () {
		        var modelClassName = $('#generator-modelclassname').val();
		        var ns = $('#generator-ns');
		        var moduleId = $('#generator-moduleid');
		        if (moduleId.length && moduleId.val() && ns.length && modelClassName) {
			        ns.val('backend\\modules\\' + moduleId.val() + '\\models');
			        $('#generator-controllerclass').val('backend\\modules\\'
				        + moduleId.val() + '\\controllers\\' + modelClassName + 'Controller');
		        }
	        });

            $(document).on('blur', '#static-page-model-generator .list-cell__id input', function () {
                var description = $(this).parents('.multiple-input-list__item').first().find('.list-cell__description input');
                var text = this.value;
                var result = text.replace( /([A-Z0-9])/g, " $1" );
                var finalResult = result.charAt(0).toUpperCase() + result.slice(1).toLowerCase();
                description.val(finalResult);
            });
        }
    };
})(jQuery);

$(function(){
    $(document).ready(function () {
        showDbErrorCorrectly();
    });

    $(document).on('focusout', '#migrations-generator #generator-tablename', function (event) {
        var migrationName = $('input[name=migration-name-beginning]').val();
        $('#generator-migrationname').val(migrationName + this.value + '_table');
    });

    if (typeof $.fn.sortable !== 'undefined') {
        $("#generator-fields tbody").sortable();
    }
});

function showDbErrorCorrectly() {
    var error = $('.default-view-results pre');
    if (error.text() == 'Data base error!') {
        var dbError = $('body').contents().eq(0);
        error.text(dbError.text());
        dbError.remove();
    }
}
