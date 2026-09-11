$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    function statusBadge(status) {
        switch (status) {
            case "submitted":
                return '<span class="badge bg-label-warning text-nowrap"><i class="mdi mdi-clock-outline me-1"></i>Menunggu Gudang</span>';
            case "confirmed":
                return '<span class="badge bg-label-info text-nowrap"><i class="mdi mdi-package-check me-1"></i>Stok Dikonfirmasi</span>';
            case "goods_out":
                return '<span class="badge bg-label-primary text-nowrap"><i class="mdi mdi-truck-fast-outline me-1"></i>Barang Keluar</span>';
            case "converted":
                return '<span class="badge bg-label-success text-nowrap"><i class="mdi mdi-check-decagram-outline me-1"></i>Sudah Dikonversi ke Invoice</span>';
            default:
                return '<span class="badge bg-label-secondary text-nowrap">' + (status || "-") + "</span>";
        }
    }

    var $table = $(".datatable-suo-accounting");
    if (!$table.length) return;

    var currentFilter = "all";

    // Custom filtering for status tabs
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData) {
        if (settings.sTableId !== "datatable-suo-accounting-table") {
            return true;
        }
        if (currentFilter === "all") {
            return true;
        }
        return rowData.status === currentFilter;
    });

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: route("suo.data.accounting"),
            headers: { "Content-Type": "application/json" },
            dataSrc: function (json) {
                var list = json.data || [];
                var counts = {
                    all: list.length,
                    submitted: 0,
                    confirmed: 0,
                    goods_out: 0,
                    converted: 0,
                };

                list.forEach(function (item) {
                    if (counts[item.status] !== undefined) {
                        counts[item.status]++;
                    }
                });

                // Update tab badges
                $("#badge-tab-all").text(counts.all);
                $("#badge-tab-submitted").text(counts.submitted);
                $("#badge-tab-confirmed").text(counts.confirmed);
                $("#badge-tab-goods_out").text(counts.goods_out);
                $("#badge-tab-converted").text(counts.converted);

                // Update KPI card numbers
                $("#kpi-count-all").text(counts.all);
                $("#kpi-count-submitted").text(counts.submitted);
                $("#kpi-count-confirmed").text(counts.confirmed);
                $("#kpi-count-goods_out").text(counts.goods_out);
                $("#kpi-count-converted").text(counts.converted);

                return list;
            },
        },
        columns: [
            { data: "no_suo" },
            { data: "created_at" },
            { data: "company" },
            { data: "pic" },
            { data: "status" },
            { data: "no_invoice_booking" },
            { data: "sales_name" },
        ],
        columnDefs: [
            {
                // Kolom 0: No. SUO (Link ke Detail SUO)
                targets: 0,
                render: function (data, type, full) {
                    if (!data) return "-";
                    if (type !== "display") return data;
                    var url = route("suo.show", full.id);
                    return (
                        '<a href="' +
                        url +
                        '" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center">' +
                        '<i class="mdi mdi-file-document-outline me-1 fs-6"></i>' +
                        data +
                        "</a>"
                    );
                },
            },
            {
                // Kolom 1: Tanggal (DD-MM-YYYY)
                targets: 1,
                render: function (data) {
                    return dateCol(data);
                },
            },
            {
                // Kolom 2: Company
                targets: 2,
                render: function (data) {
                    return data ? '<span class="fw-semibold text-dark">' + data + "</span>" : "-";
                },
            },
            {
                // Kolom 3: PIC
                targets: 3,
                render: function (data) {
                    return data ? '<span class="text-muted">' + data + "</span>" : "-";
                },
            },
            {
                // Kolom 4: Status
                targets: 4,
                className: "text-center",
                render: function (data, type) {
                    return type === "display" ? statusBadge(data) : data;
                },
            },
            {
                // Kolom 5: No. Invoice Booking (Link ke Invoice)
                targets: 5,
                render: function (data, type, full) {
                    if (!data) return '<span class="text-muted">-</span>';
                    if (type !== "display") return data;
                    if (full.invoice_url) {
                        return (
                            '<a href="' +
                            full.invoice_url +
                            '" class="fw-bold text-success text-decoration-none d-inline-flex align-items-center font-monospace">' +
                            '<i class="mdi mdi-receipt-text-outline me-1 fs-6"></i>' +
                            data +
                            "</a>"
                        );
                    }
                    return '<span class="badge bg-label-success fw-semibold font-monospace">' + data + "</span>";
                },
            },
            {
                // Kolom 6: Sales (Avatar)
                targets: 6,
                className: "text-center",
                searchable: true,
                render: function (data, type, full) {
                    var name = full.sales_name || data || "-";
                    if (type !== "display") return name;
                    if (!name || name === "-") return '<span class="text-muted">-</span>';

                    var img = full.sales_image;
                    var initials =
                        full.sales_code ||
                        name
                            .split(" ")
                            .map(function (w) {
                                return w.charAt(0);
                            })
                            .slice(0, 2)
                            .join("")
                            .toUpperCase();

                    var colors = [
                        "bg-label-primary",
                        "bg-label-success",
                        "bg-label-warning",
                        "bg-label-danger",
                        "bg-label-info",
                        "bg-label-secondary",
                    ];
                    var colorClass = colors[name.charCodeAt(0) % colors.length];

                    var av = img
                        ? '<img src="/' +
                          img +
                          '" class="rounded-circle shadow-xs" style="width:34px;height:34px;object-fit:cover;" alt="' +
                          name +
                          '" onerror="this.onerror=null;this.parentElement.innerHTML=\'<div class=\\\'avatar-initial rounded-circle ' +
                          colorClass +
                          '\\\' style=\\\'display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;font-size:11px;font-weight:700;\\\'>' +
                          initials +
                          '</div>\';">'
                        : '<div class="avatar-initial rounded-circle ' +
                          colorClass +
                          '" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;font-size:11px;font-weight:700;">' +
                          initials +
                          "</div>";

                    var tooltipText = name + (full.sales_code ? " (" + full.sales_code + ")" : "");
                    return (
                        '<div class="d-inline-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="' +
                        tooltipText +
                        '">' +
                        av +
                        "</div>"
                    );
                },
            },
        ],
        order: [[1, "desc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"row px-3 pt-2 pb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row px-3 pt-2 pb-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            emptyTable: "Belum ada Sales Urgent Order.",
            search: "Cari SUO:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                previous: '<i class="mdi mdi-chevron-left"></i>',
                next: '<i class="mdi mdi-chevron-right"></i>',
            },
        },
        drawCallback: function () {
            // Re-initialize Bootstrap 5 tooltips after DataTables redraw
            if (typeof bootstrap !== "undefined" && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        },
    });

    // Tab switching handler
    $("#suo-status-tabs [data-status]").on("click", function () {
        $("#suo-status-tabs .nav-link").removeClass("active");
        $(this).addClass("active");

        var status = $(this).data("status");
        currentFilter = status;

        // Highlight matching KPI card
        $(".suo-kpi-card").removeClass("active-kpi");
        $('.suo-kpi-card[data-kpi-target="' + status + '"]').addClass("active-kpi");

        dt.draw();
    });

    // KPI card click triggers tab switch
    $("[data-kpi-target]").on("click", function () {
        var target = $(this).data("kpi-target");
        $('#suo-status-tabs [data-status="' + target + '"]').trigger("click");
    });
});
