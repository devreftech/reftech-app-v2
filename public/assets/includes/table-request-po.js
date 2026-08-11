$(function () {
    var dt_table_request_invoice = $(".datatable-request-invoice");
    var Url = "/db/quotation/invoice";

    if (dt_table_request_invoice.length) {
        // Clone header row lalu replace isinya dengan input search
        dt_table_request_invoice.find("thead tr")
            .clone(true)
            .appendTo(dt_table_request_invoice.find("thead"));

        dt_table_request_invoice.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_request_invoice.column(i).search() !== this.value) {
                    dt_request_invoice.column(i).search(this.value).draw();
                }
            });
        });

        var dt_request_invoice = dt_table_request_invoice.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "no_quote" },
                { data: "no_po" },
                { data: "company" },
                { data: "type" },
                { data: "harga_total" },
                { data: "po_date" },
                { data: "name" },
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0,
                    className: "text-nowrap",
                },
                {
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var badge = full["row_type"] === "unit"
                            ? ' <span class="badge bg-label-danger ms-1">Unit</span>'
                            : "";
                        var detailUrl = full["row_type"] === "unit"
                            ? route("before.accept.unit", full["id"])
                            : route("before.accept", full["id"]);
                        return '<a class="fw-bold text-primary" href="' + detailUrl + '">' + data + "</a>" + badge;
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var map = {
                            "DP": '<span class="badge bg-label-warning">DP</span>',
                            "BP": '<span class="badge bg-label-info">BP</span>',
                            "CT": '<span class="badge bg-label-success">Full Payment</span>',
                        };
                        return map[data] || '<span class="badge bg-label-secondary">' + data + '</span>';
                    },
                },
                {
                    targets: 4,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between"><span>Rp.</span><span>' + formatted + '</span></div>';
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? moment(data).format("DD-MM-YYYY") : "-";
                        }
                        return data;
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                },
            ],
            orderCellsTop: true,
            order: [[0, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-2 head-request-invoice text-center">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [7, 10, 25, 50, 75, 100],
            displayLength: 10,
        });

        $("div.hl-2.head-request-invoice").html('<h5 class="card-title mb-0">Table Request Invoice</h5>');
    }
});
