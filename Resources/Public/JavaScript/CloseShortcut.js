/**
 * Based on ./SaveShortcut.js - just a way to leave the
 * edit-/create view of a new record faster.
 */
define('TYPO3/CMS/SiteCore/CloseShortcut', ['ckeditor', 'jquery'], function (CKEDITOR, $) {
    'use strict';

    // Registers event for ESC shortcut, when TBE_EDITOR is available.
    // When the event is triggered, it performs .blur() on current focused item
    // and submit the current form.

    if (typeof TBE_EDITOR === 'object') {
        var closeForm = function () {
            var focusItem = document.querySelector(':focus');

            if (focusItem) {
                focusItem.blur();
            }

            $('a.btn.t3js-editform-close').trigger('click');
        };

        window.addEventListener('keyup', function (event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeForm();
            }
        });

        CKEDITOR.on('instanceCreated', function (e) {
            var editor = e.editor;

            editor.on('contentDom', function () {
                editor.document.on('keyup', function (event) {
                    if (event.data.$.key === 'Escape') {
                        try {
                            event.data.$.preventDefault();
                        } catch (err) {
                            // 
                        }

                        closeForm();

                        return false;
                    }
                });
            });
        });
    }
});
