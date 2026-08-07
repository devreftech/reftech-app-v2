$(function () {
    var dt_table_aging_report_ar = $(".datatable-aging-report-ar");
    var Url = "/db/aging/report/ar";

    if (dt_table_aging_report_ar.length) {
        const initTooltips = () => {
            const tooltipTriggerList = document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            );
            const tooltipList = [...tooltipTriggerList].map(
                (el) => new bootstrap.Tooltip(el)
            );
        };
        initTooltips();
        // Setup - add a text input to each footer cell
        $(".datatable-aging-report-ar thead tr")
            .clone(true)
            .appendTo(".datatable-aging-report-ar thead");
        $(".datatable-aging-report-ar thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control" placeholder="Search" />'
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_aging_report_ar.DataTable({
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
                { data: "short_invoice" },
                { data: "date" },
                { data: "short_po" },
                { data: "company" },
                { data: "amount" },
                { data: "due_date" },
                { data: "due_status" },
                { data: "tax" },
                { data: "name" },
                { data: "reminder_status" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var id = full["id"];
                            var diff = full["diff"];
                            if (diff >= 10) {
                                var condition_class = " bg-success";
                            } else if (diff >= 0) {
                                var condition_class = " bg-warning";
                            } else {
                                var condition_class = " bg-danger";
                            }
                            detailRoute = route("payment_detail.aging", id);
                            return (
                                '<a class="text-black" href="' +
                                detailRoute +
                                '"><span class="badge badge-dot ' +
                                condition_class +
                                '"></span> ' +
                                data +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: [1, 2, 5, 6, 6, 7, 8],
                    className: "text-center",
                },
                {
                    targets: 4,
                    className: "text-end",
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                "Rp " +
                                new Intl.NumberFormat("id-ID").format(data)
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data, type, full, row) {
                        var dtText = data && data !== "null" ? data : '<span class="text-muted small">-</span>';
                        var invId = full["invoice_id"] || full["id"];
                        var btn = '<button type="button" class="btn btn-xs btn-outline-warning ms-1 btn-quick-due-date" ' +
                            'data-invoice-id="' + invId + '" ' +
                            'data-invoice-no="' + (full["no_invoice"] || full["short_invoice"]) + '" ' +
                            'data-due-date="' + (data || '') + '" ' +
                            'data-inv-date="' + (full["date"] || '') + '" ' +
                            'title="Set / Edit Due Date">' +
                            '<i class="mdi mdi-calendar-edit"></i></button>';
                        return '<div class="d-flex align-items-center justify-content-center">' + dtText + btn + '</div>';
                    }
                },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type, full, row) {
                        var bucket = full["aging_bucket"] || "No Due Date";
                        var badgeClass = "bg-label-secondary";
                        
                        if (bucket === "Current") {
                            badgeClass = "bg-label-success";
                        } else if (bucket === "1-30 Days") {
                            badgeClass = "bg-label-warning";
                        } else if (bucket === "31-60 Days") {
                            badgeClass = "bg-warning text-dark";
                        } else if (bucket === "61-90 Days") {
                            badgeClass = "bg-label-danger";
                        } else if (bucket === ">90 Days") {
                            badgeClass = "bg-danger text-white";
                        }

                        var text = data || bucket;
                        return '<span class="badge ' + badgeClass + ' me-1" style="font-size:11px;">' + bucket + '</span><br><small class="text-muted" style="font-size:10.5px;">' + text + '</small>';
                    }
                },
                {
                    targets: 7,
                    render: function (data, type, row) {
                        var vat;
                        if (data == 11) {
                            vat = "VAT";
                        } else {
                            vat = "Non VAT";
                        }
                        return vat;
                    },
                },
                {
                    targets: 9,
                    render: function (data, type, full, meta) {
                        var date =
                            full["reminder_date"] &&
                            full["reminder_date"] !== "null"
                                ? full["reminder_date"]
                                : "-";
                        var note =
                            full["reminder_note"] &&
                            full["reminder_note"] !== "null"
                                ? full["reminder_note"]
                                : "Belum Ada Reminder";

                        var tooltipTitle = date + " - " + note;

                        return (
                            "<span " +
                            'data-bs-toggle="tooltip" data-container="body" ' +
                            'data-bs-placement="top" ' +
                            'data-bs-custom-class="tooltip-info" ' +
                            'title="' +
                            tooltipTitle +
                            '" ' +
                            'class="badge rounded-pill bg-label-info text-center" ' +
                            'style="cursor:pointer;">' +
                            (data ?? "-") +
                            "</span>"
                        );
                    },
                },
                {
                    targets: -1,
                    render: function (data, type, full, row) {
                        var title, label;
                        if (data == "Reftech") {
                            title = "RJO";
                            label = "bg-label-primary";
                        } else {
                            title = "KII";
                            label = "bg-label-danger";
                        }
                        return (
                            '<span class="badge rounded-pill ' +
                            label +
                            '">' +
                            title +
                            "</span>"
                        );
                    },
                },
            ],
            drawCallback: function (settings) {
                initTooltips();
            },
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.agingDataTables = window.agingDataTables || {};
        window.agingDataTables.general = dt_filter;

        // Quick Filter Pills Event Listener
        $(document).on('click', '.aging-quick-filter', function () {
            $('.aging-quick-filter').removeClass('active');
            $(this).addClass('active');

            var filterType = $(this).data('filter');
            if (filterType === 'all') {
                dt_filter.column(6).search('').draw();
            } else if (filterType === 'overdue') {
                dt_filter.column(6).search('overdue|terlambat', true, false).draw();
            } else if (filterType === '30') {
                dt_filter.column(6).search('31-60|61-90|>90', true, false).draw();
            } else if (filterType === 'current') {
                dt_filter.column(6).search('Current', true, false).draw();
            } else if (filterType === 'nodue') {
                dt_filter.column(6).search('No Due Date|Belum Set', true, false).draw();
            }
        });

        // Quick Edit Due Date Modal Trigger from Table
        $(document).on('click', '.btn-quick-due-date', function (e) {
            e.preventDefault();
            var invId = $(this).data('invoice-id');
            var invNo = $(this).data('invoice-no');
            var dueDate = $(this).data('due-date');
            var invDate = $(this).data('inv-date');

            $('#quick-due-inv-no').text(invNo);
            $('#quick-due-form').attr('action', '/invoice/due_date/' + invId);

            if (invDate) {
                var parts = invDate.split('-');
                if (parts.length === 3) {
                    $('#quick-due-inv-date').val(parts[2] + '-' + parts[1] + '-' + parts[0]);
                }
            }

            if (dueDate && dueDate !== 'null') {
                var dParts = dueDate.split('-');
                if (dParts.length === 3) {
                    $('#quick-due-date-val').val(dParts[2] + '-' + dParts[1] + '-' + dParts[0]);
                }
            } else {
                // Default +30 days from invoice date
                var baseD = $('#quick-due-inv-date').val() ? new Date($('#quick-due-inv-date').val()) : new Date();
                baseD.setDate(baseD.getDate() + 30);
                $('#quick-due-date-val').val(baseD.toISOString().slice(0, 10));
            }

            $('#modalAgingQuickDueDate').modal('show');
        });
    }
    dt_table_aging_report_ar.on("draw", function () {
        initTooltips();
    });
});
