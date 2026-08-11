$(function () {
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf("/") + 1);
    var Url = "/db/client/po-history/";
    var $yearFilter = $("#poYearFilter");
    var dt_po_history;

    function formatRupiah(value) {
        return "Rp " + Number(value || 0).toLocaleString("id-ID");
    }

    function poHistoryUrl(year) {
        return Url + id + (year ? "?year=" + year : "");
    }

    function loadPoSummary(year) {
        $.ajax({
            type: "GET",
            url: "/db/client/po-summary/" + id,
            data: year ? { year: year } : {},
            headers: {
                "Content-Type": "application/json",
            },
            success: function (res) {
                $("#poTotalRevenue").text(formatRupiah(res.total_revenue));
                $("#poTotalCount").text(res.total_po);
                $("#poAvgDeal").text(formatRupiah(res.avg_deal));
            },
        });
    }

    var dt_table_po_history = $(".datatable-po-history");

    if (dt_table_po_history.length) {
        $('[data-toggle="tooltip"]').tooltip();
        dt_po_history = dt_table_po_history.DataTable({
            ajax: {
                type: "GET",
                url: poHistoryUrl($yearFilter.val()),
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "id" },
                { data: "no_quote" },
                { data: "title" },
                { data: "po_date" },
                { data: "status" },
                { data: "nett" },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    // Checkbox column removed, kept hidden to avoid reindexing other targets
                    targets: 1,
                    visible: false,
                    orderable: false,
                    searchable: false,
                },
                {
                    targets: 2,
                    searchable: true,
                    visible: false,
                },
                {
                    responsivePriority: 1,
                    targets: 3,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = full["is_unit_quotation"]
                                ? route("unit-quotation.show", $dataId)
                                : route("quotation.show", $dataId);
                            return (
                                '<a class="text-dark" href="' +
                                detailRoute +
                                '">' +
                                data +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, full, meta) {
                        return data ? data : "-";
                    },
                },
                {
                    targets: 5,
                },
                {
                    // Label Status Percent
                    targets: 6,
                    render: function (data, type, full, meta) {
                        var $status_number = full["status"];
                        var $titleTool = full["note"];
                        var $status = {
                            20: {
                                title: "20%",
                                class: "bg-label-secondary",
                                colorTip: "tooltip-secondary",
                                titleTip: $titleTool,
                            },
                            30: {
                                title: "30%",
                                class: " bg-label-dark",
                                colorTip: "tooltip-dark",
                                titleTip: $titleTool,
                            },
                            40: {
                                title: "40%",
                                class: " bg-label-info",
                                colorTip: "tooltip-info",
                                titleTip: $titleTool,
                            },
                            60: {
                                title: "60%",
                                class: " bg-label-primary",
                                colorTip: "tooltip-primary",
                                titleTip: $titleTool,
                            },
                            80: {
                                title: "80%",
                                class: " bg-label-warning",
                                colorTip: "tooltip-warning",
                                titleTip: $titleTool,
                            },
                            100: {
                                title: "100%",
                                class: " bg-label-success",
                                colorTip: "tooltip-success",
                                titleTip: $titleTool,
                            },
                            0: {
                                title: "0%",
                                class: " bg-label-danger",
                                colorTip: "tooltip-danger",
                                titleTip: $titleTool,
                            },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data;
                        }
                        return (
                            '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="' +
                            $status[$status_number].colorTip +
                            '" title="' +
                            $status[$status_number].titleTip +
                            '" class="badge rounded-pill ' +
                            $status[$status_number].class +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, meta) {
                        return formatRupiah(data);
                    },
                },
            ],
            order: [[2, "desc"]],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["company"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":" +
                                      "</td> " +
                                      "<td>" +
                                      col.data +
                                      "</td>" +
                                      "</tr>"
                                : "";
                        }).join("");

                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
        });
        $("div.head-label").html(
            '<h5 class="card-title mb-0">Table po History</h5>'
        );
    }
    dt_table_po_history.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    if ($yearFilter.length) {
        loadPoSummary($yearFilter.val());
        $yearFilter.on("change", function () {
            var year = $(this).val();
            loadPoSummary(year);
            if (dt_po_history) {
                dt_po_history.ajax.url(poHistoryUrl(year)).load();
            }
        });
    }
});
