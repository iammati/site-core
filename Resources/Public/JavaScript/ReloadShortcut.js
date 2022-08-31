/**
 * Registers a simple CTRL+R which is always available
 * and automatically reload the iframe of the TYPO3 backend,
 */
define('TYPO3/CMS/SiteCore/ReloadShortcut', [
    'ckeditor',
    'jquery',
    'TYPO3/CMS/Backend/Modal',
    'TYPO3/CMS/Backend/Severity'
], function (CKEDITOR, $, Modal, Severity) {
    'use strict';

    var reloadIframe = function () {
        var focusItem = document.querySelector(':focus');

        if (focusItem) {
            focusItem.blur();
        }

        if (typeof TBE_EDITOR !== 'object') {
            return location.reload();
        }

        Modal.advanced({
            type: Modal.types.default,
            severity: Severity.warning,

            title: 'Are you sure you want to reload?',
            content: 'Reloading won\'t save your changes and will be lost. Continue?',

            size: Modal.sizes.default,

            buttons: [
                {
                    text: 'Cancel',
                    name: 'cancel',
                    btnClass: 'btn-default',

                    dataAttributes: {
                        action: 'cancel'
                    },

                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');
                    }
                },

                {
                    text: 'Reload',
                    name: 'reload',
                    btnClass: 'btn-warning',

                    dataAttributes: {
                        action: 'reload'
                    },

                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');
                        location.reload();
                    }
                },

                {
                    text: 'Save and reload',
                    name: 'save-reload',
                    btnClass: 'btn-warning',

                    dataAttributes: {
                        action: 'save-reload'
                    },

                    trigger: function() {
                        Modal.currentModal.trigger('modal-dismiss');

                        var focusItem = document.querySelector(':focus');

                        if (focusItem) {
                            focusItem.blur();
                        }

                        $('button[form="EditDocumentController"][name="_savedok"]').trigger('click');

                        location.reload();
                    }
                }
            ]
        });
    };

    window.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && String.fromCharCode(event.which).toLowerCase() === 'r') {
            event.preventDefault();
            reloadIframe();
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

                if (event.data.$.keyCode === 82 && isCtrl === true) {
                    try {
                        event.data.$.preventDefault();
                    } catch (err) {
                        // 
                    }

                    reloadIframe();

                    return false;
                }
            });
        });
    });
});
