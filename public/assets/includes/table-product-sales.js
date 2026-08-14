$(function () {
    var dt_table_product_sales = $(".datatable-product-sales");
    var Url = "db/product/sales";

    if (dt_table_product_sales.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_product_sales.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "image" },
                { data: "brand" },
                { data: "pn" },
                { data: "description" },
                { data: "stock" },
                { data: "warehouse_stock" },
                { data: "pending_stock" },
                { data: "price" },
            ],
            columnDefs: [
                {
                    targets: 9,
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
                {
                    targets: 2,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            if (data === null || data === "") {
                                return "-";
                            }
                            return (
                                '<a class="text-dark badge bg-label-primary" target="_blank" href="' +
                                data +
                                '">' +
                                "Photo" +
                                "</a>"
                            );
                        }
                        return data;
                    },
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
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                },
                {
                    targets: [2, 3, 6, 7, 8],
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return data;
                        }
                    },
                },
                {
                    targets: 5, // description with G/R badge in front
                    render: function (data, type, full, row) {
                        var badge = '';
                        var goVal = full.go ? String(full.go).trim() : '';

                        if (goVal === 'Genuine' || goVal === 'G') {
                            badge = '<span class="badge bg-label-success me-1" data-bs-toggle="tooltip" title="Genuine">G</span>';
                        } else if (goVal === 'Replacement' || goVal === 'R') {
                            badge = '<span class="badge bg-label-warning me-1" data-bs-toggle="tooltip" title="Replacement">R</span>';
                        } else if (goVal) {
                            badge = '<span class="badge bg-label-info me-1">' + goVal + '</span>';
                        }

                        if (!data) return badge ? badge + '-' : '-';

                        if (type === "display") {
                            var truncated = data.length > 35 ? data.substr(0, 32) + '...' : data;
                            var textSpan = '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' + data.replace(/"/g, '&quot;') + '">' + truncated + '</span>';
                            return badge + textSpan;
                        }
                        return data;
                    }
                },
                {
                    targets: 4, // Part Number
                    render: function (data, type, full, row) {
                        if (!data) return "-";
                        if (type === "display") {
                            var truncated = data.length > 20 ? data.substr(0, 17) + '...' : data;
                            return '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' + data.replace(/"/g, '&quot;') + '">' + truncated + '</span>';
                        }
                        return data;
                    }
                },
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["pn"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== ""
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

                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    },
                },
            },
        });
    }
});
