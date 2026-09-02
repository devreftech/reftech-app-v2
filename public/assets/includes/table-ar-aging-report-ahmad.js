$(function () {
    var dt_table_aging_report_ahmad = $(".datatable-aging-report-ahmad");
    var Url = "/db/aging/report/ahmad";

    if (dt_table_aging_report_ahmad.length) {
        const initTooltips = () => {
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            );
            [...tooltipTriggerList].map((el) => new bootstrap.Tooltip(el));
        };
        initTooltips();

        // Setup - add a text input to each footer cell
        $(".datatable-aging-report-ahmad thead tr")
            .clone(true)
            .appendTo(".datatable-aging-report-ahmad thead");
        $(".datatable-aging-report-ahmad thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_aging_report_ahmad.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.year = window.agingYearFilter || "all";
                    d.sales_id = window.agingSalesFilter || "all";
                    return d;
                },
            },
            columns: [
                {
                    data: "short_invoice",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = typeof route === "function" 
                                ? route("payment_detail.aging", id) 
                                : "/payment-detail/aging/" + id;
                            return (
                                '<div class="d-flex flex-column gap-1">' +
                                    '<div class="d-flex align-items-center">' +
                                        '<a class="fw-bold text-primary text-decoration-none" href="' + detailRoute + '">' +
                                            '<i class="mdi mdi-file-document-outline me-1"></i>' + (full["no_invoice"] || data || "-") +
                                        '</a>' +
                                    '</div>' +
                                    '<div class="small text-muted d-flex align-items-center gap-2">' +
                                        '<span><i class="mdi mdi-pound me-1"></i>PO: ' + (full["no_po"] || "-") + '</span>' +
                                        '<span>•</span>' +
                                        '<span><i class="mdi mdi-calendar-blank-outline me-1"></i>' + (full["date"] || "-") + '</span>' +
                                    '</div>' +
                                '</div>'
                            );
                        }
                        return (full["no_invoice"] || data || "") + " " + (full["no_po"] || "") + " " + (full["date"] || "");
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
                            var taxBadge = full["tax"] 
                                ? '<span class="badge bg-label-primary px-2 py-0" style="font-size:10px;">PPN ' + full["tax"] + '%</span>'
                                : '<span class="badge bg-label-secondary px-2 py-0" style="font-size:10px;">Non PPN</span>';
                            return (
                                '<div class="text-end d-flex flex-column align-items-end gap-1">' +
                                    '<span class="fw-bold text-dark fs-6">Rp ' + new Intl.NumberFormat("id-ID").format(data || full["harga_total"] || 0) + '</span>' +
                                    taxBadge +
                                '</div>'
                            );
                        }
                        return data || full["harga_total"] || 0;
                    }
                },
                {
                    data: "due_date",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var diff = full["diff"] !== undefined ? Number(full["diff"]) : null;
                            var statusBadge = "";
                            if (!data || data === "null" || data === "-") {
                                statusBadge = '<span class="badge bg-label-warning rounded-pill px-2 py-1"><i class="mdi mdi-clock-alert-outline me-1"></i>Belum Set Tempo</span>';
                            } else if (diff !== null && diff < 0) {
                                statusBadge = '<span class="badge bg-label-danger rounded-pill px-2 py-1"><i class="mdi mdi-alert-circle-outline me-1"></i>Overdue ' + Math.abs(diff) + ' Hari</span>';
                            } else if (diff !== null && diff >= 0) {
                                statusBadge = '<span class="badge bg-label-success rounded-pill px-2 py-1"><i class="mdi mdi-check-circle-outline me-1"></i>On Due (' + diff + ' Hari)</span>';
                            } else {
                                statusBadge = '<span class="badge bg-label-info rounded-pill px-2 py-1">' + (full["due_status"] || "-") + '</span>';
                            }

                            return (
                                '<div class="d-flex flex-column align-items-center gap-1 text-center">' +
                                    '<span class="small fw-semibold text-dark"><i class="mdi mdi-calendar-clock-outline me-1 text-muted"></i>' + (data || "-") + '</span>' +
                                    statusBadge +
                                '</div>'
                            );
                        }
                        return (data || "") + " " + (full["due_status"] || "");
                    }
                },
                {
                    data: "reminder_status",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var id = full["id"];
                            var date = full["reminder_date"] && full["reminder_date"] !== "null" ? full["reminder_date"] : "-";
                            var note = full["reminder_note"] && full["reminder_note"] !== "null" ? full["reminder_note"] : "Belum Ada Reminder";
                            var tooltipTitle = date + " - " + note;

                            var detailRoute = typeof route === "function" 
                                ? route("payment_detail.aging", id) 
                                : "/payment-detail/aging/" + id;

                            return (
                                '<div class="d-flex align-items-center justify-content-center gap-2">' +
                                    '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + tooltipTitle + '" class="badge rounded-pill bg-label-warning px-2 py-1" style="cursor:pointer;">' +
                                        '<i class="mdi mdi-bell-ring-outline me-1"></i>' + (data ? 'Reminder (' + data + ')' : 'No Reminder') +
                                    '</span>' +
                                    '<a href="' + detailRoute + '" class="btn btn-icon btn-sm btn-label-primary rounded-pill" data-bs-toggle="tooltip" title="Lihat Detail">' +
                                        '<i class="mdi mdi-eye-outline"></i>' +
                                    '</a>' +
                                '</div>'
                            );
                        }
                        return (data || "") + " " + (full["reminder_note"] || "");
                    }
                },
            ],
            drawCallback: function () {
                initTooltips();
            },
            order: [],
            dom: '<"card-header d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-bottom"<"d-flex align-items-center gap-2"l><"d-flex align-items-center gap-2"f>><"table-responsive"t><"card-footer d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 bg-transparent border-top"<"small text-muted"i><"pagination-wrapper"p>>',
        });

        window.agingDataTables = window.agingDataTables || {};
        window.agingDataTables.ahmad = dt_filter;
    }
    dt_table_aging_report_ahmad.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
