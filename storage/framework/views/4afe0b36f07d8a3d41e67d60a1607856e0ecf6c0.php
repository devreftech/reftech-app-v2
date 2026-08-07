
<?php $__env->startSection('title', 'Expense'); ?>
<?php $__env->startSection('content'); ?>
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework" action="<?php echo e(route('expense.store')); ?>"
        method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-8 col-md-6">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date">
                                <label for="Date">Date</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                    id="no-voucher-input" name="no_invoice" value="<?php echo e(old('no_invoice')); ?>">
                                <label for="no-voucher-input">No Invoice</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put No Cheque Here ...."
                                    id="no-cheque-input" name="no_cheque" value="<?php echo e(old('no_cheque')); ?>">
                                <label for="no-cheque-input">No Cheque</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Put Memo Here ...." id="memo-input"
                                    name="detail" value="<?php echo e(old('detail')); ?>">
                                <label for="detail-input">Memo</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-6 col-12 mb-md-0">
                                        <label for="account" class="mb-2">Account</label>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select id="account-1" class="select2 form-select invoice-item-account"
                                                data-allow-clear="true" name="account[]" data-id="1">
                                                <option> ---- Choose Account Here ---- </option>
                                                <?php $__currentLoopData = $account; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accounts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($accounts->id); ?>"
                                                        data-memo="<?php echo e($accounts->category); ?>">
                                                        <?php echo e($accounts->code); ?> - <?php echo e($accounts->name); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Memo</p>
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input type="text" class="form-control invoice-item-memo-label"
                                                placeholder="Choose Account first" id="memo-label-1"
                                                value="<?php echo e(old('memo[]', 'Choose Account first')); ?>" disabled>
                                            <input type="text" class="form-control invoice-item-memo"
                                                placeholder="Choose Account first" name="memo[]" id="memo-1"
                                                value="<?php echo e(old('memo[]', 'Choose Account first')); ?>" hidden>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <p class="mb-2 repeater-title">Amount</p>
                                        <div class="input-group input-group-merge mb-3" data-amount="1">
                                            <span class="input-group-text">Rp. </span>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control invoice-item-amount-label"
                                                    id="amountLabel-1" data-id="1" name="harga"
                                                    placeholder="Put amount Here" data-type="currency" min="0"
                                                    pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                    @blur="focused = false" value="<?php echo e(old('amount[]')); ?>">
                                            </div>
                                            <input class="form-control invoice-item-amount" type="number" name="amount[]"
                                                id="amount-1" value="<?php echo e(old('amount[]')); ?>" hidden>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                    <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                        data-repeater-delete=""></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8 mb-2">
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light btn-add"
                                data-repeater-create="">
                                <i class="mdi mdi-plus me-1"></i> Add Item
                            </button>
                        </div>
                        <div class="col-4 mb-2">
                            <div class="input-group input-group-merge mb-3" data-amount="1">
                                <span class="input-group-text">Rp. </span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-total-label" name="harga"
                                        placeholder="Total Here" id="total-label"value="<?php echo e(old('total[]')); ?>" disabled>
                                    <input class="form-control invoice-item-total" type="number" name="total"
                                        id="total" value="<?php echo e(old('total[]')); ?>" hidden>
                                    <label for="total">Total</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="fs-5 fw-medium mt-2 p-2 invoice-item-say-total w-100"
                        style="background-color: rgb(248, 248, 248); width:70%;"> Say
                        amount: # Rupiah</p>
                    <div class="float-end">
                        <a href="<?php echo e(route('quotation.index')); ?>" type="button"
                            class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/repeater/jquery-repeater-invoice.js"></script>
    
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/includes/repeater/repeater-invoice-expense.js"></script>
    
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(() => {
            function capitalizeWords(str) {
                return str.replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
            }

            function terbilang(n) {
                const angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan",
                    "sepuluh", "sebelas"
                ];

                n = parseInt(n);

                if (n < 12) return angka[n];
                if (n < 20) return terbilang(n - 10) + " belas";
                if (n < 100) return terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
                if (n < 200) return "seratus " + terbilang(n - 100);
                if (n < 1000) return terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
                if (n < 2000) return "seribu " + terbilang(n - 1000);
                if (n < 1000000) return terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
                if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
                if (n < 1000000000000) return terbilang(Math.floor(n / 1000000000)) + " miliar " + terbilang(n %
                    1000000000);
                if (n < 1000000000000000) return terbilang(Math.floor(n / 1000000000000)) + " triliun " + terbilang(
                    n % 1000000000000);

                return "";
            }
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            const numberFormatter = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".invoice-item-bank").on('change', function() {
                var saldo = $('option:selected', this).data('saldo');
                console.log(saldo);

                $('.invoice-item-saldo-label').val(numberFormatter.format(saldo));
            });
            $(".invoice-item-amount-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(nomorInt);
                $(`#amount-${id}`).val(nomorInt);
            });
            $('.invoice-item-amount-label').on('keyup change click', function() {

                var total = 0;

                $('.invoice-item-amount').each(function() {
                    total += parseInt($(this).val()) || 0;
                });

                $('#total-label').val(numberFormatter.format(total));
                $('#total').val(total);
                let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                if (hasilTerbilang === "") hasilTerbilang = "-";

                $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
            });

            function initializeSelect2Account() {
                $('.invoice-item-account').select2({
                    placeholder: ' ---- Choose Account Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }
            // Initialize Bootstrap tooltips using jQuery
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Panggil fungsi inisialisasi saat halaman dimuat
                initializeSelect2Account();

                // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                $(document).on('repeater:added', function() {
                    initializeSelect2Account();
                });
            });
            $('.invoice-item-account').on('change', function() {
                var id = $(this).data('id');
                var memo = $('option:selected', this).data('memo');
                console.log(memo);

                $(`#memo-label-${id}`).val(memo);
                $(`#memo-${id}`).val(memo);
            });

            $('.btn-add').on('click', () => {
                initializeSelect2Account();

                $('.invoice-item-account').on('change', function() {
                    var id = $(this).data('id');
                    var memo = $('option:selected', this).data('memo');
                    console.log(memo);

                    $(`#memo-label-${id}`).val(memo);
                    $(`#memo-${id}`).val(memo);
                });
                $(".invoice-item-amount-label").on('keyup', function() {
                    var input = $(this)
                    var id = input.data('id');
                    var input_val = input.val();

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    console.log(nomorInt);
                    $(`#amount-${id}`).val(nomorInt);
                });
                $('.invoice-item-amount-label').on('keyup change click', function() {

                    var total = 0;

                    $('.invoice-item-amount').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });

                    $('#total-label').val(numberFormatter.format(total));
                    $('#total').val(total);
                    let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                    if (hasilTerbilang === "") hasilTerbilang = "-";

                    $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
                });


            });
            $(document).on('click', '.delete-expense', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '<?php echo e(url('expense-acount')); ?>/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '<?php echo e(csrf_token()); ?>'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href =
                                            '/expense-acount';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/expense/form-umum.blade.php ENDPATH**/ ?>