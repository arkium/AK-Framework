var oTable;
var oDefault = {
    "bJQueryUI": false,
    "bAutoWidth": false,
    "aLengthMenu": [[10, 15, 30, -1], [10, 15, 30, "All"]],
    "oLanguage": {
        "sSearch": "Search all columns:"
    },
    "bProcessing": true,
    "bServerSide": true,
    "sAjaxSource": "",
    "fnCreatedRow": function (nRow, aData, iDataIndex) {
        nRow.setAttribute("onclick", "");
    },
    "fnFooterCallback": function (nFoot, aData, iStart, iEnd, aiDisplay) {
        $.afficheButtons(true);
        // $.groupsDisplay(oSettings);
        //$('.dataTables_paginate').addClass('ui buttons');
        //$('.dataTables_paginate a').addClass('ui button');
        $('#exemple_previous').text('').append('<i class="big angle left icon"></i>');
        $('#exemple_next').text('').append('<i class="big angle right icon"></i>');
    	//$('.paginate_disabled_previous').prop('disabled', 'disabled');
        //$('.paginate_disabled_next').prop('disabled', 'disabled');
		//$('.paginate_enabled_previous').removeAttr('disabled');
		//$('.paginate_enabled_next').removeAttr('disabled');
        //$('.paginate_disabled_previous i').removeClass('angle left');
        //$('.paginate_disabled_next i').removeClass('angle right');
        //$('.paginate_enabled_previous i').addClass('angle left');
        //$('.paginate_enabled_next i').addClass('angle right');
    },
    "fnHeaderCallback": function (nHead, aData, iStart, iEnd, aiDisplay) {
        if ($("#dataTables_header").hasClass("ui stackable grid") === false) {
            var header = $("<div id='dataTables_header' class='ui stackable grid'></div>").prependTo(".dataTables_wrapper");
            var footer = $("<div id='dataTables_footer' class='ui stackable grid'></div>").appendTo(".dataTables_wrapper");

            var filterDialog = $('<div id="filterDialog" class="ui small modal"></div>').appendTo("body");
            var filterBody = $('<i class="close icon"></i><div class="header">Filter</div>').appendTo(filterDialog);
            var filterForm = $('<div class="content ui form"></div>').appendTo(filterDialog);

            var btnFilter = $("<button>", {
                "id": "btnSearch",
                "class": "ui button",
                "html": '<i class="search icon"></i>'
            }).appendTo(header).wrap("<div class='six wide column right aligned'></div>");

            filterDialog.modal('attach events', btnFilter, 'show');

            // Header
            $('.dataTables_length').addClass('field').appendTo(filterForm);
            $('.dataTables_filter').addClass('field').appendTo(filterForm);

            // Footer
            $('.dataTables_info').appendTo(footer).wrap("<div class='center aligned six wide column'></div>");
            $('.dataTables_paginate').appendTo(footer).wrap("<div class='right aligned five wide column'></div>");
            $('.dataTables_processing').addClass('ui active').wrapInner('<div class="ui active text loader"></div>');

            $("#dataTables_header").prepend($("#title_table"));
            $("#actionBtn").prependTo(footer).wrap("<div id='actionsBtn' class='five wide column'></div>");
        }
    }
	//fnDrawCallback
	//fnPreDrawCallback
};
var oST = $.extend(true, {}, oDefault);

$(function () {
    $.isEmpty = function (obj) {
        if (typeof obj == 'undefined' || obj === null || obj === '')
            return true;
        if (typeof obj == 'number' && isNaN(obj))
            return true;
        if (obj instanceof Date && isNaN(Number(obj)))
            return true;
        return false;
    };

    $.afficheButtons = function (value) {
        if ($.isEmpty(value))
            value = false;
        value = value ? true : false;
        $("#actionBtn:button").prop("disabled", value);
        $("#actionBtn button[ovisible$='true']").prop("disabled", false);
        $("#actionBtn.uk-nav-dropdown > li").prop("hidden", value);
        $("#actionBtn.uk-nav-dropdown > li[ovisible$='true']").prop("hidden", false);

        if (value) {
            $("#actionBtn > div.button").addClass("disabled");
            $("#actionBtn .menu .item").addClass("disabled");
        } else {
            $("#actionBtn > div.button").removeClass("disabled");
            $("#actionBtn .menu .item").removeClass("disabled");
        }
        $("#actionBtn > div.button[ovisible$='true']").removeClass("disabled");
        $("#actionBtn .menu .item[ovisible$='true']").removeClass("disabled");
    };

    $.fnGetSelected = function (oTableLocal) {
        return oTableLocal.$('tr.row_selected');
    };

    $.clickLine = function () {
        var self = $(this);
        if (typeof self.prop('id') === 'undefined')
            return false;
        if (self.hasClass('row_selected')) {
            self.removeClass('row_selected');
            $.afficheButtons(true);
        } else {
            if (self.children().hasClass('dataTables_empty') != true) {
                if (oTable)
                    oTable.$('tr.row_selected').removeClass('row_selected');
                else
                    self.parent('tbody').find('tr.row_selected').removeClass('row_selected');
                self.addClass('row_selected');
                $.afficheButtons(false);
            }
        }
    };

    $.fnMessage = function (Msg) {
        var Display = false, Logout = false, closeDialog = false;
        Msg.title = (typeof Msg.title === 'undefined') ? 'Information' : Msg.title;
        Msg.status = (typeof Msg.status === 'undefined') ? 'warning' : Msg.status;

        switch (Msg.textStatus) {
            case 'timeout':
            case 'error':
            case 'abort':
            case 'parsererror':
                Display = true;
                closeDialog = true;
                Msg.title = 'Error';
                Msg.reponse = "An error occurred when the AJAX request." + "<br/>status : " + Msg.textStatus + "<br/>errorThrown : " + Msg.errorThrown;
                break;
        }
        if (Msg.reponse !== true) {
            Display = true;
        }
        if (Display) {
            $.notify({
                title: Msg.title,
                message: Msg.reponse,
                status: Msg.status,
                timeout: 5
            });
            return false;
        }
        return true;
    };

    $.groupsDisplay = function (oSettings) {
        if (oSettings.aiDisplay.length == 0)
            return;
        var nTrs = $('#exemple tbody tr');
        var iColspan = nTrs[0].getElementsByTagName('td').length;
        var sLastGroup = "";
        for (var i = 0; i < nTrs.length; i++) {
            var iDisplayIndex = oSettings._iDisplayStart + i;
            var sGroup = oSettings.aoData[oSettings.aiDisplay[iDisplayIndex]]._aData[0];
            if (sGroup != sLastGroup) {
                var nGroup = document.createElement('tr');
                var nCell = document.createElement('td');
                nCell.colSpan = iColspan;
                nCell.className = "group";
                nCell.innerHTML = sGroup;
                nGroup.appendChild(nCell);
                nTrs[i].parentNode.insertBefore(nGroup, nTrs[i]);
                sLastGroup = sGroup;
            }
        }
    }

//    $(window).load(function () {
      $(window).on("load", function () {
        $("#loading").delay(300).fadeOut(300, function () {
            $("#content").fadeIn(300);
        });
    });
});