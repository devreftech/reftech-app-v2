$(function () {
    var dt_table_reports_admin = $(".datatable-reports-admin");
    var Url = "db/reports/admin";

    if (dt_table_reports_admin.length) {
        // Clone header row lalu replace isinya dengan input search
        dt_table_reports_admin.find("thead tr")
            .clone(true)
            .appendTo(dt_table_reports_admin.find("thead"));

        dt_table_reports_admin.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_reports_admin.column(i).search() !== this.value) {
                    dt_reports_admin.column(i).search(this.value).draw();
                }
            });
        });

        var dt_reports_admin = dt_table_reports_admin.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) {
                    d.year = $("#sr-admin-year-filter").val();
                },
            },
            columns: [
                { data: "no_service" },
                { data: "company" },
                { data: "jobdesc" },
                { data: "brand_type" },
                { data: "serial_tag" },
                {
                    data: "date",
                    render: function (data, type) {
                        if (type === "display") {
                            return data ? moment(data).format("DD-MM-YYYY") : "-";
                        }
                        return data;
                    },
                },
                { data: "sales" },
                { data: "technician" },
            ],
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: 0,
                    className: "text-nowrap",
                    render: function (data, type, full) {
                        if (type === "display") {
                            var detailRoute = route("service-reports.show", full["id"]);
                            return '<a class="fw-bold text-primary" href="' + detailRoute + '">' + data + "</a>";
                        }
                        return data;
                    },
                },
                { targets: 5, className: "text-center" },
                { targets: 6, className: "text-center" },
                { targets: 7, className: "text-center" },
            ],
            orderCellsTop: true,
            order: [[0, "desc"]],
            dom: '<"card-header flex-column flex-md-row"<"head-label hl-ra text-center"><"dt-year-filter-ra text-end pt-3 pt-md-0">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [7, 10, 25, 50, 75, 100],
            displayLength: 10,
        });

        $("div.hl-ra").html('<h5 class="card-title mb-0">Table Service Reports</h5>');

        var currentYear = new Date().getFullYear();
        var yearOpts = '';
        for (var y = currentYear; y >= 2024; y--) {
            yearOpts += '<option value="' + y + '">' + y + '</option>';
        }
        $("div.dt-year-filter-ra").html(
            '<div class="d-flex align-items-center gap-2">' +
            '<label class="form-label mb-0 fw-semibold small">Tahun</label>' +
            '<select class="form-select form-select-sm w-auto" id="sr-admin-year-filter">' + yearOpts + '</select>' +
            '</div>'
        );

        $("#sr-admin-year-filter").on("change", function () {
            dt_reports_admin.ajax.reload();
        });
    }
});
