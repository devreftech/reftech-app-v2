$(function () {
    "use strict";

    var $table = $(".datatable-purchase-order");
    if (!$table.length) return;

    // Root-relative: kalau halaman dibuka dengan trailing slash (/purchase/),
    // URL relatif "db/purchase-order" akan salah resolve jadi /purchase/db/purchase-order.
    var Url = "/db/purchase-order";
    var poFilter = "all"; // all | pending | received | noinvoice

    function rupiah(n) {
        n = Number(n) || 0;
        return "Rp " + n.toLocaleString("id-ID");
    }

    function rupiahShort(n) {
        n = Number(n) || 0;
        if (n >= 1e9) return "Rp " + (n / 1e9).toFixed(1).replace(/\.0$/, "") + " M";
        if (n >= 1e6) return "Rp " + (n / 1e6).toFixed(1).replace(/\.0$/, "") + " jt";
        return "Rp " + n.toLocaleString("id-ID");
    }

    function isReceived(row) {
        return String(row.receipt_status || "").toLowerCase() === "received";
    }

    function hasInvoice(row) {
        return !!(row.invoice_file || row.no_invoice_supplier);
    }

    function isDirectPurchase(row) {
        return Number(row.is_direct_purchase) === 1 || String(row.no_po || "").indexOf("-DP/") !== -1;
    }

    function rowMatchesFilter(row) {
        if (poFilter === "direct") return isDirectPurchase(row);
        if (poFilter === "po_formal") return !isDirectPurchase(row);
        if (poFilter === "pending") return !isReceived(row);
        if (poFilter === "received") return isReceived(row);
        if (poFilter === "noinvoice") return !hasInvoice(row);
        return true;
    }

    function categoryBadge(cat) {
        var c = String(cat || "").trim();
        var map = {
            Unit: "bg-label-primary",
            Accessories: "bg-label-info",
            Sparepart: "bg-label-secondary",
        };
        var cls = map[c] || "bg-label-secondary";
        return '<span class="badge ' + cls + '">' + (c || "-") + "</span>";
    }

    function sourceBadge(row) {
        var badges = [];
        if (isDirectPurchase(row)) {
            badges.push('<span class="badge bg-label-info font-11"><i class="mdi mdi-cart-arrow-down me-1"></i>Direct</span>');
        } else {
            badges.push('<span class="badge bg-label-primary font-11">PO Resmi</span>');
        }
        if (row.id_purchase_request) {
            badges.push('<span class="badge bg-label-dark font-11">PR</span>');
        }
        return '<div class="d-flex flex-wrap gap-1">' + badges.join("") + "</div>";
    }

    function statusCell(row) {
        var parts = [];
        parts.push(
            isReceived(row)
                ? '<span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Diterima</span>'
                : '<span class="badge bg-label-warning"><i class="mdi mdi-progress-clock me-1"></i>Pending</span>'
        );
        parts.push(
            hasInvoice(row)
                ? '<span class="badge bg-label-info" title="Invoice supplier ada"><i class="mdi mdi-file-check-outline"></i></span>'
                : '<span class="badge bg-label-secondary" title="Belum ada invoice supplier"><i class="mdi mdi-file-remove-outline"></i></span>'
        );
        if (row.vendor_signed_at) {
            parts.push(
                '<span class="badge bg-label-success" title="Sudah ditandatangani vendor"><i class="mdi mdi-draw-pen"></i></span>'
            );
        }
        return '<div class="d-flex flex-wrap gap-1 justify-content-center">' + parts.join("") + "</div>";
    }

    function refreshStats(rows) {
        var now = new Date();
        var totalValue = 0;
        var monthCount = 0;
        var monthValue = 0;
        var pending = 0;
        var noInvoice = 0;
        var counts = { all: rows.length, po_formal: 0, direct: 0, pending: 0, received: 0, noinvoice: 0 };

        rows.forEach(function (row) {
            totalValue += Number(row.total) || 0;
            if (isDirectPurchase(row)) {
                counts.direct++;
            } else {
                counts.po_formal++;
            }
            if (isReceived(row)) counts.received++;
            else {
                counts.pending++;
                pending++;
            }
            if (!hasInvoice(row)) {
                counts.noinvoice++;
                noInvoice++;
            }
            var d = row.date ? new Date(String(row.date).replace(" ", "T")) : null;
            if (d && !isNaN(d) && d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth()) {
                monthCount++;
                monthValue += Number(row.total) || 0;
            }
        });

        $("#po-stat-total").text(rows.length.toLocaleString("id-ID"));
        $("#po-stat-total-badge").text(rows.length.toLocaleString("id-ID"));
        $("#po-stat-value").text(rupiahShort(totalValue));
        $("#po-stat-month").text(monthCount.toLocaleString("id-ID"));
        $("#po-stat-month-value").text(rupiahShort(monthValue));
        $("#po-stat-pending").text(pending.toLocaleString("id-ID"));
        $("#po-stat-noinvoice").text(noInvoice.toLocaleString("id-ID"));

        Object.keys(counts).forEach(function (k) {
            $('[data-po-count="' + k + '"]').text(counts[k].toLocaleString("id-ID"));
        });
    }

    // Header filter inputs (row 2 di thead)
    $table.find("thead tr").clone(true).appendTo($table.find("thead"));
    $table.find("thead tr:eq(1) th").each(function (i) {
        var title = $(this).text().trim();
        if (i === 7) {
            $(this).html("");
            return;
        }
        $(this).html(
            '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
        );
        $("input", this).on("keyup change", function () {
            if (dt.column(i).search() !== this.value) {
                dt.column(i).search(this.value).draw();
            }
        });
    });

    // Export buttons hanya kalau ekstensi HTML5 tersedia di bundle
    var exportButtons = [];
    var btnExt = ($.fn.dataTable.ext && $.fn.dataTable.ext.buttons) || {};
    if (btnExt.print) exportButtons.push({ extend: "print", text: '<i class="mdi mdi-printer-outline me-1"></i>Print', className: "dropdown-item" });
    if (btnExt.csvHtml5) exportButtons.push({ extend: "csv", text: '<i class="mdi mdi-file-delimited-outline me-1"></i>CSV', className: "dropdown-item" });
    if (btnExt.excelHtml5) exportButtons.push({ extend: "excel", text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel', className: "dropdown-item" });
    if (btnExt.copyHtml5) exportButtons.push({ extend: "copy", text: '<i class="mdi mdi-content-copy me-1"></i>Copy', className: "dropdown-item" });

    var dtButtons = exportButtons.length
        ? [
              {
                  extend: "collection",
                  className: "btn btn-label-secondary dropdown-toggle",
                  text: '<i class="mdi mdi-export-variant me-1"></i>Export',
                  buttons: exportButtons,
              },
          ]
        : [];

    function escapeHtml(str) {
        return $("<div>").text(str || "").html();
    }

    function openPurchaseOrderDrawer(full) {
        if (!full) return;

        $('#poDrawerNoPo').text(full.no_po || 'PO-0000');
        $('#poDrawerSummaryNoPo').text(full.no_po || 'PO-0000');

        var statusBadges = [];
        if (isDirectPurchase(full)) {
            statusBadges.push('<span class="badge bg-label-info font-11 rounded-pill"><i class="mdi mdi-cart-arrow-down me-1"></i>Direct Purchase</span>');
        } else {
            statusBadges.push('<span class="badge bg-label-primary font-11 rounded-pill"><i class="mdi mdi-file-document-outline me-1"></i>PO Resmi</span>');
        }
        if (isReceived(full)) {
            statusBadges.push('<span class="badge bg-label-success font-11 rounded-pill ms-1"><i class="mdi mdi-check-circle-outline me-1"></i>Diterima</span>');
        } else {
            statusBadges.push('<span class="badge bg-label-warning font-11 rounded-pill ms-1"><i class="mdi mdi-progress-clock me-1"></i>Pending</span>');
        }
        $('#poDrawerStatusBadge').html(statusBadges.join(' '));

        $('#poDrawerCompany').text(full.company || '-');

        if (full.payment) {
            var pmtStr = String(full.payment).trim();
            var isMarketplace = pmtStr === 'Marketplace (Tokopedia/Shopee)' || pmtStr.toLowerCase().indexOf('marketplace') !== -1;
            var badgeClass = isMarketplace ? 'bg-label-warning' : 'bg-label-info';
            var iconClass = isMarketplace ? 'mdi-shopping-outline' : 'mdi-credit-card-outline';
            var displayText = isMarketplace ? 'Marketplace' : pmtStr;
            $('#poDrawerPaymentSlot').html('<span class="font-11 text-muted d-block mb-1">Tipe Pembayaran:</span><span class="badge ' + badgeClass + ' fw-semibold"><i class="mdi ' + iconClass + ' me-1"></i>' + escapeHtml(displayText) + '</span>').show();
        } else {
            $('#poDrawerPaymentSlot').empty().hide();
        }

        $('#poDrawerDate').text(full.tanggal || (full.date ? moment(full.date).format('DD-MM-YYYY') : '-'));
        $('#poDrawerTotal').text(rupiah(full.total));
        $('#poDrawerAttn').text(full.attn || full.phone || '-');

        if (full.no_pr || full.id_purchase_request || full.id_pr) {
            var prText = full.no_pr || ('PR #' + (full.id_purchase_request || full.id_pr));
            var prId = full.id_pr || full.id_purchase_request;
            var prLink = prId
                ? '<a href="/purchase-request/' + prId + '" class="text-info font-monospace fw-bold" target="_blank">' + escapeHtml(prText) + ' <i class="mdi mdi-open-in-new font-11"></i></a>'
                : '<strong class="text-info font-monospace">' + escapeHtml(prText) + '</strong>';
            $('#poDrawerNoPr').html(prLink);
            $('#poDrawerPrCol').show();
        } else {
            $('#poDrawerPrCol').hide();
        }

        $('#poDrawerCategoryBadge').html(categoryBadge(full.category));

        if (isReceived(full)) {
            $('#poDrawerReceiptBadge').html('<span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Sudah Diterima</span>');
        } else {
            $('#poDrawerReceiptBadge').html('<span class="badge bg-label-warning"><i class="mdi mdi-truck-alert-outline me-1"></i>Belum Diterima</span>');
        }

        if (full.no_gr) {
            var grHtml = '<strong class="text-success font-monospace">' + escapeHtml(full.no_gr) + '</strong>';
            if (full.id_gr) {
                grHtml += ' <a href="/product-in/' + full.id_gr + '" class="btn btn-xs btn-outline-success ms-1"><i class="mdi mdi-open-in-new me-1"></i>Lihat GR</a>';
            }
            $('#poDrawerGrBox').html(grHtml);
        } else {
            $('#poDrawerGrBox').html('<span class="text-muted">-</span>');
        }

        if (full.invoice_file) {
            $('#poDrawerInvoiceBox').html('<a href="/' + full.invoice_file + '" target="_blank" class="btn btn-xs btn-outline-primary"><i class="mdi mdi-file-pdf-box me-1"></i>' + escapeHtml(full.no_invoice_supplier || 'Lihat File Invoice') + '</a>');
        } else if (full.no_invoice_supplier) {
            $('#poDrawerInvoiceBox').html('<span class="fw-semibold font-monospace font-12">' + escapeHtml(full.no_invoice_supplier) + '</span>');
        } else {
            $('#poDrawerInvoiceBox').html('<span class="text-muted small">Belum ada invoice</span>');
        }

        if (full.vendor_signed_at) {
            $('#poDrawerSignBox').html('<span class="badge bg-label-success font-11"><i class="mdi mdi-draw-pen me-1"></i>Signed (' + moment(full.vendor_signed_at).format('DD/MM/YYYY') + ')</span>');
        } else {
            $('#poDrawerSignBox').html('<span class="text-muted small">Belum TTD online</span>');
        }

        // Ordered Items Table
        var tbodyHtml = '';
        var items = full.items_list || full.detail || [];
        if (items.length > 0) {
            items.forEach(function (it, idx) {
                var prodName = escapeHtml(it.product || '-');
                var catBadge = it.category ? '<span class="badge bg-label-secondary font-10 me-1">' + escapeHtml(it.category) + '</span>' : '';
                var kondBadge = it.kondisi ? '<span class="badge bg-label-info font-10">' + escapeHtml(it.kondisi) + '</span>' : '';
                var unit = escapeHtml(it.unit || it.info_qty || 'Pcs');
                var qtyVal = Number(it.qty) || 0;
                var subtotalVal = rupiah(it.amount || (qtyVal * (Number(it.price) || 0)));

                tbodyHtml += '<tr>' +
                    '<td class="text-muted font-11 text-center align-middle">' + (idx + 1) + '</td>' +
                    '<td>' +
                        '<div class="fw-semibold text-heading font-12">' + prodName + '</div>' +
                        '<div class="mt-1">' + catBadge + kondBadge + '</div>' +
                    '</td>' +
                    '<td class="text-center align-middle">' +
                        '<span class="fw-bold font-13 text-primary">' + qtyVal + '</span> <span class="font-11 text-muted">' + unit + '</span>' +
                    '</td>' +
                    '<td class="text-end align-middle">' +
                        '<span class="fw-semibold font-12 text-heading">' + subtotalVal + '</span>' +
                    '</td>' +
                '</tr>';
            });
            $('#poDrawerItemCount').text(items.length);
            $('#poDrawerTotalPcs').text(full.qty_full || (items.length + ' item'));
        } else {
            tbodyHtml = '<tr><td colspan="4" class="text-center py-4 text-muted font-12">Tidak ada rincian item.</td></tr>';
            $('#poDrawerItemCount').text('0');
            $('#poDrawerTotalPcs').text('0 item');
        }
        $('#poDrawerItemsTbody').html(tbodyHtml);

        // Action links in footer
        if (full.id) {
            $('#poDrawerDetailBtn').attr('href', '/purchase/' + full.id).show();
        } else {
            $('#poDrawerDetailBtn').hide();
        }

        var extraHtml = '';
        if (full.id) {
            extraHtml += '<a href="/purchase/print/' + full.id + '" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs"><i class="mdi mdi-printer-outline me-1"></i> Cetak PO</a>';
        }
        var prId = full.id_pr || full.id_purchase_request;
        if (prId) {
            extraHtml += '<a href="/purchase-request/' + prId + '" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs"><i class="mdi mdi-clipboard-text-outline me-1"></i> Buka PR</a>';
        }
        $('#poDrawerAdditionalLinks').html(extraHtml);

        var offcanvasEl = document.getElementById('purchaseOrderOffcanvas');
        if (offcanvasEl) {
            var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            bsOffcanvas.show();
        }
    }

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: Url,
            dataSrc: function (json) {
                var rows = json.data || [];
                refreshStats(rows);
                return rows;
            },
        },
        columns: [
            { data: "no_po" },
            { data: "company" },
            { data: "item_count" },
            { data: null, orderable: false },
            { data: "total" },
            { data: "tanggal" },
            { data: "payment" },
            { data: null, orderable: false, searchable: false },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, full) {
                    // Urut "No PO terakhir": No PO tidak bisa di-sort sebagai teks
                    // (bulan romawi + seq reset per bulan), jadi pakai id auto-increment
                    // yang monoton dengan urutan pembuatan PO.
                    if (type === "sort" || type === "type") return Number(full.id) || 0;
                    if (type !== "display") return data || "";
                    var url = route("purchase.show", full.id);
                    var main = '<a href="' + url + '" class="fw-semibold text-primary">' + (data || "-") + "</a>";
                    var gr = full.no_gr ? '<div class="po-sub">GR: ' + full.no_gr + "</div>" : "";
                    return main + gr;
                },
            },
            {
                targets: 1,
                render: function (data, type, full) {
                    if (type !== "display") return data || "";
                    var name = '<div class="po-vendor-name">' + (data || "-") + "</div>";
                    var attn = full.attn ? '<div class="po-sub">ATTN: ' + full.attn + "</div>" : "";
                    return name + attn;
                },
            },
            {
                targets: 2,
                className: "text-center",
                render: function (data, type, full) {
                    var count = (full && full.item_count !== undefined) ? parseInt(full.item_count) : (data ? parseInt(data) : 0);
                    if (type === "sort" || type === "type") return count;
                    if (type === "filter") {
                        var names = (full && full.items_list) ? full.items_list.map(function (it) { return it.product || ""; }).join(" ") : "";
                        return count + " item " + names;
                    }
                    if (type !== "display") return count + " item";
                    return '<button type="button" class="btn-item-preview d-inline-flex align-items-center gap-1 btn-po-drawer" data-bs-toggle="tooltip" title="Klik untuk membuka slide-over rincian PO &amp; item">' +
                        '<i class="mdi mdi-package-variant-closed font-14 text-primary"></i>' +
                        '<span class="fw-semibold">' + count + ' item</span>' +
                        '</button>';
                },
            },
            {
                targets: 3,
                render: function (data, type, full) {
                    return type === "display" ? sourceBadge(full) : full.id_purchase_request ? "PR" : "Langsung";
                },
            },
            {
                targets: 4,
                className: "text-end",
                render: function (data, type) {
                    return type === "display" ? rupiah(data) : Number(data) || 0;
                },
            },
            {
                targets: 5,
                className: "text-center",
                render: function (data, type, full) {
                    // Tampil dd-mm-yyyy, tapi urut pakai tanggal ISO mentah (purchase_order.date)
                    // biar kronologis — bukan sort teks "dd" di depan.
                    if (type === "sort" || type === "type") return full.date || "";
                    return data || "-";
                },
            },
            {
                targets: 6,
                className: "po-col-payment",
                render: function (data, type) {
                    if (type !== "display") return data || "";
                    if (!data) return '<span class="text-muted">-</span>';
                    var pmt = String(data).trim();
                    if (pmt === 'Marketplace (Tokopedia/Shopee)' || pmt.toLowerCase().indexOf('marketplace') !== -1) {
                        return '<span class="badge bg-label-warning text-wrap lh-sm text-start"><i class="mdi mdi-shopping-outline me-1"></i>Marketplace</span>';
                    }
                    return '<span class="badge bg-label-secondary text-wrap lh-sm text-start" style="white-space: normal !important; max-width: 200px; display: inline-block;">' + escapeHtml(pmt) + '</span>';
                },
            },
            {
                targets: 7,
                className: "text-center",
                render: function (data, type, full) {
                    return type === "display" ? statusCell(full) : "";
                },
            },
        ],
        orderCellsTop: true,
        order: [[0, "desc"]], // No PO terakhir di atas (via id, lihat render targets:0)
        dom:
            '<"row mx-1 mb-2"<"col-sm-12 col-md-6 d-flex align-items-center"l><"col-sm-12 col-md-6 d-flex align-items-center justify-content-md-end gap-2"fB>>' +
            '<"table-responsive"t>' +
            '<"row mx-1 mt-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        displayLength: 10,
        buttons: dtButtons,
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                        return "Detail PO: " + (row.data().no_po || "-");
                    },
                }),
                type: "column",
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col) {
                        return col.title
                            ? "<tr><td>" + col.title + ":</td><td>" + col.data + "</td></tr>"
                            : "";
                    }).join("");
                    return data ? $('<table class="table"/>').append("<tbody>" + data + "</tbody>") : false;
                },
            },
        },
    });

    // Delegated click on .btn-po-drawer to open slide-over
    $(document).on('click', '.btn-po-drawer', function (e) {
        e.preventDefault();
        var $tr = $(this).closest('tr');
        var $table = $tr.closest('table');
        if (!$table.length) return;
        var dt = $table.DataTable();
        var full = dt.row($tr).data();
        if (!full) return;

        openPurchaseOrderDrawer(full);
    });

    // Delegated hover on tooltips
    $(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function () {
        var $el = $(this);
        if (!$el.attr('data-bs-original-title') && !$el.data('bs.tooltip')) {
            new bootstrap.Tooltip(this, { boundary: 'window' }).show();
        }
    });

    // Tab filter (Semua / Belum Diterima / Sudah Diterima / Tanpa Invoice)
    $.fn.dataTable.ext.search.push(function (settings, searchData, dataIndex, rowData) {
        if (settings.nTable !== $table[0]) return true;
        return rowMatchesFilter(rowData);
    });

    $(document).on("click", "[data-po-filter]", function () {
        poFilter = $(this).data("po-filter");
        $("[data-po-filter]").removeClass("active");
        $(this).addClass("active");
        dt.draw();
    });

    $table.on("draw.dt", function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
});
