(function() {
    'use strict';
    window.wpudebugtoolbar_r = function(f) {
        /loaded|complete/.test(document.readyState) ? f() : setTimeout("window.wpudebugtoolbar_r(" + f + ")", 9);
    };

    window.wpudebugtoolbar_r(function() {
        var $ = function(id) {
            return document.getElementById(id);
        };

        var toolbar = $('wputh-debug-toolbar');
        /* Debug queries */
        if ($('wputh-debug-display-queries')) {
            $('wputh-debug-display-queries').onclick = function() {
                toolbar.setAttribute('data-show-queries', '1');
            };
            $('wputh-debug-hide-queries').onclick = function() {
                toolbar.setAttribute('data-show-queries', '');
            };
        }

        /* Debug hooks */
        if ($('wputh-debug-display-hooks')) {
            $('wputh-debug-display-hooks').onclick = function() {
                toolbar.setAttribute('data-show-hooks', '1');
            };
            $('wputh-debug-hide-hooks').onclick = function() {
                toolbar.setAttribute('data-show-hooks', '');
            };
        }
    });

}());