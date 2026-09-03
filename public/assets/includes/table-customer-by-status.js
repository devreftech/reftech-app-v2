$(function () {
    var baseAjaxUrl = "/db/crm/status";
    var selectedSales = $("#admin-sales-filter").val() || "";
    var selectedRuType = $("#ru-type-filter").val() || "";

    function getAjaxUrl(statusId) {
        var url = baseAjaxUrl + "?status=" + statusId;
        if (selectedSales) {
            url += "&sales_id=" + encodeURIComponent(selectedSales);
        }
        if (selectedRuType) {
            url += "&ru_type=" + encodeURIComponent(selectedRuType);
        }
        return url;
    }

    function initDataTable(selector, statusId) {
        var $el = $(selector);
        if (!$el.length) return null;

        // Clone header row lalu replace isinya dengan input search
        if ($el.find("thead tr").length === 1) {
            $el.find("thead tr")
                .clone(true)
                .appendTo($el.find("thead"));

            $el.find("thead tr:eq(1) th").each(function (i) {
                var title = $(this).text();
                $(this).html(
                    '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
                );
            });
        }

        var dt = $el.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: getAjaxUrl(statusId),
                headers: {
                    "Content-Type": "application/json",
                },
                error: function (xhr, error, code) {
                    console.error("Error loading customer data:", error);
                },
            },
            columns: [
                { data: "company" },
                { data: "status" },
                { data: "area" },
                { data: "date" },
                { data: "follow_up" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: [1, 3, 4, 5],
                    className: "text-center",
                },
                {
                    targets: [0, 2],
                    className: "text-nowrap",
                },
                {
                    responsivePriority: 1,
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var $dataId = full["id"];
                        var detailRoute = (typeof route === "function") ? route("existing.show", $dataId) : ("/existing/" + $dataId);
                        var companyName = data || "-";

                        var $status_ru = full["ru"];
                        var $ruBadge = "";
                        if ($status_ru) {
                            var $ruClass = $status_ru === "User" ? "bg-success" : ($status_ru === "Reseller" ? "bg-warning" : "bg-label-secondary");
                            $ruBadge = '<span class="badge ' + $ruClass + ' me-1" style="font-size: 0.68rem;">' + $status_ru + '</span> ';
                        }

                        if (companyName.length > 30) {
                            companyName = companyName.substring(0, 30) + "...";
                        }

                        return $ruBadge + '<a class="fw-bold text-primary" href="' + detailRoute + '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + (data || "") + '">' + companyName + '</a>';
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full, meta) {
                        var currentStatus = String(full.status || data || "2");
                        var dropdown = '<select class="form-select form-select-sm status-dropdown" data-id="' + full.id + '" style="min-width: 110px; font-size: 0.8rem;">';
                        dropdown += '<option value="2" ' + (currentStatus === "2" ? "selected" : "") + '>Aktif</option>';
                        dropdown += '<option value="3" ' + (currentStatus === "3" ? "selected" : "") + '>Non Aktif</option>';
                        dropdown += '<option value="1" ' + (currentStatus === "1" ? "selected" : "") + '>Bangkrupt</option>';
                        dropdown += '</select>';
                        return dropdown;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return (data && data !== "null") ? data : "-";
                    },
                },
                {
                    targets: [3, 4],
                    render: function (data, type, row) {
                        if (data === null || data === undefined || data === "null") {
                            return "-";
                        }
                        return type === "display" ? data : "-";
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var flag = full["info"] || data;
                            if (!flag || flag === "null") return "-";
                            var note = full["note"] || "No recent activity";
                            var $info = {
                                Reftech: { class: "bg-label-primary" },
                                Kojisha: { class: "bg-label-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($info[flag] ? $info[flag].class : "bg-label-secondary") +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" title="' + note + '">' +
                                flag +
                                "</span>"
                            );
                        }
                        return data || "-";
                    },
                },
            ],
            order: [[0, "asc"]],
            dom:
                '<"row p-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
                '<"table-responsive"t>' +
                '<"row p-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            language: {
                paginate: {
                    next: '<i class="mdi mdi-chevron-right"></i>',
                    previous: '<i class="mdi mdi-chevron-left"></i>',
                },
                emptyTable: "Tidak ada data customer",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ customer",
                infoEmpty: "Menampilkan 0 customer",
            },
        });

        $el.find("thead tr:eq(1) th input").each(function (i) {
            $(this).on("keyup change", function () {
                if (dt.column(i).search() !== this.value) {
                    dt.column(i).search(this.value).draw();
                }
            });
        });

        return dt;
    }

    var tables = [
        { dt: initDataTable(".datatable-customers-active", 2), statusId: 2, badgeId: "badge-active" },
        { dt: initDataTable(".datatable-customers-non-active", 3), statusId: 3, badgeId: "badge-non-active" },
        { dt: initDataTable(".datatable-customers-bangkrupt", 1), statusId: 1, badgeId: "badge-bangkrupt" },
    ];

    window.dtCustomerActive = tables[0].dt;
    window.dtCustomerNonActive = tables[1].dt;
    window.dtCustomerBangkrupt = tables[2].dt;

    function reloadAllTables() {
        tables.forEach(function (t) {
            if (!t.dt) return;
            var url = getAjaxUrl(t.statusId);
            t.dt.ajax.url(url).load();
        });
    }

    $("#ru-type-filter").on("change", function () {
        selectedRuType = $(this).val();
        reloadAllTables();
    });

    $("#admin-sales-filter").on("change", function () {
        selectedSales = $(this).val();
        reloadAllTables();
    });

    $('#crm-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    $(document).on('draw.dt', function (e) {
        var $tbl = $(e.target);
        $tbl.find('[data-bs-toggle="tooltip"]').tooltip();

        var badgeId = $tbl.data('badge');
        if (!badgeId) return;
        var api = $tbl.DataTable();
        var count = api.page.info().recordsTotal;
        $('#' + badgeId).text(count);
    });
});
