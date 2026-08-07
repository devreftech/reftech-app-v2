$(function () {
    var dt_table_machine_client = $(".datatable-machine-client");
    var Url = "/db/machine/client/";
    var path = window.location.pathname;
    var id = path.substring(path.lastIndexOf("/") + 1);

    // console.log("ID:", id); // Output: ID: 2

    if (dt_table_machine_client.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_product = dt_table_machine_client.DataTable({
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
                { data: "unit" },
                { data: "brand" },
                { data: "sku" },
                { data: "serial" },
                { data: "tag" },
                { data: "location" },
                { data: "report_count" },
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
                    render: function (data, type, full, row) {
                        var unit = full["unit"] || "-";
                        var isForecasted = full["is_forecasted"];
                        var forecastType = full["forecast_type"];
                        
                        var isCompressor = unit.toUpperCase() === "AIR COMPRESSOR SCREW";
                        if (!isCompressor) {
                            return '<div class="d-flex align-items-center"><span class="fw-semibold">' + unit + '</span></div>';
                        }
                        
                        var badge = '';
                        if (isForecasted == 1 || isForecasted === true || isForecasted == '1') {
                            var typeLabel = 'Parts';
                            if (forecastType === 'regular_service') typeLabel = 'Service';
                            if (forecastType === 'contract') typeLabel = 'Contract';
                            badge = ' <span class="badge bg-label-success ms-2" style="font-size:0.7rem; padding: 0.25em 0.5em;"><i class="mdi mdi-creation me-1"></i>Forecast: ' + typeLabel + '</span>';
                        } else {
                            badge = ' <span class="badge bg-label-secondary ms-2" style="font-size:0.7rem; padding: 0.25em 0.5em;">Non-Forecast</span>';
                        }
                        
                        return '<div class="d-flex align-items-center"><span class="fw-semibold">' + unit + '</span>' + badge + '</div>';
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, full, row) {
                        var idGlobalUnit = full["id_global_unit"];
                        var sku = full["sku"] || "-";
                        if (idGlobalUnit) {
                            return '<a href="/unit-global/' + idGlobalUnit + '" class="fw-semibold text-primary" target="_blank">' + sku + '</a>';
                        }
                        return sku;
                    }
                },
                {
                    targets: 9,
                    className: "text-center",
                    render: function (data, type, full, row) {
                        var count = full["report_count"] || 0;
                        var id = full["id"];
                        var label = (full["sku"] || full["unit"] || "Machine #" + id) +
                            (full["serial"] ? " - SN " + full["serial"] : "");

                        if (count <= 0) {
                            return '<span class="badge rounded-pill bg-label-secondary">0</span>';
                        }

                        return (
                            '<span class="badge rounded-pill bg-label-info machine-report-badge" style="cursor:pointer" ' +
                            'data-bs-toggle="modal" data-bs-target="#machineReportsModal" ' +
                            'data-machine-id="' + id + '" data-machine-label="' + label.replace(/"/g, "&quot;") + '">' +
                            count +
                            "</span>"
                        );
                    },
                },
                {
                    targets: 10,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, row) {
                        var id = full["id"];
                        var routeCreate = route("create.daily-monitoring", id);
                        var routeVisit = route("visitor.daily-monitoring", id);
                        return (
                            '<div class="btn-group">' +
                            '<a type="button" href="#" data-bs-toggle="modal" data-bs-target="#editMachine-' + id + '" data-id="' + id + '" class="btn btn-sm btn-label-primary">' +
                            '<i class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline me-1"></i>Edit</a>' +
                            '<button type="button" class="btn btn-sm btn-label-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">' +
                            '<span class="visually-hidden">Toggle Dropdown</span></button>' +
                            '<ul class="dropdown-menu dropdown-menu-end">' +
                            '<li><a class="dropdown-item" href="' + routeCreate + '"><i class="menu-icon tf-icons mdi mdi-14px mdi-import me-1"></i>Create Daily Monitoring</a></li>' +
                            '<li><a class="dropdown-item" href="' + routeVisit + '"><i class="menu-icon tf-icons mdi mdi-14px mdi-eye-outline me-1"></i>Visit Daily Monitoring</a></li>' +
                            '<li><hr class="dropdown-divider"></li>' +
                            '<li><a href="#" data-id="' + id + '" class="dropdown-item text-danger delete-machine"><i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline me-1"></i>Delete</a></li>' +
                            '</ul>' +
                            '</div>'
                        );
                    },
                },
            ],
            // order: [[2, "desc"]],
            // dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            // displayLength: 7,
            // lengthMenu: [7, 10, 25, 50, 75, 100],
            // buttons: [
            //     {
            //         extend: "collection",
            //         className: "btn btn-label-primary dropdown-toggle me-2",
            //         text: '<i class="mdi mdi-export-variant me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
            //         buttons: [
            //             {
            //                 extend: "print",
            //                 text: '<i class="mdi mdi-printer-outline me-1" ></i>Print',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [3, 4, 5, 6, 7, 8, 9],
            //                     // prevent avatar to be display
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //                 customize: function (win) {
            //                     //customize print view for dark
            //                     $(win.document.body)
            //                         .css("color", config.colors.headingColor)
            //                         .css(
            //                             "border-color",
            //                             config.colors.borderColor
            //                         )
            //                         .css(
            //                             "background-color",
            //                             config.colors.bodyBg
            //                         );
            //                     $(win.document.body)
            //                         .find("table")
            //                         .addClass("compact")
            //                         .css("color", "inherit")
            //                         .css("border-color", "inherit")
            //                         .css("background-color", "inherit");
            //                 },
            //             },
            //             {
            //                 extend: "csv",
            //                 text: '<i class="mdi mdi-file-document-outline me-1" ></i>Csv',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [3, 4, 5, 6, 7, 8, 9],
            //                     // prevent avatar to be display
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //             {
            //                 extend: "excel",
            //                 text: '<i class="mdi mdi-file-excel-outline me-1"></i>Excel',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [3, 4, 5, 6, 7, 8, 9],
            //                     // prevent avatar to be display
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //             {
            //                 extend: "pdf",
            //                 text: '<i class="mdi mdi-file-pdf-box me-1"></i>Pdf',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [3, 4, 5, 6, 7, 8, 9],
            //                     // prevent avatar to be display
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //             {
            //                 extend: "copy",
            //                 text: '<i class="mdi mdi-content-copy me-1" ></i>Copy',
            //                 className: "dropdown-item",
            //                 exportOptions: {
            //                     columns: [3, 4, 5, 6, 7, 8, 9],
            //                     // prevent avatar to be display
            //                     format: {
            //                         body: function (inner, coldex, rowdex) {
            //                             if (inner.length <= 0) return inner;
            //                             var el = $.parseHTML(inner);
            //                             var result = "";
            //                             $.each(el, function (index, item) {
            //                                 if (
            //                                     item.classList !== undefined &&
            //                                     item.classList.contains(
            //                                         "user-name"
            //                                     )
            //                                 ) {
            //                                     result =
            //                                         result +
            //                                         item.lastChild.firstChild
            //                                             .textContent;
            //                                 } else if (
            //                                     item.innerText === undefined
            //                                 ) {
            //                                     result =
            //                                         result + item.textContent;
            //                                 } else
            //                                     result =
            //                                         result + item.innerText;
            //                             });
            //                             return result;
            //                         },
            //                     },
            //                 },
            //             },
            //         ],
            //     },
            //     {
            //         text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Product</span>',
            //         className: "btn btn-primary",
            //         attr: {
            //             "data-bs-target": "#createProduct",
            //             "data-bs-toggle": "modal",
            //         },
            //     },
            // ],
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
    dt_table_machine_client.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    $(document).on("click", ".machine-report-badge", function () {
        var machineId = $(this).data("machine-id");
        var machineLabel = $(this).data("machine-label");
        var $list = $("#machineReportsList");

        $("#machineReportsModalTitle").text("Service Report - " + machineLabel);
        $list.html('<p class="text-center text-muted mb-0">Memuat...</p>');

        $.ajax({
            type: "GET",
            url: "/db/machine/" + machineId + "/reports",
            headers: {
                "Content-Type": "application/json",
            },
            success: function (res) {
                var reports = res.data || [];
                if (!reports.length) {
                    $list.html(
                        '<p class="text-center text-muted mb-0">Belum ada Service Report.</p>'
                    );
                    return;
                }

                var typeColor = {
                    Service: "bg-label-primary",
                    Visit: "bg-label-info",
                    General: "bg-label-warning",
                };

                var html = '<ul class="list-group">';
                reports.forEach(function (r) {
                    var color = typeColor[r.type] || "bg-label-secondary";
                    var detailUrl = route("service-reports.show", r.id);
                    html +=
                        '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                        "<div>" +
                        '<a href="' +
                        detailUrl +
                        '" class="fw-medium text-dark">' +
                        r.no_service +
                        "</a>" +
                        '<div class="text-muted small">' +
                        (r.technician || "-") +
                        " &middot; " +
                        r.date +
                        "</div>" +
                        "</div>" +
                        '<span class="badge rounded-pill ' +
                        color +
                        '">' +
                        (r.type || "-") +
                        "</span>" +
                        "</li>";
                });
                html += "</ul>";
                $list.html(html);
            },
            error: function () {
                $list.html(
                    '<p class="text-center text-danger mb-0">Gagal memuat data.</p>'
                );
            },
        });
    });
});
