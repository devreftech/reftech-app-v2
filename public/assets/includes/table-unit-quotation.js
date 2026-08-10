$(function () {
    var dt_table_unit_quotation = $(".datatable-unit-quotation");
    var Url = "/db/unit-quotation";

    if (dt_table_unit_quotation.length) {
        dt_table_unit_quotation.find("thead tr")
            .clone(true)
            .appendTo(dt_table_unit_quotation.find("thead"));

        dt_table_unit_quotation.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_unit_quotation.column(i).search() !== this.value) {
                    dt_unit_quotation.column(i).search(this.value).draw();
                }
            });
        });

        var dt_unit_quotation = dt_table_unit_quotation.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) {
                    d.year = window.quotationYearFilter || 'all';
                    return d;
                },
            },
            columns: [
                { data: "no_quote" },
                { data: "client" },
                { data: "title" },
                { data: "date" },
                { data: "total" },
                { data: "status" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var detailRoute = "/unit-quotation/" + full["id"];
                        var full_no = data || "-";
                        return '<a class="fw-bold text-primary" href="' + detailRoute + '">' + full_no + '</a>';
                    },
                },
                {
                    targets: 1,
                    className: "text-start",
                },
                {
                    targets: 2,
                    className: "text-start",
                },
                {
                    targets: 3,
                    className: "text-center",
                },
                {
                    targets: 4,
                    className: "text-end text-nowrap",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var val = parseFloat(data) || 0;
                        return "Rp " + val.toLocaleString("id-ID", { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var statusMap = {
                            'draft': '<span class="badge bg-label-secondary">Draft</span>',
                            'quotation_sent': '<span class="badge bg-label-primary">Quotation Sent</span>',
                            'hot_prospect': '<span class="badge bg-label-warning">Hot Prospect</span>',
                            'po_received': '<span class="badge bg-label-success">PO Received</span>',
                            'loss': '<span class="badge bg-label-danger">Loss</span>',
                            'cancel': '<span class="badge bg-label-danger">Cancel</span>'
                        };
                        var formatted = statusMap[data] || '<span class="badge bg-label-info">' + (data || '-') + '</span>';
                        if (full.last_note && full.last_note !== 'Belum di update') {
                            formatted += '<br><small class="text-muted fs-tiny" data-bs-toggle="tooltip" title="' + full.last_note + '">' +
                                '<i class="mdi mdi-information-outline me-1"></i>' + full.last_note_date + '</small>';
                        }
                        return formatted;
                    },
                },
            ],
            order: [[0, "desc"]],
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            drawCallback: function () {
                if (typeof initTooltips === "function") initTooltips();
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
        });

        window.dtUnitQuotation = dt_unit_quotation;
    }
});
