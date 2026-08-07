$(function () {
    var dt_table_quotation_leads = $(".datatable-quotation-leads");
    var Url = "/db/product/quotation/";
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf('/') + 1);

    if (dt_table_quotation_leads.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_quotation_leads = dt_table_quotation_leads.DataTable({
            ajax: {
                type: "GET",
                url: Url + id,
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
                { data: "" },
                { data: "id" },
                { data: "id" },
                { data: "estimated_date" },
                { data: "no_quote" },
                { data: "status" },
                { data: "nett" },
            ],
            columnDefs: [
                {
                    targets: 6,
                    render: $.fn.dataTable.render.number(".", "", 0, "Rp."),
                },
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    // For Checkboxes
                    targets: 1,
                    orderable: false,
                    searchable: false,
                    responsivePriority: 3,
                    checkboxes: true,
                    render: function () {
                        return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                    },
                    checkboxes: {
                        selectAllRender:
                            '<input type="checkbox" class="form-check-input">',
                    },
                },
                {
                    targets: 2,
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 4,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route("quotation.show", $dataId);
                            return (
                                '<a class="text-dark" href="' + detailRoute + '">' + data + "</a>"
                            );
                        }
                        return data;
                    },
                },
                {
                    responsivePriority: 1,
                    targets: 3,
                },
                {
                    // Label Status Percent
                    targets: 5,
                    render: function (data, type, full, meta) {
                        var $status_number = full["status"];
                        var $titleTool = full["note"];
                        var $status = {
                            20: {
                                title: "20%",
                                class: "bg-label-secondary",
                                colorTip: "tooltip-secondary",
                                titleTip: $titleTool,
                            },
                            30: {
                                title: "30%",
                                class: " bg-label-dark",
                                colorTip: "tooltip-dark",
                                titleTip: $titleTool,
                            },
                            40: {
                                title: "40%",
                                class: " bg-label-info",
                                colorTip: "tooltip-info",
                                titleTip: $titleTool,
                            },
                            60: {
                                title: "60%",
                                class: " bg-label-primary",
                                colorTip: "tooltip-primary",
                                titleTip: $titleTool,
                            },
                            80: {
                                title: "80%",
                                class: " bg-label-warning",
                                colorTip: "tooltip-warning",
                                titleTip: $titleTool,
                            },
                            100: {
                                title: "100%",
                                class: " bg-label-success",
                                colorTip: "tooltip-success",
                                titleTip: $titleTool,
                            },
                            0: {
                                title: "0%",
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
                            '" class="badge rounded-pill ' +
                            $status[$status_number].class +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
            ],
            drawCallback: function (settings) {
                console.log("drawCallback");
                $('[data-toggle="tooltip"]').tooltip();
            },
            order: [[2, "desc"]],
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
            buttons: [
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["full_name"];
                        },
                    }),
                    type: "column",
                    renderer: function (api, rowIdx, columns) {
                        var data = $.map(columns, function (col, i) {
                            return col.title !== "" // ? Do not show row in modal popup if title is blank (for check box)
                                ? '<tr data-dt-row="' +
                                      col.rowIndex +
                                      '" data-dt-column="' +
                                      col.columnIndex +
                                      '">' +
                                      "<td>" +
                                      col.title +
                                      ":" +
                                      "</td> " +
                                      "<td>" +
                                      col.data +
                                      "</td>" +
                                      "</tr>"
                                : "";
                        }).join("");

                        return data
                            ? $('<table class="table"/><tbody />').append(data)
                            : false;
                    },
                },
            },
        });
    }
    dt_table_quotation_leads.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});