$(function () {
    var dt_table_product_sales = $(".datatable-product-sales");
    var Url = "db/product/sales";

    if (!dt_table_product_sales.length) return;

    $('[data-toggle="tooltip"]').tooltip();

    // ── Global Filter State ─────────────────────────────────────────────
    var currentFilter = "all";

    // Custom DataTables filter function
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
        if (!settings.nTable.classList.contains("datatable-product-sales")) {
            return true;
        }
        if (currentFilter === "all") return true;

        var stkBdg = parseInt(rowData.stock, 10) || 0;
        var stkBks = parseInt(rowData.warehouse_stock, 10) || 0;
        var go = String(rowData.go || "").trim().toLowerCase();

        if (currentFilter === "ready") {
            return stkBdg > 0 || stkBks > 0;
        }
        if (currentFilter === "genuine") {
            return go === "genuine" || go === "g";
        }
        if (currentFilter === "replacement") {
            return go === "replacement" || go === "r";
        }
        return true;
    });

    // ── Update Quick KPI Cards ──────────────────────────────────────────
    function updateSalesStats(data) {
        var total = data.length;
        var ready = 0;
        var genuine = 0;
        var replacement = 0;

        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            var stkBdg = parseInt(item.stock, 10) || 0;
            var stkBks = parseInt(item.warehouse_stock, 10) || 0;
            if (stkBdg > 0 || stkBks > 0) {
                ready++;
            }
            var go = String(item.go || "").trim().toLowerCase();
            if (go === "genuine" || go === "g") {
                genuine++;
            } else if (go === "replacement" || go === "r") {
                replacement++;
            }
        }

        $("#stat-total-products").text(total.toLocaleString("id-ID"));
        $("#stat-ready-stock").text(ready.toLocaleString("id-ID"));
        $("#stat-genuine").text(genuine.toLocaleString("id-ID"));
        $("#stat-replacement").text(replacement.toLocaleString("id-ID"));
        $("#table-filtered-info").html('<i class="mdi mdi-check-all me-1 text-success"></i> ' + total.toLocaleString("id-ID") + ' item');
    }

    // ── Initialize DataTables ───────────────────────────────────────────
    var dt_product = dt_table_product_sales.DataTable({
        ajax: {
            type: "GET",
            url: Url,
            headers: {
                "Content-Type": "application/json",
            },
            dataSrc: function (json) {
                var data = json.data || [];
                updateSalesStats(data);
                return data;
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
            { data: "price_updated_at" },
        ],
        columnDefs: [
            {
                // Control column for responsive
                className: "control",
                orderable: false,
                searchable: false,
                responsivePriority: 2,
                targets: 0,
                render: function () {
                    return "";
                },
            },
            {
                // ID column (hidden for sorting only)
                targets: 1,
                searchable: true,
                visible: false,
            },
            {
                // Photo
                targets: 2,
                className: "text-center",
                responsivePriority: 1,
                render: function (data, type) {
                    if (type !== "display") return data || "";
                    if (!data || data === "" || data === "image") {
                        return '<span class="text-muted small">-</span>';
                    }
                    return (
                        '<a class="btn btn-xs btn-label-primary px-2 py-1 shadow-none" target="_blank" href="' +
                        data +
                        '" title="Buka foto produk di tab baru">' +
                        '<i class="mdi mdi-image-outline me-1"></i>Foto' +
                        "</a>"
                    );
                },
            },
            {
                // Brand
                targets: 3,
                render: function (data, type) {
                    if (!data) return "-";
                    if (type !== "display") return data;
                    return '<span class="fw-semibold text-dark">' + data + '</span>';
                },
            },
            {
                // Part Number (with copy button)
                targets: 4,
                render: function (data, type) {
                    if (!data) return "-";
                    if (type !== "display") return data;
                    var cleanPn = String(data).trim();
                    var truncated = cleanPn.length > 20 ? cleanPn.substr(0, 17) + "..." : cleanPn;
                    return (
                        '<div class="d-flex align-items-center justify-content-between gap-1">' +
                        '<span class="font-monospace fw-semibold text-primary" data-bs-toggle="tooltip" title="' +
                        cleanPn.replace(/"/g, "&quot;") +
                        '">' +
                        truncated +
                        "</span>" +
                        '<button type="button" class="btn btn-xs btn-icon btn-text-secondary btn-copy-pn p-0" data-pn="' +
                        cleanPn.replace(/"/g, "&quot;") +
                        '" title="Salin Part Number">' +
                        '<i class="mdi mdi-content-copy mdi-14px"></i>' +
                        "</button>" +
                        "</div>"
                    );
                },
            },
            {
                // Description with Genuine (G) / Replacement (R) badge
                targets: 5,
                render: function (data, type, full) {
                    var badge = "";
                    var goVal = full.go ? String(full.go).trim() : "";

                    if (goVal === "Genuine" || goVal === "G") {
                        badge = '<span class="badge bg-label-success me-1 px-2" data-bs-toggle="tooltip" title="Genuine Part">G</span>';
                    } else if (goVal === "Replacement" || goVal === "R") {
                        badge = '<span class="badge bg-label-warning me-1 px-2" data-bs-toggle="tooltip" title="Replacement Part">R</span>';
                    } else if (goVal) {
                        badge = '<span class="badge bg-label-info me-1 px-2">' + goVal + "</span>";
                    }

                    if (!data) return badge ? badge + "-" : "-";

                    if (type === "display") {
                        var truncated = data.length > 35 ? data.substr(0, 32) + "..." : data;
                        var textSpan =
                            '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' +
                            data.replace(/"/g, "&quot;") +
                            '">' +
                            truncated +
                            "</span>";
                        return '<div class="d-flex align-items-center">' + badge + textSpan + "</div>";
                    }
                    return data;
                },
            },
            {
                // Stock BDG (Bandung)
                targets: 6,
                className: "text-center",
                render: function (data, type) {
                    if (type !== "display") return parseInt(data, 10) || 0;
                    var val = parseInt(data, 10) || 0;
                    if (val > 0) {
                        return '<span class="badge bg-label-success fw-bold px-2 py-1">' + val + "</span>";
                    }
                    return '<span class="text-muted small">0</span>';
                },
            },
            {
                // Stock BKS (Bekasi)
                targets: 7,
                className: "text-center",
                render: function (data, type) {
                    if (type !== "display") return parseInt(data, 10) || 0;
                    var val = parseInt(data, 10) || 0;
                    if (val > 0) {
                        return '<span class="badge bg-label-info fw-bold px-2 py-1">' + val + "</span>";
                    }
                    return '<span class="text-muted small">0</span>';
                },
            },
            {
                // Pending Stock
                targets: 8,
                className: "text-center",
                render: function (data, type) {
                    if (type !== "display") return parseInt(data, 10) || 0;
                    var val = parseInt(data, 10) || 0;
                    if (val > 0) {
                        return '<span class="badge bg-label-warning fw-semibold px-2 py-1" title="Dalam proses pesanan/pengiriman">' + val + "</span>";
                    }
                    return '<span class="text-muted small">0</span>';
                },
            },
            {
                // Price (Rp rata kiri, angka rata kanan)
                targets: 9,
                className: "text-nowrap",
                render: function (data, type) {
                    if (type !== "display" && type !== "filter") {
                        return data === null || data === "" ? 0 : data;
                    }
                    if (!data || Number(data) === 0) {
                        return '<span class="text-muted small">Belum diset</span>';
                    }
                    var n = parseFloat(data);
                    if (isNaN(n)) n = 0;
                    var num = String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    return (
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<span class="text-muted me-2 small">Rp</span>' +
                        '<span class="fw-semibold">' +
                        num +
                        "</span>" +
                        "</div>"
                    );
                },
            },
            {
                // Last Update Price
                targets: 10,
                className: "text-center text-nowrap",
                render: function (data, type) {
                    if (!data || data === "0000-00-00 00:00:00" || data === "0000-00-00") {
                        return '<span class="text-muted small">-</span>';
                    }
                    if (type !== "display") return data;
                    return '<span class="text-muted small"><i class="mdi mdi-clock-outline me-1"></i>' + moment(data).format("DD MMM YYYY") + "</span>";
                },
            },
        ],
        order: [[1, "desc"]],
        dom:
            '<"row px-3 pt-3 pb-2"<"col-sm-12 col-md-6 d-flex align-items-center"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
            '<"table-responsive"t>' +
            '<"row px-3 pt-2 pb-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        buttons: [],
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-bs-toggle="tooltip"]').tooltip();

            var count = dt_product.rows({ filter: "applied" }).count();
            var total = dt_product.rows().count();
            if (count === total) {
                $("#table-filtered-info").html('<i class="mdi mdi-check-all me-1 text-success"></i> ' + total.toLocaleString("id-ID") + ' item');
            } else {
                $("#table-filtered-info").html('<i class="mdi mdi-filter me-1 text-primary"></i> ' + count.toLocaleString("id-ID") + ' dari ' + total.toLocaleString("id-ID") + ' item');
            }
        },
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        var data = row.data();
                        return "Detail Part: " + (data["pn"] || "-");
                    },
                }),
                type: "column",
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col) {
                        return col.title !== ""
                            ? '<tr data-dt-row="' +
                                  col.rowIndex +
                                  '" data-dt-column="' +
                                  col.columnIndex +
                                  '">' +
                                  '<td class="fw-semibold text-muted py-2">' +
                                  col.title +
                                  ":" +
                                  "</td> " +
                                  '<td class="py-2">' +
                                  col.data +
                                  "</td>" +
                                  "</tr>"
                            : "";
                    }).join("");

                    return data ? $('<table class="table table-sm mb-0"/><tbody />').append(data) : false;
                },
            },
        },
    });

    // ── Filter Buttons & Card Clicks ────────────────────────────────────
    function applyFilter(filter) {
        currentFilter = filter;

        // Sync buttons
        $("#sales-filter-btn-group button").removeClass("active");
        $('#sales-filter-btn-group button[data-filter="' + filter + '"]').addClass("active");

        // Sync stat cards
        $(".stat-card").removeClass("active-filter");
        $('.stat-card[data-filter="' + filter + '"]').addClass("active-filter");

        dt_product.draw();
    }

    $(document).on("click", "#sales-filter-btn-group button[data-filter]", function () {
        var filter = $(this).data("filter");
        applyFilter(filter);
    });

    $(document).on("click", ".stat-card[data-filter]", function () {
        var filter = $(this).data("filter");
        applyFilter(filter);
    });

    // ── Refresh Button ──────────────────────────────────────────────────
    $("#btn-refresh-table").on("click", function () {
        var $btn = $(this);
        var $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.addClass("mdi-spin");

        dt_product.ajax.reload(function () {
            $btn.prop("disabled", false);
            $icon.removeClass("mdi-spin");
        }, false);
    });

    // ── Copy Part Number to Clipboard ───────────────────────────────────
    $(document).on("click", ".btn-copy-pn", function (e) {
        e.stopPropagation();
        var pn = $(this).data("pn");
        if (!pn) return;

        var $btn = $(this);
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(pn).then(function () {
                var origHtml = $btn.html();
                $btn.html('<i class="mdi mdi-check text-success mdi-14px"></i>');
                setTimeout(function () {
                    $btn.html(origHtml);
                }, 1500);
            });
        }
    });
});
