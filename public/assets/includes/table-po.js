$(function () {
    var dt_table = $(".datatable-po-quote");
    var Url = "db/po";

    if (dt_table.length) {
        dt_table.find("thead tr")
            .clone(true)
            .appendTo(dt_table.find("thead"));

        dt_table.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_po.column(i).search() !== this.value) {
                    dt_po.column(i).search(this.value).draw();
                }
            });
        });

        var dt_po = dt_table.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) { d.year = window.quotationYearFilter || 'all'; return d; },
            },
            columns: [
                { data: "no_quote" },
                { data: "company" },
                { data: "nett" },
                { data: "type" },
                { data: "title" },
                { data: "po_date" },
                { data: "no_po" },
                { data: "no_invoice" },
            ],
            columnDefs: [
                { targets: [2, 3, 4, 5, 6, 7], className: "text-center" },
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    responsivePriority: 1,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var url = full["row_type"] === "unit"
                            ? "/smart-quote/" + full["id"]
                            : route("quotation.show", full["id"]);
                        var badge = full["row_type"] === "unit"
                            ? ' <span class="badge bg-label-danger ms-1">Unit</span>'
                            : "";
                        return '<a class="fw-bold text-primary" href="' + url + '">' + (data || "-") + "</a>" + badge;
                    },
                },
                {
                    targets: 1,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var ru  = full["ru"];
                        var map = {
                            User:     '<span class="badge rounded-pill bg-label-info me-1">U</span>',
                            Reseller: '<span class="badge rounded-pill bg-label-dark me-1">R</span>',
                        };
                        return (map[ru] || "") + (data || "-");
                    },
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                {
                    // Type quotation (Sparepart/Service/Overhaul/Rental untuk quotation biasa,
                    // Unit/Rental/Project/Parts/Service untuk Smart Quote).
                    targets: 3,
                    render: function (data, type) {
                        if (type === "filter" || type === "sort") return data || "-";
                        if (type !== "display") return data;
                        if (!data) {
                            return '<span class="badge rounded-pill bg-label-secondary">-</span>';
                        }
                        var colors = {
                            Unit:      "bg-label-primary",
                            Rental:    "bg-label-info",
                            Project:   "bg-label-dark",
                            Parts:     "bg-label-warning",
                            Sparepart: "bg-label-warning",
                            Service:   "bg-label-success",
                            Piping:    "bg-label-secondary",
                            "Air Audit": "bg-label-danger",
                            Overhaul:  "bg-label-danger",
                            "General Check / Visit": "bg-label-info",
                            HVAC:      "bg-label-primary",
                            "Fire System": "bg-label-danger",
                        };
                        var labels = { Parts: "Sparepart" };
                        var cls   = colors[data] || "bg-label-secondary";
                        var label = labels[data] || data;
                        return '<span class="badge rounded-pill ' + cls + '">' + label + "</span>";
                    },
                },
                { targets: 4, render: function (data) { return data || "-"; } },
                {
                    targets: 5,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return data ? moment(data).format("DD-MM-YYYY") : "-";
                    },
                },
                {
                    targets: 6,
                    className: "text-center text-nowrap",
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 7,
                    className: "text-center text-nowrap",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        if (!data) return "-";
                        var invoiceId = full["invoice_id"];
                        var url = route("invoice.show", invoiceId);
                        return '<a class="fw-bold text-primary" href="' + url + '">' + data + "</a>";
                    },
                },
            ],
            orderCellsTop: true,
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) { return "Detail: " + row.data()["no_quote"]; },
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

        window.dtPo = dt_po;

        dt_table.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    }
});
