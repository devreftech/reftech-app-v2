$(function () {
    var dt_table_comment = $(".datatable-comment");
    var Url = "db/comment/sales";

    if (dt_table_comment.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_comment = dt_table_comment.DataTable({
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
                { data: "" },
                { data: "id" },
                { data: "id" },
                { data: "no_quote" },
                { data: "status" },
                { data: "name" },
                {
                    data: "date",
                    render: function (data, type, row) {
                        return moment(data).fromNow();
                    },
                },
            ],
            columnDefs: [
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
                    responsivePriority: 1,
                    targets: 3,
                },
                {
                    targets: 3,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id_q"];
                            var $Id = full["id"]; 
                            var detailRoute = route("quotation.show", $dataId); 
                            var $title = full["comment"]; 

                            return (
                                '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' +
                                $title +
                                '">' +
                                '<a class="text-dark view-quote" data-quotation="' +
                                $dataId +
                                '" data-id="' +
                                $Id +
                                '" href="' +
                                detailRoute +
                                '#viewComment">' +
                                data +
                                "</a>" +
                                "</span>"
                            );
                        }
                        return data;
                    },
                },
                {
                    // Label Status Name
                    targets: 4,
                    render: function (data, type, full, meta) {
                        var $status_number = full["status"];
                        var $status = {
                            20: {
                                class: "bg-label-secondary",
                            },
                            30: {
                                class: " bg-label-dark",
                            },
                            40: {
                                class: " bg-label-info",
                            },
                            60: {
                                class: " bg-label-primary",
                            },
                            80: {
                                class: " bg-label-warning",
                            },
                            100: {
                                class: " bg-label-success",
                            },
                            0: {
                                class: " bg-label-danger",
                            },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data;
                        }
                        return (
                            '<span class="badge rounded-pill ' +
                            $status[$status_number].class +
                            '">' +
                            data +
                            "</span>"
                        );
                    },
                },
            ],
            order: [[2, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label-comment text-center">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 15,
            lengthMenu: [15, 25, 50, 75, 100],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["company"];
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
        $("div.head-label-comment").html(
            '<h5 class="card-title mb-0">Table Comment</h5>'
        );
    }
    dt_table_comment.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
