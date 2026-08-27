$(function () {
    var $table = $(".datatable-tool-master");
    if (!$table.length) return;

    function currency(data) {
        if (data === null || data === undefined || data === "") return "-";
        return "Rp " + Number(data).toLocaleString("id-ID");
    }

    function fotoCol(data, type, full) {
        if (type !== "display") return data || "";
        if (!full.foto_referensi) {
            return '<span class="d-inline-flex align-items-center justify-content-center rounded bg-label-secondary" style="width:40px;height:40px;"><i class="mdi mdi-tools"></i></span>';
        }
        return '<img src="/' + full.foto_referensi + '" alt="' + (full.nama_tools || "") +
            '" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">';
    }

    var statusBadges = {
        1: '<span class="badge bg-label-success">Aktif</span>',
        0: '<span class="badge bg-label-secondary">Nonaktif</span>',
    };

    var dt = $table.DataTable({
        ajax: {
            type: "GET",
            url: "/db/tool-master",
            headers: { "Content-Type": "application/json" },
            dataSrc: "data",
        },
        columns: [
            { data: null },
            { data: null },
            { data: "foto_referensi" },
            { data: "nama_tools" },
            { data: "kategori" },
            { data: "spesifikasi" },
            { data: "harga_referensi" },
            { data: "status_aktif" },
            { data: null },
        ],
        columnDefs: [
            {
                targets: 0,
                className: "control",
                orderable: false,
                searchable: false,
                render: function () { return ""; },
            },
            {
                targets: 1,
                orderable: false,
                searchable: false,
                render: function () { return ""; },
            },
            { targets: 2, orderable: false, searchable: false, render: fotoCol },
            { targets: 3, render: function (data) { return data || "-"; } },
            {
                targets: 4,
                render: function (data) {
                    return data ? '<span class="badge bg-label-primary">' + data + "</span>" : "-";
                },
            },
            { targets: 5, render: function (data) { return data || "-"; } },
            { targets: 6, render: function (data) { return currency(data); } },
            {
                targets: 7,
                render: function (data) {
                    return statusBadges[data] || statusBadges[1];
                },
            },
            {
                targets: 8,
                orderable: false,
                searchable: false,
                render: function (data, type, full) {
                    return (
                        '<button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect btn-edit-tool" data-id="' + full.id + '" title="Edit">' +
                        '<i class="mdi mdi-pencil-outline"></i></button>' +
                        '<button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect btn-delete-tool" data-id="' + full.id + '" title="Hapus">' +
                        '<i class="mdi mdi-delete-outline"></i></button>'
                    );
                },
            },
        ],
        order: [[3, "asc"]],
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        dom:
            '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>>' +
            '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: [
            {
                text: '<i class="mdi mdi-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Master Tools</span>',
                className: "btn btn-primary",
                action: function () {
                    resetToolForm();
                    $("#toolMasterModalLabel").text("Create New Master Tools");
                    var modal = new bootstrap.Modal(document.getElementById("toolMasterModal"));
                    modal.show();
                },
            },
        ],
        language: { emptyTable: "Belum ada data master tools." },
    });

    $("div.head-label").html('<h5 class="card-title mb-0">Master Tools</h5>');

    function resetToolForm() {
        var $form = $("#formToolMaster");
        $form.attr("action", route("tool-master.store"));
        $("#toolMasterMethod").val("post");
        $form[0].reset();
        $("#fotoReferensiPreviewWrapper").hide();
        $("#fotoReferensiPreview").attr("src", "");
    }

    $table.on("click", ".btn-edit-tool", function () {
        var id = $(this).data("id");
        var row = dt.row($(this).closest("tr")).data();
        if (!row) return;

        resetToolForm();
        $("#toolMasterModalLabel").text("Edit Master Tools");
        $("#formToolMaster").attr("action", route("tool-master.update", id));
        $("#toolMasterMethod").val("patch");

        $("#nama_tools").val(row.nama_tools || "");
        $("#kategori").val(row.kategori || "");
        $("#spesifikasi").val(row.spesifikasi || "");
        $("#link_pembelian").val(row.link_pembelian || "");
        $("#harga_referensi").val(row.harga_referensi || "");
        $("#status_aktif").val(row.status_aktif == 0 ? "0" : "1");

        if (row.foto_referensi) {
            $("#fotoReferensiPreview").attr("src", "/" + row.foto_referensi);
            $("#fotoReferensiPreviewWrapper").show();
        }

        var modal = new bootstrap.Modal(document.getElementById("toolMasterModal"));
        modal.show();
    });

    $table.on("click", ".btn-delete-tool", function () {
        var id = $(this).data("id");

        Swal.fire({
            title: "Hapus Master Tools?",
            text: "Data yang sudah dihapus tidak bisa dikembalikan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn btn-danger me-2",
                cancelButton: "btn btn-label-secondary",
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: route("tool-master.destroy", id),
                type: "DELETE",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function () {
                    dt.ajax.reload(null, false);
                    Swal.fire({
                        icon: "success",
                        title: "Terhapus!",
                        text: "Master Tools berhasil dihapus.",
                        customClass: { confirmButton: "btn btn-success" },
                        buttonsStyling: false,
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: "Terjadi kesalahan saat menghapus data.",
                        customClass: { confirmButton: "btn btn-danger" },
                        buttonsStyling: false,
                    });
                },
            });
        });
    });

    document.getElementById("toolMasterModal")?.addEventListener("hidden.bs.modal", function () {
        resetToolForm();
        $("#toolMasterModalLabel").text("Create New Master Tools");
    });
});
