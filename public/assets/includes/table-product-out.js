$(function () {
    var dt_table_product = $(".datatable-product-out");
    var Url = "/db/productOut";

    if (dt_table_product.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_product.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },

                // success: function (hasil, Url) {
                //     console.log("Url:", Url);
                //     console.log(hasil);
                // },
                // error: function (error) {
                //     console.log("Url:", Url);
                //     console.error("Error:", error);
                //     console.log("error disini");
                // },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_product_out" },
                { data: "invoice" },
                { data: "po" },
                { data: "detail_client" },
                { data: "product" },
                { data: "vers" },
                { data: "qty" },
                { data: "date" },
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
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 2,
                    render: function (data) { return data || "-"; },
                },
                {
                    responsivePriority: 1,
                    targets: 3,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("product-out.show", $dataId);
                            return (
                                '<a class="text-dark fw-semibold" href="' +
                                detailRoute +
                                '">' +
                                (data || "-") +
                                "</a>"
                            );
                        }
                        return data || "-";
                    },
                },
                {
                    // Product Column (shows single product or expandable items summary)
                    targets: 6,
                    render: function (data, type, full, meta) {
                        if (!full.items_detail) return "-";
                        var items = full.items_detail.split("||");
                        if (items.length === 1) {
                            var parts = items[0].split("::");
                            var name = parts[0] || "-";
                            var pn = parts[1] && parts[1] !== "-" ? ' <span class="badge bg-label-secondary ms-1">' + parts[1] + '</span>' : '';
                            return '<div class="fw-medium text-dark text-truncate" style="max-width: 280px;" title="' + name + '">' + name + pn + '</div>';
                        } else {
                            var firstItem = items[0].split("::")[0] || "";
                            return '<div class="d-flex align-items-center gap-1">' +
                                '<span class="badge bg-label-primary rounded-pill"><i class="mdi mdi-package-variant-closed me-1"></i>' + items.length + ' Items</span>' +
                                '<a href="javascript:;" class="btn-toggle-items text-primary ms-1 small fw-semibold"><i class="mdi mdi-chevron-down toggle-icon"></i> Detail</a>' +
                                '</div>' +
                                '<div class="text-muted small text-truncate" style="max-width: 250px;" title="' + firstItem + '...">' + firstItem + ', ...</div>';
                        }
                    },
                },
                {
                    // Qty Column
                    targets: 8,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        if (full.total_items > 1) {
                            return '<span class="fw-bold text-dark">' + (full.total_qty || 0) + '</span> <span class="badge bg-label-info">' + full.total_items + ' items</span>';
                        }
                        if (full.items_detail) {
                            var first = full.items_detail.split("||")[0];
                            var q = first.split("::")[2];
                            return q ? '<span class="fw-semibold text-dark">' + q + '</span>' : (full.total_qty || "-");
                        }
                        return full.total_qty || "-";
                    },
                },
            ],
            order: [[1, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: "collection",
                    className: "btn btn-label-primary dropdown-toggle me-2",
                    text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                    buttons: [
                        {
                            extend: "print",
                            text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                            customize: function (win) {
                                //customize print view for dark
                                $(win.document.body)
                                    .css("color", config.colors.headingColor)
                                    .css(
                                        "border-color",
                                        config.colors.borderColor
                                    )
                                    .css(
                                        "background-color",
                                        config.colors.bodyBg
                                    );
                                $(win.document.body)
                                    .find("table")
                                    .addClass("compact")
                                    .css("color", "inherit")
                                    .css("border-color", "inherit")
                                    .css("background-color", "inherit");
                            },
                        },
                        {
                            extend: "csv",
                            text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "excel",
                            text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "pdf",
                            text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                        {
                            extend: "copy",
                            text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
                            className: "dropdown-item",
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9],
                                // prevent avatar to be display
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;
                                        var el = $.parseHTML(inner);
                                        var result = "";
                                        $.each(el, function (index, item) {
                                            if (
                                                item.classList !== undefined &&
                                                item.classList.contains(
                                                    "user-name"
                                                )
                                            ) {
                                                result =
                                                    result +
                                                    item.lastChild.firstChild
                                                        .textContent;
                                            } else if (
                                                item.innerText === undefined
                                            ) {
                                                result =
                                                    result + item.textContent;
                                            } else
                                                result =
                                                    result + item.innerText;
                                        });
                                        return result;
                                    },
                                },
                            },
                        },
                    ],
                },
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">New Invoice Product Out</span>',
                    className: "btn btn-primary btn-new",
                    action: function (e, dt, node, config) {
                        window.location = route("product-out.create");
                    },
                },
            ],
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
        function formatProductDetail(d) {
            if (!d.items_detail) {
                return '<div class="p-3 text-muted text-center">Tidak ada rincian item.</div>';
            }
            var items = d.items_detail.split("||");
            var html = '<div class="p-3 bg-light rounded-3 my-2 border shadow-xs">' +
                '<div class="fw-semibold text-dark mb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">' +
                '<span><i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> Rincian Barang Keluar (No BK: ' + (d.no_product_out || "-") + ' / Inv: ' + (d.invoice || "-") + ')</span>' +
                '<span class="badge bg-label-primary">' + items.length + ' Jenis Item | Total Qty: ' + (d.total_qty || 0) + '</span>' +
                '</div>' +
                '<div class="table-responsive bg-white rounded-2 border">' +
                '<table class="table table-sm table-striped mb-0">' +
                '<thead class="table-light"><tr>' +
                '<th style="width: 40px;" class="text-center">#</th>' +
                '<th>Nama Barang / Replacement</th>' +
                '<th>Part Number (PN)</th>' +
                '<th class="text-center" style="width: 120px;">Qty</th>' +
                '</tr></thead><tbody>';

            items.forEach(function (item, idx) {
                var p = item.split("::");
                var name = p[0] || "-";
                var pn = p[1] && p[1] !== "-" ? p[1] : "-";
                var q = p[2] || "-";
                html += '<tr>' +
                    '<td class="text-center text-muted fw-semibold">' + (idx + 1) + '</td>' +
                    '<td class="fw-medium text-dark">' + name + '</td>' +
                    '<td><span class="badge bg-label-secondary">' + pn + '</span></td>' +
                    '<td class="text-center fw-bold text-primary">' + q + '</td>' +
                    '</tr>';
            });

            html += '</tbody></table></div></div>';
            return html;
        }

        // Event listener for opening and closing product detail row
        dt_table_product.on("click", ".btn-toggle-items", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var tr = $(this).closest("tr");
            var row = dt_product.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass("shown");
                $(this).find(".toggle-icon").removeClass("mdi-chevron-up").addClass("mdi-chevron-down");
            } else {
                row.child(formatProductDetail(row.data())).show();
                tr.addClass("shown");
                $(this).find(".toggle-icon").removeClass("mdi-chevron-down").addClass("mdi-chevron-up");
            }
        });

        $("div.head-label").html(
            '<h5 class="card-title mb-0">Table Product</h5>'
        );
    }
    dt_table_product.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
