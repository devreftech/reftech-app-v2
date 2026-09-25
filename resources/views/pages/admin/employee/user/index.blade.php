@extends('layouts.sales.app')
@section('title', 'Data User')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        User
    </h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-user table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Employee</th>
                        <th>NIP</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Position</th>
                        <th>Area</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Entry Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('components.modal.user.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle Password Visibility
            $(document).on('click', '.toggle-password-visibility', function() {
                var targetSelector = $(this).data('target');
                var $target = targetSelector ? $(targetSelector) : $(this).closest('.input-group').find('input[type="password"], input[type="text"]');
                var $icon = $(this).find('i');

                if ($target.attr('type') === 'password') {
                    $target.attr('type', 'text');
                    $icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
                } else {
                    $target.attr('type', 'password');
                    $icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
                }
            });

            // Filter phone to numbers only
            $(document).on('input', '.phone-number-input, #phone', function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });

            // Format Rupiah currency
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $(document).on('keyup click change input', '.total-label', function() {
                var input = $(this);
                var input_val = input.val();
                input_val = formatNumber(input_val);
                input.val(input_val);

                var nomorInt = parseFloat(input_val.replace(/[.,]/g, '')) || 0;
                input.closest('.input-group').find('.total').val(nomorInt);
            });

            // Role Configuration Settings
            var roleConfig = {
                'Sales': {
                    isSales: true,
                    position: 'Sales Engineer',
                    area: '',
                    areaPlaceholder: 'Contoh: Surabaya, Jawa Timur, Jakarta',
                    codePlaceholder: 'Contoh: RZA (Inisial Sales)'
                },
                'Admin': {
                    isSales: false,
                    position: 'Administrator',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: ADM'
                },
                'Developer': {
                    isSales: false,
                    position: 'Fullstack Developer',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: DEV'
                },
                'Project Manager': {
                    isSales: false,
                    position: 'Project Manager',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office / Site',
                    codePlaceholder: 'Contoh: PM'
                },
                'Accounting': {
                    isSales: false,
                    position: 'Accounting Staff',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: ACT'
                },
                'Finance Manager': {
                    isSales: false,
                    position: 'Finance Manager',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: FM'
                },
                'Logistic': {
                    isSales: false,
                    position: 'Logistic Staff',
                    area: 'Warehouse / Head Office',
                    areaPlaceholder: 'Contoh: Warehouse / Head Office',
                    codePlaceholder: 'Contoh: LOG'
                },
                'Technician': {
                    isSales: false,
                    position: 'Field Technician',
                    area: 'Workshop / Field',
                    areaPlaceholder: 'Contoh: Workshop / Field',
                    codePlaceholder: 'Contoh: TCH'
                },
                'Coordinator': {
                    isSales: false,
                    position: 'Service Coordinator',
                    area: 'Head Office / Workshop',
                    areaPlaceholder: 'Contoh: Head Office / Workshop',
                    codePlaceholder: 'Contoh: CRD'
                },
                'ServiceM': {
                    isSales: false,
                    position: 'Service Admin',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: SVA'
                },
                'Supervisor': {
                    isSales: false,
                    position: 'Site Supervisor',
                    area: 'Operational Site',
                    areaPlaceholder: 'Contoh: Operational Site',
                    codePlaceholder: 'Contoh: SPV'
                },
                'Support': {
                    isSales: false,
                    position: 'Technical Support',
                    area: 'Head Office',
                    areaPlaceholder: 'Contoh: Head Office',
                    codePlaceholder: 'Contoh: SUP'
                },
                'Client': {
                    isSales: false,
                    position: 'Client PIC',
                    area: 'Client Company',
                    areaPlaceholder: 'Contoh: Client Company',
                    codePlaceholder: 'Contoh: CLI'
                },
                'Client Vendor': {
                    isSales: false,
                    position: 'Client Vendor',
                    area: 'Vendor / Partner',
                    areaPlaceholder: 'Contoh: Vendor / Partner',
                    codePlaceholder: 'Contoh: VND'
                }
            };

            function adaptFormToRole($selectEl, isUserAction = false) {
                var role = $selectEl.val();
                var $modal = $selectEl.closest('.modal');
                var isCreateModal = $modal.attr('id') === 'createUsers';
                var config = roleConfig[role] || {
                    isSales: false,
                    position: role,
                    area: 'Head Office',
                    areaPlaceholder: 'Area Kerja',
                    codePlaceholder: 'Kode Karyawan'
                };

                var $targetCard = $modal.find('[id^="inputTarget"]');
                var $noticeNonSales = $modal.find('[id^="roleNoticeNonSales"]');
                var $noticeClientVendor = $modal.find('[id^="roleNoticeClientVendor"]');
                var $posInput = $modal.find('.user-position-input');
                var $areaInput = $modal.find('.user-area-input');
                var $codeInput = $modal.find('.user-code-input');
                var $nameInput = $modal.find('.user-name-input');
                var $vendorDisableFields = $modal.find('.vendor-disable-field');

                if (config.isSales) {
                    $targetCard.stop(true, true).slideDown(280);
                    $targetCard.find('input').prop('disabled', false);
                    $noticeNonSales.stop(true, true).slideUp(180);
                    $noticeClientVendor.stop(true, true).slideUp(180);
                } else if (role === 'Client Vendor') {
                    $targetCard.stop(true, true).slideUp(220);
                    $targetCard.find('input').prop('disabled', true);
                    $noticeNonSales.stop(true, true).slideUp(180);
                    $noticeClientVendor.stop(true, true).slideDown(220);
                } else {
                    $targetCard.stop(true, true).slideUp(220);
                    $targetCard.find('input').prop('disabled', true);
                    $noticeNonSales.find('.notice-role-name').text(role);
                    $noticeNonSales.stop(true, true).slideDown(220);
                    $noticeClientVendor.stop(true, true).slideUp(180);
                }

                // Handle Client Vendor disabled inputs for Data Profil & Penempatan
                if (role === 'Client Vendor') {
                    // Disable inputs in vendor-disable-field
                    $vendorDisableFields.each(function() {
                        var $container = $(this);
                        $container.css({
                            'opacity': '0.55',
                            'pointer-events': 'none',
                            'filter': 'grayscale(40%)'
                        });
                        $container.find('input, select, textarea').each(function() {
                            $(this).prop('disabled', true).removeAttr('required');
                        });
                    });

                    // Pastikan Nama Lengkap TETAP AKTIF & REQUIRED
                    $nameInput.prop('disabled', false).attr('required', 'required').closest('.col-md-6').css({
                        'opacity': '1',
                        'pointer-events': 'auto',
                        'filter': 'none'
                    });

                    // Set default dummy values jika di create modal
                    if (isCreateModal) {
                        var $nipInput = $modal.find('[id^="nip-"]');
                        if (!$nipInput.val()) {
                            $nipInput.val('VND-' + Math.floor(100000 + Math.random() * 900000));
                        }
                        var $addrInput = $modal.find('[id^="address-"]');
                        if (!$addrInput.val()) {
                            $addrInput.val('-');
                        }
                        $posInput.val('Client Vendor');
                        $areaInput.val('Vendor / Partner');
                        $codeInput.val('VND');
                    }
                } else {
                    // Re-enable all fields for internal roles
                    $vendorDisableFields.each(function() {
                        var $container = $(this);
                        $container.css({
                            'opacity': '1',
                            'pointer-events': 'auto',
                            'filter': 'none'
                        });
                        $container.find('input, select, textarea').each(function() {
                            $(this).prop('disabled', false);
                            var fieldId = $(this).attr('id') || '';
                            if (fieldId.startsWith('nip-') || fieldId.startsWith('phone-') || fieldId.startsWith('address-') || $(this).hasClass('user-position-input') || $(this).hasClass('user-area-input') || $(this).hasClass('user-code-input')) {
                                $(this).attr('required', 'required');
                            }
                        });
                    });

                    $nameInput.prop('disabled', false).attr('required', 'required');
                }

                // Toggle HR employee registration box based on role
                var $hrBox = $('#hrEmployeeIntegrationBox');
                var $hrCheck = $('#create_employee_check');
                if (role === 'Client' || role === 'Client Vendor') {
                    $hrBox.stop(true, true).slideUp(200);
                    $hrCheck.prop('checked', false);
                } else if (isCreateModal) {
                    $hrBox.stop(true, true).slideDown(200);
                    if (isUserAction && !$hrCheck.data('manually-toggled')) {
                        $hrCheck.prop('checked', true);
                    }
                }

                // Update input placeholders
                $posInput.attr('placeholder', config.position);
                $areaInput.attr('placeholder', config.areaPlaceholder);
                $codeInput.attr('placeholder', config.codePlaceholder);

                // Auto-fill defaults on create modal when user changes role
                if (isCreateModal && isUserAction && role !== 'Client Vendor') {
                    if (!$posInput.data('custom-edited') || !$posInput.val()) {
                        $posInput.val(config.position);
                    }
                    if (!$areaInput.data('custom-edited') || !$areaInput.val()) {
                        $areaInput.val(config.area);
                    }
                }
            }

            // Mark inputs if manually modified by user
            $(document).on('input', '.user-position-input, .user-area-input', function() {
                $(this).data('custom-edited', true);
            });

            $(document).on('change', '#create_employee_check', function() {
                $(this).data('manually-toggled', true);
            });

            // Listen for role select changes
            $(document).on('change', '.user-role-select', function() {
                adaptFormToRole($(this), true);
            });

            // Re-sync form layout whenever modal opens smoothly
            $(document).on('show.bs.modal', '.smooth-user-modal, #createUsers', function() {
                var $roleSelect = $(this).find('.user-role-select');
                adaptFormToRole($roleSelect, false);
            });

            // Initial trigger on document ready
            $('.user-role-select').each(function() {
                adaptFormToRole($(this), false);
            });
        });
    </script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/pages-account-settings-account.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product.js"></script>
    <script src="{{ asset('assets') }}/includes/table-user.js"></script>
@endpush
