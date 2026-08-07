$(function () {
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf('/') + 1);

    function initQuotationTable(selector, url) {
        var $table = $(selector);
        if (!$table.length) return;

        $('[data-toggle="tooltip"]').tooltip();

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "id" },
                { data: "no_quote" },
                { data: "nett" },
                { data: "title" },
                { data: "estimated_date" },
                { data: "status" },
                { data: "expired_date" },
                { data: "status" },
            ],
            columnDefs: [
                {
                    targets: 4,
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
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
                    targets: 3,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var isUnit = full["is_unit_quotation"];
                            var detailRoute = isUnit ? route("unit-quotation.show", $dataId) : route("quotation.show", $dataId);
                            var badgeUnit = isUnit ? ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Smart</span>' : '';
                            return (
                                '<a class="text-dark" href="' + detailRoute + '">' + data + "</a>" + badgeUnit
                            );
                        }
                        return data;
                    },
                },
                {
                    responsivePriority: 1,
                    targets: 3,
                },
                {
                    // Label Status Name
                    targets: 7,
                    render: function (data, type, full, meta) {
                        var $status_number = full["status"];
                        var $status = {
                            20: {
                                title: "Send WA / Email",
                                class: "bg-label-secondary",
                            },
                            30: {
                                title: "Inquiry Accepted",
                                class: " bg-label-dark",
                            },
                            40: {
                                title: "Progress Follow Up",
                                class: " bg-label-info",
                            },
                            60: {
                                title: "Negotiation / Revisi",
                                class: " bg-label-primary",
                            },
                            80: {
                                title: "Hot Prospect",
                                class: " bg-label-warning",
                            },
                            100: {
                                title: "Done PO",
                                class: " bg-label-success",
                            },
                            0: {
                                title: "Loss",
                                class: " bg-label-danger",
                            },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data;
                        }
                        return (
                            '<span class="badge rounded-pill ' +
                            $status[$status_number].class +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
                {
                    // Label Status Percent
                    targets: 9,
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
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            order: [[2, "desc"]],
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["full_name"];
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

        $table.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    initQuotationTable(".datatable-quotation-active", "/db/product/quotation/active/" + id);
    initQuotationTable(".datatable-quotation-loss", "/db/product/quotation/loss/" + id);
    initQuotationTable(".datatable-quotation-archive", "/db/product/quotation/archive/" + id);
});
