$(function () {
    var dt_table_sales_invoice_escrow = $(".datatable-sales-invoice-escrow");
    var Url = "/db/sales/invoice/escrow";
    var initialized = false;

    function initTable() {
        if (initialized || !dt_table_sales_invoice_escrow.length) return;
        initialized = true;

        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-sales-invoice-escrow thead tr")
            .clone(true)
            .appendTo(".datatable-sales-invoice-escrow thead");
        $(".datatable-sales-invoice-escrow thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control" placeholder="Search ' +
                    title +
                    '" />'
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_sales_invoice_escrow.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.year = window.invoiceYearFilter || "all";
                    d.sales_id = window.invoiceSalesFilter || "all";
                    return d;
                },
            },
            columns: [
                { data: "no_invoice" },
                { data: "tanggal" },
                { data: "company" },
                { data: "harga_total" },
                { data: "company" },
                {
                    data: "bendera"
                },
                { data: "marketplace_name" },
                { data: "name" },
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0,
                },
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var id = full["id"];
                            var detailRoute = typeof route === "function" ? route("payment_detail.invoice", id) : "/payment/invoice/" + id;
                            return (
                                '<a class="text-black fw-semibold" href="' +
                                detailRoute +
                                '">' +
                                (data || full["no_invoice"] || "-") +
                                "</a>"
                            );
                        }
                        return data || full["no_invoice"] || "";
                    },
                },
                {
                    targets: 2,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var plantBadge = full["plant_name"]
                            ? ' <span class="badge bg-label-success ms-1" style="font-size: 10.5px;">' + $('<div>').text(full["plant_name"]).html() + '</span>'
                            : '';
                        return (data || "-") + plantBadge;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-end">Rp ' +
                                new Intl.NumberFormat("id-ID").format(data) +
                                "</div>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        if (type === "display" || type === "filter") {
                            return (
                                '<div class="text-center">'+ '-' +'</div>'
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 5,
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
                {
                    targets: 6,
                    render: function (data, type, row) {
                        if (type === "display") {
                            return data ? data : '<span class="text-muted">-</span>';
                        }
                        return data || "-";
                    },
                },
                {
                    targets: -1,
                    className: "text-center",
                    orderable: false,
                    render: function (data, type, full) {
                        if (type !== "display") return full.name || "";
                        var name = full.name || "-";
                        var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                        var colors = ["bg-label-primary","bg-label-success","bg-label-warning","bg-label-danger","bg-label-info","bg-label-secondary"];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = full.sales_image
                            ? '<img src="/' + full.sales_image + '" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                        return '<span data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                    },
                },
            ],
            order: [],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        });

        window.invoiceDataTables = window.invoiceDataTables || {};
        window.invoiceDataTables.escrow = dt_filter;

        dt_table_sales_invoice_escrow.on("draw", function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    }

    $("#nav-inv-escrow").on("shown.bs.tab", initTable);
});
