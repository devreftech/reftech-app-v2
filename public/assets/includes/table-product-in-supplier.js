$(function () {
    var dt_table_product_in_supplier = $(".datatable-product-in-supplier");
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf("/") + 1);
    var Url = "/db/productIn-supplier/" + id;

    function formatRupiah(val) {
        if (val === null || val === undefined || isNaN(val)) return "Rp 0";
        return "Rp " + parseInt(val).toLocaleString("id-ID");
    }

    function formatDate(dateStr) {
        if (!dateStr) return "-";
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var day = String(d.getDate()).padStart(2, "0");
        var month = String(d.getMonth() + 1).padStart(2, "0");
        var year = d.getFullYear();
        return day + "-" + month + "-" + year;
    }

    function openInvoiceDrawer(full, dt) {
        if (!full) return;

        var offcanvasEl = document.getElementById("invoiceProductInOffcanvas");
        if (!offcanvasEl) return;

        // Header badges & titles
        var invNumber = full.invoice || "INV #" + full.id;
        $("#invDrawerInvoiceBadge").text(invNumber);
        $("#invDrawerInvoiceNo").text(full.invoice || "-");

        var statusBadgeHtml = '<span class="badge bg-label-success rounded-pill px-2.5 py-1 font-11 fw-semibold"><i class="mdi mdi-check-circle-outline me-1"></i>Faktur Barang Masuk</span>';
        if (full.info === "Import") {
            statusBadgeHtml += ' <span class="badge bg-label-info rounded-pill px-2 py-1 font-11"><i class="mdi mdi-airplane-takeoff me-1"></i>Import</span>';
        } else {
            statusBadgeHtml += ' <span class="badge bg-label-secondary rounded-pill px-2 py-1 font-11"><i class="mdi mdi-map-marker-radius-outline me-1"></i>Lokal</span>';
        }
        $("#invDrawerStatusBadge").html(statusBadgeHtml);

        // Document Details
        $("#invDrawerNoDo").text(full.no_do || "-");
        $("#invDrawerNoProductIn").text(full.no_product_in || ("#" + full.id));
        $("#invDrawerDate").text(formatDate(full.date));
        $("#invDrawerPaymentDate").text(formatDate(full.date_payment));

        // Financials
        var subtotal = formatRupiah(full.subtotal);
        var tax = formatRupiah(full.tax);
        var totalVal = full.total !== undefined && full.total !== null ? full.total : (parseInt(full.subtotal || 0) + parseInt(full.tax || 0));
        var total = formatRupiah(totalVal);

        $("#invDrawerSubtotal").text(subtotal);
        $("#invDrawerTax").text(tax);
        $("#invDrawerTotal").text(total);

        // Note
        if (full.note && full.note.trim() !== "" && full.note.trim() !== "-") {
            $("#invDrawerNote").text(full.note);
            $("#invDrawerNoteCard").show();
        } else {
            $("#invDrawerNoteCard").hide();
        }

        // Match all item rows for this invoice / product_in id in the datatable cache
        var matchedItems = [];
        if (dt) {
            var allData = dt.rows().data().toArray();
            matchedItems = allData.filter(function (r) {
                return (full.id && r.id == full.id) || (full.invoice && r.invoice && r.invoice == full.invoice);
            });
        }
        if (matchedItems.length === 0) {
            matchedItems = [full];
        }

        var tbodyHtml = "";
        matchedItems.forEach(function (item, idx) {
            var productName = item.product || "-";
            var productDesc = item.product_desc || item.description || "";
            var qtyText = item.qty ? String(item.qty).trim() : "-";

            var descHtml = "";
            if (productDesc && productDesc.trim() !== "" && productDesc.trim() !== "-") {
                descHtml = '<div class="text-muted font-11 mt-0.5 d-flex align-items-start gap-1">' +
                    '<i class="mdi mdi-text-box-outline text-secondary flex-shrink-0 mt-0.5 font-12"></i>' +
                    '<span>' + productDesc + '</span>' +
                    '</div>';
            }

            tbodyHtml +=
                '<tr>' +
                '<td class="text-muted font-11 text-center align-middle">' + (idx + 1) + '</td>' +
                '<td class="align-middle">' +
                '<div class="fw-semibold text-heading font-12">' + productName + '</div>' +
                descHtml +
                '</td>' +
                '<td class="text-end align-middle">' +
                '<span class="badge bg-label-primary rounded-pill font-11 fw-bold px-2 py-0.5">' + qtyText + '</span>' +
                '</td>' +
                '</tr>';
        });

        $("#invDrawerItemsTbody").html(tbodyHtml);
        $("#invDrawerItemCount").text(matchedItems.length);

        // Action links in footer
        $("#invDrawerDetailBtn").attr("href", "/product-in/" + full.id);
        $("#invDrawerPrintBtn").attr("href", "/product-in/print/" + full.id);

        // Open Offcanvas Drawer
        var bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        bsOffcanvas.show();
    }

    if (dt_table_product_in_supplier.length) {
        $('[data-toggle="tooltip"]').tooltip();

        var dt_product_in_supplier = dt_table_product_in_supplier.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
            },
            columns: [
                { data: null, defaultContent: "" },
                { data: "id" },
                { data: "invoice" },
                { data: "product" },
                { data: "subtotal" },
                { data: "tax" },
                { data: "date" },
                { data: null, defaultContent: "" },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 4,
                    targets: 0,
                    render: function () {
                        return "";
                    },
                },
                {
                    targets: 1,
                    visible: false,
                    orderable: false,
                    searchable: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var invText = data ? String(data).trim() : "Tanpa Invoice";
                        return (
                            '<a href="javascript:void(0);" class="btn-invoice-drawer text-primary fw-bold font-monospace d-inline-flex align-items-center gap-1.5" data-id="' +
                            full.id +
                            '" data-bs-toggle="tooltip" title="Klik untuk membuka rincian faktur">' +
                            '<i class="mdi mdi-receipt-text-outline font-15 text-primary"></i>' +
                            '<span>' +
                            invText +
                            '</span>' +
                            '</a>'
                        );
                    },
                },
                {
                    responsivePriority: 2,
                    targets: 3,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var productText = data || "-";
                        var productDesc = full.product_desc || full.description || "";
                        var qtyText = full.qty ? String(full.qty).trim() : "-";

                        var descHtml = "";
                        if (productDesc && productDesc.trim() !== "" && productDesc.trim() !== "-") {
                            descHtml = '<div class="text-muted font-11 text-truncate mt-0.5" style="max-width: 320px;" title="' + productDesc + '">' +
                                '<i class="mdi mdi-text-box-outline text-secondary me-1"></i>' + productDesc +
                                '</div>';
                        }

                        return (
                            '<div class="py-1">' +
                            '<div class="fw-semibold text-heading font-13" title="' + productText + '">' +
                            productText +
                            '</div>' +
                            descHtml +
                            '<div class="d-flex align-items-center gap-1 mt-1">' +
                            '<span class="badge bg-label-primary rounded-pill font-11 px-2.5 py-0.5 fw-semibold">' +
                            '<i class="mdi mdi-cube-outline me-1"></i>' +
                            qtyText +
                            '</span>' +
                            '</div>' +
                            '</div>'
                        );
                    },
                },
                {
                    targets: 4,
                    className: "text-end align-middle",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return formatRupiah(data);
                    },
                },
                {
                    targets: 5,
                    className: "text-end align-middle",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return formatRupiah(data);
                    },
                },
                {
                    targets: 6,
                    className: "text-center align-middle",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        if (!data) return "-";
                        var formatted = formatDate(data);
                        return '<span class="text-nowrap font-12 font-monospace">' + formatted + '</span>';
                    },
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle",
                    render: function (data, type, full) {
                        return (
                            '<div class="d-flex align-items-center justify-content-center gap-1">' +
                            '<button type="button" class="btn btn-icon btn-xs btn-label-primary waves-effect btn-invoice-drawer" data-id="' +
                            full.id +
                            '" title="Buka Slider Rincian">' +
                            '<i class="mdi mdi-dock-right font-15"></i>' +
                            '</button>' +
                            '<a href="/product-in/' +
                            full.id +
                            '" class="btn btn-icon btn-xs btn-label-secondary waves-effect" title="Buka Halaman Barang Masuk">' +
                            '<i class="mdi mdi-open-in-new font-14"></i>' +
                            '</a>' +
                            '</div>'
                        );
                    },
                },
            ],
            order: [[6, "desc"]],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Rincian Faktur: " + (row.data()["invoice"] || "-");
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? "<tr><td>" + col.title + ":</td><td>" + col.data + "</td></tr>"
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/>').append("<tbody>" + data + "</tbody>") : false;
                    },
                },
            },
        });

        // Click handler for invoice link and drawer button
        $(document).on("click", ".btn-invoice-drawer", function (e) {
            e.preventDefault();
            var rowId = $(this).data("id");
            var rowData = null;

            if (dt_product_in_supplier) {
                var allData = dt_product_in_supplier.rows().data().toArray();
                for (var i = 0; i < allData.length; i++) {
                    if (allData[i].id == rowId) {
                        rowData = allData[i];
                        break;
                    }
                }
            }

            if (rowData) {
                openInvoiceDrawer(rowData, dt_product_in_supplier);
            }
        });

        dt_table_product_in_supplier.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
