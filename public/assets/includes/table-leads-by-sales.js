$(function () {
    var dt_table_leads_by_sales = $(".datatable-leads-by-sales");
    var baseAjaxUrl = "/leads-sales/db";
    var selectedSales =
        $(".select-sales.active").data("id") ||
        $(".select-sales:first").data("id") ||
        1;

    function buildUrl() {
        return baseAjaxUrl + "?sales_id=" + selectedSales;
    }

    if (dt_table_leads_by_sales.length) {
        $('[data-toggle="tooltip"]').tooltip();

        // Clone header row lalu jadikan baris kedua sebagai input pencarian per kolom
        dt_table_leads_by_sales
            .find("thead tr")
            .clone(true)
            .appendTo(dt_table_leads_by_sales.find("thead"));

        dt_table_leads_by_sales.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' +
                    title +
                    '..." />',
            );

            $("input", this).on("keyup change", function () {
                if (dt_leads_by_sales.column(i).search() !== this.value) {
                    dt_leads_by_sales.column(i).search(this.value).draw();
                }
            });
        });

        var dt_leads_by_sales = dt_table_leads_by_sales.DataTable({
            ordering: false,
            ajax: {
                type: "GET",
                url: buildUrl(),
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "company" },
                { data: "ru" },
                { data: "issue" },
                { data: "area" },
                { data: "date" },
                { data: "follow_up" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("detail.leads", $dataId);
                            return (
                                '<a class="text-dark" href="' +
                                detailRoute +
                                '">' +
                                (data || "-") +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $status_ru = full["ru"];
                            var $status = {
                                User: { class: "bg-success" },
                                Reseller: { class: " bg-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($status[$status_ru]
                                    ? $status[$status_ru].class
                                    : "bg-label-secondary") +
                                '">' +
                                (data || "-") +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
                {
                    // Label status leads (berdasarkan id_issues)
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $status_number = full["id_issues"];
                        var $titleTool = full["note"];
                        var $status = {
                            1: {
                                title: "New Client",
                                class: "bg-label-warning",
                                colorTip: "tooltip-warning",
                                titleTip: $titleTool,
                            },
                            2: {
                                title: "Not Responded",
                                class: " bg-label-danger",
                                colorTip: "tooltip-danger",
                                titleTip: $titleTool,
                            },
                            3: {
                                title: "Send Introduction",
                                class: " bg-label-info",
                                colorTip: "tooltip-info",
                                titleTip: $titleTool,
                            },
                            4: {
                                title: "Send Quote",
                                class: " bg-label-primary",
                                colorTip: "tooltip-primary",
                                titleTip: $titleTool,
                            },
                            5: {
                                title: "Done PO",
                                class: " bg-label-success",
                                colorTip: "tooltip-success",
                                titleTip: $titleTool,
                            },
                            6: {
                                title: "Loss",
                                class: " bg-label-danger",
                                colorTip: "tooltip-danger",
                                titleTip: $titleTool,
                            },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data || "-";
                        }
                        return (
                            '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="' +
                            $status[$status_number].colorTip +
                            '" title="' +
                            ($status[$status_number].titleTip || "") +
                            '" class="badge ' +
                            $status[$status_number].class +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
                {
                    targets: [4, 5],
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var flag = full["info"];
                            if (!flag) return "-";
                            var $info = {
                                Reftech: { class: "bg-label-primary" },
                                Kojisha: { class: " bg-label-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($info[flag]
                                    ? $info[flag].class
                                    : "bg-label-secondary") +
                                '">' +
                                data +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
            ],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
        });

        // Dipanggil dari indexBySales.blade.php saat tab sales diklik
        window.reloadLeadsBySales = function (salesId) {
            selectedSales = salesId;
            dt_leads_by_sales.ajax.url(buildUrl()).load();
        };

        dt_table_leads_by_sales.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }
});
