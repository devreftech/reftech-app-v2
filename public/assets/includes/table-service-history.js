$(function () {
    var dt_table_service_history = $(".datatable-service-history");
    var Url = "/db/client/service-history/";
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf('/') + 1);

    if (dt_table_service_history.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_service_history = dt_table_service_history.DataTable({
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
                { data: "no_service" },
                { data: "brand_type" },
                { data: "running" },
                { data: "date" },
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
                    targets: 3,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = route(
                                "service-reports.show",
                                $dataId
                            );
                            var dataSub = data.substring(0, 5);
                            var pmBadge = '';
                            if (full["pm_level"]) {
                                var badgeColor = 'bg-label-primary';
                                if (full["pm_level"] === 'PM2') badgeColor = 'bg-label-warning';
                                if (full["pm_level"] === 'PM3') badgeColor = 'bg-label-danger';
                                if (full["pm_level"] === 'PM4') badgeColor = 'bg-label-info';
                                if (full["pm_level"] === 'Troubleshooting') badgeColor = 'bg-label-secondary';
                                pmBadge = ' <span class="badge ' + badgeColor + ' ms-1" style="font-size: 0.7rem;">' + full["pm_level"] + '</span>';
                            }
                            return (
                                '<a class="text-dark fw-semibold" href="' +
                                detailRoute +
                                '">' +
                                dataSub +
                                "</a>" + pmBadge
                            );
                        }
                        return data;
                    },
                },
            ],
            order: [[2, "desc"]],
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
        $("div.head-label").html(
            '<h5 class="card-title mb-0">Table Service History</h5>'
        );
    }
    dt_table_service_history.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
