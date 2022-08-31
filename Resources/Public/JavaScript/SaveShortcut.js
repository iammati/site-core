/** @credits https://bitbucket.org/t--3/save */

define('TYPO3/CMS/SiteCore/SaveShortcut', ['ckeditor', 'jquery'], function (CKEDITOR, $) {
    'use strict';

    // Registers event for CTRL+S shortcut, when TBE_EDITOR is available.
    // When the event is triggered, it performs .blur() on current focused item
    // and submit the current form.

    if (typeof TBE_EDITOR === 'object') {
        var submitForm = function () {
            var focusItem = document.querySelector(':focus');

            if (focusItem) {
                focusItem.blur();
            }

            $('button[form="EditDocumentController"][name="_savedok"]').trigger('click');
        };

        window.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && String.fromCharCode(event.which).toLowerCase() === 's') {
                event.preventDefault();
                submitForm();
            }
        });

        CKEDITOR.on('instanceCreated', function (e) {
            var editor = e.editor;

            editor.on('contentDom', function () {
                var isCtrl;

                editor.document.on('keyup', function (event) {
                    if (event.data.$.keyCode === 17) {
                        isCtrl = false;
                    }
                });

                editor.document.on('keydown', function (event) {
                    if (event.data.$.keyCode === 17) {
                        isCtrl = true;
                    }

                    if (event.data.$.keyCode === 83 && isCtrl === true) {
                        try {
                            event.data.$.preventDefault();
                        } catch (err) {
                            // 
                        }

                        submitForm();

                        return false;
                    }
                });
            });
        });
    }
});
