$(function () {
    var dt_table_req_visit_coordinator = $(".datatable-visit-coordinator");
    var Url = "db/reqVisit/coordinator";

    if (dt_table_req_visit_coordinator.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_visit = dt_table_req_visit_coordinator.DataTable({
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
                { data: "company" },
                { data: "machine" },
                { data: "req_date" },
                { data: "name" },
                { data: "" },
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
                    targets: 5,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $title = full["note"]
                            return (
                                
                            '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="' +
                            $title +
                            '">'+
                            data +
                            "</span>"
                            );
                        }
                        return data;
                    },
                },
                {
                    // Actions
                    targets: -1,
                    title: "Actions",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        var $dataId = full["id"];
                        return (
                            '<div class="d-inline-block">' +
                            '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>' +
                            '<ul class="dropdown-menu dropdown-menu-end m-0">' +
                            '<li><a type="button" data-bs-toggle="modal" data-bs-target="#acceptReqVisit'+ $dataId +'" class="dropdown-item">Accept</a></li>' +
                            "</ul>" +
                            "</div>"
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
    dt_table_req_visit_coordinator.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
