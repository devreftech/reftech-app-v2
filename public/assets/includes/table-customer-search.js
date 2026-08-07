$(function () {
    var dt_table_customer_search = $(".datatable-customer-search");
    var Url = "db/crm";

    if (dt_table_customer_search.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-customer-search thead tr")
            .clone(true)
            .appendTo(".datatable-customer-search thead");
        $(".datatable-customer-search thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control" placeholder="Search ' +
                    title +
                    '" />',
            );

            $("input", this).on("keyup change", function () {
                if (dt_filter.column(i).search() !== this.value) {
                    dt_filter.column(i).search(this.value).draw();
                }
            });
        });

        var dt_filter = dt_table_customer_search.DataTable({
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
                { data: "company" },
                { data: "ru" },
                { data: "status" },
                { data: "area" },
                {
                    data: "note",
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                { data: "date" },
                { data: "follow_up" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("existing.show", $dataId);
                            return (
                                '<a class="text-dark" href="' +
                                detailRoute +
                                '">' +
                                data +
                                "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $status_ru = full["ru"];
                            var $status = {
                                User: {
                                    class: "bg-success",
                                },
                                Reseller: {
                                    class: " bg-warning",
                                },
                            };
                            return (
                                '<span class="badge ' +
                                $status[$status_ru].class +
                                '">' +
                                data +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        // Tambahkan dropdown ke dalam kolom
                        var dropdown =
                            '<select class="form-select status-dropdown" data-id="' +
                            full.id +
                            '">';
                        dropdown +=
                            '<option value="1" ' +
                            (data === "1" ? "selected" : "") +
                            ">Bangkrupt</option>";
                        dropdown +=
                            '<option value="2" ' +
                            (data === "2" ? "selected" : "") +
                            ">Aktif</option>";
                        dropdown +=
                            '<option value="3" ' +
                            (data === "3" ? "selected" : "") +
                            ">Non Aktif</option>";
                        dropdown += "</select>";
                        return dropdown;
                    },
                },
                {
                    targets: [5, 6],
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var flag = full["info"];
                            var $info = {
                                Reftech: {
                                    class: "bg-label-primary",
                                },
                                Kojisha: {
                                    class: " bg-label-warning",
                                },
                            };
                            return (
                                '<span class="badge ' +
                                $info[flag].class +
                                '">' +
                                data +
                                "</span> "
                            );
                        }
                        return data;
                    },
                },
            ],
            order: [[1, "desc"]],
            // orderCellsTop: true,
            dom: '<"row me-2"<"col-md-2"<"me-3"l>><"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"B>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-secondary dropdown-toggle',
                    text: '<i class="mdi mdi-export-variant me-1"></i>Export',
                    buttons: [
                        { extend: 'csv', className: 'dropdown-item', text: '<i class="mdi mdi-file-document-outline me-1"></i>CSV' },
                        { extend: 'excel', className: 'dropdown-item', text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel' }
                    ]
                }
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
                var api = this.api();
                var totalCount = api.rows({ search: 'applied' }).count();
                $('#badge-customers').text(totalCount);
            },
        });
    }
    dt_table_customer_search.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
        var api = dt_filter;
        if (api) {
            var totalCount = api.rows({ search: 'applied' }).count();
            $('#badge-customers').text(totalCount);
        }
    });
});
