$(function () {
    function dateCol(data) {
        if (!data) return "-";
        var m = moment(data);
        return m.isValid() ? m.format("DD-MM-YYYY") : data;
    }

    // Total gabungan BDG + BKS buat badge di tab induk "Barang Diterima" — mulai dari
    // 0 dan nambah tiap kali salah satu sub-tab selesai load (lazy, jadi awalnya bisa
    // parsial sampai kedua sub-tab pernah dibuka).
    var receivedCounts = { BDG: 0, BKS: 0 };
    function updateReceivedTotalBadge() {
        var total = receivedCounts.BDG + receivedCounts.BKS;
        $("#barang-diterima-count-badge").text(total).toggleClass("d-none", total === 0);
    }

    function initReceivedGoodsTable(selector, ajaxUrl, warehouse, subtabBadgeId) {
        var $table = $(selector);
        if (!$table.length) return;

        var initialized = false;

        function init() {
            if (initialized) return;
            initialized = true;

            $table.DataTable({
                ajax: {
                    type: "GET",
                    url: ajaxUrl,
                    headers: { "Content-Type": "application/json" },
                    dataSrc: function (json) {
                        var count = (json.data || []).length;
                        receivedCounts[warehouse] = count;
                        $("#" + subtabBadgeId).text(count).toggleClass("d-none", count === 0);
                        updateReceivedTotalBadge();
                        return json.data || [];
                    },
                },
                columns: [
                    { data: "no_gr" },
                    { data: "no_po" },
                    { data: "no_do" },
                    { data: "item" },
                    { data: "qty" },
                    { data: "received_by" },
                    { data: "date" },
                    { data: "damaged_count" },
                    { data: null },
                ],
                columnDefs: [
                    {
                        // No GR — kosong artinya GR Manual (id_purchase_order null), gak
                        // lahir dari alur PO, jadi ditandai badge "Manual" alih-alih "-".
                        targets: 0,
                        render: function (data, type, full) {
                            if (type !== "display") return data || "-";
                            var url = route("product-in.show", full.product_in_id);
                            var label = data || '<span class="badge bg-label-secondary">Manual</span>';
                            return '<a href="' + url + '" class="fw-semibold text-primary">' + label + "</a>";
                        },
                    },
                    { targets: [1, 2, 3, 5], render: function (data) { return data || "-"; } },
                    { targets: 4, className: "text-center fw-bold", render: function (data) { return data; } },
                    { targets: 6, render: function (data) { return dateCol(data); } },
                    {
                        targets: 7,
                        className: "text-center",
                        render: function (data) {
                            if (data > 0) {
                                return '<span class="badge bg-label-danger">' + data + " item</span>";
                            }
                            return '<span class="text-muted">-</span>';
                        },
                    },
                    {
                        targets: 8,
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, full) {
                            var url = route("product-in.show", full.product_in_id);
                            return '<a href="' + url + '" class="btn btn-sm btn-icon btn-label-secondary waves-effect rounded-circle" data-bs-toggle="tooltip" title="Lihat Detail">' +
                                '<i class="mdi mdi-eye-outline"></i></a>';
                        },
                    },
                ],
                order: [[6, "desc"]],
                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                dom:
                    '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: { emptyTable: "Belum ada barang yang diterima di gudang ini." },
                drawCallback: function () {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
            });
        }

        return init;
    }

    var initBdg = initReceivedGoodsTable(".datatable-received-goods-bdg", "/db/product-in/received/BDG", "BDG", "bdg-count-badge");
    var initBks = initReceivedGoodsTable(".datatable-received-goods-bks", "/db/product-in/received/BKS", "BKS", "bks-count-badge");

    // BDG adalah sub-tab aktif default, tapi tab induk "Barang Diterima" sendiri
    // belum tentu aktif saat halaman dimuat — jadi initnya baru jalan begitu
    // salah satu dari dua event ini kejadian duluan.
    if (initBdg) {
        $('[data-bs-target="#tab-barang-diterima"]').on("shown.bs.tab", initBdg);
        $("#subtab-bdg-btn").on("shown.bs.tab", initBdg);
        // Kalau tab induknya udah aktif dari awal (mis. reload dengan hash), langsung init.
        if ($("#tab-barang-diterima").hasClass("active")) initBdg();
    }
    if (initBks) {
        $("#subtab-bks-btn").on("shown.bs.tab", initBks);
    }
});
