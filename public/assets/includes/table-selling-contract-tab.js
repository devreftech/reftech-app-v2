$(function () {
    var dt_table_selling_contract_tab = $(".datatable-selling-contract-tab");

    function buildSellingUrl() {
        var year = $('#filter-year-selling').val() || 'all';
        var tax = $('#filter-tax-selling').val() || 'all';
        return '/db/selling-contract?year=' + year + '&tax=' + tax;
    }

    if (dt_table_selling_contract_tab.length) {
        $('[data-toggle="tooltip"]').tooltip();
        dt_table_selling_contract_tab.DataTable({
            ajax: {
                type: "GET",
                url: buildSellingUrl(),
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_contract" },
                { data: "company" },
                { data: "ppn" },
                { data: "harga_total" },
                { data: "date" },
                { data: "name" },
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
                    targets: 4,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return Number(data) === 1
                            ? '<span class="badge bg-label-primary">PPN</span>'
                            : '<span class="badge bg-label-danger">Non-PPN</span>';
                    },
                },
                {
                    targets: 5,
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
            ],
            drawCallback: function () { $('[data-toggle="tooltip"]').tooltip(); },
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
    }

    function reloadSelling() {
        if (dt_table_selling_contract_tab.length) {
            dt_table_selling_contract_tab.DataTable().ajax.url(buildSellingUrl()).load();
        }
    }

    $('#filter-year-selling').on('change', reloadSelling);
    $('#filter-tax-selling').on('change', reloadSelling);
});
