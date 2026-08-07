@extends('layouts.sales.app')
@section('title', 'Detail Quotation')
@section('content')
    @php
        if ($quote->flag == 'Reftech') {
            $bgColor = 'rgb(224, 248, 248)';
        } else {
            $bgColor = 'rgb(255, 232, 210)';
        }
    @endphp
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3">
                <div class="card-body">
                    @if ($quote->flag == 'Reftech')
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                        {{ '   ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                    </p>
                                    <p class="mb-1">
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">QUOTATION</h3>
                                <div>
                                    <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                                </div>
                                @if ($quote->num_rev >= 1)
                                    <div class="mt-1">
                                        <span class="fw-bolder py-1 px-2"
                                            style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                            {{ $quote->num_rev }}</span>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                        {{ '   ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">QUOTATION</h3>
                                <div>
                                    <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                                </div>
                                @if ($quote->num_rev >= 1)
                                    <div class="mt-1">
                                        <span class="fw-bolder py-1 px-2"
                                            style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                            {{ $quote->num_rev }}</span>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-4 mb-3">Quote To:</h6>
                        </div>
                        <div class="col-6 mb-2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Company </p>
                            <p class="mb-1">Name PIC</p>
                            <p class="mb-1">Phone </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                            <p class="mb-1">: {{ $quote->pic->name_pic }}</p>
                            <p class="mb-1">: {{ $quote->pic->phone_pic }}</p>
                        </div>
                        <div class="col-3 fw-medium text-end">
                            <p class="mb-1">Sales :</p>
                            <p class="mb-1">No PR :</p>
                            <p class="mb-1">Email :</p>
                        </div>
                        <div class="col-3 text-end">
                            <p class="mb-1">
                                {{ $quote->flag == 'Reftech' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                            </p>
                            <p class="mb-1"> {{ $quote->no_pr ?? '-' }}</p>
                            <p class="mb-1"> {{ $quote->pic->client->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Discount</th>
                                <th>Amount</th>
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
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            @if ($product->id_equivalent == '0')
                                                -
                                            @else
                                                {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                            @endif
                                        </p>
                                        <pre class="mb-0"
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                    </td>
                                    <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                                    <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                    <td class="align-top">{{ $product->disc }}%</td>
                                    <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="align-top px-4 py-5">
                                    <span>Thanks for your business</span>
                                </td>
                                <td colspan="2" class="text-end px-4 py-5">
                                    <p class="mb-2">Subtotal:</p>
                                    <p class="mb-2">Discount Quote:</p>
                                    <p class="mb-2">Subtotal After Discount:</p>
                                    <p class="mb-2">Tax {{ $quote->tax == '11' ? '(11%)' : '' }}:</p>
                                    <p class="mb-2">Shipping Cost:</p>
                                    <p class="mb-0">Total:</p>
                                </td>
                                <td colspan="2" class="px-4 py-5">
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($quote->diskon, 0, '', '.') }}
                                    </p>
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($afterDisc, 0, '', '.') }}
                                    </p>
                                    <p class="fw-semibold mb-2 text-end">
                                        {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($quote->shipping, 0, '', '.') }}</p>
                                    <p class="fw-semibold mb-0 text-end">RP
                                        {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body mt-2">
                    <h5 class="my-4">Term & Condition</h5>
                    <div class="row">
                        <div class="col-3 fw-medium">
                            <p class="mb-1">Validity Of Quotation</p>
                            <p class="mb-1">Price </p>
                            <p class="mb-1">Delivery Process </p>
                            <p class="mb-1">Payment </p>
                            <p class="mb-1">Note </p>
                        </div>
                        <div class="col">
                            <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                            <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                            <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                            <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                            <p class="mb-1">: {{ $quote->termncon[0]->note }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if ($status->count() >= 1)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Activity Timeline</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4" id="viewComment">
                        <ul class="timeline card-timeline mb-0">
                            @foreach ($status as $stats)
                                @php
                                    if ($stats->status == '20') {
                                        $status = 'Send Quotation';
                                        $color = 'secondary';
                                    } elseif ($stats->status == '30') {
                                        $status = 'Inquiry Accepted';
                                        $color = 'dark';
                                    } elseif ($stats->status == '40') {
                                        $status = 'Progress Follow Up';
                                        $color = 'info';
                                    } elseif ($stats->status == '60') {
                                        $status = 'Negotiation / Revisi';
                                        $color = 'primary';
                                    } elseif ($stats->status == '80') {
                                        $status = 'Hot Prospect';
                                        $color = 'warning';
                                    } elseif ($stats->status == '100') {
                                        $status = 'Done PO';
                                        $color = 'success';
                                    } elseif ($stats->status == '0') {
                                        $status = 'Loss';
                                        $color = 'danger';
                                    } else {
                                        $status = 'Quotation Created';
                                        $color = 'secondary';
                                    }
                                @endphp
                                <li class="timeline-item timeline-item-transparent clearfix">
                                    <span class="timeline-point timeline-point-{{ $color }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">{{ $status }}</h6>
                                            <small
                                                class="text-muted">{{ $stats->date->diffInHours(Carbon\Carbon::now()) > 24 ? $stats->date->format('d M y h:i:s') : $stats->date->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-3">
                                            {{ $stats->note }}
                                        </p>
                                        @foreach ($stats->comment as $item)
                                            <div class="d-flex justify-content-between align-items-center px-2 mb-2{{ $item->id_user == Auth::user()->id ? ' rounded bg-label-primary float-end' : '' }}"
                                                style="width : 80%;">
                                                <div class="d-flex align-items-center mb-1">
                                                    <img src="{{ url('') . '/' . $item->user->image }}" alt="ini photo"
                                                        style="width: 50px;" class="mx-2 rounded-pill">
                                                    <p class="mb-0">
                                                        <span class="fw-medium">{{ $item->user->name }}</span>:
                                                        {{ $item->comment }}
                                                    </p>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $item->date->diffInHours(Carbon\Carbon::now()) > 24 ? $item->date->format('d M y h:i:s') : $item->date->diffForHumans() }}</small>
                                            </div>
                                        @endforeach
                                        {{-- <div class="d-flex align-items-center mb-1">
                                        <div class="badge bg-lighter rounded-3">
                                            <img src="../../assets//img/icons/misc/pdf.png" alt="img" width="15"
                                                class="me-2">
                                            <span class="h6 mb-0">invoices.pdf</span>
                                        </div>
                                    </div>  --}}
                                        @php
                                            $lastStat = App\Models\ChangeStatus::where(
                                                'id_quotation',
                                                $quote->primary_id,
                                            )
                                                ->orderByDesc('id')
                                                ->first();
                                        @endphp
                                    </div>
                                </li>
                                @if ($stats->id == $lastStat->id)
                                    <form action="{{ route('add-comment.quotation', $quote->id) }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-floating mt-3">
                                            <input type="text" class="form-control" id="floatingInputFilled"
                                                placeholder="Comment" name="comment"
                                                aria-describedby="floatingInputFilledHelp">
                                            <label for="floatingInputFilled">Comment</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary waves-effect waves-light float-end">Comment</button>
                                    </form>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            @if ($quote->id_sales == Auth::user()->id && $quote->status != 100)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="form-floating form-floating-outline mb-2">
                            <select class="form-select change-primary" name="changePrimary" id="changePrimary"
                                aria-label="Default select example">
                                @foreach ($quotations as $item)
                                    <option data-id="{{ $item->id }}" value="{{ $item->id }}"
                                        {{ $item->is_primary == '1' ? 'Selected' : '' }}>
                                        {{ $item->no_quote }}{{ $item->num_rev >= 1 ? '-REV-' . $item->num_rev : '' }}
                                        {{ $item->level == '0' ? '(Archived)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="changePrimary">Primary Quote</label>
                        </div>
                        <a class="btn btn-outline-primary d-grid w-100 mb-3 waves-effect"
                            href="{{ route('revisi.quotation', @$primQuote->id ?? $lastQuote->id) }}">
                            + Revisi Quotation
                        </a>
                    </div>
                </div>
            @endif

            @if ($quote->level == '1')
                <div class="card mb-3">
                    <div class="card-body">
                        <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                            href="{{ route('print.quotation', $quote->id) }}">
                            Download
                        </a>
                        @if (Auth::user()->role == 'Sales')
                            @if ($quote->status != '100')
                                <button type="button" class="btn btn-secondary d-grid w-100 waves-effect mb-3"
                                    data-bs-toggle="modal" data-bs-target="#changeStatus-{{ $quote->id }}">Change
                                    Status</button>
                                {{-- <button type="button" class="btn btn-twitter d-grid w-100 waves-effect mb-3"
                                    data-bs-toggle="modal" data-bs-target="#addMention"> Mention
                                </button> --}}
                            @endif
                            @if ($quote->status == '100')
                                <a href="#" class="btn btn-secondary d-grid w-100 waves-effect cancel-po mb-3"
                                    data-id="{{ $quote->id }}">Cancel
                                    PO</a>
                            @endif
                        @endif
                        {{-- <button type="button" class="btn btn-reddit d-grid w-100 waves-effect mb-3"
                            data-bs-toggle="modal" data-bs-target="#addComment"> Comment
                        </button> --}}
                        @if (Auth::user()->role == 'Sales')
                            <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-quotation"
                                data-id="{{ $quote->id }}">Delete</a>
                        @endif
                    </div>
                </div>
                @if (Auth::user()->role == 'Sales')
                    <div class="card mb-3">
                        <div class="card-body">
                            @if ((Auth::user()->id == '1' || Auth::user()->id == '16') && $invoice->count() < 1)
                                <a href="#" data-id="{{ $quote->id }}"
                                    class="btn btn-instagram d-grid w-100 waves-effect mb-3 convert-flag">Change to
                                    {{ $quote->flag == 'Reftech' ? 'Kojisha' : 'Reftech' }}</a>
                            @endif
                            @if ($quote->status != '100')
                                <button type="button" class="btn btn-outline-whatsapp d-grid w-100 waves-effect mb-3"
                                    data-bs-toggle="modal" data-bs-target="#convertPo">Convert to PO</button>
                            @else
                                @if ($quote->po_file != null)
                                    @if ($invoice->count() >= 1 && $invoice[0]->no_invoice == null)
                                        <button type="button"
                                            class="btn btn-outline-primary d-grid w-100 waves-effect mb-3">
                                            Waiting Accounting Apply
                                        </button>
                                    @elseif($invoice->count() >= 1 && $invoice[0]->no_invoice)
                                        <a class="btn btn-facebook d-grid w-100 mb-3 waves-effect"
                                            href="{{ route('invoice.show', $invoice[0]->id) }}">
                                            Go To Invoice {{ $invoice[0]->type == 'CT' ? '' : 'DP' }}
                                        </a>
                                        @if (@$payments)
                                            @if ($invoice->count() == 1)
                                                <button type="button"
                                                    class="btn btn-outline-dark d-grid w-100 waves-effect mb-3"
                                                    data-bs-toggle="modal" data-bs-target="#request-bp">
                                                    Request Invoice BP
                                                </button>
                                            @elseif ($invoice[1]->no_invoice == null)
                                                <button type="button"
                                                    class="btn btn-outline-primary d-grid w-100 waves-effect mb-3">
                                                    Waiting Accounting Apply
                                                </button>
                                            @elseif ($invoice[1]->no_invoice)
                                                <a class="btn btn-facebook d-grid w-100 mb-3 waves-effect"
                                                    href="{{ route('invoice.show', $invoice[1]->id) }}">
                                                    Go To Invoice BP
                                                </a>
                                            @endif
                                        @endif
                                    @endif
                                    <div class="d-flex justify-content-between mb-3">
                                        <button class="btn btn-primary d-grid w-100 waves-effect"
                                            onclick="copyDownloadLink('{{ route('download-po.quotation', $quote->id) }}')">
                                            Copy Link PO
                                        </button>
                                        <a href="#"
                                            class="btn btn-label-danger d-grid waves-effect delete-file mx-2"
                                            data-id="{{ $quote->id }}"> <i
                                                class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                        </a>
                                    </div>
                                    @php
                                        $invo = 0;
                                    @endphp
                                    @foreach ($invoice as $invoices)
                                        <button type="button" class="btn btn-outline-dark d-grid w-100 waves-effect mb-3"
                                            data-bs-toggle="modal" data-bs-target="#changePo{{ $invo }}">
                                            Change No PO {{ $invo == 0 ? 'DP' : 'BP' }}
                                        </button>
                                        @php
                                            $invo++;
                                        @endphp
                                    @endforeach
                                @else
                                    <button type="button" class="btn btn-whatsapp d-grid w-100 waves-effect mb-3 btn-upload-po"
                                        data-npwp="{{ $quote->pic->client->npwp ?? '' }}">Upload PO</button>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if ($quote->status == 100 && isset($invoice))
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    @if ($remaining != 0)
                                        <button type="button" class="btn btn-success d-grid w-100 waves-effect"
                                            data-bs-toggle="modal" data-bs-target="#addPayment">Add Payment</button>
                                    @endif
                                    <button type="button"
                                        class="btn btn-secondary waves-effect waves-light mx-2 {{ $remaining == 0 ? 'w-100' : '' }}"
                                        data-bs-toggle="modal" data-bs-target="#detailPayment">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-list-box-outline m-0">
                                            {{ $remaining == 0 ? 'Detail' : '' }}</i>
                                    </button>
                                </div>
                                <h5>Remaining : Rp {{ number_format($remaining, 0, '.', ',') }}</h5>
                            </div>
                        </div>
                    @endif
                @elseif(Auth::user()->role == 'Admin')
                    @if ($quote->po_file != null)
                        <div class="card mb-3">
                            <div class="card-body">
                                <a href="{{ route('download-po.quotation', $quote->id) }}"
                                    class="btn btn-primary d-grid w-100 waves-effect"> Download PO</a>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between mb-4">
                                <div class="total mb-0">
                                    <h5>Fee : Rp. {{ number_format($quote->fee, 0, ',', '.') }}</h5>
                                    <h5 class="mb-0">Nett : Rp. {{ number_format($quote->nett, 0, ',', '.') }}</h5>
                                </div>
                                @if (Auth::user()->role == 'Sales' && $quote->fee != '0')
                                    <a href="#" data-id="{{ $quote->id }}"
                                        class="btn btn-sm btn-label-danger delete-fee">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if (Auth::user()->role == 'Sales' && $quote->fee == '0')
                            <button type="button" class="btn btn-whatsapp d-grid w-100 waves-effect mb-3"
                                data-bs-toggle="modal" data-bs-target="#insertFee">Insert Fee</button>
                        @elseif(Auth::user()->role == 'Sales' && $quote->fee != '0')
                            <div class="mt-6">
                                <button type="button" class="btn btn-warning me-3 waves-effect w-75"
                                    data-bs-toggle="modal" data-bs-target="#insertFee"><i
                                        class="menu-icon tf-icons mdi mdi-14px mdi-square-edit-outline me-1"></i>Update
                                    Fee</button>
                                <button type="button" class="btn btn-secondary waves-effect waves-light w-px-50"
                                    data-bs-toggle="modal" data-bs-target="#detailFee"><i
                                        class="menu-icon tf-icons mdi mdi-14px mdi-list-box-outline me-1"></i></button>
                            </div>
                        @endif
                    </div>
                </div>
                @if (Auth::user()->role == 'Sales')
                    <div class="card">
                        <div class="card-body">
                            @php
                                // Inisialisasi variabel
                                $sellingContract = null;
                                $orderContract = null;
                                $requestedSellingContract = null;
                                $requestedOrderContract = null;

                                // Loop untuk menemukan kontrak dengan tipe Selling dan Order
                                if (isset($quote) && $quote->contract) {
                                    foreach ($quote->contract as $contract) {
                                        if ($contract->type == 'Selling' && $contract->level == '0') {
                                            $requestedSellingContract = $contract;
                                        } elseif ($contract->type == 'Selling' && $contract->level == '1') {
                                            $sellingContract = $contract;
                                        } elseif ($contract->type == 'Order' && $contract->level == '0') {
                                            $requestedOrderContract = $contract;
                                        } elseif ($contract->type == 'Order' && $contract->level == '1') {
                                            $orderContract = $contract;
                                        }
                                    }
                                }
                            @endphp
                            @if ($sellingContract)
                                <a class="btn btn-facebook d-grid w-100 mb-3 waves-effect"
                                    href="{{ route('contract.show', $sellingContract->id) }}">
                                    Go To Selling Contract
                                </a>
                            @elseif ($requestedSellingContract)
                                <button type="button" class="btn btn-outline-primary d-grid w-100 waves-effect mb-3">
                                    Waiting Accounting Apply
                                </button>
                            @else
                                <a href="#" data-id="{{ $quote->id }}"
                                    class="btn btn-outline-dark d-grid w-100 waves-effect mb-3 request-selling">Request
                                    Selling
                                    Contract</a>
                            @endif
                            @if (Auth::user()->id == '1' || Auth::user()->id == '16')
                                @if ($orderContract)
                                    <a class="btn btn-google-plus d-grid w-100 mb-3 waves-effect"
                                        href="{{ route('contract.show', $orderContract->id) }}">
                                        Go To Confirm Order
                                    </a>
                                @elseif ($requestedOrderContract)
                                    <button type="button" class="btn btn-outline-primary d-grid w-100 waves-effect mb-3">
                                        Waiting Accounting Apply
                                    </button>
                                @else
                                    <a href="#" data-id="{{ $quote->id }}"
                                        class="btn btn-outline-dark d-grid w-100 waves-effect mb-3 request-order">Request
                                        Confirm
                                        Order</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Cek apakah ada kontrak bertipe Selling -->
                @if (Auth::user()->role == 'Admin')
                    <div class="card">
                        <div class="card-body">
                            @php
                                // Inisialisasi variabel
                                $sellingContract = null;
                                $orderContract = null;

                                // Loop untuk menemukan kontrak dengan tipe Selling dan Order
                                if (isset($quote) && $quote->contract) {
                                    foreach ($quote->contract as $contract) {
                                        if ($contract->type == 'Selling') {
                                            $sellingContract = $contract;
                                        } elseif ($contract->type == 'Order') {
                                            $orderContract = $contract;
                                        }
                                    }
                                }
                            @endphp
                            @if ($sellingContract)
                                <a class="btn btn-facebook d-grid w-100 mb-3 waves-effect"
                                    href="{{ route('contract.show', $sellingContract->id) }}">
                                    Go To Selling Contract
                                </a>
                            @else
                                <button type="button" class="btn btn-facebook d-grid w-100 waves-effect mb-3"
                                    data-bs-toggle="modal" data-bs-target="#sellingContract">
                                    Create Selling Contract
                                </button>
                            @endif

                            <!-- Cek apakah ada kontrak bertipe Order -->
                            @if ($orderContract)
                                <a class="btn btn-google-plus d-grid w-100 mb-3 waves-effect"
                                    href="{{ route('contract.show', $orderContract->id) }}">
                                    Go To Confirm Order
                                </a>
                            @else
                                <button type="button" class="btn btn-google-plus d-grid w-100 waves-effect mb-3"
                                    data-bs-toggle="modal" data-bs-target="#confirmOrder">
                                    Create Confirm Order
                                </button>
                            @endif
                            {{-- <button type="button" class="btn btn-google-plus d-grid w-100 waves-effect mb-3"
                        data-bs-toggle="modal" data-bs-target="#convertPo">Create Contract Order</button> --}}
                        </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body">
                        <a href="#" class="btn btn-primary d-grid w-100 waves-effect unarchive-quotation mb-3"
                            data-id="{{ $quote->id }}">Un Archive</a>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-archive"
                            data-id="{{ $quote->id }}">Delete Archive</a>
                    </div>
                </div>
            @endif
        </div>
        {{-- @endif --}}
    </div>
    {{-- End : Button Invoice --}}
    @include('pages.sales.quotation.modal-status')
    @include('components.modal.quotation.convert-po')
    @include('components.modal.quotation.upload-po')
    @php
        $invo = 0;
    @endphp
    @foreach ($invoice as $invoices)
        @include('components.modal.quotation.change-po')
        @php
            $invo++;
        @endphp
    @endforeach
    @include('components.modal.quotation.request-bp')
    @include('components.modal.accounting.selling-contract')
    @include('components.modal.accounting.confirm-order')
    @include('components.modal.quotation.insert-fee')
    @include('components.modal.quotation.detail-fee')
    @include('components.modal.quotation.add-payment')
    @include('components.modal.quotation.mentions')
    @include('components.modal.quotation.detail-payment')
    </div>
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <style>
        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-file-upload.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        let formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
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
            console.log(id);
            $(`#price-${id}`).val(nomorInt);
        });
        $(".invoice-item-price-label").on('keyup', function() {
            var total = 0; // Mengatur ulang total pada setiap event keyup
            $('.invoice-item-price').each((index, element) => {
                let value = $(element).val();
                value = value ? parseInt(value) : 0;
                total += value;
            });
            $('#totalLabel').val(`${formatter.format(total)}`);
            $('#total').val(total);
        });
        $(".invoice-item-amount-label").on('keyup', function() {
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
            $(`#amount`).val(nomorInt);
        });
        $(document).on('click', '.delete-quotation', function() {
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
                        'url': '{{ url('quotation') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quotation';
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
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '{{ url('leads') }}/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
        $(document).on('click', '.cancel-po', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Convert this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('quotation') }}/' + id + '/cancel_po',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.convert-flag', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Convert this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('quotation') }}/' + id + '/convert_flag',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-fee', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Delete this fee?",
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
                        'url': '{{ url('quotation') }}/' + id + '/delete_fee',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your fee has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-file', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Delete this file?",
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
                        'url': '{{ url('quotation') }}/' + id + '/delete_po',
                        'type': 'DELETE',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.request-selling', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Request this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Request it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('request/selling-contract') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Requested!",
                                    text: "Your file has been Requested.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Request!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Request is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.request-order', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Request this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Request it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('request/confirm-order') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Requested!",
                                    text: "Your file has been Requested.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Request!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Request is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.unarchive-quotation', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Un Archive this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Un Archive it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('un-archive') }}/quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your Quotation has been Un Archive.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quote/unit-detail/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-archive', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure Delete this with all source (invoice, selling contract, ect)?",
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
                        'url': '{{ url('delete-archive') }}/quotation/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quotation';
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
        $(document).on('click', '.delete-payments', function() {
            var id = $(this).data('id');
            var quote = $(this).data('quote');
            Swal.fire({
                title: "Are you sure Delete this payment?",
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
                        'url': '{{ url('quotation') }}/' + id + '/delete_payment',
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
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
                                    window.location.href = '/quote/unit-detail/' +
                                        quote;
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
        $(document).on('change', '.change-primary', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/quotation/' + selectedValue + '/change_primary',
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    console.log('Perubahan status berhasil dikirim ke server');
                    window.setTimeout(function() {
                        window.location.href = '/quote/unit-detail/' + selectedValue;
                    }, 10);
                },
                error: function(error) {
                    console.error('Gagal mengirim permintaan ke server:', error);
                }
            });
        });

        function copyDownloadLink(link) {
            navigator.clipboard.writeText(link)
                .then(() => {
                    alert('Link berhasil disalin!');
                })
                .catch(err => {
                    alert('Gagal menyalin link');
                    console.error(err);
                });
        }
    </script>
@endpush
