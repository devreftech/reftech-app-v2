$(function () {
    var baseAjaxUrl = "/db/crm/status";
    var selectedSales = $("#cbs-sales-tab-nav").data("default-sales-id") || $(".select-sales.active").data("id") || 1;

    function initDataTable(selector, statusId) {
        var $el = $(selector);
        if (!$el.length) return null;

        // Clone header row lalu replace isinya dengan input search (seperti di halaman invoice)
        $el.find("thead tr")
            .clone(true)
            .appendTo($el.find("thead"));

        $el.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
        });

        var dt = $el.DataTable({
            ajax: {
                type: "GET",
                url: baseAjaxUrl + "?status=" + statusId + "&sales_id=" + selectedSales,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "company" },
                { data: "ru" },
                { data: "status" },
                { data: "area" },
                {
                    data: "note",
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                { data: "date" },
                { data: "follow_up" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: [2, 5, 6, 7],
                    className: "text-center",
                },
                {
                    targets: [0, 3],
                    className: "text-nowrap",
                },
                {
                    responsivePriority: 1,
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var $dataId = full["id"];
                        var detailRoute = route("existing.show", $dataId);
                        var companyName = data || "-";

                        if (companyName.length > 25) {
                            companyName = companyName.substring(0, 25) + "...";
                        }

                        return '<a class="fw-bold text-primary" href="' + detailRoute + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + (data || "") + '">' + companyName + '</a>';
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $status_ru = full["ru"];
                            var $status = {
                                User: { class: "bg-success" },
                                Reseller: { class: "bg-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($status[$status_ru] ? $status[$status_ru].class : "bg-label-secondary") +
                                '">' +
                                data +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var dropdown =
                            '<select class="form-select status-dropdown" data-id="' +
                            full.id +
                            '">';
                        dropdown +=
                            '<option value="1" ' +
                            (data === "1" ? "selected" : "") +
                            ">Bangkrupt</option>";
                        dropdown +=
                            '<option value="2" ' +
                            (data === "2" ? "selected" : "") +
                            ">Aktif</option>";
                        dropdown +=
                            '<option value="3" ' +
                            (data === "3" ? "selected" : "") +
                            ">Non Aktif</option>";
                        dropdown += "</select>";
                        return dropdown;
                    },
                },
                {
                    targets: [5, 6],
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var flag = full["info"];
                            if (!flag) return "-";
                            var note = full["note"] || "No notes available";
                            var $info = {
                                Reftech: { class: "bg-label-primary" },
                                Kojisha: { class: " bg-label-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($info[flag] ? $info[flag].class : "bg-label-secondary") +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + note + '">' +
                                data +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
            ],
            order: [[0, "asc"]],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f><"dt-action-buttons text-end pt-3 pt-md-0"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [],
        });

        $el.find("thead tr:eq(1) th input").each(function (i) {
            $(this).on("keyup change", function () {
                if (dt.column(i).search() !== this.value) {
                    dt.column(i).search(this.value).draw();
                }
            });
        });

        return dt;
    }

    if ($(".datatable-customers-by-sales-active").length) {
        var tables = [
            { dt: initDataTable(".datatable-customers-by-sales-active", 2), statusId: 2 },
            { dt: initDataTable(".datatable-customers-by-sales-non-active", 3), statusId: 3 },
            { dt: initDataTable(".datatable-customers-by-sales-bangkrupt", 1), statusId: 1 },
        ];

        window.dtCustomerBySalesActive = tables[0].dt;
        window.dtCustomerBySalesNonActive = tables[1].dt;
        window.dtCustomerBySalesBangkrupt = tables[2].dt;

        window.reloadCustomersBySales = function (salesId) {
            selectedSales = salesId;
            tables.forEach(function (t) {
                if (!t.dt) return;
                var url = baseAjaxUrl + "?status=" + t.statusId + "&sales_id=" + selectedSales;
                t.dt.ajax.url(url).load();
            });
        };

        $('#cbs-status-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });

        $(document).on('draw.dt', function (e) {
            var $tbl = $(e.target);
            $tbl.find('[data-bs-toggle="tooltip"]').tooltip();

            var badgeId = $tbl.data('badge');
            if (!badgeId) return;
            var api = $tbl.DataTable();
            var count = api.page.info().recordsTotal;
            $('#' + badgeId).text(count);
        });
    }
});
