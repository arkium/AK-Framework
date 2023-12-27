(function ($) {
	$.fn.clickTime = function (as_period_id, as_user_id) {
		$(this).each(function () {
			$(this).on('dblclick', function (e) {
				e.stopImmediatePropagation();
				var p = "";
				if ($.hasData(this) && $(this).data('p') == '1') {
					p = "&p=1";
				}
				if ($.hasData(this) && typeof $(this).data('time_id') !== 'undefined') {
					document.location.href = "frmtimesheet?id=" + $(this).data('time_id') + "&op=edit" + p;
				} else {
					document.location.href = "frmtimesheet?period_id=" + as_period_id + "&user_id=" + as_user_id + "&date=" + $(this).data('date') + "&task_id=" + $(this).data('task_id') + p;
				}
			});
		});
	}

	// **********************************************
	// * Gestion de la timesheet
	// **********************************************
	$.IsNumeric = function (sText) {
		var ValidChars = "0123456789";
		var IsNumber = true;
		var Char;
		for (i = 0; i < sText.length && IsNumber == true; i++) {
			Char = sText.charAt(i);
			if (ValidChars.indexOf(Char) == -1) {
				IsNumber = false;
			}
		}
		return IsNumber;
	}

	$.TestTime = function (champ) {
		function pad(n) {
			return (n < 10 && n.length < 2) ? '0' + n : n
		}
		if (champ.value.length > 5)
			return false;
		var Pos_1 = champ.value.substr(1, 1);
		var Pos_2 = champ.value.substr(2, 1);
		var Separateur = '';
		if (!$.IsNumeric(Pos_1))
			var Separateur = Pos_1;
		else if (!$.IsNumeric(Pos_2))
			var Separateur = Pos_2;
		var Pos = champ.value.indexOf(Separateur);
		var Hrs = $.IsNumeric(champ.value.substring(0, Pos)) ? champ.value.substring(0, Pos) : -1;
		var Mins = $.IsNumeric(champ.value.substring(Pos + 1, 5)) ? champ.value.substring(Pos + 1, 5) : -1;
		if (Mins >= 0 && Mins < 60 && Separateur == ":") {
		} else if (Mins >= 0 && Mins < 100 && (Separateur == "," || Separateur == ".")) {
			if (Mins < 10 && Mins.length < 2)
				Mins = Mins + '0';
			Mins = (Mins * 60) / 100;
			Mins = Mins.toFixed(0);
		} else if (Pos == 0 && Separateur == "") {
			Hrs = $.IsNumeric(champ.value.substring(0, 2)) ? champ.value.substring(0, 2) : '0';
			if (Hrs == '')
				Hrs = '0';
			Mins = '0';
		} else
			return false;
		if (Hrs >= 0 && Hrs < 24) {
			champ.value = pad(Hrs) + ":" + pad(Mins);
			return true;
		} else
			return false;
	}

	$.UpTimeTotal = function (date_id, val_new, val_old) {
		function pad(n) {
			return n < 10 ? '0' + n : n
		}
		var n = $('#' + date_id).text();
		if (n == '')
			n = "00:00";
		n = n.split(":");
		var sec_total = (Number(n[0]) * 3600) + (Number(n[1]) * 60);
		if (val_old == false) {
			var sec_old = 0;
		} else {
			var n = val_old.split(":");
			var sec_old = (Number(n[0]) * 3600) + (Number(n[1]) * 60);
		}
		var n = val_new.split(":");
		var sec_new = (Number(n[0]) * 3600) + (Number(n[1]) * 60);
		var sec_total_new = sec_total - sec_old + sec_new;
		var Hrs = ((sec_total_new - (sec_total_new % 3600)) / 3600);
		var Mins = (sec_total_new % 3600) / 60;
		var text = pad(Hrs) + ":" + pad(Mins);
		$('#' + date_id).text(text);
	}

	$.TestDate = function (champ) {
		if (champ.value == "")
			return true;
		var date = moment(champ.value, 'YYYY-MM-DD');
		var startDate = moment().subtract('years', 10);
		if (!date.isValid() || !date.isAfter(startDate, 'year'))
			return false;
		champ.value = date.format('YYYY-MM-DD');
		return true;
	}

})(jQuery);
