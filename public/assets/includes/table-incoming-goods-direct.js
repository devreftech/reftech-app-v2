$(function () {
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

    // Kolom Item selalu jadi tombol expand yang buka child row berisi rincian
    // tiap item — sama polanya kayak tabel PR, biar tampilannya konsisten.
    function itemCol(data, type, full) {
        if (type !== "display") return data || "-";
        if (full.items_detail) {
            return '<a href="javascript:;" class="item-expand-toggle text-primary fw-semibold text-decoration-none">' +
                '<i class="mdi mdi-chevron-right item-expand-icon me-1"></i>' + (data || "-") + "</a>";
        }
        return data || "-";
    }

    // Format items_detail sama kayak tabel PR ("deskripsi::(kosong)::(kosong)")
    // biar bisa pakai parser yang sama — cuma badge extra stock/GO gak pernah kepakai di sini.
    function itemsChildHtml(itemsDetail) {
        if (!itemsDetail) {
            return '<div class="p-2 ps-4 text-muted small">Tidak ada rincian item.</div>';
        }
        var rows = itemsDetail.split("||").map(function (it) {
            var parts = it.split("::");
            var desc = parts[0] || it;
            return "<li>" + desc + "</li>";
        }).join("");
        return '<div class="p-2 ps-4"><ul class="mb-0 ps-3 small">' + rows + "</ul></div>";
    }

    var $table = $(".datatable-incoming-goods-direct");
    if (!$table.length) return;

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/purchase-order/incoming/direct",
            headers: { "Content-Type": "application/json" },
            dataSrc: function (json) {
                var count = (json.data || []).length;
                $("#menunggu-penerimaan-direct-count-badge").text(count).toggleClass("d-none", count === 0);
                window.updateIncomingGoodsTotalBadge && window.updateIncomingGoodsTotalBadge("direct", count);
                return json.data || [];
            },
        },
        columns: [
            { data: "no_po" },
            { data: "category" },
            { data: "item" },
            { data: "supplier" },
            { data: "cargo" },
            { data: "po_date" },
            { data: null },
            { data: null },
            // Kolom tersembunyi biar search box ikut nyari nama unit/part di rincian item.
            { data: "items_detail" },
        ],
        columnDefs: [
            {
                // No PO — sama seperti tab Purchase Order di halaman Purchase Request.
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
                // Type — kategori PO tanpa PR ini (Unit/Parts), nentuin form GR mana yang
                // dipakai (Unit lewat UnitProductInController, Parts lewat goods-receipt-direct).
                targets: 1,
                className: "text-center",
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    if (data === "Unit") {
                        return '<span class="badge bg-label-warning">Unit</span>';
                    }
                    return '<span class="badge bg-label-primary">Parts</span>';
                },
            },
            { targets: 2, render: itemCol },
            {
                // Vendor — dikasih badge Lokal/Impor di depan nama, ditentukan dari info supplier-nya.
                targets: 3,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var badgeMap = {
                        Lokal: '<span class="badge bg-label-success me-1">Lokal</span>',
                        Impor: '<span class="badge bg-label-info me-1">Impor</span>',
                    };
                    var badge = badgeMap[full.purchase_type] || "";
                    return badge + (data || "-");
                },
            },
            { targets: 4, render: function (data) { return data || "-"; } },
            { targets: 5, render: function (data) { return dateCol(data); } },
            {
                // is_on_delivery: 1 kalau on_delivery_at sudah diisi Admin (lihat
                // POController::deliveryUnit) — beda konsep dari purchase_type PR karena
                // PO di tabel ini gak lewat PurchaseRequestDetailAllocation sama sekali.
                targets: 6,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    if (full.is_on_delivery == 1) {
                        return '<span class="badge bg-label-warning"><i class="mdi mdi-truck-delivery-outline me-1"></i>Dikirim</span>';
                    }
                    return '<span class="badge bg-label-secondary"><i class="mdi mdi-clock-outline me-1"></i>Belum Dikirim</span>';
                },
            },
            {
                // Action GR — Unit ke UnitProductInController, selain itu (Parts tanpa PR)
                // ke PurchaseController::goodsReceiptFormDirect().
                targets: 7,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    if (full.is_on_delivery != 1) {
                        return '<span class="text-muted small">Menunggu Info Pengiriman</span>';
                    }
                    var url = full.category === "Unit"
                        ? route("unit-product-in.goods-receipt-form", full.id)
                        : route("purchase.goods-receipt-direct", full.id);
                    return '<a href="' + url + '" class="btn btn-sm btn-primary text-white waves-effect waves-light">' +
                        '<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>GR</a>';
                },
            },
            { targets: 8, visible: false, orderable: false },
        ],
        order: [[5, "asc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Tidak ada PO tanpa Purchase Request yang sedang menunggu penerimaan." },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });

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
});
