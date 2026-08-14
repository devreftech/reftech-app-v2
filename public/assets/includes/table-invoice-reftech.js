$(function () {
    var dt_table_invoice = $(".datatable-invoice-reftech");
    var Url = "/db/invoice/reftech";

    if (dt_table_invoice.length) {
        // Clone header row lalu replace isinya dengan input search (kolom PPN pakai dropdown filter, bukan search text)
        dt_table_invoice.find("thead tr")
            .clone(true)
            .appendTo(dt_table_invoice.find("thead"));

        dt_table_invoice.find("thead tr:eq(1) th").each(function (i) {
            if (i === 3) {
                $(this).html("");
                return;
            }
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_invoice.column(i).search() !== this.value) {
                    dt_invoice.column(i).search(this.value).draw();
                }
            });
        });

        var dt_invoice = dt_table_invoice.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) {
                    d.year = $("#invoice-year-filter").val();
                },
            },
            columns: [
                { data: "no_invoice" },
                { data: "no_po" },
                { data: "company" },
                { data: "ppn" },
                {
                    data: "type",
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
                { data: "harga_total" },
                {
                    data: "po_date",
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? moment(data).format("DD-MM-YYYY") : "-";
                        }
                        return data;
                    },
                },
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
                        if (type === "display") {
                            var detailRoute = full["source"] === "unit"
                                ? route("invoice.show_unit", full["id"])
                                : route("invoice.show", full["id"]);
                            return '<a class="fw-bold text-primary" href="' + detailRoute + '">' + data + "</a>";
                        }
                        return data;
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return data === "PPN"
                            ? '<span class="badge bg-label-primary">PPN</span>'
                            : '<span class="badge bg-label-secondary">Non PPN</span>';
                    },
                },
                {
                    targets: 4,
                    className: "text-center",
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between"><span>Rp.</span><span>' + formatted + '</span></div>';
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                },
                {
                    targets: 7,
                    className: "text-center",
                },
            ],
            orderCellsTop: true,
            order: [[0, "desc"]],
            // Default filter: hanya tampilkan PPN saat pertama kali load
            searchCols: [null, null, null, { search: "^PPN$", regex: true, smart: false }, null, null, null, null],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-2 head-invoice-reftech text-center"><"dt-invoice-reftech-filters d-flex gap-3 justify-content-end pt-3 pt-md-0 flex-wrap">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [7, 10, 25, 50, 75, 100],
            displayLength: 10,
        });

        $("div.hl-2.head-invoice-reftech").html('<h5 class="card-title mb-0">Table Invoice Reftech</h5>');

        // Filter PPN / Non PPN
        $("div.dt-invoice-reftech-filters").html(
            '<div class="d-flex align-items-center gap-2">' +
            '<label class="form-label mb-0 fw-semibold small">PPN</label>' +
            '<select class="form-select form-select-sm w-auto" id="invoice-reftech-ppn-filter">' +
            '<option value="">Semua</option><option value="PPN" selected>PPN</option><option value="Non PPN">Non PPN</option>' +
            '</select></div>' +
            '<div class="d-flex align-items-center gap-2">' +
            '<label class="form-label mb-0 fw-semibold small">Tahun Invoice</label>' +
            '<select class="form-select form-select-sm w-auto" id="invoice-year-filter"></select></div>'
        );

        var currentYear = new Date().getFullYear();
        var yearOpts = '';
        for (var y = currentYear; y >= 2024; y--) {
            yearOpts += '<option value="' + y + '">' + y + '</option>';
        }
        $("#invoice-year-filter").html(yearOpts);

        $("#invoice-year-filter").on("change", function () {
            dt_invoice.ajax.reload();
        });
        $("#invoice-reftech-ppn-filter").on("change", function () {
            var val = $(this).val();
            dt_invoice.column(3).search(val ? "^" + val + "$" : "", true, false).draw();
        });
    }
});
