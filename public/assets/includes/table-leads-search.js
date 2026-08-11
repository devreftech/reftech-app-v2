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
            dom:
                '<"row"<"dt-action-buttons text-end pt-3 pt-md-0"B><"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Leads</span>',
                    className: "btn btn-primary",
                    attr: {
                        "data-bs-target": "#createLeads",
                        "data-bs-toggle": "modal",
                    },
                },
                // {
                //     text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Payable</span>',
                //     className: "btn btn-primary btn-new",
                //     action: function (e, dt, node, config) {
                //         window.location = route("payable.create");
                //     },
                // },
                // {
                //     text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Payable</span>',
                //     className: "btn btn-primary",
                //     attr: {
                //         href: "{{ route('payable.create') }}",
                //     },
                // },
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
        });
    }
    dt_table_leads_search.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
