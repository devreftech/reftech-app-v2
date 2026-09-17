$(function () {
    var dt_table_product_sales = $(".datatable-product-sales");
    var Url = "/db/product/sales";

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

    // ── Helper Formatter for Copy Row Data ─────────────────────────────
    function formatRowText(full) {
        var brand = full.brand ? String(full.brand).trim() : "-";
        var pn = full.pn ? String(full.pn).trim() : "-";
        var desc = full.description ? String(full.description).trim() : "";
        var go = full.go ? String(full.go).trim() : "";
        var goBadge = "";
        if (go.toLowerCase() === "genuine" || go.toLowerCase() === "g") {
            goBadge = " (Genuine)";
        } else if (go.toLowerCase() === "replacement" || go.toLowerCase() === "r") {
            goBadge = " (Replacement)";
        } else if (go) {
            goBadge = " (" + go + ")";
        }

        var stkBdg = parseInt(full.stock, 10) || 0;
        var stkBks = parseInt(full.warehouse_stock, 10) || 0;
        var stkPend = parseInt(full.pending_stock, 10) || 0;

        var priceNum = parseFloat(full.price);
        var formattedPrice = (!full.price || isNaN(priceNum) || priceNum === 0)
            ? "Belum diset"
            : "Rp " + String(Math.round(priceNum)).replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        var rawDate = full.price_updated_at || full.updated_at;
        var updateDateStr = "";
        if (rawDate && rawDate !== "0000-00-00 00:00:00" && rawDate !== "0000-00-00" && moment(rawDate).isValid()) {
            updateDateStr = " (Update: " + moment(rawDate).format("DD-MM-YYYY") + ")";
        }

        var descPart = desc ? (" - " + desc + goBadge) : (goBadge ? (" - " + goBadge) : "");

        return "[" + brand + "] " + pn + descPart + " | Stok: BDG: " + stkBdg + ", BKS: " + stkBks + (stkPend > 0 ? (", KEEP: " + stkPend) : "") + " | Price: " + formattedPrice + updateDateStr;
    }

    // ── Columns & ColumnDefs Configuration ──────────────────────────────
    var isDev = Boolean(window.isDeveloper || window.showTransaksi);
    var isSales = Boolean(window.isSales);

    var dtColumns = [
        {
            // Control column for responsive
            data: "",
            className: "control",
            orderable: false,
            searchable: false,
            responsivePriority: 2,
            render: function () {
                return "";
            },
        },
        {
            // ID column (hidden for sorting only)
            data: "id",
            searchable: true,
            visible: false,
        },
    ];

    if (!isSales) {
        dtColumns.push({
            // SKU (Commodity) with rich styled AVG & LAST HPP tooltip
            data: "commodity",
            responsivePriority: 1,
            render: function (data, type, full) {
                if (type !== "display") return data || "-";
                var prodId = full.product_id || full.id;
                var detailLink = prodId ? ('/product/' + prodId) : '#';
                var val = data ? String(data).trim() : "-";

                var avgVal = (full.avg_hpp !== null && full.avg_hpp !== undefined && !isNaN(full.avg_hpp)) ? Number(full.avg_hpp) : null;
                var lastVal = (full.last_hpp !== null && full.last_hpp !== undefined && !isNaN(full.last_hpp)) ? Number(full.last_hpp) : null;
                var replacement = full.replacement_name ? String(full.replacement_name).trim() : "";

                var formattedAvg = (avgVal && avgVal > 0)
                    ? ("Rp " + Math.round(avgVal).toLocaleString("id-ID"))
                    : (lastVal && lastVal > 0 ? ("Rp " + Math.round(lastVal).toLocaleString("id-ID")) : "-");

                var formattedLast = (lastVal && lastVal > 0)
                    ? ("Rp " + Math.round(lastVal).toLocaleString("id-ID"))
                    : (avgVal && avgVal > 0 ? ("Rp " + Math.round(avgVal).toLocaleString("id-ID")) : "-");

                var shortVal = val.length > 22 ? (val.substr(0, 19) + "...") : val;

                var tooltipHtml =
                    '<div class="text-start py-1 px-1" style="min-width: 250px;">' +
                        '<div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom border-light border-opacity-25">' +
                            '<span class="fw-bold text-white small d-flex align-items-center"><i class="mdi mdi-calculator-variant-outline me-1 text-warning"></i>Kalkulasi HPP</span>' +
                            '<span class="badge bg-label-primary px-2 py-0 fs-8 font-monospace">' + shortVal.replace(/"/g, '&quot;') + '</span>' +
                        '</div>' +
                        '<div class="mb-2">' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="text-white-50 fw-semibold fs-8"><i class="mdi mdi-chart-line me-1 text-warning"></i>AVG HPP</span>' +
                                '<span class="fw-bold ' + (formattedAvg !== '-' ? 'text-warning' : 'text-white-50') + '">' + formattedAvg + '</span>' +
                            '</div>' +
                            '<div class="text-white-50" style="font-size: 0.72rem; line-height: 1.25;">Total harga pembelian dibagi stok saat itu</div>' +
                        '</div>' +
                        '<div class="pt-2 border-top border-light border-opacity-10">' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<span class="text-white-50 fw-semibold fs-8"><i class="mdi mdi-clock-check-outline me-1 text-info"></i>LAST HPP</span>' +
                                '<span class="fw-bold ' + (formattedLast !== '-' ? 'text-info' : 'text-white-50') + '">' + formattedLast + '</span>' +
                            '</div>' +
                            '<div class="text-white-50" style="font-size: 0.72rem; line-height: 1.25;">Harga pembelian terakhir</div>' +
                        '</div>' +
                        (replacement ? ('<div class="mt-2 pt-1 border-top border-light border-opacity-10 text-white-50" style="font-size: 0.7rem;"><i class="mdi mdi-tag-outline me-1"></i>Part: ' + replacement.replace(/"/g, '&quot;') + '</div>') : '') +
                    '</div>';

                var escapedTooltip = tooltipHtml.replace(/"/g, '&quot;');

                return (
                    '<a href="' + detailLink + '" class="fw-semibold text-primary text-decoration-none sku-hpp-trigger" ' +
                    'data-bs-toggle="tooltip" data-bs-html="true" data-bs-custom-class="tooltip-hpp" data-bs-placement="top" ' +
                    'title="' + escapedTooltip + '" data-bs-title="' + escapedTooltip + '">' +
                    val +
                    '</a>'
                );
            },
        });
    }

    dtColumns.push(
        {
            // Brand
            data: "brand",
            className: "text-nowrap",
            render: function (data, type) {
                if (!data) return "-";
                if (type !== "display") return data;
                return '<span class="fw-semibold text-dark text-nowrap">' + data + '</span>';
            },
        },
        {
            // Part Number (with photo icon if available)
            data: "pn",
            responsivePriority: 1,
            render: function (data, type, full) {
                if (!data) return "-";
                if (type !== "display") return data;
                var cleanPn = String(data).trim();
                var truncated = cleanPn.length > 20 ? cleanPn.substr(0, 17) + "..." : cleanPn;

                var photoBtn = "";
                if (full.image && full.image !== "" && full.image !== "image") {
                    photoBtn =
                        '<a class="btn btn-xs btn-icon btn-text-primary p-0 ms-1" target="_blank" href="' +
                        full.image +
                        '" title="Lihat Foto Produk"><i class="mdi mdi-image-outline mdi-16px"></i></a>';
                }

                return (
                    '<div class="d-flex align-items-center justify-content-between gap-1">' +
                    '<span class="fw-semibold text-dark" title="' +
                    cleanPn.replace(/"/g, "&quot;") +
                    '">' +
                    truncated +
                    "</span>" +
                    photoBtn +
                    "</div>"
                );
            },
        },
        {
            // Description with Genuine (G) / Replacement (R) badge
            data: "description",
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
            data: "stock",
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
            data: "warehouse_stock",
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
            // Keep Stock (Pending)
            data: "pending_stock",
            className: "text-center",
            render: function (data, type) {
                if (type !== "display") return parseInt(data, 10) || 0;
                var val = parseInt(data, 10) || 0;
                if (val > 0) {
                    return '<span class="badge bg-label-warning fw-semibold px-2 py-1" title="Keep Stock">' + val + "</span>";
                }
                return '<span class="text-muted small">0</span>';
            },
        },
        {
            // Price (Rp rata kiri, angka rata kanan)
            data: "price",
            className: "text-nowrap",
            render: function (data, type, full) {
                if (type !== "display" && type !== "filter") {
                    return data === null || data === "" ? 0 : data;
                }
                if (!data || Number(data) === 0) {
                    return '<span class="text-muted small">Belum diset</span>';
                }
                var n = parseFloat(data);
                if (isNaN(n)) n = 0;
                var num = String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                var dateTooltip = full.price_updated_at && full.price_updated_at !== "0000-00-00 00:00:00"
                    ? ' title="Update: ' + moment(full.price_updated_at).format("DD MMM YYYY") + '"'
                    : '';
                return (
                    '<div class="d-flex justify-content-between align-items-center px-1"' + dateTooltip + ' data-bs-toggle="tooltip">' +
                    '<span class="text-muted me-2 small">Rp</span>' +
                    '<span class="fw-semibold text-dark">' +
                    num +
                    "</span>" +
                    "</div>"
                );
            },
        }
    );

    if (isSales) {
        dtColumns.push({
            // Update Price (DD-MM-YYYY)
            data: "price_updated_at",
            className: "text-center text-nowrap",
            render: function (data, type, full) {
                var rawDate = data || full.updated_at;
                if (!rawDate || rawDate === "0000-00-00 00:00:00" || rawDate === "0000-00-00") {
                    if (type !== "display") return 0;
                    return '<span class="text-muted small">-</span>';
                }
                var m = moment(rawDate);
                if (!m.isValid()) {
                    if (type !== "display") return 0;
                    return '<span class="text-muted small">-</span>';
                }
                if (type !== "display") {
                    return m.valueOf();
                }
                var formatted = m.format("DD-MM-YYYY");
                return '<span class="text-secondary small fw-medium">' + formatted + '</span>';
            },
        });
    }

    if (isDev) {
        dtColumns.push({
            // Transaksi (developer & admin role only)
            data: "total_transaksi",
            className: "text-center text-nowrap",
            render: function (data, type, full) {
                var total = parseInt(data, 10) || 0;
                if (type !== "display") return total;
                var inCount = parseInt(full.total_in, 10) || 0;
                var outCount = parseInt(full.total_out, 10) || 0;
                var quoteCount = parseInt(full.total_quotation, 10) || 0;
                var setCount = parseInt(full.total_set, 10) || 0;
                var inSet = Boolean(full.in_product_set || setCount > 0);
                var setNames = full.product_set_names ? String(full.product_set_names) : "";

                var prodId = full.product_id || full.id;
                var detailLink = prodId ? ('/product/' + prodId) : '#';

                var tooltip = 'Total: ' + total + ' Transaksi (Masuk: ' + inCount + ', Keluar: ' + outCount + ', Quote: ' + quoteCount + (setCount > 0 ? ', Product Set: ' + setCount : '') + ')';

                var setBadge = "";
                if (inSet) {
                    var setTitle = setNames ? ('Masuk Product Set: ' + setNames.replace(/"/g, '&quot;')) : 'Masuk ke dalam Product Set';
                    setBadge = '<span class="badge bg-label-info ms-1 py-1 px-1 fw-semibold" data-bs-toggle="tooltip" data-bs-placement="top" title="' + setTitle + '"><i class="mdi mdi-package-variant-closed me-1"></i>Set</span>';
                }

                if (total > 0) {
                    return (
                        '<div class="d-inline-flex align-items-center justify-content-center">' +
                        '<a href="' + detailLink + '" class="badge rounded-pill bg-label-primary fw-bold px-2 py-1 text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="' + tooltip + '">' +
                        '<i class="mdi mdi-swap-vertical me-1"></i>' + total +
                        '</a>' +
                        setBadge +
                        '</div>'
                    );
                }

                if (inSet) {
                    return (
                        '<div class="d-inline-flex align-items-center justify-content-center">' +
                        '<span class="badge rounded-pill bg-label-secondary px-2 py-1 me-1" data-bs-toggle="tooltip" title="' + tooltip + '">0</span>' +
                        setBadge +
                        '</div>'
                    );
                }

                return '<span class="badge rounded-pill bg-label-secondary px-2 py-1" data-bs-toggle="tooltip" title="' + tooltip + '">0</span>';
            },
        });
    }

    // Aksi column (Salin data 1 baris)
    dtColumns.push({
        data: null,
        className: "text-center text-nowrap",
        orderable: false,
        searchable: false,
        responsivePriority: 1,
        render: function (data, type, full) {
            var rowText = formatRowText(full);
            return (
                '<button type="button" class="btn btn-xs btn-icon btn-label-primary shadow-none btn-copy-row" ' +
                'data-row-text="' + rowText.replace(/"/g, '&quot;') + '" ' +
                'data-bs-toggle="tooltip" data-bs-placement="top" title="Salin info produk baris ini">' +
                '<i class="mdi mdi-content-copy mdi-18px"></i>' +
                '</button>'
            );
        },
    });

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
            error: function (xhr, error, thrown) {
                console.error("Gagal memuat katalog produk:", error, thrown);
                $("#table-filtered-info").html(
                    '<span class="text-danger"><i class="mdi mdi-alert-circle-outline me-1"></i>Gagal memuat data. <button type="button" class="btn btn-xs btn-outline-danger ms-1" id="btn-retry-table">Coba Lagi</button></span>'
                );
            }
        },
        columns: dtColumns,
        order: [[1, "desc"]],
        dom:
            '<"row px-3 pt-3 pb-2"<"col-sm-12 col-md-6 d-flex align-items-center"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
            '<"table-responsive"t>' +
            '<"row px-3 pt-2 pb-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        buttons: [
            {
                extend: "excel",
                text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
                className: "btn-export-hidden-excel d-none",
                title: "Katalog_Produk_SparePart_Reftech",
                exportOptions: {
                    columns: ":visible:not(.control)",
                },
            },
            {
                extend: "csv",
                text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV',
                className: "btn-export-hidden-csv d-none",
                title: "Katalog_Produk_SparePart_Reftech",
                exportOptions: {
                    columns: ":visible:not(.control)",
                },
            },
            {
                extend: "print",
                text: '<i class="mdi mdi-printer-outline me-1"></i>Print',
                className: "btn-export-hidden-print d-none",
                title: "Katalog Produk & Spare Part - Reftech",
                exportOptions: {
                    columns: ":visible:not(.control)",
                },
            },
        ],
        drawCallback: function () {
            if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (el) {
                    bootstrap.Tooltip.getOrCreateInstance(el, {
                        html: true,
                        sanitize: false,
                        container: "body"
                    });
                });
            } else {
                $('[data-toggle="tooltip"]').tooltip({ html: true, container: "body" });
                $('[data-bs-toggle="tooltip"]').tooltip({ html: true, container: "body" });
            }

            var api = this.api();
            var count = api ? api.rows({ filter: "applied" }).count() : 0;
            var total = api ? api.rows().count() : 0;
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

        if (dt_product) {
            dt_product.draw();
        }
    }

    $(document).on("click", "#sales-filter-btn-group button[data-filter]", function () {
        var filter = $(this).data("filter");
        applyFilter(filter);
    });

    $(document).on("click", ".stat-card[data-filter]", function () {
        var filter = $(this).data("filter");
        applyFilter(filter);
    });

    // ── Refresh & Retry Button ──────────────────────────────────────────
    function reloadCatalogData(forceRefresh, onComplete) {
        $("#table-filtered-info").html(
            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memuat data...'
        );
        var targetUrl = forceRefresh ? Url + "?refresh=1&_t=" + Date.now() : Url;
        if (dt_product) {
            dt_product.ajax.url(targetUrl).load(function () {
                if (typeof onComplete === "function") {
                    onComplete();
                }
            }, false);
        }
    }

    $("#btn-refresh-table").on("click", function () {
        var $btn = $(this);
        var $icon = $btn.find("i");
        $btn.prop("disabled", true);
        $icon.addClass("mdi-spin");

        reloadCatalogData(true, function () {
            $btn.prop("disabled", false);
            $icon.removeClass("mdi-spin");
        });
    });

    $(document).on("click", "#btn-retry-table", function () {
        reloadCatalogData(true);
    });

    // ── Copy Row Data to Clipboard ──────────────────────────────────────
    $(document).on("click", ".btn-copy-row", function (e) {
        e.stopPropagation();
        var rowText = $(this).attr("data-row-text") || $(this).data("row-text");
        if (!rowText) return;

        var $btn = $(this);
        var origHtml = $btn.html();

        function showSuccess() {
            $btn.removeClass("btn-label-primary").addClass("btn-success").html('<i class="mdi mdi-check mdi-18px"></i>');
            setTimeout(function () {
                $btn.removeClass("btn-success").addClass("btn-label-primary").html(origHtml);
            }, 1500);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(rowText).then(function () {
                showSuccess();
            }).catch(function () {
                var $temp = $("<textarea>");
                $("body").append($temp);
                $temp.val(rowText).select();
                document.execCommand("copy");
                $temp.remove();
                showSuccess();
            });
        } else {
            var $temp = $("<textarea>");
            $("body").append($temp);
            $temp.val(rowText).select();
            document.execCommand("copy");
            $temp.remove();
            showSuccess();
        }
    });

    // ── Export Trigger Handlers ─────────────────────────────────────────
    $(document).on("click", "#btn-export-excel", function () {
        if (dt_product) {
            dt_product.button(".btn-export-hidden-excel").trigger();
        }
    });

    $(document).on("click", "#btn-export-csv", function () {
        if (dt_product) {
            dt_product.button(".btn-export-hidden-csv").trigger();
        }
    });

    $(document).on("click", "#btn-export-print", function () {
        if (dt_product) {
            dt_product.button(".btn-export-hidden-print").trigger();
        }
    });
});
