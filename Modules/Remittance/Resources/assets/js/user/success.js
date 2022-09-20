'use strict';

$(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    //disabling F5
    function disable_f5(e) {
        if ((e.which || e.keyCode) == 116) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_f5);
    });

    //disabling ctrl+r
    function disable_ctrl_r(e) {
        if (e.keyCode == 82 && e.ctrlKey) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_ctrl_r);
    });