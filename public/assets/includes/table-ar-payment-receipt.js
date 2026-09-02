$(function () {
    var dt_table_payment_receipt_ar = $(".datatable-payment-receipt-ar");
    var Url = "/db/payment/receipt/ar";

    if (dt_table_payment_receipt_ar.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-payment-receipt-ar thead tr")
            .clone(true)
            .appendTo(".datatable-payment-receipt-ar thead");
        $(".datatable-payment-receipt-ar thead tr:eq(1) th").each(function (i) {
            var title = $(this).text().trim();
            $(this).html(
                '<input type="text" class="form-control form-control-sm bg-white" placeholder="Filter ' +
                    title +
                    '" />'
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_payment_receipt_ar.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.year = window.paymentYearFilter || "all";
                    d.sales_id = window.paymentSalesFilter || "all";
                    return d;
                },
            },
            columns: [
                {
                    data: "no_receipt",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var id = full["id"];
                            var level = full["level"];
                            var file = full["file"];
                            var condition_class = level === 0 ? (file === null ? "bg-danger" : "bg-warning") : "bg-success";
                            var detailRoute = typeof route === "function" 
                                ? route("payment_detail.payment", id) 
                                : "/payment-detail/payment/" + id;
                            
                            var flagBadge = full["flag"] 
                                ? '<span class="badge bg-label-secondary py-0 px-2 ms-1" style="font-size:10px;">' + full["flag"] + '</span>' 
                                : '';

                            return (
                                '<div class="d-flex flex-column gap-1">' +
                                    '<div class="d-flex align-items-center">' +
                                        '<span class="badge badge-dot me-1 ' + condition_class + '"></span>' +
                                        '<a class="fw-bold text-primary text-decoration-none" href="' + detailRoute + '">' +
                                            '<i class="mdi mdi-receipt-text-outline me-1"></i>' + (data || "-") +
                                        '</a>' +
                                        flagBadge +
                                    '</div>' +
                                    '<div class="small text-muted d-flex align-items-center gap-2">' +
                                        '<span><i class="mdi mdi-file-document-outline me-1"></i>' + (full["no_invoice"] || "-") + '</span>' +
                                        '<span>•</span>' +
                                        '<span><i class="mdi mdi-calendar-blank-outline me-1"></i>' + (full["date"] || "-") + '</span>' +
                                    '</div>' +
                                '</div>'
                            );
                        }
                        return (data || "") + " " + (full["no_invoice"] || "") + " " + (full["date"] || "") + " " + (full["flag"] || "");
                    }
                },
                {
                    data: "company",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var salesName = full["name"] ? full["name"] : "Unassigned";
                            return (
                                '<div class="d-flex flex-column gap-1">' +
                                    '<span class="fw-bold text-dark">' + (data || "-") + '</span>' +
                                    '<small class="text-muted"><i class="mdi mdi-account-tie-outline me-1 text-secondary"></i>Sales: <span class="fw-medium text-secondary">' + salesName + '</span></small>' +
                                '</div>'
                            );
                        }
                        return (data || "") + " " + (full["name"] || "");
                    }
                },
                {
                    data: "amount",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var sisa = full["sisa"] ? Number(full["sisa"]) : 0;
                            var sisaColor = sisa > 0 ? "text-danger" : "text-muted";
                            return (
                                '<div class="text-end d-flex flex-column gap-1">' +
                                    '<span class="fw-bold text-success fs-6">Rp ' + new Intl.NumberFormat("id-ID").format(data || 0) + '</span>' +
                                    '<small class="' + sisaColor + '">Sisa: Rp ' + new Intl.NumberFormat("id-ID").format(sisa) + '</small>' +
                                '</div>'
                            );
                        }
                        return data;
                    }
                },
                {
                    data: "title",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var isPartial = data === "Partial";
                            var statusBadge = isPartial 
                                ? '<span class="badge bg-label-warning rounded-pill px-2 py-1"><i class="mdi mdi-clock-outline me-1"></i>Partial</span>'
                                : '<span class="badge bg-label-success rounded-pill px-2 py-1"><i class="mdi mdi-check-circle-outline me-1"></i>Full Paid</span>';
                            
                            var method = full["method"] || "-";
                            var confirmInfo = full["date_confirm"] 
                                ? '<span class="text-success"><i class="mdi mdi-check-decagram me-1"></i>' + full["date_confirm"] + '</span>'
                                : '<span class="text-muted"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>';

                            return (
                                '<div class="d-flex flex-column align-items-center gap-1 text-center">' +
                                    statusBadge +
                                    '<small class="text-muted" style="font-size: 11px;">' + method + ' • ' + confirmInfo + '</small>' +
                                '</div>'
                            );
                        }
                        return (data || "") + " " + (full["method"] || "") + " " + (full["date_confirm"] || "");
                    }
                },
            ],
            order: [],
            dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-bottom"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>><"table-responsive"t><"card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-top"<"small text-muted"i><"pagination-wrapper"p>>',
        });

        window.paymentDataTables = window.paymentDataTables || {};
        window.paymentDataTables.receipt = dt_filter;
    }
    dt_table_payment_receipt_ar.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
