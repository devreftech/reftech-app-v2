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

    function rowMatchesFilter(row) {
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
        return row.id_purchase_request
            ? '<span class="badge bg-label-dark">Dari PR</span>'
            : '<span class="badge bg-label-warning">Langsung</span>';
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
        var counts = { all: rows.length, pending: 0, received: 0, noinvoice: 0 };

        rows.forEach(function (row) {
            totalValue += Number(row.total) || 0;
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
            { data: "category" },
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
                render: function (data, type) {
                    return type === "display" ? categoryBadge(data) : data || "";
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
                render: function (data, type) {
                    if (type !== "display") return data || "";
                    return data
                        ? '<span class="badge bg-label-secondary">' + data + "</span>"
                        : '<span class="text-muted">-</span>';
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
