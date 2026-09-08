{{-- Shared BAST create/edit modal. Include once per page; JS API: window.openBastModal(options) --}}
<div class="modal fade" id="bastModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form id="bastForm">
                @csrf
                <input type="hidden" id="bastFormMethod" name="_method" value="">
                <input type="hidden" id="bastId" name="bast_id" value="">
                <input type="hidden" id="bastIdKanbanTask" name="id_kanban_task" value="">
                <input type="hidden" id="bastIdQuotation" name="id_quotation" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="bastModalTitle">Buat BAST</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bastFormAlert" class="alert alert-danger d-none"></div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Tipe BAST</label>
                            <select class="form-select fw-bold text-primary" id="bastType" name="type" required>
                                <option value="Default" selected>Default BAST (Standard)</option>
                                <option value="Rental">BAST Rental (Khusus Rental)</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Entitas</label>
                            <select class="form-select" id="bastEntity" name="entity" required>
                                <option value="Reftech">PT Reftech Jaya Optima</option>
                                <option value="Kojisha">PT Kojisha Innotiv Indonesia</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" id="bastWorkDateLabel">Tanggal Pekerjaan</label>
                            <input type="date" class="form-control" id="bastWorkDate" name="work_date">
                            <div class="form-text text-muted" style="font-size: 11px;">Opsional — kosongkan jika ingin ditulis manual di printout.</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Sesuai PO / Kontrak No.</label>
                            <input type="text" class="form-control" id="bastPoNumber" name="po_number">
                        </div>

                        {{-- Baris Khusus Tipe BAST Rental --}}
                        <div class="col-12 d-none" id="bastRentalDatesRow">
                            <div class="p-3 bg-light rounded border border-primary border-opacity-25">
                                <label class="form-label fw-bold text-primary mb-2 d-flex align-items-center gap-1">
                                    <i class="mdi mdi-calendar-range"></i> Masa Rental :
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Tanggal Mulai Rental</label>
                                        <input type="date" class="form-control" id="bastRentalStartDate" name="rental_start_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Tanggal Berakhir Rental</label>
                                        <input type="date" class="form-control" id="bastRentalEndDate" name="rental_end_date">
                                    </div>
                                </div>
                                <div class="form-text text-muted mt-2" style="font-size: 11px;">
                                    <i class="mdi mdi-information-outline me-1"></i>Masa rental bisa dikosongkan jika ingin diisi/ditulis manual pada printout.
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label fw-semibold">Customer / Perusahaan</label>
                            <input type="text" class="form-control" id="bastCustomerName" name="customer_name" required>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label fw-semibold">Judul Pekerjaan</label>
                            <input type="text" class="form-control" id="bastWorkTitle" name="work_title" required>
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Unit</span>
                                <button type="button" class="btn btn-xs btn-outline-primary" id="bastAddUnitRow">
                                    <i class="mdi mdi-plus"></i> Tambah Baris
                                </button>
                            </label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" id="bastUnitTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">No.</th>
                                            <th>Unit</th>
                                            <th>Serial No.</th>
                                            <th style="width: 15%">Jumlah</th>
                                            <th style="width: 5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="bastUnitTableBody"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label fw-semibold">Hasil pengecekan pada saat test running</label>
                            <textarea class="form-control" id="bastTestRunningResult" name="test_running_result" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="bastSubmitBtn">Simpan BAST</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    (function() {
        const unitRowTemplate = (index) => `
            <tr data-row="${index}">
                <td class="text-center bast-row-no">${index + 1}</td>
                <td><input type="text" class="form-control form-control-sm" name="units[${index}][unit_name]" required></td>
                <td><input type="text" class="form-control form-control-sm" name="units[${index}][serial_no]"></td>
                <td><input type="number" min="1" value="1" class="form-control form-control-sm" name="units[${index}][qty]"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger bast-remove-row" style="padding: 2px 6px;">
                        <i class="mdi mdi-close"></i>
                    </button>
                </td>
            </tr>`;

        let rowIndex = 0;

        function renumberRows() {
            $('#bastUnitTableBody tr').each(function(i) {
                $(this).find('.bast-row-no').text(i + 1);
                $(this).find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        $(this).attr('name', name.replace(/units\[\d+\]/, `units[${i}]`));
                    }
                });
            });
        }

        function addUnitRow(data) {
            $('#bastUnitTableBody').append(unitRowTemplate(rowIndex));
            const $row = $('#bastUnitTableBody tr').last();
            if (data) {
                $row.find('input[name$="[unit_name]"]').val(data.unit_name || '');
                $row.find('input[name$="[serial_no]"]').val(data.serial_no || '');
                $row.find('input[name$="[qty]"]').val(data.qty || 1);
            }
            rowIndex++;
        }

        $(document).on('click', '#bastAddUnitRow', function() {
            addUnitRow();
        });

        $(document).on('click', '.bast-remove-row', function() {
            $(this).closest('tr').remove();
            renumberRows();
        });

        $('#bastType').on('change', function() {
            const type = $(this).val();
            if (type === 'Rental') {
                $('#bastWorkDateLabel').text('Tanggal Commissioning');
                $('#bastRentalDatesRow').removeClass('d-none');
                $('#bastUnitTableBody input[name$="[qty]"]').val(1);
            } else {
                $('#bastWorkDateLabel').text('Tanggal Pekerjaan');
                $('#bastRentalDatesRow').addClass('d-none');
            }
        });

        window.openBastModal = function(options) {
            options = options || {};
            $('#bastForm')[0].reset();
            $('#bastUnitTableBody').empty();
            $('#bastFormAlert').addClass('d-none').text('');
            rowIndex = 0;

            const isEdit = !!options.bastId;
            $('#bastModalTitle').text(isEdit ? 'Edit BAST' : 'Buat BAST');
            $('#bastId').val(options.bastId || '');
            $('#bastFormMethod').val(isEdit ? 'PATCH' : '');
            $('#bastIdKanbanTask').val(options.idKanbanTask || '');
            $('#bastIdQuotation').val(options.idQuotation || '');
            $('#bastType').val(options.type || 'Default').trigger('change');
            $('#bastEntity').val(options.entity || 'Reftech');
            $('#bastCustomerName').val(options.customerName || '');
            $('#bastWorkTitle').val(options.workTitle || '');
            $('#bastPoNumber').val(options.poNumber || '');
            $('#bastWorkDate').val(options.workDate || '');
            $('#bastRentalStartDate').val(options.rentalStartDate || '');
            $('#bastRentalEndDate').val(options.rentalEndDate || '');
            $('#bastTestRunningResult').val(options.testRunningResult || '');

            if (options.units && options.units.length > 0) {
                options.units.forEach((u) => addUnitRow(u));
            } else {
                addUnitRow();
            }

            $('#bastModal').modal('show');
        };

        $(document).on('submit', '#bastForm', function(e) {
            e.preventDefault();

            const bastId = $('#bastId').val();
            const isEdit = !!bastId;
            const url = isEdit ? `{{ url('/bast') }}/${bastId}` : `{{ route('bast.store') }}`;

            const $btn = $('#bastSubmitBtn');
            $btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $('#bastModal').modal('hide');
                    $(document).trigger('bast:saved', [response, isEdit]);
                },
                error: function(xhr) {
                    let msg = 'Gagal menyimpan BAST.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                        }
                    }
                    $('#bastFormAlert').removeClass('d-none').html(msg);
                },
                complete: function() {
                    $btn.prop('disabled', false).text('Simpan BAST');
                }
            });
        });
    })();
</script>
@endpush
