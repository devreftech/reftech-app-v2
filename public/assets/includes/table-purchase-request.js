$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    // Sama seperti format nomor quotation di tab Quotation Sales — cuma 5 karakter
    // pertama + "…", nomor lengkapnya tetap kelihatan lewat tooltip.
    function shortCode(data) {
        var full = data || "-";
        var short = full.length > 6 ? full.substring(0, 6) + "…" : full;
        return { full: full, short: short };
    }

    function signCol(data, type, full) {
        var name = full.user_name || "-";
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
        var av = full.user_image
            ? '<img src="/' + full.user_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + "</div>";
        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + "</span>";
    }

    // Kolom Item — kalau lebih dari 1 item, teksnya jadi tombol expand (bukan cuma
    // "N item" mati) yang saat diklik buka child row berisi rincian tiap item.
    function itemCol(data, type, full) {
        if (type !== "display") return data || "-";
        if ((full.item_count || 0) > 1 && full.items_detail) {
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

    // Dipasang sekali per tabel (event delegation), toggle child row DataTables
    // buat baris yang tombol expand item-nya diklik.
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
                row.child(itemsChildHtml(row.data().items_detail)).show();
                $icon.removeClass("mdi-chevron-right").addClass("mdi-chevron-down");
            }
        });
    }

    function paymentStatusCol(data, type) {
        if (type !== "display") return data || "";
        if (data === "tempo") {
            return '<span class="badge bg-label-info">Credit / Tempo</span>';
        }
        if (data === "confirmed") {
            return '<span class="badge bg-label-success">Sudah Dikonfirmasi</span>';
        }
        if (data === "unconfirmed") {
            return '<span class="badge bg-label-warning">Belum Dikonfirmasi</span>';
        }
        return '<span class="text-muted">-</span>';
    }

    function initPurchaseRequestTable(selector, ajaxUrl, withDelivery, hideNoPo, showPayment, showGr) {
        var $table = $(selector);
        if (!$table.length) return;

        // Kolom "No GR" (kalau ada) dipasang paling depan, jadi semua target kolom
        // lain yang tadinya hardcode geser +1 — offset ini yang ngurus pergeserannya.
        var offset = showGr ? 1 : 0;

        var columns = [];
        if (showGr) {
            columns.push({ data: "no_gr" });
        }
        columns.push(
            { data: "no_pr" },
            { data: "no_po" },
            { data: "no_pending" },
            { data: "company" },
            { data: "item" },
            { data: "qty_full" }
        );

        // Tab Delivery tidak punya kolom Date/Tgl Pembelian terpisah — tanggal
        // pembeliannya digabung ke dalam kolom Pengiriman.
        columns.push(withDelivery ? { data: null } : { data: "date" });

        if (showPayment) {
            columns.push({ data: "payment_status" });
        }

        columns.push({ data: null }); // Sign — selalu di kolom paling kanan
        var signTarget = columns.length - 1;

        // Kolom tersembunyi biar search box ikut nyari nama part di rincian item
        // (bukan cuma teks ringkasan "N item" yang tampil di kolom Item).
        columns.push({ data: "items_detail" });
        var itemsDetailTarget = columns.length - 1;

        var columnDefs = [
            {
                targets: offset,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var url = route("purchase-request.show", full.id);
                    var code = shortCode(data);
                    return '<a href="' + url + '" class="fw-semibold text-primary"' +
                        ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' +
                        code.short + "</a>";
                },
            },
            {
                // No SO — record yang sama dengan No PR (satu baris pending_po),
                // jadi linknya ke halaman yang sama.
                targets: offset + 2,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var url = route("purchase-request.show", full.id);
                    var code = shortCode(data);
                    return '<a href="' + url + '" class="fw-semibold text-primary"' +
                        ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' +
                        code.short + "</a>";
                },
            },
            {
                // No PO — sama kayak No SO, diklik ke halaman detail PO-nya. Cuma bisa
                // dibikin link kalau PR ini persis 1 PO (full.id_po keisi); kalau pecah ke
                // beberapa PO ("N PO") atau belum ada PO sama sekali, tetap teks biasa.
                targets: offset + 1,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    if (!data || !full.id_po) return data || "-";
                    var url = route("purchase.show", full.id_po);
                    var code = shortCode(data);
                    return '<a href="' + url + '" class="fw-semibold text-primary"' +
                        ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' +
                        code.short + "</a>";
                },
            },
            { targets: offset + 3, render: function (data) { return data || "-"; } },
            { targets: offset + 4, render: itemCol },
            { targets: signTarget, className: "text-center", orderable: false, searchable: false, render: signCol },
            { targets: itemsDetailTarget, visible: false, orderable: false },
        ];

        if (showGr) {
            columnDefs.push({
                // No GR — sama polanya, diklik ke halaman detail Goods Receipt-nya. Cuma
                // bisa dibikin link kalau PR ini persis 1 PO (full.id_gr keisi).
                targets: 0,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    if (!data || !full.id_gr) return data || "-";
                    var url = route("product-in.show", full.id_gr);
                    var code = shortCode(data);
                    return '<a href="' + url + '" class="fw-semibold text-primary"' +
                        ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' +
                        code.short + "</a>";
                },
            });
        }

        // No PO belum relevan di tab New — PO baru dibuat setelah PR di-Acc,
        // jadi kolomnya disembunyikan dulu di sini (bukan dihapus, tinggal diaktifkan lagi nanti).
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
                    if (type !== "display") return full.purchase_date || "";
                    var cargo = full.cargo || "-";
                    var resi = full.no_resi || "Belum ada resi";
                    var tglBeli = full.purchase_date ? dateCol(full.purchase_date) : "-";
                    return '<div class="small">' + cargo + '<div class="text-muted">' + resi + '</div>' +
                        '<div class="text-muted">Tgl Beli: ' + tglBeli + "</div></div>";
                },
            });
        } else {
            columnDefs.push({ targets: offset + 6, render: function (data) { return dateCol(data); } });
        }

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: ajaxUrl,
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
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseRequestTable(".datatable-purchase-request-new", "/db/purchase-request/new", false, true, true);
    initPurchaseRequestTable(".datatable-purchase-request-acc", "/db/purchase-request/acc", false, true, true);
    initPurchaseRequestTable(".datatable-purchase-request-delivery", "/db/purchase-request/delivery", true);
    initPurchaseRequestTable(".datatable-purchase-request-done", "/db/purchase-request/done", false, false, false, true);

    function initPurchaseOrderTable(selector, ajaxUrl) {
        var $table = $(selector);
        if (!$table.length) return;

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: ajaxUrl,
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_po" },
                { data: "no_pr" },
                { data: "no_pending" },
                { data: "company" },
                { data: "item_count" },
                { data: "date" },
                { data: "receipt_status" },
                // Kolom tersembunyi biar search box ikut nyari nama part di rincian item.
                { data: "items_detail" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var url = route("purchase.show", full.id);
                        var code = shortCode(data);
                        return '<a href="' + url + '" class="fw-semibold text-primary"' +
                            ' data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' +
                            code.short + "</a>";
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full.id_pending) {
                            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        var url = route("purchase-request.show", full.id_pending);
                        return '<a href="' + url + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                {
                    // Sales Order — record pending_po yang sama dengan No PR, jadi linknya ke halaman yang sama.
                    targets: 2,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "-";
                        var code = shortCode(data);
                        if (!full.id_pending) {
                            return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</span>";
                        }
                        var url = route("purchase-request.show", full.id_pending);
                        return '<a href="' + url + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + code.full + '">' + code.short + "</a>";
                    },
                },
                { targets: 3, render: function (data) { return data || "-"; } },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var text = data + " item";
                        if (full.items_detail) {
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
                { targets: 7, visible: false, orderable: false },
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
            },
        });

        bindItemExpand($table, dt);
    }

    initPurchaseOrderTable(".datatable-purchase-order", "/db/purchase-request/po");
});
