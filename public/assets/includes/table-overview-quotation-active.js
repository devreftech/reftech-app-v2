$(function () {
    var dt_table_overview_quotation_active = $(".datatable-overview-quotation-active");
    var Url = "/db/overview/quotation-active/";
    var path = window.location.pathname;
    var segments = path.split("/").filter(function(s) { return s.length > 0; });

    var sales = segments[segments.length - 2];
    var dateRep = segments[segments.length - 1];
    var targetUrl = dt_table_overview_quotation_active.data("url") || (Url + sales + "/" + dateRep);

    if (dt_table_overview_quotation_active.length) {
        var dt_quotation_active = dt_table_overview_quotation_active.DataTable({
            ajax: {
                type: "GET",
                url: targetUrl,
                headers: {
                    "Content-Type": "application/json",
                },
                error: function (error) {
                    console.error("Error:", error);
                },
            },
            columns: [
                { data: null, defaultContent: "" },
                { data: "id", defaultContent: "-" },
                { data: "no_quote", defaultContent: "-" },
                { data: "company", defaultContent: "-" },
                { data: "nett", defaultContent: 0 },
                { data: "title", defaultContent: "-" },
                { data: "estimated_date", defaultContent: "-" },
                { data: "status", defaultContent: "-" },
            ],
            columnDefs: [
                {
                    className: "control",
                    orderable: false,
                    searchable: false,
                    responsivePriority: 2,
                    targets: 0,
                    render: function () {
                        return "";
                    },
                },
                {
                    targets: 1,
                    searchable: true,
                    visible: false,
                },
                {
                    responsivePriority: 1,
                    targets: 2,
                    render: function (data, type, full) {
                        if (type === "display") {
                            var $dataId = full["id"];
                            var detailRoute = full["is_smart"] ? "/smart-quote/" + $dataId : (typeof route === "function" ? route("quotation.show", $dataId) : "/quotation/" + $dataId);
                            var smartBadge = full["is_smart"] ? ' <span class="badge bg-label-primary fs-tiny py-0 px-1">Smart</span>' : '';
                            return '<a class="text-dark fw-semibold" href="' + detailRoute + '">' + (data || "-") + smartBadge + "</a>";
                        }
                        return data || "-";
                    },
                },
                {
                    className: "text-nowrap",
                    targets: 6,
                },
                {
                    targets: 4,
                    render: function (data, type) {
                        if (type === "display" && data !== null && data !== undefined) {
                            return "Rp " + Number(data).toLocaleString("id-ID");
                        }
                        return data || "Rp 0";
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full) {
                        var $status_number = full["status"];
                        var $titleTool = full["note"] || "";
                        var $status = {
                            20: { title: "20%", class: "bg-label-secondary" },
                            30: { title: "30%", class: "bg-label-dark" },
                            40: { title: "40%", class: "bg-label-info" },
                            60: { title: "60%", class: "bg-label-primary" },
                            80: { title: "80%", class: "bg-label-warning" },
                            100: { title: "100%", class: "bg-label-success" },
                            0: { title: "Loss", class: "bg-label-danger" },
                        };
                        if (typeof $status[$status_number] === "undefined") {
                            return data || "-";
                        }
                        return (
                            '<span class="badge rounded-pill ' +
                            $status[$status_number].class +
                            ' text-capitalized" data-bs-toggle="tooltip" title="' +
                            $titleTool +
                            '">' +
                            $status[$status_number].title +
                            "</span>"
                        );
                    },
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            order: [[1, "desc"]],
            dom: '<"row p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row p-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            language: {
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
                emptyTable: "Tidak ada data quotation active",
            },
        });
    }
});
