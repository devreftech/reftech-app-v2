$(function () {
    var baseAjaxUrl = "/db/crm/status";

    function initCustomerTable(selector, statusId) {
        var $el = $(selector);
        if (!$el.length) return null;

        // Clone header row for search inputs if not cloned yet
        if ($el.find("thead tr").length === 1) {
            $el.find("thead tr")
                .clone(true)
                .appendTo($el.find("thead"));

            $el.find("thead tr:eq(1) th").each(function (i) {
                var title = $(this).text();
                $(this).html(
                    '<input type="text" class="form-control form-control-sm" placeholder="Cari ' +
                        title +
                        '..." />'
                );
            });
        }

        var dt = $el.DataTable({
            processing: true,
            ajax: {
                type: "GET",
                url: baseAjaxUrl + "?status=" + statusId,
                headers: {
                    "Content-Type": "application/json",
                },
                error: function (xhr, error, code) {
                    console.error("Error loading customer data:", error);
                },
            },
            columns: [
                { data: "company" },
                { data: "ru" },
                { data: "status" },
                { data: "area" },
                {
                    data: "note",
                    render: function (data, type, row) {
                        if (data === null || data === undefined || data === "null") {
                            return "-";
                        }
                        return type === "display" ? data : "-";
                    },
                },
                { data: "date" },
                { data: "follow_up" },
                { data: "info" },
            ],
            columnDefs: [
                {
                    targets: [1, 2, 5, 6, 7],
                    className: "text-center",
                },
                {
                    targets: [0, 3],
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

                        if (companyName.length > 25) {
                            companyName = companyName.substring(0, 25) + "...";
                        }

                        return (
                            '<a class="fw-bold text-primary" href="' +
                            detailRoute +
                            '" data-bs-toggle="tooltip" data-bs-placement="top" title="' +
                            (data || "") +
                            '">' +
                            companyName +
                            "</a>"
                        );
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var $status_ru = full["ru"];
                            var $status = {
                                User: { class: "bg-success" },
                                Reseller: { class: "bg-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($status[$status_ru] ? $status[$status_ru].class : "bg-label-secondary") +
                                '">' +
                                (data || "-") +
                                "</span>"
                            );
                        }
                        return data;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, full, meta) {
                        var currentStatus = String(full.status || data || "2");
                        var dropdown =
                            '<select class="form-select form-select-sm status-dropdown" data-id="' +
                            full.id +
                            '" style="min-width: 105px; font-size: 0.8rem;">';
                        dropdown +=
                            '<option value="2" ' +
                            (currentStatus === "2" ? "selected" : "") +
                            ">Aktif</option>";
                        dropdown +=
                            '<option value="3" ' +
                            (currentStatus === "3" ? "selected" : "") +
                            ">Non Aktif</option>";
                        dropdown +=
                            '<option value="1" ' +
                            (currentStatus === "1" ? "selected" : "") +
                            ">Bangkrupt</option>";
                        dropdown += "</select>";
                        return dropdown;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return (data && data !== "null") ? data : "-";
                    },
                },
                {
                    targets: [5, 6],
                    render: function (data, type, row) {
                        if (data === null || data === undefined || data === "null") {
                            return "-";
                        }
                        return type === "display" ? data : "-";
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, full, row) {
                        if (type === "display") {
                            var flag = full["info"] || data;
                            if (!flag || flag === "null") return "-";
                            var note = full["note"] || "No notes available";
                            var $info = {
                                Reftech: { class: "bg-label-primary" },
                                Kojisha: { class: "bg-label-warning" },
                            };
                            return (
                                '<span class="badge ' +
                                ($info[flag] ? $info[flag].class : "bg-label-secondary") +
                                '" data-bs-toggle="tooltip" data-bs-placement="top" title="' +
                                note +
                                '">' +
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

    // Initialize the 3 sub-tables if present
    if ($(".datatable-customer-active, .datatable-customer-non-active, .datatable-customer-bangkrupt").length) {
        var custTables = [
            { dt: initCustomerTable(".datatable-customer-active", 2), statusId: 2, badgeId: "badge-cust-active" },
            { dt: initCustomerTable(".datatable-customer-non-active", 3), statusId: 3, badgeId: "badge-cust-non-active" },
            { dt: initCustomerTable(".datatable-customer-bangkrupt", 1), statusId: 1, badgeId: "badge-cust-bangkrupt" },
        ];

        window.dtCustActive = custTables[0].dt;
        window.dtCustNonActive = custTables[1].dt;
        window.dtCustBangkrupt = custTables[2].dt;

        function updateMasterCustomerBadge() {
            var total = 0;
            custTables.forEach(function (t) {
                if (t.dt) {
                    total += t.dt.page.info().recordsTotal || 0;
                }
            });
            $("#badge-customers").text(total);
        }

        $(document).on("draw.dt", function (e) {
            var $tbl = $(e.target);
            $tbl.find('[data-bs-toggle="tooltip"]').tooltip();

            var badgeId = $tbl.data("badge");
            if (badgeId) {
                var api = $tbl.DataTable();
                var count = api.page.info().recordsTotal;
                $("#" + badgeId).text(count);
            }
            updateMasterCustomerBadge();
        });

        $('#cust-status-tab-nav button[data-bs-toggle="tab"], #crm-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        // Handle dropdown status update for customer tables
        $(document).on('change', '.status-dropdown', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/existing/update-status/' + rowId,
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    custTables.forEach(function(t) {
                        if (t.dt) t.dt.ajax.reload(null, false);
                    });
                },
                error: function(error) {
                    console.error('Gagal memperbarui status:', error);
                }
            });
        });
    } else if ($(".datatable-customer-search").length) {
        // Fallback for single table if needed
        initCustomerTable(".datatable-customer-search", "");
    }
});
