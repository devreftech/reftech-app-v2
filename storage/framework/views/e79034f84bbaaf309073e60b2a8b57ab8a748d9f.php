
<?php $__env->startSection('title', 'Product In Request'); ?>
<?php $__env->startSection('content'); ?>
    <form action="<?php echo e(route(Auth::user()->role == 'Logistic' ? 'purchase-request.store-done-all-logistic' : 'purchase-request.store-done-all', $pending->id)); ?>"
        method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(Auth::user()->role == 'Admin'): ?>
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-lg fw-bold fs-3" id="floatingInputFilled"
                    placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="invoice">
                <label for="floatingInputFilled">No Invoice</label>
                <span class="form-floating-focused"></span>
            </div>
        <?php else: ?>
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-lg fw-bold fs-3" id="floatingInputFilled"
                    placeholder="xxx/xx/xx/xxxx xxxx" aria-describedby="floatingInputFilledHelp" name="no_do">
                <label for="floatingInputFilled">No Delivery Order</label>
                <span class="form-floating-focused"></span>
            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <div class="form-floating form-floating-outline mb-2">
                                <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                    data-allow-clear="true" name="supplier" data-id="1">
                                    <option selected>Pilih Supplier...</option>
                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($supp->id); ?>" data-info="<?php echo e($supp->info); ?>">
                                            <?php echo e($supp->supplier); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="supplier-dropdown">Supplier</label>
                            </div>
                            
                            
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <select class="form-select invoice-item-info" id="info-dropdown" name="info"
                                    aria-label="Default select example" disabled>
                                    <option selected disabled>Pilih supplier dulu...</option>
                                </select>
                                <label for="info-dropdown">Supplier Info</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    
                                    value="<?php echo e(old('date', @$productIn->date ?? now()->format('Y-m-d'))); ?>"
                                    >
                                <?php if(empty($productIn->date)): ?>
                                    <input type="date" name="estimated_date" id=""
                                        value="<?php echo e(now()->format('Y-m-d')); ?>" hidden>
                                <?php endif; ?>
                                <label for="Date">Date Product In</label>
                            </div>
                        </div>
                        <div class="col-6 col-lg-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    
                                    value="<?php echo e(old('date', @$productIn->date ?? now()->format('Y-m-d'))); ?>"
                                     <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>
                                <label for="Date">Date Invoice</label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                                data-bs-target="#createSupplier">
                                + Supplier
                            </button>
                        </div>
                    </div>
                    <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-3" data-repeater-list="group-a">
                            <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                                <div class="d-flex border rounded position-relative pe-0">
                                    <div class="row w-100 p-3">
                                        <div
                                            class="<?php echo e(Auth::user()->role == 'Admin' ? 'col-md-4' : 'col-md-6'); ?> col-12 mb-md-0 mb-3">
                                            <label for="product" class="mb-2">Product</label>
                                            <div class="form-floating form-floating-outline mb-2">
                                                <select id="replacement-dropdown-<?php echo e($key); ?>"
                                                    class="select2 form-select invoice-item-replacement"
                                                    data-allow-clear="true" name="replacement[]" data-id="<?php echo e($key); ?>">
                                                    <option value=""> ---- Choose Commodity || Replacement Here ---- </option>
                                                    <?php $__currentLoopData = $fullRep[$key]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $products): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($products->id); ?>" <?php echo e(count($fullRep[$key]) == 1 ? 'selected' : ''); ?>>
                                                            <?php echo e($products->product->commodity); ?>

                                                            (<?php echo e($products->product->detail_desc); ?>)
                                                            ||
                                                            <?php echo e($products->replacement); ?> -
                                                            <?php echo e($products->product->go == 'Genuine' ? 'G' : 'R'); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <label for="replacement-dropdown">Commodity || Replacement</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">Qty</p>
                                            <input type="number" class="form-control mb-3 invoice-item-qty"
                                                placeholder="Min 1" name="qty[]" id="qty-<?php echo e($key); ?>" data-id="<?php echo e($key); ?>"
                                                min="1" value="<?php echo e($pr->qty); ?>">
                                        </div>
                                        <div class="col-md-1 col-12 mb-md-0 mb-3">
                                            <p class="mb-2 repeater-title">warehouse</p>
                                            <div class="form-floating form-floating-outline mb-4">
                                                <select class="form-select invoice-item-warehouse" id="warehouse-<?php echo e($key); ?>"
                                                    data-id="<?php echo e($key); ?>" aria-label="Default select example"
                                                    name="warehouse[]">
                                                    <option>---Info---</option>
                                                    <option value="BDG" selected>BDG
                                                    </option>
                                                    <option value="BKS">BKS
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if(Auth::user()->role == 'Admin'): ?>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Price</p>
                                                <div class="input-group" data-price="<?php echo e($key); ?>">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-price-label"
                                                        id="price-label" data-id="<?php echo e($key); ?>" min="0"
                                                        placeholder="Put Price Here" data-type="currency"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                        @blur="focused = false" value="<?php echo e(old('price[]')); ?>"
                                                        <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>
                                                    <input class="form-control invoice-item-price" type="number"
                                                        name="price[]" id="price-<?php echo e($key); ?>" value="<?php echo e(old('price[]')); ?>"
                                                        hidden>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-12 mb-md-0 mb-3">
                                                <p class="mb-2 repeater-title">Discount</p>
                                                <div class="input-group" data-disc="<?php echo e($key); ?>">
                                                    <span class="input-group-text">Rp. </span>
                                                    <input type="text" class="form-control invoice-item-disc-label"
                                                        id="disc-label" data-id="<?php echo e($key); ?>" min="0"
                                                        placeholder="Put Discount Here" data-type="currency"
                                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                                        @blur="focused = false" value="<?php echo e(old('disc[]')); ?>"
                                                        <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>
                                                    <input class="form-control invoice-item-disc" type="number"
                                                        name="disc[]" id="disc-<?php echo e($key); ?>" value="<?php echo e(old('disc[]')); ?>"
                                                        hidden>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-12 pe-0">
                                                <p class="mb-2 repeater-title">Amount</p>
                                                <p class="mb-0 amount-label" id="amount-label-<?php echo e($key); ?>" data-id="<?php echo e($key); ?>">
                                                    <?php echo e(old(strval('amount[]'))); ?></p>
                                                <input type="number" class="form-control invoice-item-amount"
                                                    name="amount[]" id="amount-<?php echo e($key); ?>" data-id="<?php echo e($key); ?>" min="0"
                                                    value="<?php echo e(old('amount[]')); ?>" hidden>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                        <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del"
                                            data-repeater-delete=""></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if(Auth::user()->role == 'Admin'): ?>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Subtotal
                                </h5>
                                <div class="input-group" data-subtotal="1">
                                    <span class="input-group-text">Rp. </span>
                                    <p class="form-control invoice-item-subtotal-label h-px-25 mb-0" id="subtotal-label">
                                        Subtotal
                                        Here </p>
                                    <input class="form-control invoice-item-subtotal" type="number" name="subtotal"
                                        id="subtotal" value="<?php echo e(old('subtotal')); ?>" hidden>
                                    <input class="form-control invoice-item-total-no-tax" type="number"
                                        name="total_no_tax" id="totalNoTax" value="<?php echo e(old('total_no_tax')); ?>" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Discount
                                </h5>
                                <div class="input-group" data-total-disc="1">
                                    <span class="input-group-text">Rp. </span>
                                    <p class="form-control invoice-item-total-disc-label h-px-25 mb-0"
                                        id="total-disc-label">
                                        Total Discount Here </p>
                                    <input class="form-control invoice-item-total-disc" type="number" name="total-disc"
                                        id="total-disc" value="<?php echo e(old('total-disc')); ?>" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Shipping
                                </h5>
                                <div class="input-group" data-shipping="1">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" class="form-control invoice-item-shipping-label"
                                        id="shipping-label" data-id="1" min="0"
                                        placeholder="Put shipping Here" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="<?php echo e(old('shipping')); ?>"
                                        <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>
                                    <input class="form-control invoice-item-shipping" type="number" name="shipping"
                                        id="shipping" value="<?php echo e(old('shipping')); ?>" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Tax
                                </h5>
                                <div class="form-floating form-floating-outline mb-4">
                                    <select class="form-select invoice-item-tax" id="tax" name="tax"
                                        aria-label="Default select example"
                                        <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>
                                        <option selected disabled>----- Choose Tax Here -----</option>
                                        <option value="11">11%</option>
                                        <option value="0">0%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Total Price
                                </h5>
                                <div class="input-group" data-total="1">
                                    <span class="input-group-text">Rp. </span>
                                    <p class="form-control invoice-item-total-label h-px-25 mb-0" id="total-label"> Total
                                        Price Here </p>
                                    <input class="form-control invoice-item-total" type="number" name="total"
                                        id="total" value="<?php echo e(old('total')); ?>" hidden>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4 col-12">
                                <h5 class="my-2">
                                    Note
                                </h5>
                                <textarea class="form-control h-px-100" rows="2" placeholder="Write your note here...." name="note"
                                    <?php echo e(Auth::user()->role == 'Logistic' ? 'Disabled' : ''); ?>>-</textarea>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="float-end">
                        <a href="<?php echo e(route('quotation.index')); ?>" type="button"
                            class="btn btn-lg btn-outline-secondary w-px-120">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary w-px-120">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php echo $__env->make('components.modal.warehouse.supplier.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/repeater/jquery-repeater-invoice.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/repeater/repeater-invoice-productIn.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-add.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(() => {
            var rep = 1;
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            function initializeSelect2Replacement() {
                $(`#replacement-dropdown-${rep}`).select2({
                    placeholder: ' ---- Choose Commodity || Replacement Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }

            $(".invoice-item-shipping-label").on('keyup', function() {
                var input = $(this)
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
                // console.log(id);
                $(`#shipping`).val(nomorInt);
            });
            $('#supplier-dropdown').on('change', function() {
                let info = $(this).find(':selected').data('info');

                $('#info-dropdown').empty().append(`
                    <option value="${info}" selected>${info}</option>
                `);
            });

            $(".invoice-item-price-label").on('keyup', function() {
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
                // console.log(id);
                $(`#price-${id}`).val(nomorInt);
            })
            $(".invoice-item-disc-label").on('keyup', function() {
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
                // console.log(id);
                $(`#disc-${id}`).val(nomorInt);
            })

            $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc-label').on('keyup change click',
                function(
                    ev) {
                    var id = $(this).data('id');
                    var sTotal = 0,
                        totalDisc = 0,
                        row = 0,
                        rowD = 0;
                    var amount = 0,
                        valDiscount = $(`#disc-${id}`).val(),
                        valHarga = $(`#price-${id}`).val(),
                        disc = Number(valDiscount),
                        harga = Number(valHarga);

                    amount = harga * $(`#qty-${id}`).val() - disc;
                    $(`#amount-${id}`).val(amount);
                    $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                    $('.amount-label').each(() => {
                        sTotal += parseInt($(`#amount-${row}`).val())
                        row++;
                    });
                    $('.invoice-item-disc-label').each(() => {
                        let val = Number($(`#disc-${rowD}`).val());
                        rowD++;
                        
                        totalDisc += isNaN(val) ? 0 : val;
                    });
                    console.log(sTotal);
                    console.log('discount : ' + totalDisc);
                    $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                    $('#total-disc-label').html(`${formatter.format(totalDisc)}`);
                    $('#subtotal').val(sTotal);
                    $('#total-disc').val(totalDisc);
                });

            // Logic Harga Total
            $('#shipping-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-tax, .invoice-item-disc-label')
                .on('keyup change',
                    () => {
                        var row = 0,
                            total = 0,
                            hTotal = 0,
                            totalNoTax = 0,
                            tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val()),
                            subtotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($('#subtotal').val()),
                            shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($('#shipping').val());
                        hTotal = parseInt(subtotal + (subtotal * tax / 100) + shipping);
                        totalNoTax = parseInt(subtotal + shipping);
                        $('#total-label').html(`${formatter.format(hTotal)}`);
                        $('#total').val(hTotal);
                        $('#totalNoTax').val(totalNoTax);
                        console.log('Harga total: ' + hTotal);
                    });
            // Logic Subtotal dan Amount Setelah Tambah Product
            $('.btn-add').on('click', () => {
                $(".invoice-item-price-label").on('keyup', function() {
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
                    // console.log(id);
                    $(`#price-${id}`).val(nomorInt);
                });


                $(".invoice-item-disc-label").on('keyup', function() {
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
                    // console.log(id);
                    $(`#disc-${id}`).val(nomorInt);
                })

                $('.invoice-item-price-label, .invoice-item-qty, .invoice-item-disc-label').on(
                    'keyup change click',
                    function(
                        ev) {
                        var id = $(this).data('id');
                        var sTotal = 0,
                            totalDisc = 0,
                            row = 0,
                            rowD = 0;
                        var amount = 0,
                            valDiscount = $(`#disc-${id}`).val(),
                            valHarga = $(`#price-${id}`).val(),
                            disc = Number(valDiscount),
                            harga = Number(valHarga);

                        amount = harga * $(`#qty-${id}`).val() - disc;
                        $(`#amount-${id}`).val(amount);
                        $(`#amount-label-${id}`).html(`${formatter.format(amount)}`);
                        $('.amount-label').each(() => {
                            sTotal += parseInt($(`#amount-${row}`).val())
                            row++;
                        });
                        $('.invoice-item-disc-label').each(() => {
                            let val = Number($(`#disc-${rowD}`).val());
                            totalDisc += isNaN(val) ? 0 : val;
                            rowD++;
                        });
                        $('#subtotal-label').html(`${formatter.format(sTotal)}`);
                        $('#total-disc-label').html(`${formatter.format(totalDisc)}`);
                        $('#subtotal').val(sTotal);
                        $('#total-disc').val(totalDisc);
                    });
                // Logic Harga Total
                $('#shipping-label, .invoice-item-price-label, .invoice-item-qty, .invoice-item-tax, .invoice-item-disc-label')
                    .on('keyup change',
                        () => {
                            var row = 0,
                                total = 0,
                                hTotal = 0,
                                totalNoTax = 0,
                                tax = isNaN(parseInt($('#tax').val())) ? 0 : parseInt($('#tax').val()),
                                subtotal = isNaN(parseInt($('#subtotal').val())) ? 0 : parseInt($(
                                    '#subtotal').val()),
                                shipping = isNaN(parseInt($('#shipping').val())) ? 0 : parseInt($(
                                    '#shipping').val());
                            hTotal = parseInt(subtotal + (subtotal * tax / 100) + shipping);
                            totalNoTax = parseInt(subtotal + shipping);
                            $('#total-label').html(`${formatter.format(hTotal)}`);
                            $('#total').val(hTotal);
                            $('#totalNoTax').val(totalNoTax);
                            console.log('Harga total: ' + hTotal);
                        });
                rep++;
                initializeSelect2Replacement();
            })
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/purchase/form.blade.php ENDPATH**/ ?>