/**
 * Legacy helper. Admin Summernote is initialized globally from layouts/adminbase.blade.php
 * via initAdminSummernote() for every description / long-text field.
 */
(function () {
    'use strict';
    if (typeof window.initAdminSummernote === 'function') {
        window.initAdminSummernote(document);
    }
})();
