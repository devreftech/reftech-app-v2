$(function () {
    var dt_table_pic_client = $(".datatable-pic-client");
    var Url = "/db/pic/";
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf("/") + 1);

    // console.log("ID:", id); // Output: ID: 2

    if (dt_table_pic_client.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_pic_client.DataTable({
            ajax: {
                type: "GET",
                url: Url + id,
                headers: {
                    "Content-Type": "application/json",
                },
                // success: function (hasil, url) {
                //     console.log("Url:", url);
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
                { data: "name_pic" },
                { data: "position" },
                { data: "phone_pic" },
                { data: "email_pic" },
                {
                    data: "",
                },
            ],
            columnDefs: [
                {
                    // For Responsive
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 5,
                    targets: 0,
                    render: function (data, type, full, meta) {
                        return "";
                    },
                },
                {
                    // Checkbox column removed, kept hidden to avoid reindexing other targets
                    targets: 1,
                    visible: false,
                    orderable: false,
                    searchable: false,
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
                    targets: 7,
                    render: function (data, type, full, row) {
                        var id = full["id"];
                        return (
                            '<a type="button" href="#" data-bs-toggle="modal" data-bs-target="#updatePic-' +
                            id +
                            '" data-id="' +
                            id +
                            '" class="btn btn-sm btn-label-primary"><i class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline m-0"></i></a>'
                        );
                    },
                },
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal({
                        header: function (row) {
                            var data = row.data();
                            return "Details of " + data["pn"];
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
        $("div.head-label").html(
            '<h5 class="card-title mb-0">Table Product</h5>'
        );
    }
    dt_table_pic_client.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
