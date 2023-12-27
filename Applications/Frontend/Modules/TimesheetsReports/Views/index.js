$(document).ready(function () {

	webix.ready(function () {

		webix.i18n.setLocale("fr-FR");

		function checkDate() {
			var date1 = moment($$("date1").getValue());
			var date2 = moment($$("date2").getValue());
			if (date1.isValid() && date2.isValid()) {
				if (date2.diff(date1) >= 0) {
					return "&date1=" + date1.format("YYYY-MM-DD") + "&date2=" + date2.format("YYYY-MM-DD");
				}
			}
			webix.message({ type: "error", text: "La date 'au' doit etre supérieure à la date 'du'" });
			return false;
		}

		function click_filter() {
			if (this.getValue() == 0) {
				$$("myToolbar").show();
			} else {
				$$("myToolbar").hide();
			}
		}

		grid = new webix.ui({
			container: "unseen",
			id: "mylayout",
			type: "space",
			cols: [
				{
					type: "space", padding: 0, responsive:"mylayout",
					rows: [
						{
							minWidth: 900,
							cols: [
								{ view: "template", id: "title", type: "clean", height: 45, content: "title_table" },
								{ view: "toggle", type: "Button", name: "s4", label: "Modifier le rapport", width: 150, click: click_filter }
							]
						},
						{
							view: "form",
							id: "myToolbar",
							elements: [
								{
									view: "select", id: "rapport", label: "Rapport :", name: "rapport", value: 1, options: data2, labelAlign: "left"
								},
								{
									cols: [
									   { view: "datepicker", id: "date1", label: "Date du :", name: "date1", stringResult: true, format: "%Y-%m-%d", labelAlign: "left" },
									   { view: "datepicker", id: "date2", label: "au :", name: "date2", stringResult: true, format: "%Y-%m-%d", labelAlign: "center" },
									   { view: "button", id: "update", label: "Mettre à jour le rapport", width: 200, align: "center" }
									]
								}
							],
							data: data
						},
						{
							view: "datatable", id: "table", columns: colonnes, pager: "pagerB", autoConfig: true, data: database, select: true, autoheight: true, autowidth: true,
							fixedRowHeight: false,
							on: { "onresize": webix.once(function () { this.adjustRowHeight("comment", true); }) }
						},
						{
							cols: [
							   { view: "pager", id: "pagerB", size: 10, group: 5, template: "{common.prev()}{common.pages()}{common.next()}Page {common.page()} sur #limit#" },
							   {
							   	view: "button", label: "Export to Excel", width: 150, click: function () {
							   		webix.toExcel($$("table"));
							   	}
							   },
							   { view: "button", id: "btnTimeline", width: 100, label: "Timeline" },
							   { view: "button", id: "btnChangePointage", width: 200, label: "Modifier le pointage" },
							]
						}
					]

				}
			]

		});

		$$("myToolbar").hide();

		if ($$("rapport").getValue() == '5') {
			$$("btnChangePointage").show();
		} else {
			$$("btnChangePointage").hide();
		}

		$$("rapport").attachEvent("onChange", function (newv, oldv) {
			var period = checkDate();
			if (period !== false) {
				document.location.href = "timesheetsreports_index?report_id=" + newv + period;
			}
			return false;
		});
		$$("update").attachEvent("onItemClick", function (id, ev) {
			var period = checkDate();
			if (period !== false) {
				document.location.href = "timesheetsreports_index?report_id=" + $$("rapport").getValue() + period;
			}
			return false;
		});
		$$("btnTimeline").attachEvent("onItemClick", function (id, ev) {
			var item = $$("table").getSelectedItem();
			console.log(item);
			if (typeof item !== 'undefined')
				document.location.href = "timelines_index?task_id=" + item.task_id;
			return false;
		});
		$$("btnChangePointage").attachEvent("onItemClick", function (id, ev) {
			var item = $$("table").getSelectedItem();
			console.log(item);
			if (typeof item !== 'undefined')
				document.location.href = "frmtimesheet?id=" + item.time_id + "&op=edit&p=2";
			return false;
		});
	});


});
