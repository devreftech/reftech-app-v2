
<?php $__env->startSection('title', 'My Prospect'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->role != 'Sales'): ?>
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm kpi-tile" style="border-left: 4px solid var(--bs-primary);">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-muted">Quotation</p>
                            <h4 class="mb-2">Rp
                                <?php echo e(number_format(Auth::user()->role == 'Admin' ? $forecastAdmin : $forecast, 2, ',', '.')); ?>

                            </h4>
                            <span
                                class="badge rounded-pill bg-label-primary"><?php echo e((Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->whereIn('status', ['20', '30', '40', '60', '80'])->count()); ?></span>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="mdi mdi-home-outline mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm kpi-tile" style="border-left: 4px solid var(--bs-warning);">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-muted">Hot Prospect</p>
                            <h4 class="mb-2">Rp
                                <?php echo e(number_format(Auth::user()->role == 'Admin' ? $prospectAdmin : $prospect, 2, ',', '.')); ?>

                            </h4>
                            <span
                                class="badge rounded-pill bg-label-warning"><?php echo e((Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '80')->count()); ?></span>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="mdi mdi-laptop mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm kpi-tile" style="border-left: 4px solid var(--bs-success);">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-muted">Purchase Order</p>
                            <h4 class="mb-2">Rp
                                <?php echo e(number_format(Auth::user()->role == 'Admin' ? $poAdmin : $po, 2, ',', '.')); ?>

                            </h4>
                            <span
                                class="badge rounded-pill bg-label-success"><?php echo e((Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '100')->count()); ?></span>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm kpi-tile" style="border-left: 4px solid var(--bs-danger);">
                    <div class="card-body d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-2 text-muted">Loss Order</p>
                            <h4 class="mb-2">Rp
                                <?php echo e(number_format(Auth::user()->role == 'Admin' ? $lossAdmin : $loss, 2, ',', '.')); ?>

                            </h4>
                            <span
                                class="badge rounded-pill bg-label-danger"><?php echo e((Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '0')->count()); ?></span>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-danger">
                                <i class="mdi mdi-currency-usd mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(Auth::user()->role != 'Sales'): ?>
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body px-4 py-4">

                    <!-- Header -->
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="header-content">
                            <h5 class="fw-semibold mb-1">Monthly Leads Distribution</h5>
                            <p class="text-muted">Akumulasi leads bulan berjalan · detail per minggu via dropdown</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Week <?php echo e($selectedWeekNum); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php $__currentLoopData = $availableWeeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a class="dropdown-item <?php echo e($week['week'] == $selectedWeekNum ? 'active' : ''); ?>"
                                           href="<?php echo e(route('prospect.index', ['week' => $week['week']])); ?>">
                                            <?php echo e($week['label']); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>


                    <div class="row g-4 row-cols-1 row-cols-sm-2 row-cols-lg-5">

                        <?php $__currentLoopData = $salesLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sales): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $count = $sales->monthly_leads;
                                $weekCount = $sales->weekly_leads;

                                if ($count <= 20) {
                                    $color = 'success';
                                } elseif ($count <= 40) {
                                    $color = 'warning';
                                } else {
                                    $color = 'danger';
                                }
                            ?>

                            <?php
                                $workloadLabel = $count <= 20 ? 'Aman' : ($count <= 40 ? 'Waspada' : 'Tinggi');
                            ?>

                            <div class="col">
                                <div
                                    class="p-3 rounded-3 border h-100 transition-hover sales-lead-card position-relative"
                                    data-sales-id="<?php echo e($sales->id); ?>" data-sales-name="<?php echo e($sales->name); ?>"
                                    style="cursor: pointer; border-left: 4px solid var(--bs-<?php echo e($color); ?>);">

                                    <span
                                        class="badge rounded-pill bg-label-<?php echo e($color); ?> position-absolute top-0 end-0 m-2"
                                        style="font-size:0.65rem;">
                                        <?php echo e($workloadLabel); ?>

                                    </span>

                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo e($sales->name); ?>" style="cursor: default;">

                                            <?php if($sales->image): ?>
                                                <img src="<?php echo e(url('') . '/' . $sales->image); ?>" class="rounded-circle"
                                                    width="46" height="46" style="object-fit:cover;"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="rounded-circle bg-label-primary align-items-center justify-content-center"
                                                    style="width:46px;height:46px;display:none;">
                                                    <span class="fw-bold text-primary">
                                                        <?php echo e(strtoupper(substr($sales->name, 0, 1))); ?>

                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center"
                                                    style="width:46px;height:46px;">
                                                    <span class="fw-bold text-primary">
                                                        <?php echo e(strtoupper(substr($sales->name, 0, 1))); ?>

                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                        <div>
                                            <p class="mb-1 text-dark medium fw-medium">
                                                <?php echo e($sales->name); ?>

                                            </p>

                                            <h4 class="mb-0 fw-bold text-<?php echo e($color); ?>">
                                                <?php echo e($count); ?>

                                                <span class="fs-6 fw-normal text-muted">Leads/Bulan</span>
                                            </h4>

                                            <p class="mb-0 text-muted" style="font-size:0.78rem;">
                                                Week <?php echo e($selectedWeekNum); ?>: <?php echo e($weekCount); ?> leads
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="modal animate__animated animate__fadeIn" id="salesLeadsModal" tabindex="-1" style="display: none;"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="max-width: 90%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salesLeadsModalTitle">Prospect List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Company</th>
                                        <th>Category</th>
                                        <th>Kebutuhan</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody id="salesLeadsModalBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-end mb-3 gap-2 flex-wrap">
            <?php if(Auth::user()->role == 'Admin'): ?>
                <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Sales:</label>
                <select class="form-select form-select-sm" id="prospect-sales-filter" style="max-width:220px;">
                    <option value="">Semua Sales</option>
                    <?php $__currentLoopData = $salesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>
            <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Tahun:</label>
            <select class="form-select form-select-sm" id="prospect-year-filter" style="max-width:150px;">
                <option value="">Semua Tahun</option>
                <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($year); ?>" <?php echo e($year == now()->year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <script>
            // Must run before the page-script datatable init below so the first ajax call already carries the default year.
            window.prospectSalesFilter = '';
            window.prospectYearFilter = '<?php echo e(now()->year); ?>';
        </script>

        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table
                    class="datatable<?php echo e(Auth::user()->role == 'Admin' ? '-prospect-admin' : '-prospect'); ?> table table-bordered">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Category</th>
                            <th>Kebutuhan</th>
                            <th class="text-center">Value</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Info</th>
                            <th class="text-center">Sales</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    <?php elseif(Auth::user()->role == 'Sales'): ?>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-sales table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Prospect</th>
                            <th>Date</th>
                            <th>Support</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-prospect-fu-sales table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Prospect</th>
                            <th>Date</th>
                            <th>Support</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <?php $__currentLoopData = $prospects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prospect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('components.modal.prospect.confirm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    </div>
    <?php echo $__env->make('components.modal.client.support.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .transition-hover {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .transition-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
        }

        .kpi-tile {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .kpi-tile:hover {
            transform: translateY(-2px);
        }

        table.datatable-prospect-admin td,
        table.datatable-prospect-admin th,
        table.datatable-prospect td,
        table.datatable-prospect th {
            font-size: 14px;
            vertical-align: middle;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-prospect-support.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-prospect-support-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-prospect-support-sales.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-prospect-support-fu-sales.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            $('#selectArea').select2({
                placeholder: 'Area',
                dropdownParent: $('#createProspect'),
                width: '100%',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() { return 'Ketik minimal 2 karakter...'; },
                    searching: function() { return 'Mencari...'; },
                    noResults: function() { return 'Kota/Kabupaten tidak ditemukan'; }
                },
                ajax: {
                    url: '<?php echo e(route("kota.search")); ?>',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data }; },
                    cache: true
                }
            });

            $('#selectArea').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Masukkan Kota/Kabupaten');
            });

            function toggleDomainField() {
                var isWebsite = $('#selectSource').val() === 'Website';
                $('#domainWrapper').toggle(isWebsite);
                if (!isWebsite) {
                    $('#domainInput').val('');
                }
            }
            toggleDomainField();
            $('#selectSource').on('change', toggleDomainField);
        });

        $('#prospect-sales-filter').on('change', function() {
            window.prospectSalesFilter = $(this).val();
            $('.datatable-prospect-admin').DataTable().ajax.reload();
        });

        $('#prospect-year-filter').on('change', function() {
            window.prospectYearFilter = $(this).val();
            if ($.fn.DataTable.isDataTable('.datatable-prospect-admin')) {
                $('.datatable-prospect-admin').DataTable().ajax.reload();
            }
            if ($.fn.DataTable.isDataTable('.datatable-prospect')) {
                $('.datatable-prospect').DataTable().ajax.reload();
            }
        });

        $(document).on('click', '.sales-lead-card', function() {
            var salesId = $(this).data('sales-id');
            var salesName = $(this).data('sales-name');
            var modalEl = document.getElementById('salesLeadsModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            var statusLabel = {
                20: { title: 'Send WA / Email', class: 'bg-label-secondary' },
                30: { title: 'Inquiry Accepted', class: 'bg-label-dark' },
                40: { title: 'Progress Follow Up', class: 'bg-label-info' },
                60: { title: 'Negotiation / Revisi', class: 'bg-label-primary' },
                80: { title: 'Hot Prospect', class: 'bg-label-warning' },
                100: { title: 'Done PO', class: 'bg-label-success' },
                0: { title: 'Loss', class: 'bg-label-danger' },
            };

            $('#salesLeadsModalTitle').text('Prospect Bulan Ini - ' + salesName);
            $('#salesLeadsModalBody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
            modal.show();

            $.ajax({
                url: '<?php echo e(url('prospect/monthly-leads')); ?>/' + salesId,
                type: 'GET',
                success: function(response) {
                    var rows = '';
                    if (response.data && response.data.length > 0) {
                        $.each(response.data, function(i, item) {
                            var status = statusLabel[item.status];
                            var statusHtml = status ?
                                '<span class="badge rounded-pill ' + status.class + '">' + status.title + '</span>' :
                                '-';
                            var value = item.nett ? 'Rp ' + Number(item.nett).toLocaleString('id-ID') : '-';
                            rows += '<tr>' +
                                '<td>' + (item.company ?? '-') + '</td>' +
                                '<td>' + (item.category ?? '-') + '</td>' +
                                '<td>' + (item.kebutuhan ?? '-') + '</td>' +
                                '<td>' + (item.date ?? '-') + '</td>' +
                                '<td>' + statusHtml + '</td>' +
                                '<td>' + value + '</td>' +
                                '</tr>';
                        });
                    } else {
                        rows = '<tr><td colspan="6" class="text-center">Belum ada prospect bulan ini</td></tr>';
                    }
                    $('#salesLeadsModalBody').html(rows);
                },
                error: function() {
                    $('#salesLeadsModalBody').html('<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>');
                }
            });
        });

        $(document).on('click', '#withQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure With Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, With Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'with_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/prospect/create_quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed With Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '#withoutQuote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure without Quotation?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, without Quotation!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('prospect')); ?>/' + 'without_quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/leads/detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed With Quotation!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/support/prospect/index.blade.php ENDPATH**/ ?>