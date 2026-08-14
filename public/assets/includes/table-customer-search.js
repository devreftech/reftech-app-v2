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
            dom:
                '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f><"dt-action-buttons text-end pt-3 pt-md-0"B>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
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
        });
    }
    dt_table_customer_search.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
