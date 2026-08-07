<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendor/css/rtl/core.css" />
    <style>
        .container,
        .container-fluid,
        {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 0;
        width: 100%;
        padding-right: calc(var(--bs-gutter-x) * 0.5);
        padding-left: calc(var(--bs-gutter-x) * 0.5);
        margin-right: auto;
        margin-left: auto;
        }

        .flex-grow-1 {
            flex-grow: 1 !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .flex-xl-row {
            flex-direction: row !important;
        }

        .flex-md-column {
            flex-direction: column !important;
        }

        .flex-sm-row {
            flex-direction: row !important;
        }

        .flex-column,
        {
        min-height: 1px;
        }

        .pb-3 {
            padding-bottom: 1rem !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        h6,
        .h6,
        h5,
        .h5,
        h4,
        .h4,
        h3,
        .h3,
        h2,
        .h2,
        h1,
        .h1 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-weight: 500;
            line-height: 1.1;
            color: #636578;
        }

        .fw-bolder {
            font-weight: 900 !important;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .text-muted {
            --bs-text-opacity: 1;
            color: #bbbcc4 !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-0.5 * var(--bs-gutter-x));
            margin-left: calc(-0.5 * var(--bs-gutter-x));
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * 0.5);
            padding-left: calc(var(--bs-gutter-x) * 0.5);
            margin-top: var(--bs-gutter-y);
        }

    </style>
</head>

<body>
    <div class="invoice-print p-4">
        <div class="container-fluid flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                <div class="pb-3">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span style="width: 50px">
                            <img src="{{ asset('assets') }}/img/favicon/logo-reftech1.png" alt=""
                                srcset="">
                        </span>
                        <span class="h4 mb-0 fw-bold">PT REFTECH JAYA OPTIMA</span>
                    </div>
                    <p class="mb-1">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-0"><i class="mdi mdi-whatsapp scaleX-n1-rtl me-1"></i>+62 813-2058-3277
                    </p>
                    <p>
                        <i class="mdi mdi-whatsapp scaleX-n1-rtl me-1"></i>022 54417653
                    </p>
                </div>
                <div>
                    <h5 class="fw-bold">QUOTATION</h5>
                    <div>
                        <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                    </div>
                    <div class="mt-1">
                        <span class="text-muted">{{ $quote->status }}</span>
                    </div>
                    <div class="mt-1">
                        <span>Date Estimated:</span>
                        <span>{{ \Carbon\Carbon::parse($quote->estimated_date)->toFormattedDateString() }}</span>
                    </div>
                    <div class="mt-1">
                        <span>Date Expired:</span>
                        <span>{{ \Carbon\Carbon::parse($quote->expired_date)->toFormattedDateString() }}</span>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-4">
                <div class="row">
                    <div class="col-6 my-3">
                        <h6 class="pb-2 fw-semibold fs-4">Quote To:</h6>
                    </div>
                    <div class="col-6 my-3">
                    </div>
                </div>
                <div class="row">
                    <div class="col-2 fw-medium">
                        <p class="mb-1">Name PIC</p>
                        <p class="mb-1">Company </p>
                        <p class="mb-1">Phone </p>
                        <p class="mb-1">Email </p>
                        <p class="mb-1">Address</p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1">: {{ $quote->client->pic->name_pic }}</p>
                        <p class="mb-1">: {{ $quote->client->company }}</p>
                        <p class="mb-1">: {{ $quote->client->phone }}</p>
                        <p class="mb-1">: {{ $quote->client->email }}</p>
                        <p class="mb-1">: {{ $quote->client->address }}</p>
                    </div>
                    <div class="col-2 fw-medium">
                        <p class="mb-1">Sales</p>
                        <p class="mb-1">Phone Sales </p>
                    </div>
                    <div class="col-4">
                        <p class="mb-1">: {{ $quote->sales->name }}</p>
                        <p class="mb-1">: {{ $quote->sales->phone }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <table class="table m-0">
                    <thead class="table-light border-top">
                        <tr>
                            <th class="no">No.</th>
                            <th class="item">Item</th>
                            <th class="desc">Description</th>
                            <th class="price">Price</th>
                            <th class="qty">Qty</th>
                            <th class="disc">Disc</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($dquote as $product)
                            @php
                                $no++;
                            @endphp
                            <tr>
                                <td>{{ $no }}</td>
                                <td class="text-nowrap">{{ $product->product }}</td>
                                <td class="text-nowrap">{{ $product->detail_product }}</td>
                                <td>RP {{ number_format($product->price, 0, '', '.') }}</td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ $product->disc }}%</td>
                                <td>RP {{ number_format($product->amount, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="align-top px-4 py-5">
                                <span>Thanks for your business</span>
                            </td>
                            <td class="text-end px-4 py-5">
                                <p class="mb-2">Subtotal:</p>
                                <p class="mb-2">Tax:</p>
                                <p class="mb-2">Shipping Cost:</p>
                                <p class="mb-0">Total:</p>
                            </td>
                            <td colspan="2" class="px-4 py-5">
                                <p class="fw-semibold mb-2 text-end">RP
                                    {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                <p class="fw-semibold mb-2 text-end">{{ $quote->tax }}%</p>
                                <p class="fw-semibold mb-2 text-end">RP
                                    {{ number_format($quote->shipping, 0, '', '.') }}</p>
                                <p class="fw-semibold mb-0 text-end">RP
                                    {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-0">
                <h5 class="my-4">Term & Condition</h5>
                <div class="row">
                    <div class="col-3 fw-medium">
                        <p class="mb-1">Validity Of Quotation</p>
                        <p class="mb-1">Price </p>
                        <p class="mb-1">Delivery Process </p>
                        <p class="mb-1">Payment </p>
                    </div>
                    <div class="col">
                        <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                        <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
