$(function () {
    // Pastikan tidak ada filter global DataTables dari script demo lain yang menyaring data
    if ($.fn.dataTableExt && $.fn.dataTableExt.afnFiltering) {
        $.fn.dataTableExt.afnFiltering.length = 0;
    }

    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    function shortCode(data) {
        var full = data || "-";
        var short = full.length > 6 ? full.substring(0, 6) + "…" : full;
        return { full: full, short: short };
    }

    function signCol(data, type, full) {
        var name = (full && full.user_name) ? full.user_name : "-";
        if (type !== "display") return name;
        var initials = name
            .split(" ")
            .map(function (w) {
                return w.charAt(0);
            })
            .slice(0, 2)
            .join("")
            .toUpperCase();
        var colors = ["bg-label-primary", "bg-label-success", "bg-label-warning", "bg-label-danger", "bg-label-info", "bg-label-secondary"];
        var colorClass = colors[name.charCodeAt(0) % colors.length];
        var av = (full && full.user_image)
            ? '<img src="/' + full.user_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + "</div>";
        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + "</span>";
    }

    function itemCol(data, type, full) {
        if (type !== "display") return data || "-";
        if (full && (full.item_count || 0) > 1 && full.items_detail) {
            return '<a href="javascript:;" class="item-expand-toggle text-primary fw-semibold text-decoration-none">' +
                '<i class="mdi mdi-chevron-right item-expand-icon me-1"></i>' + (data || "-") + "</a>";
        }
        return data || "-";
    }

    function itemsChildHtml(itemsDetail) {
        if (!itemsDetail) {
            return '<div class="p-2 ps-4 text-muted small">Tidak ada rincian item.</div>';
        }
        var rows = itemsDetail.split("||").map(function (it) {
            return "<li>" + it + "</li>";
        }).join("");
        return '<div class="p-2 ps-4"><ul class="mb-0 ps-3 small">' + rows + "</ul></div>";
    }

    function bindItemExpand($table, dt) {
        $table.on("click", ".item-expand-toggle", function (e) {
            e.preventDefault();
            var $icon = $(this).find(".item-expand-icon");
            var tr = $(this).closest("tr");
            var row = dt.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                $icon.removeClass("mdi-chevron-down").addClass("mdi-chevron-right");
            } else {
                row.child(itemsChildHtml(row.data() ? row.data().items_detail : "")).show();
                $icon.removeClass("mdi-chevron-right").addClass("mdi-chevron-down");
            }
        });
    }

    function paymentStatusCol(data, type) {
        if (type !== "display") return data || "";
        if (!data) return '<span class="text-muted">-</span>';
        if (data.indexOf("tempo") === 0) {
            var raw = data.substring(6).trim();
            var label = "Credit";
            if (raw && raw.toLowerCase() !== "credit" && raw.toLowerCase() !== "tempo") {
                if (/^\d+$/.test(raw)) {
                    label = "Credit (" + raw + " Days)";
                } else {
                    var match = raw.match(/(\d+)\s*(days?|hari)/i);
                    if (match) {
                        label = "Credit (" + match[1] + " Days)";
                    } else {
                        label = "Credit (" + raw + ")";
                    }
                }
            }
            return '<span class="badge bg-label-info">' + label + '</span>';
        }
        if (data === "confirmed") {
            return '<span class="badge bg-label-success">Sudah Dikonfirmasi</span>';
        }
        if (data === "unconfirmed") {
            return '<span class="badge bg-label-warning">Belum Dikonfirmasi</span>';
        }
        return '<span class="text-muted">-</span>';
    }

    function initPurchaseRequestTable(selector, ajaxUrl, withDelivery, hideNoPo, showPayment, showGr, badgeKey) {
        var $table = $(selector);
        if (!$table.length) return;

        var offset = showGr ? 1 : 0;
        var columns = [];
        if (showGr) {
            columns.push({ data: "no_gr", defaultContent: "-" });
        }
        columns.push(
            { data: "no_pr", defaultContent: "-" },
            { data: "no_po", defaultContent: "-" },
            { data: "no_pending", defaultContent: "-" },
            { data: "company", defaultContent: "-" },
            { data: "item", defaultContent: "-" },
            { data: "qty_full", defaultContent: "-" },
            { data: "date", defaultContent: "-" }
        );
        if (showPayment) {
            columns.push({ data: "payment_status", defaultContent: "" });
        }
        columns.push({ data: "user_name", defaultContent: "-" });

        var signTarget = columns.length - 1;

        var columnDefs = [
            {
                targets: offset + 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var code = shortCode(data);
                    if (!full || !full.id) return '<span data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                    return '<a href="/purchase-request/' + full.id + '" class="fw-semibold text-primary" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                },
            },
            {
                targets: offset + 1,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    if (!data) return "-";
                    if (full && full.id_po) {
                        var code = shortCode(data);
                        return '<a href="/purchase/' + full.id_po + '" class="fw-semibold text-primary" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                    }
                    return data;
                },
            },
            {
                targets: offset + 2,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var code = shortCode(data);
                    if (!full || !full.id) return '<span data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                    return '<a href="/purchase-request/' + full.id + '" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                },
            },
            { targets: offset + 3, render: function (data) { return data || "-"; } },
            { targets: offset + 4, render: itemCol },
            { targets: offset + 5, className: "text-center", render: function (data) { return data || "-"; } },
            { targets: signTarget, className: "text-center", orderable: false, searchable: false, render: signCol },
        ];

        if (showGr) {
            columnDefs.push({
                targets: 0,
                render: function (data, type, full) {
                    if (!showGr || type !== "display") return data || "-";
                    if (!data) return "-";
                    if (full && full.id_gr) {
                        return '<a href="/product-in/' + full.id_gr + '" class="fw-semibold text-primary" data-bs-toggle="tooltip" title="Lihat detail Barang Masuk">' + data + "</a>";
                    }
                    return '<span class="fw-semibold text-dark">' + data + "</span>";
                },
            });
        }

        if (hideNoPo) {
            columnDefs.push({ targets: offset + 1, visible: false });
        }

        if (showPayment) {
            columnDefs.push({ targets: signTarget - 1, className: "text-center", render: paymentStatusCol });
        }

        if (withDelivery) {
            columnDefs.push({
                targets: offset + 6,
                render: function (data, type, full) {
                    if (type !== "display") return (full && full.purchase_date) ? full.purchase_date : "";
                    var cargo = (full && full.cargo) ? full.cargo : "-";
                    var resi = (full && full.no_resi) ? full.no_resi : "Belum ada resi";
                    var tglBeli = (full && full.purchase_date) ? dateCol(full.purchase_date) : "-";
                    return '<div class="small">' + cargo + '<div class="text-muted">' + resi + '</div>' +
                        '<div class="text-muted">Tgl Beli: ' + tglBeli + "</div></div>";
                },
            });
        } else {
            columnDefs.push({ targets: offset + 6, render: function (data) { return dateCol(data); } });
        }

        var dt = $table.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                type: "GET",
                url: ajaxUrl,
                dataSrc: "data",
                headers: { "Content-Type": "application/json" },
            },
            columns: columns,
            columnDefs: columnDefs,
            order: [[offset + 6, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Tidak ada data Purchase Request." },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (badgeKey) {
                    var api = this.api ? this.api() : null;
                    var total = (api && api.page && api.page.info()) ? (api.page.info().recordsTotal || 0) : 0;
                    $('.stat-count-' + badgeKey).text(total);
                    $('.stat-badge-' + badgeKey).text(total);
                    var $tabBadge = $('.tab-badge-' + badgeKey);
                    if (total >= 1) {
                        $tabBadge.text(total).show();
                    } else {
                        $tabBadge.hide();
                    }
                }
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseRequestTable(".datatable-purchase-request-new", "/db/purchase-request/new", false, true, true, false, "new");
    initPurchaseRequestTable(".datatable-purchase-request-acc", "/db/purchase-request/acc", false, true, true, false, "acc");
    initPurchaseRequestTable(".datatable-purchase-request-delivery", "/db/purchase-request/delivery", true, false, false, false, "delivery");
    initPurchaseRequestTable(".datatable-purchase-request-done", "/db/purchase-request/done", false, false, false, true, "done");

    function initPurchaseOrderTable(selector, ajaxUrl, badgeKey) {
        var $table = $(selector);
        if (!$table.length) return;

        var dt = $table.DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                type: "GET",
                url: ajaxUrl,
                dataSrc: "data",
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_po", defaultContent: "-" },
                { data: "no_pr", defaultContent: "-" },
                { data: "no_pending", defaultContent: "-" },
                { data: "company", defaultContent: "-" },
                { data: "item_count", defaultContent: "0" },
                { data: "date", defaultContent: "-" },
                { data: "receipt_status", defaultContent: "-" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id) return '<span data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</span>";
                        return '<a href="/purchase/' + full.id + '" class="fw-semibold text-primary" data-bs-toggle="tooltip" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id_pending) {
                            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        return '<a href="/purchase-request/' + full.id_pending + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full || !full.id_pending) {
                            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        return '<a href="/purchase-request/' + full.id_pending + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                { targets: 3, render: function (data) { return data || "-"; } },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var text = data + " item";
                        if (full && full.items_detail) {
                            return '<a href="javascript:;" class="item-expand-toggle text-primary fw-semibold text-decoration-none">' +
                                '<i class="mdi mdi-chevron-right item-expand-icon me-1"></i>' + text + "</a>";
                        }
                        return text;
                    },
                },
                { targets: 5, render: function (data) { return dateCol(data); } },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data || "-";
                        if (data === "Received") {
                            return '<span class="badge bg-label-success">Diterima</span>';
                        }
                        return '<span class="badge bg-label-warning">Menunggu Pengiriman</span>';
                    },
                },
            ],
            order: [[5, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: { emptyTable: "Belum ada Purchase Order." },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
                if (badgeKey) {
                    var api = this.api ? this.api() : null;
                    var total = (api && api.page && api.page.info()) ? (api.page.info().recordsTotal || 0) : 0;
                    $('.stat-count-' + badgeKey).text(total);
                    $('.stat-badge-' + badgeKey).text(total);
                    var $tabBadge = $('.tab-badge-' + badgeKey);
                    if (total >= 1) {
                        $tabBadge.text(total).show();
                    } else {
                        $tabBadge.hide();
                    }
                }
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseOrderTable(".datatable-purchase-order", "/db/purchase-request/po", "po");

    // Adjust columns when switching tabs
    $('#purchaseRequestTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
