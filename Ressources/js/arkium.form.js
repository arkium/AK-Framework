/*
 * Arkium Module - Form for UI Semantic and Framework ARKIUM
 * http://github.com/semantic-org/semantic-ui/
 *
 *
 * Copyright 2012-2019 Arkium SCS
 * Released under the MIT license
 * http://opensource.org/licenses/MIT
 *
 */

;
var that = this;
(function ($) {
	"use strict";

	$.initForm = function (options) {
		var defaults = {
			form: '',
			id: '',
			data: {
				op: 'view',
				token: as_token,
			},
			fields: {},
			GetData: null
		};
		var o = $.extend({}, defaults, options);
		o.data = $.extend({}, defaults.data, options.data);

		var form = $('#' + o.form);
		var op = $('#op').prop('value');

		// le script dropdown pose un soucis dans les select (TODO)
		//$('select:not(".search")').dropdown();
		$('#tab-' + o.form + ' .menu .item').tab({
			context: $('#tab-' + o.form)
		});

		form.form({
			rules: {
				dateISO: function (value) {
					return (value === undefined || '' === value || $.isArray(value) && value.length === 0) || /^\d{4}[\/\-]\d{2}[\/\-]\d{2}$/.test(value);
				},
				dateValide: function (value) {
					return (value === undefined || '' === value || $.isArray(value) && value.length === 0) || !/Invalid|NaN/.test(new Date(value).toString());
				},
				url: function (value) {
					return (value === undefined || '' === value || $.isArray(value) && value.length === 0) || /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
				}
			},
			prompt: {
				dateISO: '{name} must have a format date : YYYY/MM/DD',
				dateValide: '{name} must have a valid date',
				url: '{name} must be a valid URL beginning with http://'
			},
			fields: o.fields
		});
		switch (op) {
			case "view":
			case "edit":
				console.log("edit");
				$.ajax({
					type: form.prop('method'),
					url: form.prop('action'),
					dataType: 'json',
					data: o.data,
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (op == 'view') {
							o.GetData(form, data);
							$('#' + o.form + ' :input:not([ovisible$="true"])').prop("disabled", "disabled");
							$('#' + o.form + ' .ui.dropdown').addClass("disabled");
						} else if (op == "edit") {
							o.GetData(form, data);
						}
					}
				});
				break;
			case "add":
				break;
		}

		$('#btnSave').click(function (event) {
			event.stopPropagation(); // Ajout d'un stoppeur
			form.form('validate form');
			if (form.form('is valid') === true) {
				$('#' + o.form + ' > #btnSave').addClass('disabled'); // Correction sur le pointage du bouton
				$.ajax({
					type: form.prop('method'),
					url: form.prop('action'),
					dataType: 'json',
					data: form.serialize(),
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (data.reponse === true) {
							$.notify({
								title: 'Data saved successfully!',
								message: 'You will be redirected to the previous page.',
								timeout: 3,
								callback: function (e) {
									$("#btnCancel").trigger("click");
								}
							});
						}
						$.fnMessage(data);
					}
				});
			}
		});

		$('#btnCancel').click(function (e) {
			e.preventDefault();
			that.document.location.href = $(this).data('return');
		});
	}

	$.fn.listSelect = function (options) {
		var defaults = {
			form: null,
		};

		var o = $.extend({}, defaults, options);
		var el = this;
		var form = $('#' + o.form);
		var field_change = $('#tf_name_edit');
		var field_add = $('#tf_name_add');

		var _load = function () {
			$.ajax({
				type: 'POST',
				url: form.prop('action'),
				dataType: 'json',
				data: {
					op: 'list',
					token: as_token
				},
				success: function (data) {
					el.empty();
					if (data !== null) {
						$.each(data, function (key, value) {
							el.append('<option value="' + value.id + '">' + value.name + '</option>');
						});
					}
				}
			});
		}

		_load();

		el.change(function () {
			field_change.val(el.find('option:selected').text());
		});

		$('#tf_return').click(function (e) {
			e.preventDefault();
			that.document.location.href = $(this).data('return');
		});

		$('#tf_btn_add').on('click', function () {
			if (field_add.is(':disabled') === false) {
				$.ajax({
					type: 'POST',
					url: form.prop('action'),
					dataType: 'json',
					data: {
						op: 'add',
						field_add: field_add.val(),
						token: as_token
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (data.reponse === true) {
							_load();
							field_add.val("");
						}
						$.fnMessage(data);
					}
				});
			}
		});

		$('#tf_btn_edit').on('click', function () {
			if (field_change.is(':disabled') === false) {
				$.ajax({
					type: 'POST',
					url: form.prop('action'),
					dataType: 'json',
					data: {
						op: 'edit',
						id: el.val(),
						field_change: field_change.val(),
						token: as_token
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (data.reponse === true) {
							_load();
							field_change.val("");
						}
						$.fnMessage(data);
					}
				});
			}
		});

		$("#tf_btn_remove").on('click', function () {
			if (field_change.is(':disabled') === false) {
				$.ajax({
					type: 'POST',
					url: form.prop('action'),
					dataType: 'json',
					data: {
						op: 'delete',
						id: el.val(),
						token: as_token
					},
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (data.reponse === true) {
							_load();
							field_change.val("");
						}
						$.fnMessage(data);
					}
				});
			}
		});

		field_add.focus(function () {
			el.val("");
			field_change.val("");
		});

		field_change.focus(function () {
			field_add.val("");
		});
	}

	$.fnDialog = function (options) {
		var defaults = {
			title: 'Titre',
			message: "Message"
		};
		var o = $.extend(defaults, options);
		var note = $("<div>").attr('id', 'dial').addClass("ui basic modal").hide();
		var no = $('<div>').addClass("ui red basic cancel inverted button").append('<i class="remove icon"></i>No');
		var yes = $('<div>').addClass("ui green basic ok inverted button").append('<i class="checkmark icon"></i>Yes');
		var actions = $('<div>').addClass("two fluid ui inverted buttons").append(no).append(yes);
		note.append($('<i>').addClass("close icon"));
		note.append($('<div>').addClass("header").append(o.title));
		note.append($('<div>').addClass("content").append($('<div>' + o.message + '</div>').addClass('description')));
		note.append($('<div>').addClass("actions").append(actions));
		note.appendTo("body");
		return note;
	};

	$.DlgDelete = function (options) {
		var defaults = {
			url: '',
			dialog: {
				title: 'Titre',
				message: "Message"
			},
			data: {
				op: 'delete',
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			},
			callback: null
		};
		var o = $.extend(defaults, options);
		var choice = null;

		var dial = $.fnDialog(o.dialog);
		dial.modal({
			blurring: true,
			closable: false,
			onDeny: function () {
				console.log('Dialog quit');
				return true;
			},
			onApprove: function () {
				console.log('Item deleted');
				$.ajax({
					type: 'post',
					url: o.url,
					dataType: 'json',
					data: o.data,
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (oTable)
							if (data.reponse == true) {
								$.afficheButtons(true);
								console.log(oTable);
								if (oTable != undefined)
									oTable.fnDeleteRow(o.anSelected);
							}
						$.fnMessage(data);
						if ($.isFunction(o.callback))
							o.callback();
					}
				});
				return true;
			}
		}).modal('show');
	};

	$.fnModal = function (options) {
		var defaults = {
			url: '',
			dialog: {
				title: 'Titre',
				message: "Message"
			},
			data: {
				op: '',
				token: as_token
			},
			anSelected: function () {
				return $.fnGetSelected(oTable);
			},
			callback: null
		};
		var o = $.extend(defaults, options);
		var choice = null;

		var dial = $.fnDialog(o.dialog);
		dial.modal({
			blurring: true,
			closable: false,
			onDeny: function () {
				return true;
			},
			onApprove: function () {
				$.ajax({
					type: 'post',
					url: o.url,
					dataType: 'json',
					data: o.data,
					error: function (jqXHR, textStatus, errorThrown) {
						$.fnMessage({
							textStatus: textStatus,
							errorThrown: errorThrown
						});
					},
					success: function (data) {
						if (oTable)
							if (data.reponse == true) {
								$.afficheButtons(true);
								if (oTable != undefined)
									oTable.fnDraw();
							}
						$.fnMessage(data);
						if ($.isFunction(o.callback))
							o.callback();
					}
				});
				return true;
			}
		}).modal('show');
	};

}(jQuery));