$(function () {
    var dt_table_invoice_marketplace = $(".datatable-invoice-marketplace");
    var Url = "/db/quotation/invoice/marketplace";

    if (dt_table_invoice_marketplace.length) {
        // Clone header row lalu replace isinya dengan input search
        dt_table_invoice_marketplace.find("thead tr")
            .clone(true)
            .appendTo(dt_table_invoice_marketplace.find("thead"));

        var marketplaceOptions = ["Airend Center", "Parts Compressor", "Kojisha Filter"];

        dt_table_invoice_marketplace.find("thead tr:eq(1) th").each(function (i) {
            if (i === 6) { $(this).html(""); return; }

            if (i === 3) {
                var $select = $('<select class="form-select form-select-sm"></select>');
                $select.append('<option value="">Semua Marketplace</option>');
                marketplaceOptions.forEach(function (o) {
                    $select.append('<option value="' + o + '">' + o + '</option>');
                });
                $(this).html($select);
                $select.on("change", function () {
                    var val = $(this).val();
                    dt_invoice_marketplace.column(i).search(val ? "^" + val + "$" : "", true, false).draw();
                });
                return;
            }

            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_invoice_marketplace.column(i).search() !== this.value) {
                    dt_invoice_marketplace.column(i).search(this.value).draw();
                }
            });
        });

        var dt_invoice_marketplace = dt_table_invoice_marketplace.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) {
                    d.year = $("#invoice-marketplace-year-filter").val();
                },
            },
            columns: [
                { data: "no_quote" },
                { data: "no_po" },
                { data: "company" },
                { data: "escrow_channel" },
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
                        // Unit quotation Escrow sudah langsung terbit (bukan pending
                        // approval lagi), jadi arahkan ke halaman invoice yang sudah jadi.
                        var detailUrl = full["row_type"] === "unit"
                            ? route("invoice.show_unit", full["id"])
                            : route("before.accept", full["id"]);
                        return '<a class="fw-bold text-primary" href="' + detailUrl + '">' + data + "</a>";
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var map = {
                            "Airend Center": '<span class="badge bg-label-primary">Airend Center</span>',
                            "Parts Compressor": '<span class="badge bg-label-info">Parts Compressor</span>',
                            "Kojisha Filter": '<span class="badge bg-label-warning">Kojisha Filter</span>',
                        };
                        return map[data] || '<span class="badge bg-label-secondary">-</span>';
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
                    width: "48px",
                    orderable: false,
                    searchable: false,
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
            orderCellsTop: true,
            order: [[0, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-2 head-invoice-marketplace text-center"><"dt-invoice-marketplace-filters d-flex gap-3 justify-content-end pt-3 pt-md-0 flex-wrap">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [7, 10, 25, 50, 75, 100],
            displayLength: 10,
        });

        $("div.hl-2.head-invoice-marketplace").html('<h5 class="card-title mb-0">Table Marketplace (Escrow)</h5>');

        // Filter Tahun
        $("div.dt-invoice-marketplace-filters").html(
            '<div class="d-flex align-items-center gap-2">' +
            '<label class="form-label mb-0 fw-semibold small">Tahun</label>' +
            '<select class="form-select form-select-sm w-auto" id="invoice-marketplace-year-filter"></select></div>'
        );

        var currentYear = new Date().getFullYear();
        var yearOpts = '<option value="">Semua</option>';
        for (var y = currentYear; y >= 2024; y--) {
            yearOpts += '<option value="' + y + '">' + y + '</option>';
        }
        $("#invoice-marketplace-year-filter").html(yearOpts);

        $("#invoice-marketplace-year-filter").on("change", function () {
            dt_invoice_marketplace.ajax.reload();
        });
    }
});
