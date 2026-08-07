$(function () {
    var dt_table_prospect = $(".datatable-prospect-admin");
    var Url = "/db/prospect/admin";

    if (dt_table_prospect.length) {
        $('[data-toggle="tooltip"]').tooltip();
        var dt_prospect = dt_table_prospect.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: {
                    "Content-Type": "application/json",
                },
                data: function (d) {
                    d.sales_id = window.prospectSalesFilter || "";
                    d.year = window.prospectYearFilter || "";
                    return d;
                },
            },
            columns: [
                { data: "company" },
                { data: "category" },
                { data: "kebutuhan" },
                { data: "nett" },
                { data: "status" },
                { data: "provide" },
                { data: "image" },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, full, meta) {
                        if (type !== "display") {
                            return data;
                        }
                        var detailRoute = route("prospect.show", full["id"]);
                        var parts = [];
                        if (full["date"]) {
                            parts.push(moment(full["date"]).format("DD-MM-YYYY"));
                        }
                        if (full["name_pic"]) {
                            parts.push(full["name_pic"]);
                        }
                        var subtitle = parts.length
                            ? '<br><small class="text-muted">' + parts.join(" &middot; ") + "</small>"
                            : "";
                        return (
                            '<a class="fw-bold text-primary" href="' +
                            detailRoute +
                            '">' +
                            data +
                            "</a>" +
                            subtitle
                        );
                    },
                },
                {
                    targets: 1,
                    className: "text-start",
                    render: function (data, type, full, meta) {
                        return data ? data : "-";
                    },
                },
                {
                    targets: 2,
                    className: "text-start",
                    render: function (data, type, full, meta) {
                        if (data === null || data === undefined || data === "") {
                            return "-";
                        }
                        if (type !== "display") {
                            return data;
                        }
                        var escaped = $("<div>").text(String(data)).html();
                        return (
                            '<span class="d-inline-block text-truncate align-middle" style="max-width: 260px;" ' +
                            'data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="' +
                            escaped +
                            '">' +
                            escaped +
                            "</span>"
                        );
                    },
                },
                {
                    targets: 3,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        if (data === null || data === undefined) {
                            return "-";
                        }
                        if (type !== "display") {
                            return data;
                        }
                        var formatted = parseInt(data).toLocaleString("id-ID");
                        return (
                            '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' +
                            formatted +
                            "</span></div>"
                        );
                    },
                },
                {
                    // Label Status Name
                    targets: 4,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        var $status_number = full["status"];
                        var $status = {
                            20: {
                                title: "Send WA / Email",
                                class: "bg-label-secondary",
                            },
                            30: {
                                title: "Inquiry Accepted",
                                class: " bg-label-dark",
                            },
                            40: {
                                title: "Progress Follow Up",
                                class: " bg-label-info",
                            },
                            60: {
                                title: "Negotiation / Revisi",
                                class: " bg-label-primary",
                            },
                            80: {
                                title: "Hot Prospect",
                                class: " bg-label-warning",
                            },
                            100: {
                                title: "Done PO",
                                class: " bg-label-success",
                            },
                            0: {
                                title: "Loss",
                                class: " bg-label-danger",
                            },
                        };
                        if (
                            $status[$status_number] === null ||
                            $status[$status_number] === undefined
                        ) {
                            return "-";
                        } else {
                            return (
                                '<span class="badge rounded-pill ' +
                                $status[$status_number].class +
                                '">' +
                                $status[$status_number].title +
                                "</span>"
                            );
                        }
                    },
                },
                {
                    targets: 5,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        var $provide_number = full["provide"];
                        var $provide = {
                            '1': {
                                title: "Provided",
                                class: "bg-label-success",
                            },
                            '0': {
                                title: "No Provide",
                                class: " bg-label-danger",
                            },
                        };
                        if (
                            $provide[$provide_number] === null ||
                            $provide[$provide_number] === undefined
                        ) {
                            return "-";
                        } else {
                            return (
                                '<span class="badge rounded-pill ' +
                                $provide[$provide_number].class +
                                '">' +
                                $provide[$provide_number].title +
                                "</span>"
                            );
                        }
                    },
                },
                {
                    // Label Status Name
                    targets: 6,
                    className: "text-center",
                    render: function (data, type, full, meta) {
                        var name = full["sales"];
                        const domain = window.location.origin;

                        if (data === null || data === undefined) {
                            return (
                                '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="No Sales">' +
                                '<img src="' +
                                domain +
                                "/asset/profile/profile.jpg" +
                                '" class="w-px-40 h-auto rounded-circle" alt="Profile Image">' +
                                "</span>"
                            );
                        } else {
                            return (
                                '<span data-toggle="tooltip" data-container="body" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="' +
                                name +
                                '">' +
                                '<img src="' +
                                domain +
                                "/" +
                                data +
                                '" class="w-px-40 h-auto rounded-circle" alt="Profile Image" onerror="this.onerror=null;this.src=\'' +
                                domain +
                                "/asset/profile/profile.jpg';\">" +
                                "</span>"
                            );
                        }
                    },
                },
            ],
            drawCallback: function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            },
            order: [],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-1 text-center">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 7,
            lengthMenu: [7, 10, 25, 50, 75, 100],
        });
        $("div.hl-1").html('<h5 class="card-title mb-0"><i class="mdi mdi-account-multiple-outline me-2"></i>Daftar Prospect</h5>');
    }
    dt_table_prospect.on("draw", function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
