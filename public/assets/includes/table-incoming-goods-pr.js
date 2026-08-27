// Badge "Menunggu Penerimaan" di tab luar (incomingGoodsTabs) adalah total dari
// kedua sub-tab (Via PR + Tanpa PR) — dipanggil dari masing-masing dataSrc callback.
window.__incomingGoodsCounts = window.__incomingGoodsCounts || {};
window.updateIncomingGoodsTotalBadge = window.updateIncomingGoodsTotalBadge || function (key, count) {
    window.__incomingGoodsCounts[key] = count;
    var total = Object.keys(window.__incomingGoodsCounts).reduce(function (sum, k) {
        return sum + window.__incomingGoodsCounts[k];
    }, 0);
    $("#menunggu-penerimaan-count-badge").text(total).toggleClass("d-none", total === 0);
};

$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    // Sama seperti format nomor di tabel Purchase Request — cuma 6 karakter
    // pertama + "…", nomor lengkapnya tetap kelihatan lewat tooltip.
    function shortCode(data) {
        var full = data || "-";
        var short = full.length > 6 ? full.substring(0, 6) + "…" : full;
        return { full: full, short: short };
    }

    // Kolom Item selalu jadi tombol expand yang buka child row berisi rincian
    // tiap item — sama persis baik cuma 1 item maupun lebih dari 1, biar tampilannya konsisten.
    function itemCol(data, type, full) {
        if (type !== "display") return data || "-";
        if (full.items_detail) {
            return '<a href="javascript:;" class="item-expand-toggle text-primary fw-semibold text-decoration-none">' +
                '<i class="mdi mdi-chevron-right item-expand-icon me-1"></i>' + (data || "-") + "</a>";
        }
        return data || "-";
    }

    // Tiap entri items_detail formatnya "deskripsi::qty_stok_tambahan::status_go",
    // dipisah pas di-GROUP_CONCAT dari server — lihat /db/purchase-order/incoming/pr.
    // qty_stok_tambahan kosong kalau qty yang dibeli di PO sama dengan qty yang
    // dialokasikan buat PR ini (gak ada kelebihan buat stok).
    function itemsChildHtml(itemsDetail) {
        if (!itemsDetail) {
            return '<div class="p-2 ps-4 text-muted small">Tidak ada rincian item.</div>';
        }
        var rows = itemsDetail.split("||").map(function (it) {
            var parts = it.split("::");
            var desc = parts[0] || it;
            var extraStock = parts[1] || "";
            var go = parts[2] || "";
            var badges = "";
            if (extraStock) {
                badges += ' <span class="badge bg-label-info" data-toggle="tooltip"' +
                    ' title="Kelebihan qty dari kebutuhan PR, buat tambahan stok">+' + extraStock + " stok</span>";
            }
            if (go === "Genuine") {
                badges += ' <span class="badge bg-label-success">Genuine</span>';
            } else if (go && go !== "-") {
                badges += ' <span class="badge bg-label-warning">' + go + "</span>";
            }
            return "<li>" + desc + badges + "</li>";
        }).join("");
        return '<div class="p-2 ps-4"><ul class="mb-0 ps-3 small">' + rows + "</ul></div>";
    }

    var $table = $(".datatable-incoming-goods-pr");
    if (!$table.length) return;

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/purchase-order/incoming/pr",
            headers: { "Content-Type": "application/json" },
            dataSrc: function (json) {
                var count = (json.data || []).length;
                $("#menunggu-penerimaan-pr-count-badge").text(count).toggleClass("d-none", count === 0);
                window.updateIncomingGoodsTotalBadge && window.updateIncomingGoodsTotalBadge("pr", count);
                return json.data || [];
            },
        },
        columns: [
            { data: "no_po" },
            { data: "no_pr" },
            { data: "company" },
            { data: "item" },
            { data: "supplier" },
            { data: "cargo" },
            { data: "po_date" },
            { data: null },
            { data: null },
            // Kolom tersembunyi biar search box ikut nyari nama part di rincian item.
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
                // No PR — link ke halaman pending_po yang sama, sama seperti halaman Purchase Request.
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
            { targets: [2, 5], render: function (data) { return data || "-"; } },
            { targets: 3, render: itemCol },
            {
                // Vendor — dikasih badge Lokal/Impor di depan nama, sesuai data purchase_type
                // vendor itu di PO ini (Campuran kalau item-nya campur lokal & impor).
                targets: 4,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var badgeMap = {
                        Lokal: '<span class="badge bg-label-success me-1">Lokal</span>',
                        Impor: '<span class="badge bg-label-info me-1">Impor</span>',
                        Campuran: '<span class="badge bg-label-secondary me-1">Campuran</span>',
                    };
                    var badge = badgeMap[full.purchase_type] || "";
                    return badge + (data || "-");
                },
            },
            { targets: 6, render: function (data) { return dateCol(data); } },
            {
                // Tabel ini nampilin semua PO yang belum diterima — is_on_delivery
                // (dari route /db/purchase-order/incoming/pr) yang bedain PO yang info
                // pengirimannya udah lengkap (siap/lagi jalan) vs yang masih nunggu
                // Admin isi delivery info per-PO.
                targets: 7,
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
                targets: 8,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    if (full.is_on_delivery != 1) {
                        return '<span class="text-muted small">Menunggu Info Pengiriman</span>';
                    }
                    var url = route("purchase.goods-receipt", full.id);
                    return '<a href="' + url + '" class="btn btn-sm btn-primary text-white waves-effect waves-light">' +
                        '<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>GR</a>';
                },
            },
            { targets: 9, visible: false, orderable: false },
        ],
        order: [[6, "asc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: { emptyTable: "Tidak ada PO via Purchase Request yang sedang menunggu penerimaan." },
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
