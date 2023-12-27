/*
 * Arkium Module - Notify for UI Semantic
 * http://github.com/semantic-org/semantic-ui/
 *
 *
 * Copyright 2015 Contributor
 * Released under the MIT license
 * http://opensource.org/licenses/MIT
 *
 */

;
(function ($, window, document, undefined) {

    "use strict";

    $.fn.notify = function (parameters) {
        var defaults = {
            id: 'arkium-notification',
            title: "",
            message: "",
            status: "positive",
            permanent: false,
            timeout: 5,
            fade: true,
            width: 300,
            callback: null
        };
        var parameters = $.extend(defaults, parameters);
        var note_area = $('#' + parameters.id);
        if (note_area.length == 0) {
            var attr = {
                "id": parameters.id,
            };
            var css = {
                "display": "block",
                "position": "fixed",
                "top": "10px",
                "right": "10px",
                "z-index": "1000"
            };
            note_area = $("<div>", attr).css(css).appendTo("body");
        }

        var note = $("<div>").addClass("ui " + parameters['status'] + " message").hide();
        if (!parameters['permanent']) {
            note.prepend($('<i>').addClass("close icon").on("click", function () {
                $(this).closest('.message').transition('fade up').remove();
                return false;
            }));
        }
        if (parameters['title'] !== "") {
            note.append($('<div>').addClass('header').append(parameters['title']));
        }
        note.append("<p>" + parameters['message'] + "</p>");
        note_area.append(note);
        if (!parameters['permanent']) {
            if (parameters['timeout'] != 0) {
                if (parameters['fade']) {
                    note.transition('fade down').delay(parameters['timeout'] * 1000).queue(function () {
                        $(this).closest('.message').transition('fade up').remove();
                        if ($.isFunction(parameters.callback))
                            parameters.callback();
                    });
                } else {
                    note.delay(parameters['timeout'] * 1000).queue(function () {
                        $(this).closest('.message').remove();
                        if ($.isFunction(parameters.callback))
                            parameters.callback();
                    });
                }
            }
        }
    };

    $.notify = $.fn.notify;
})(jQuery);
