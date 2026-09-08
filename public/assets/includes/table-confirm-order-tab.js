$(function () {
    function initConfirmOrderDataTable(tableSelector, taxValue) {
        var $table = $(tableSelector);
        if (!$table.length) return null;

        function getUrl() {
            var year = $('#filter-year-order').val() || 'all';
            return '/db/confirm-order?year=' + year + '&tax=' + taxValue;
        }

        var dt = $table.DataTable({
            ajax: {
                type: "GET",
                url: getUrl(),
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_contract" },
                { data: "no_quote" },
                { data: "company" },
                { data: "ppn" },
                { data: "harga_total" },
                { data: "date" },
                { data: "name" },
                { data: "approver_name" },
            ],
            columnDefs: [
                {
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function () { return ""; },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 2,
                    responsivePriority: 1,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var url = route("contract.show", full["id"]);
                            return '<a class="text-primary fw-semibold" href="' + url + '">' + data + '</a>';
                        }
                        return data;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, full) {
                        if (!data) return "-";
                        if (type !== "display") return data;
                        var quoteUrl = null;
                        if (full.source === "unit" || full.quote_type === "unit" || full.quote_type === "Unit") {
                            quoteUrl = "/smart-quote/" + full.id_quote;
                        } else if (full.quote_type === "Sparepart") {
                            quoteUrl = route("quotation.show", full.id_quote);
                        } else if (full.quote_type === "Service") {
                            quoteUrl = route("show-service.quotation", full.id_quote);
                        } else if (full.id_quote) {
                            quoteUrl = route("show-overhaul.quotation", full.id_quote);
                        }
                        if (quoteUrl) {
                            return '<a class="text-secondary fw-medium" href="' + quoteUrl + '">' + data + '</a>';
                        }
                        return data;
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return Number(data) === 1
                            ? '<span class="badge bg-label-primary">PPN</span>'
                            : '<span class="badge bg-label-danger">Non-PPN</span>';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = (parseInt(data) || 0).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                {
                    targets: 7,
                    className: "text-center",
                    render: function (data, type) {
                        if (!data) return "-";
                        if (type === "display" || type === "filter") {
                            return (typeof moment !== "undefined" && moment(data).isValid())
                                ? moment(data).format("DD-MM-YYYY")
                                : data;
                        }
                        return data;
                    },
                },
                {
                    targets: 8,
                    className: "text-center",
                    orderable: false,
                    searchable: true,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var name = data || "-";
                        var img = full.image;
                        var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                        var colors = ["bg-label-primary","bg-label-success","bg-label-warning","bg-label-danger","bg-label-info","bg-label-secondary"];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = img
                            ? '<img src="/' + img + '" class="rounded-circle shadow-xs" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                        return '<span data-toggle="tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                    },
                },
                {
                    targets: 9,
                    className: "text-center",
                    orderable: false,
                    searchable: true,
                    render: function (data, type, full) {
                        if (type !== "display") return data || "";
                        var name = full.approver_name || data;
                        if (!name) return '<span class="text-muted">-</span>';
                        var img = full.approver_image;
                        var initials = name.split(" ").map(function (w) { return w.charAt(0); }).slice(0, 2).join("").toUpperCase();
                        var colors = ["bg-label-primary","bg-label-success","bg-label-warning","bg-label-danger","bg-label-info","bg-label-secondary"];
                        var colorClass = colors[name.charCodeAt(0) % colors.length];
                        var av = img
                            ? '<img src="/' + img + '" class="rounded-circle shadow-xs" style="width:32px;height:32px;object-fit:cover;" alt="' + name + '">'
                            : '<div class="avatar-initial rounded-circle ' + colorClass + '" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;font-size:11px;font-weight:700;">' + initials + '</div>';
                        return '<span data-toggle="tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="' + name + '">' + av + '</span>';
                    },
                },
            ],
            drawCallback: function () {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[1, "desc"]],
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            return "Details of " + row.data()["company"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col) {
                            return col.title !== ""
                                ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '"><td>' + col.title + ':</td><td>' + col.data + '</td></tr>'
                                : "";
                        }).join("");
                        return data ? $('<table class="table"/><tbody />').append(data) : false;
                    },
                },
            },
        });

        return dt;
    }

    var dtPpn = initConfirmOrderDataTable(".datatable-confirm-order-ppn", "ppn");
    var dtNonPpn = initConfirmOrderDataTable(".datatable-confirm-order-non-ppn", "non-ppn");

    $('#filter-year-order').on('change', function () {
        var year = $(this).val() || 'all';
        if (dtPpn) dtPpn.ajax.url('/db/confirm-order?year=' + year + '&tax=ppn').load();
        if (dtNonPpn) dtNonPpn.ajax.url('/db/confirm-order?year=' + year + '&tax=non-ppn').load();
    });

    // Adjust responsive columns when main tabs or sub-tabs are toggled
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        if (dtPpn) dtPpn.columns.adjust().responsive.recalc();
        if (dtNonPpn) dtNonPpn.columns.adjust().responsive.recalc();
    });
});
