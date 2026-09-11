$(function () {
    var dt_table_draft = $(".datatable-draft");
    var Url = "db/quotation/draft";

    if (dt_table_draft.length) {
        dt_table_draft.find("thead tr")
            .clone(true)
            .appendTo(dt_table_draft.find("thead"));

        dt_table_draft.find("thead tr:eq(1) th").each(function (i) {
            var title = $(this).text();
            if (title === "Action" || i === 7) {
                $(this).html("");
                return;
            }
            $(this).html(
                '<input type="text" class="form-control form-control-sm" placeholder="Cari ' + title + '..." />'
            );
            $("input", this).on("keyup change", function () {
                if (dt_draft.column(i).search() !== this.value) {
                    dt_draft.column(i).search(this.value).draw();
                }
            });
        });

        var dt_draft = dt_table_draft.DataTable({
            ajax: {
                type: "GET",
                url: Url,
                headers: { "Content-Type": "application/json" },
                data: function (d) { d.year = window.quotationYearFilter || 'all'; return d; },
            },
            columns: [
                { data: "no_quote" },
                { data: "company" },
                { data: "subtotal" },
                { data: "type" },
                { data: "title" },
                { data: "estimated_date" },
                { data: "status" },
                { data: "id" },
            ],
            columnDefs: [
                {
                    targets: [2, 3, 4, 5, 6, 7],
                    className: "text-center",
                },
                {
                    targets: 0,
                    className: "text-center text-nowrap",
                    width: "60px",
                },
                {
                    responsivePriority: 1,
                    targets: 0,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var $dataId = full["id"];
                        var detailRoute = "/smart-quote/" + $dataId;
                        var full_no = data || "-";
                        var short   = full_no.length > 5 ? full_no.substring(0, 5) + "…" : full_no;
                        return '<a class="fw-bold text-primary" href="' + detailRoute + '"' +
                            ' data-bs-toggle="tooltip" data-bs-placement="top"' +
                            ' data-bs-custom-class="tooltip-quote-no" title="' + full_no + '">' +
                            short + "</a>";
                    },
                },
                {
                    targets: 1,
                    className: "text-start",
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var ru = full["ru"];
                        var ruMap = {
                            User:     '<span class="badge rounded-pill bg-label-info me-1">U</span>',
                            Reseller: '<span class="badge rounded-pill bg-label-dark me-1">R</span>',
                        };
                        var badge = ruMap[ru] || "";
                        return badge + (data || "-");
                    },
                },
                {
                    targets: 2,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        var formatted = parseInt(data || 0).toLocaleString("id-ID");
                        return '<div class="d-flex justify-content-between px-2"><span>Rp.</span><span>' + formatted + "</span></div>";
                    },
                },
                {
                    targets: 3,
                    render: function (data, type) {
                        if (type === "filter" || type === "sort") return data || "-";
                        if (type !== "display") return data;
                        if (!data) {
                            return '<span class="badge rounded-pill bg-label-secondary">-</span>';
                        }
                        var colors = {
                            Unit:      "bg-label-primary",
                            Rental:    "bg-label-info",
                            Project:   "bg-label-dark",
                            Parts:     "bg-label-warning",
                            Sparepart: "bg-label-warning",
                            Service:   "bg-label-success",
                        };
                        var cls = colors[data] || "bg-label-secondary";
                        return '<span class="badge rounded-pill ' + cls + '">' + data + "</span>";
                    },
                },
                {
                    targets: 4,
                    render: function (data) { return data || "-"; },
                },
                {
                    targets: 5,
                    render: function (data, type) {
                        if (type !== "display") return data;
                        return data ? moment(data).format("DD-MM-YYYY") : "-";
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var tip = full["tip"] || "Draft tersimpan";
                        var badge = '<span class="badge rounded-pill bg-label-secondary cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="' + tip + '">Draft</span>';
                        badge += ' <span class="badge bg-label-info ms-1">Smart</span>';
                        return badge;
                    },
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full) {
                        if (type !== "display") return data;
                        var id = full["id"];
                        var editUrl = "/smart-quote/" + id + "/edit";
                        var viewUrl = "/smart-quote/" + id;
                        return '<div class="d-inline-flex gap-1">' +
                            '<a href="' + editUrl + '" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="tooltip" title="Edit / Lanjutkan Draft">' +
                                '<i class="mdi mdi-pencil-outline"></i>' +
                            '</a>' +
                            '<a href="' + viewUrl + '" class="btn btn-sm btn-icon btn-label-info" data-bs-toggle="tooltip" title="Lihat Detail">' +
                                '<i class="mdi mdi-eye-outline"></i>' +
                            '</a>' +
                            '<button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-draft" data-id="' + id + '" data-bs-toggle="tooltip" title="Hapus Draft">' +
                                '<i class="mdi mdi-trash-can-outline"></i>' +
                            '</button>' +
                        '</div>';
                    },
                },
            ],
            orderCellsTop: true,
            order: [],
            dom: '<"row align-items-center mb-2"<"col-auto"l><"col-auto ms-auto dt-btn-draft">><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            displayLength: 10,
            initComplete: function () {
                $(".dt-btn-draft").html(
                    '<a href="' + route("unit-quotation.create") + '" class="btn btn-primary btn-sm">' +
                        '<i class="mdi mdi-plus me-1"></i>Draft Baru' +
                    '</a>'
                );
            },
        });

        window.dtDraft = dt_draft;

        dt_table_draft.on("draw.dt", function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Handle delete draft
        $(document).on("click", ".btn-delete-draft", function () {
            var draftId = $(this).data("id");
            Swal.fire({
                title: "Hapus Draft?",
                text: "Draft penawaran ini akan dihapus secara permanen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#8592a3",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-1",
                    cancelButton: "btn btn-label-secondary",
                },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/smart-quote/" + draftId,
                        type: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function () {
                            Swal.fire({
                                icon: "success",
                                title: "Terhapus!",
                                text: "Draft berhasil dihapus.",
                                timer: 1500,
                                showConfirmButton: false,
                            });
                            dt_draft.ajax.reload();
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal",
                                text: xhr.responseJSON?.message || "Gagal menghapus draft.",
                            });
                        },
                    });
                }
            });
        });
    }
});
