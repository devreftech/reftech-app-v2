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
                // success: function (hasil, Url) {
                //     console.log("Url:", Url);
                //     console.log(hasil);
                // },
                // error: function (error) {
                //     console.log("Url:", Url);
                //     console.error("Error:", error);
                //     console.log("error disini");
                // },
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
                        // pastikan data dari Laravel aman
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

                        // tampilkan tooltip dengan kombinasi tanggal + catatan
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
                console.log("drawCallback");
                initTooltips();
            },
            order: [],
            // orderCellsTop: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });
    }
    dt_table_aging_report_ar.on("draw", function () {
        initTooltips();
    });
});
