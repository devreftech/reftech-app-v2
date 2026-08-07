$(function () {
    var dt_table_leads_search = $(".datatable-leads-search");
    var Url = "db/leads";

    if (dt_table_leads_search.length) {
        $('[data-toggle="tooltip"]').tooltip();
        // Setup - add a text input to each footer cell
        $(".datatable-leads-search thead tr")
            .clone(true)
            .appendTo(".datatable-leads-search thead");
        $(".datatable-leads-search thead tr:eq(1) th").each(function (i) {
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

        var dt_filter = dt_table_leads_search.DataTable({
            ordering: false,
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
                { data: "issue" },
                { data: "area" },
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
                            var detailRoute = route("detail.leads", $dataId);
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
                    // Label
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var $status_number = full["id_issues"];
                        var $titleTool = full["note"];
                        var $status = {
                            1: {
                                title: "New Client",
                                class: "bg-label-warning",
                                colorTip: "tooltip-warning",
                                titleTip: $titleTool,
                            },
                            2: {
                                title: "Not Responded",
                                class: " bg-label-danger",
                                colorTip: "tooltip-danger",
                                titleTip: $titleTool,
                            },
                            3: {
                                title: "Send Introduction",
                                class: " bg-label-info",
                                colorTip: "tooltip-info",
                                titleTip: $titleTool,
                            },
                            4: {
                                title: "Send Quote",
                                class: " bg-label-primary",
                                colorTip: "tooltip-primary",
                                titleTip: $titleTool,
                            },
                            5: {
                                title: "Done PO",
                                class: " bg-label-success",
                                colorTip: "tooltip-success",
                                titleTip: $titleTool,
                            },
                            6: {
                                title: "Loss",
                                class: " bg-label-danger",
                                colorTip: "tooltip-danger",
                                titleTip: $titleTool,
                            },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data;
                        }
                        return (
                            '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="' +
                            $status[$status_number].colorTip +
                            '" title="' +
                            $status[$status_number].titleTip +
                            '" class="badge ' +
                            $status[$status_number].class +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
                {
                    targets: [4, 5],
                    render: function (data, type, row) {
                        if (data === null || data === undefined) {
                            return "-";
                        } else {
                            return type === "display" ? data : "-";
                        }
                    },
                },
                {
                    targets: 6,
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
            // order: [[1, "desc"]],
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
                $('#badge-leads').text(totalCount);
            },
        });
    }
    dt_table_leads_search.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
        var api = dt_filter;
        if (api) {
            var totalCount = api.rows({ search: 'applied' }).count();
            $('#badge-leads').text(totalCount);
        }
    });
});
