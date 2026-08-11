$(function () {
    var dt_table_request_contract = $(".datatable-request-contract");

    function buildRequestUrl() {
        var year = $('#filter-year-request').val() || 'all';
        return '/db/request-contract?year=' + year;
    }

    if (dt_table_request_contract.length) {
        $('[data-toggle="tooltip"]').tooltip();
        dt_table_request_contract.DataTable({
            ajax: {
                type: "GET",
                url: buildRequestUrl(),
                headers: { "Content-Type": "application/json" },
            },
            columns: [
                { data: "" },
                { data: "id" },
                { data: "no_contract" },
                { data: "company" },
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

    $('#filter-year-request').on('change', function () {
        if (dt_table_request_contract.length) {
            dt_table_request_contract.DataTable().ajax.url(buildRequestUrl()).load();
        }
    });
});
