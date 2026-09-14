$(function () {
    var dt_table_product = $(".datatable-product-out");
    var Url = "/db/productOut";

    if (!dt_table_product.length) return;

    $('[data-bs-toggle="tooltip"]').tooltip();

    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    var dt_product = dt_table_product.DataTable({
        ajax: {
            type: "GET",
            url: Url,
            headers: {
                "Content-Type": "application/json",
            },
        },
        columns: [
            { data: "" },               // 0: control
            { data: "id" },             // 1: id (hidden)
            { data: "no_product_out" }, // 2: no bk
            { data: "invoice" },        // 3: invoice
            { data: "po" },             // 4: po
            { data: "detail_client" },  // 5: client
            { data: "product" },        // 6: item
            { data: "vers" },           // 7: note
            { data: "qty" },            // 8: qty
            { data: "date" },           // 9: date
            { data: null },             // 10: actions
        ],
        columnDefs: [
            {
                // Responsive control
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
                targets: 1,
                searchable: true,
                visible: false,
            },
            {
                // No. Dokumen BK
                targets: 2,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var detailRoute = route("product-out.show", full.id);
                    var noBk = data || ("BK #" + full.id);
                    return '<a class="fw-bold text-primary font-monospace text-decoration-none" href="' +
                        detailRoute + '" data-bs-toggle="tooltip" title="Lihat detail surat jalan">' +
                        '<i class="mdi mdi-file-document-outline me-1"></i>' + noBk + '</a>';
                },
            },
            {
                // No. Invoice / SJ
                responsivePriority: 1,
                targets: 3,
                render: function (data, type, full) {
                    if (type !== "display") return data || "-";
                    var detailRoute = route("product-out.show", full.id);
                    return '<a class="text-heading fw-semibold text-decoration-none" href="' +
                        detailRoute + '">' + (data || "-") + '</a>';
                },
            },
            {
                // No. PO Customer
                targets: 4,
                render: function (data) {
                    if (!data || data === "-") return '<span class="text-muted">-</span>';
                    return '<span class="font-monospace text-dark fw-medium">' + data + '</span>';
                },
            },
            {
                // Customer / Client
                targets: 5,
                render: function (data) {
                    if (!data || data === "-") return '<span class="text-muted">-</span>';
                    return '<div class="d-flex align-items-center gap-1">' +
                        '<i class="mdi mdi-domain text-muted font-14"></i>' +
                        '<span class="fw-semibold text-dark text-truncate" style="max-width: 200px;" title="' + data + '">' + data + '</span>' +
                        '</div>';
                },
            },
            {
                // Product / Item Column (Drawer Trigger)
                targets: 6,
                render: function (data, type, full) {
                    if (type !== "display") return full.total_items || (data || "-");
                    var items = full.items_detail ? full.items_detail.split("||").filter(Boolean) : [];
                    var count = items.length || full.total_items || 1;
                    var totalQty = full.total_qty || (items.length ? items[0].split("::")[2] : 1);

                    if (items.length > 1) {
                        return '<button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-bk-drawer text-start" data-bs-toggle="tooltip" title="Klik untuk membuka slide-over rincian barang keluar">' +
                            '<i class="mdi mdi-package-variant-closed font-14 text-primary"></i>' +
                            '<span class="fw-semibold">' + count + ' item (' + totalQty + ' pcs)</span>' +
                            '</button>';
                    } else if (items.length === 1) {
                        var parts = items[0].split("::");
                        var name = parts[0] || (data || "-");
                        var pn = (parts[1] && parts[1] !== "-") ? parts[1] : "";
                        return '<button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-bk-drawer text-start" data-bs-toggle="tooltip" title="Klik untuk membuka slide-over rincian dokumen">' +
                            '<i class="mdi mdi-package-variant-closed font-14 text-primary"></i>' +
                            '<span class="fw-semibold text-truncate" style="max-width: 220px;" title="' + name + '">' + name + '</span>' +
                            (pn ? ('<span class="badge bg-label-secondary font-10 ms-1">' + pn + '</span>') : '') +
                            '</button>';
                    }
                    return '<span class="text-muted">-</span>';
                },
            },
            {
                // Catatan / Vers
                targets: 7,
                render: function (data, type, full) {
                    var noteText = full.note || data || "-";
                    if (noteText === "-") return '<span class="text-muted">-</span>';
                    return '<div class="text-truncate text-muted small" style="max-width: 150px;" data-bs-toggle="tooltip" title="' + noteText + '">' + noteText + '</div>';
                },
            },
            {
                // Qty Column
                targets: 8,
                className: "text-center",
                render: function (data, type, full) {
                    var qtyVal = full.total_qty || data || 0;
                    return '<span class="badge bg-label-primary px-2 py-1 font-12 fw-bold">' + qtyVal + '</span>';
                },
            },
            {
                // Date Column
                targets: 9,
                render: function (data) {
                    return dateCol(data);
                },
            },
            {
                // Actions Column
                targets: 10,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    var detailRoute = route("product-out.show", full.id);
                    return '<div class="d-inline-flex align-items-center gap-1">' +
                        '<a href="' + detailRoute + '" class="btn btn-sm btn-icon btn-label-primary rounded-circle" data-bs-toggle="tooltip" title="Buka Surat Jalan">' +
                        '<i class="mdi mdi-eye-outline"></i></a>' +
                        '</div>';
                },
            },
        ],
        order: [[1, "desc"]],
        dom:
            '<"card-header flex-column flex-md-row d-flex align-items-center justify-content-between gap-3 pt-3 pb-2"<"head-label"><"dt-action-buttons text-end"B>>' +
            '<"row px-3 pt-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
            '<"table-responsive"t>' +
            '<"row px-3 pb-3 pt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        buttons: [
            {
                extend: "collection",
                className: "btn btn-label-secondary dropdown-toggle me-2 shadow-xs",
                text: '<i class="mdi mdi-export-variant me-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                buttons: [
                    {
                        extend: "print",
                        text: '<i class="mdi mdi-printer-outline me-1"></i>Print',
                        className: "dropdown-item",
                        exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                    },
                    {
                        extend: "pdf",
                        text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
                        className: "dropdown-item",
                        exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                    },
                    {
                        extend: "copy",
                        text: '<i class="mdi mdi-content-copy me-1"></i>Copy',
                        className: "dropdown-item",
                        exportOptions: { columns: [2, 3, 4, 5, 6, 7, 8, 9] },
                    },
                ],
            },
        ],
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        language: {
            emptyTable: "Belum ada riwayat dokumen barang keluar (Product Out).",
            search: "",
            searchPlaceholder: "Cari No BK / Invoice / Client...",
            paginate: {
                next: '<i class="mdi mdi-chevron-right"></i>',
                previous: '<i class="mdi mdi-chevron-left"></i>',
            },
        },
    });

    $("div.head-label").html(
        '<span class="badge bg-label-primary px-3 py-2 font-12 rounded-pill"><i class="mdi mdi-history me-1"></i>Riwayat Pengeluaran Barang</span>'
    );

    // Slide-over Drawer Opener
    window.openProductOutDrawer = function (full) {
        if (!full) return;
        var offcanvasEl = document.getElementById("productOutOffcanvas");
        if (!offcanvasEl) return;

        var noBk = full.no_product_out || ("BK #" + full.id);
        $("#bkDrawerDocCode").text(noBk);
        $("#bkDrawerClient").text(full.detail_client || "-");
        $("#bkDrawerInvoice").text(full.invoice || "-");
        $("#bkDrawerPo").text(full.po || "-");
        $("#bkDrawerDate").text(dateCol(full.date));
        $("#bkDrawerShipping").text(full.shipping || "-");
        $("#bkDrawerNote").text(full.note || full.vers || "-");

        var items = full.items_detail ? full.items_detail.split("||").filter(Boolean) : [];
        var tbodyHtml = "";

        if (items.length > 0) {
            items.forEach(function (item, idx) {
                var parts = item.split("::");
                var name = parts[0] || "-";
                var pn = (parts[1] && parts[1] !== "-") ? parts[1] : "-";
                var q = parts[2] || "-";

                tbodyHtml += '<tr>' +
                    '<td class="text-center text-muted font-11 align-middle">' + (idx + 1) + '</td>' +
                    '<td>' +
                        '<div class="fw-semibold text-heading font-12">' + name + '</div>' +
                        (pn !== "-" ? ('<span class="badge bg-label-secondary font-10 mt-1">PN: ' + pn + '</span>') : '') +
                    '</td>' +
                    '<td class="text-end align-middle"><span class="fw-bold font-13 text-primary">' + q + '</span></td>' +
                    '</tr>';
            });
            $("#bkDrawerItemCount").text(items.length);
            $("#bkDrawerTotalPcs").text((full.total_qty || items.length) + " pcs");
        } else {
            tbodyHtml = '<tr><td class="text-center text-muted font-11">1</td><td><div class="fw-semibold text-heading font-12">' + (full.product || "-") + '</div></td><td class="text-end align-middle"><span class="fw-bold font-13 text-primary">' + (full.total_qty || 1) + ' pcs</span></td></tr>';
            $("#bkDrawerItemCount").text("1");
            $("#bkDrawerTotalPcs").text((full.total_qty || 1) + " pcs");
        }
        $("#bkDrawerItemsTbody").html(tbodyHtml);

        var detailUrl = route("product-out.show", full.id);
        $("#bkDrawerDetailBtn").attr("href", detailUrl);

        var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        bsOffcanvas.show();
    };

    // Delegated click on .btn-bk-drawer
    $(document).on("click", ".btn-bk-drawer", function (e) {
        e.preventDefault();
        var $tr = $(this).closest("tr");
        var dt = $(".datatable-product-out").DataTable();
        var full = dt.row($tr).data();
        if (!full) return;
        window.openProductOutDrawer(full);
    });
});
