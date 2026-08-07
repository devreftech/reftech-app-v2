@extends('layouts.sales.app')
@section('title', 'Goods Receipt Verification')
@section('content')
    <div class="container-fluid premium-container p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="row">
            <div class="col-12">
                
                {{-- Form action --}}
                <form action="{{ route('purchase-request.store-goods-receipt', $pending->id) }}" method="POST">
                    @csrf
                    
                    {{-- Header Card --}}
                    <div class="card modern-card mb-4 overflow-hidden border-0">
                        <div class="gradient-header-blue p-4 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary mb-1">Logistics Reception</span>
                                <h3 class="mb-0 text-white fw-bold">Goods Receipt (GR) Verification</h3>
                                <p class="mb-0 text-white-50 small">Verifikasi kesesuaian fisik barang yang diterima dari supplier</p>
                            </div>
                            <div class="text-end">
                                <span class="text-white-50 d-block small">SO Number</span>
                                <span class="fs-4 fw-bold text-white"><i class="mdi mdi-receipt-text-outline me-1"></i>{{ $pending->no_pending }}</span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4 bg-light">
                            @if ($errors->any())
                                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control bg-white" id="no_do" name="no_do" placeholder="Nomor DO dari Supplier" required value="{{ old('no_do') }}">
                                        <label for="no_do">No. Delivery Order (DO) Supplier</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="date" class="form-control bg-white" id="gr_date" name="gr_date" required value="{{ old('gr_date', date('Y-m-d')) }}">
                                        <label for="gr_date">Tanggal Penerimaan</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="supplier" name="supplier" class="form-select bg-white" required>
                                            <option value="">Pilih Supplier...</option>
                                            @foreach ($suppliers as $supp)
                                                <option value="{{ $supp->id }}" {{ old('supplier') == $supp->id ? 'selected' : '' }}>
                                                    {{ $supp->supplier }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="supplier">Supplier</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Checklist Card --}}
                    <div class="card modern-card border-0 mb-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-checkbox-marked-circle-outline me-2 text-primary fs-4"></i> Checklist Kesesuaian Item PR
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive text-nowrap p-3 bg-light">
                                <table class="table custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">No</th>
                                            <th>Item PR</th>
                                            <th style="width: 100px;">Qty Order</th>
                                            <th style="width: 250px;">Verifikasi Status</th>
                                            <th style="width: 130px;">Qty Diterima</th>
                                            <th style="width: 200px;">Replacement</th>
                                            <th style="width: 100px;">Warehouse</th>
                                            <th>Catatan Masalah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchases as $key => $pr)
                                            <input type="hidden" name="pr_id[{{ $key }}]" value="{{ $pr->id }}">
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $pr->equivalent->brand }} {{ $pr->equivalent->pn }}</span>
                                                    <small class="text-muted d-block" style="font-size: 11px;">PR No: {{ $pr->no_pr }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-5" id="qty-order-{{ $key }}">{{ $pr->qty }}</span>
                                                    <span class="text-muted small d-block">{{ $pr->equivalent->product->unit ?? 'pcs' }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm w-100" role="group">
                                                        <input type="radio" class="btn-check gr-status-radio" 
                                                            name="gr_status[{{ $key }}]" 
                                                            id="status-ok-{{ $key }}" 
                                                            value="Sesuai" 
                                                            checked 
                                                            data-key="{{ $key }}"
                                                            data-qty="{{ $pr->qty }}">
                                                        <label class="btn btn-outline-success" for="status-ok-{{ $key }}">
                                                            <i class="mdi mdi-check-circle-outline me-1"></i> Sesuai
                                                        </label>
                                                        
                                                        <input type="radio" class="btn-check gr-status-radio" 
                                                            name="gr_status[{{ $key }}]" 
                                                            id="status-diff-{{ $key }}" 
                                                            value="Tidak Sesuai" 
                                                            data-key="{{ $key }}"
                                                            data-qty="{{ $pr->qty }}">
                                                        <label class="btn btn-outline-danger" for="status-diff-{{ $key }}">
                                                            <i class="mdi mdi-alert-circle-outline me-1"></i> Selisih
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                        class="form-control form-control-sm qty-received-input text-center fw-bold" 
                                                        name="qty_received[{{ $key }}]" 
                                                        value="{{ $pr->qty }}" 
                                                        min="0" 
                                                        id="qty-rec-{{ $key }}" 
                                                        readonly 
                                                        style="max-width: 100px; background-color: #f1f5f9; border-width: 2px;">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="replacement[{{ $key }}]" required style="min-width: 200px;">
                                                        <option value="">-- Pilih Replacement --</option>
                                                        @foreach ($fullRep[$key] as $products)
                                                            <option value="{{ $products->id }}" {{ count($fullRep[$key]) == 1 ? 'selected' : '' }}>
                                                                {{ $products->product->commodity }} ({{ $products->replacement }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="warehouse[{{ $key }}]">
                                                        <option value="BDG" selected>BDG</option>
                                                        <option value="BKS">BKS</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                        class="form-control form-control-sm gr-note-input" 
                                                        name="gr_note[{{ $key }}]" 
                                                        placeholder="Catatan selisih (jika ada)" 
                                                        id="note-{{ $key }}" 
                                                        readonly 
                                                        style="background-color: #f1f5f9;">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top p-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('purchase-request.show', $pending->id) }}" class="btn btn-outline-secondary btn-premium">
                                <i class="mdi mdi-close me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-premium">
                                <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Simpan Verifikasi Goods Receipt
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        
        .premium-container {
            font-family: 'Outfit', 'Inter', sans-serif !important;
        }
        
        .modern-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
            border-radius: 16px;
            background: #ffffff;
        }

        .gradient-header-blue {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
        }

        .custom-table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .custom-table tr {
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-radius: 8px;
        }

        .custom-table td, .custom-table th {
            border: none !important;
            padding: 14px 16px !important;
            vertical-align: middle;
        }

        .custom-table thead th {
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
        }

        .custom-table tbody tr {
            background: #ffffff;
        }
        
        .btn-premium {
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 500;
        }
    </style>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            // Handle radio toggle change
            $('.gr-status-radio').on('change', function() {
                var key = $(this).data('key');
                var val = $(this).val();
                var orderedQty = $(this).data('qty');
                
                var $qtyInput = $('#qty-rec-' + key);
                var $noteInput = $('#note-' + key);
                
                if (val === 'Sesuai') {
                    // Set input back to ordered quantity, lock it
                    $qtyInput.val(orderedQty);
                    $qtyInput.prop('readonly', true);
                    $qtyInput.css('background-color', '#f1f5f9');
                    $qtyInput.css('border-color', '#d9ade8'); // soft border
                    
                    // Clear and lock note input
                    $noteInput.val('');
                    $noteInput.prop('readonly', true);
                    $noteInput.css('background-color', '#f1f5f9');
                } else {
                    // Unlock received qty input to type actual count
                    $qtyInput.prop('readonly', false);
                    $qtyInput.css('background-color', '#ffffff');
                    $qtyInput.css('border-color', '#ff4d49'); // danger border indicating difference
                    $qtyInput.focus();
                    
                    // Unlock note input
                    $noteInput.prop('readonly', false);
                    $noteInput.css('background-color', '#ffffff');
                    $noteInput.attr('placeholder', 'Masukkan deskripsi selisih (wajib)...');
                }
            });
        });
    </script>
@endpush
