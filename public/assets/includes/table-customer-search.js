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
                    targets: [1, 2, 7],
                    className: "text-center",
                },
                {
                    targets: [5, 6],
                    className: "text-center text-nowrap",
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
                        if (type !== "display") return full.status || data || "2";
                        var currentStatus = String(full.status || data || "2");
                        var companyName = (full.company || "").replace(/"/g, "&quot;");

                        var statusConfig = {
                            "2": {
                                label: "Aktif",
                                badgeClass: "btn-label-success text-success",
                                icon: "mdi-check-circle-outline",
                            },
                            "3": {
                                label: "Non Aktif",
                                badgeClass: "btn-label-warning text-warning",
                                icon: "mdi-close-circle-outline",
                            },
                            "1": {
                                label: "Bangkrupt",
                                badgeClass: "btn-label-danger text-danger",
                                icon: "mdi-alert-circle-outline",
                            },
                        };

                        var config = statusConfig[currentStatus] || statusConfig["2"];

                        return (
                            '<button type="button" class="btn btn-xs ' +
                            config.badgeClass +
                            ' rounded-pill px-2 py-1 btn-change-status d-inline-flex align-items-center gap-1" ' +
                            'data-id="' +
                            full.id +
                            '" ' +
                            'data-company="' +
                            companyName +
                            '" ' +
                            'data-status="' +
                            currentStatus +
                            '" ' +
                            'title="Klik untuk ubah status">' +
                            '<i class="mdi ' +
                            config.icon +
                            '" style="font-size: 0.9rem;"></i> ' +
                            '<span class="fw-semibold">' +
                            config.label +
                            "</span>" +
                            '<i class="mdi mdi-pencil-outline ms-1" style="font-size: 0.75rem; opacity: 0.65;"></i>' +
                            "</button>"
                        );
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

        // Open Modal Ubah Status Customer
        $(document).on('click', '.btn-change-status', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var clientId = $btn.data('id');
            var companyName = $btn.data('company') || '-';
            var currentStatus = String($btn.data('status') || '2');

            $('#statusClientId').val(clientId);
            $('#statusCompanyName').text(companyName);

            // Set radio checked
            $('input[name="customer_status"][value="' + currentStatus + '"]').prop('checked', true);

            var modalEl = document.getElementById('modalChangeCustomerStatus');
            if (modalEl) {
                var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.show();
            }
        });

        // Submit update status via modal
        $('#formChangeCustomerStatus').on('submit', function(e) {
            e.preventDefault();
            var clientId = $('#statusClientId').val();
            var newStatus = $('input[name="customer_status"]:checked').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var $btnSubmit = $('#btnSubmitCustomerStatus');
            var $spinner = $btnSubmit.find('.spinner-border');

            if (!clientId || !newStatus) return;

            $btnSubmit.prop('disabled', true);
            $spinner.removeClass('d-none');

            $.ajax({
                type: 'POST',
                url: '/existing/update-status/' + clientId,
                data: {
                    status: newStatus,
                    _token: csrfToken
                },
                success: function(response) {
                    $btnSubmit.prop('disabled', false);
                    $spinner.addClass('d-none');

                    var modalEl = document.getElementById('modalChangeCustomerStatus');
                    if (modalEl) {
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }

                    // Reload all customer DataTables
                    custTables.forEach(function(t) {
                        if (t.dt) t.dt.ajax.reload(null, false);
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Status customer berhasil diperbarui.',
                            timer: 2000,
                            showConfirmButton: false,
                            customClass: {
                                confirmButton: 'btn btn-success waves-effect'
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $btnSubmit.prop('disabled', false);
                    $spinner.addClass('d-none');

                    var errorMsg = 'Gagal memperbarui status customer.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMsg
                        });
                    } else {
                        alert(errorMsg);
                    }
                }
            });
        });

        // Fallback for dropdown status update if any exists
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
