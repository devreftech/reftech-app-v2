@extends('layouts.sales.app')
@section('title', 'Service Contract Schedules')
@section('content')
<div class="container flex-grow-1 container-p-y">
    <!-- Custom Modern Styling -->
    <style>
        .page-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.2rem;
        }
        .page-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .custom-card {
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            background: #ffffff;
        }
        .custom-card .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            padding: 1.5rem;
        }
        .custom-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #4b5563;
            background-color: #f9fafb;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .custom-table td {
            padding: 1.2rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 0.9rem;
        }
        .custom-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.02);
        }
        .visit-badge {
            border-radius: 8px;
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
        }
        .visit-badge-warning {
            background-color: rgba(253, 224, 71, 0.15);
            color: #854d0e;
            border-color: rgba(253, 224, 71, 0.3);
        }
        .visit-badge-success {
            background-color: rgba(34, 197, 94, 0.12);
            color: #166534;
            border-color: rgba(34, 197, 94, 0.2);
        }
        .visit-badge-danger {
            background-color: rgba(239, 68, 68, 0.12);
            color: #991b1b;
            border-color: rgba(239, 68, 68, 0.2);
        }
        .btn-modern-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .btn-modern-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            color: #ffffff;
        }
        .modal-content-modern {
            border-radius: 20px;
            border: none;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .modal-header-modern {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 1.5rem;
            border: none;
        }
        .modal-header-modern .modal-title {
            color: #ffffff;
            font-weight: 700;
        }
        .modal-header-modern .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
        }
        .form-control-modern {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }
        .form-control-modern:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
    </style>

    <!-- Header Section -->
    <div class="forecast-header mb-4">
        <h1 class="page-title">Service Contract Schedules</h1>
        <p class="page-subtitle">Configure, auto-generate, and distribute scheduled contract visits and projected revenues.</p>
    </div>

    @if(session('message'))
    <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 12px;">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Contracts Table Card -->
    <div class="card custom-card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: #374151;">Active Service Contracts</h5>
            <button type="button" class="btn btn-modern-primary py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalAddContract">
                <i class="mdi mdi-plus me-1"></i> Add Contract
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table custom-table table-hover">
                <thead>
                    <tr>
                        <th>Contract No.</th>
                        <th>Customer / Company</th>
                        <th>Start Date</th>
                        <th>Progress Status</th>
                        <th>Planned Visits Timeline</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                    @php
                        $totalVisits = $contract->visitSchedules->count();
                        $completedVisits = $contract->visitSchedules->where('status', 'Completed')->count();
                    @endphp
                    <tr>
                        <td>
                            <span class="badge bg-label-primary fw-bold px-3 py-2" style="font-size: 0.85rem; border-radius: 8px;">{{ $contract->no_contract }}</span>
                            <br><small class="text-muted d-inline-block mt-1 fw-semibold"><i class="mdi mdi-tag-outline me-1"></i>{{ $contract->type ?? 'Service Contract' }}</small>
                        </td>
                        <td>
                            @if($contract->id_client)
                                <a href="{{ route('existing.show', $contract->id_client) }}" class="fw-bold text-primary" target="_blank" style="font-size: 0.95rem;">
                                    {{ $contract->client->company ?? 'No Client' }}
                                </a>
                            @elseif($contract->quotation?->pic?->id_client)
                                <a href="{{ route('existing.show', $contract->quotation->pic->id_client) }}" class="fw-bold text-primary" target="_blank" style="font-size: 0.95rem;">
                                    {{ $contract->quotation->pic->client->company ?? 'No Client' }}
                                </a>
                            @else
                                <span class="fw-bold text-secondary" style="font-size: 0.95rem;">{{ $contract->quotation?->pic?->client?->company ?? 'No Client' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-secondary"><i class="mdi mdi-calendar-range me-1 text-muted"></i>{{ $contract->date ? \Carbon\Carbon::parse($contract->date)->format('d M Y') : '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-label-info fw-bold px-2 py-1.5" style="border-radius: 6px;">{{ $completedVisits }} / {{ $totalVisits }} Visits</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2 py-1">
                                @forelse($contract->visitSchedules->take(4) as $schedule)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="visit-badge visit-badge-{{ $schedule->status == 'Completed' ? 'success' : ($schedule->status == 'Cancelled' ? 'danger' : 'warning') }} py-1 px-2">
                                            Visit #{{ $schedule->visit_number }}
                                        </span>
                                        <small class="text-muted fw-semibold">
                                            {{ \Carbon\Carbon::parse($schedule->planned_date)->format('d M Y') }}
                                            <span class="text-primary mx-1">|</span>
                                            Rp {{ number_format($schedule->estimated_revenue, 0, ',', '.') }}
                                        </small>
                                    </div>
                                @empty
                                    <span class="text-danger fw-semibold" style="font-size: 0.8rem;"><i class="mdi mdi-alert-circle-outline me-1"></i> No visits scheduled yet.</span>
                                @endforelse
                                @if($totalVisits > 4)
                                    <small class="text-muted ms-2 fw-semibold">...+{{ $totalVisits - 4 }} more visits</small>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-modern-primary py-2 px-3" data-bs-toggle="modal" data-bs-target="#modalSchedule-{{ $contract->id }}">
                                    <i class="mdi mdi-calendar-edit me-1"></i> Manage Visits
                                </button>
                                
                                <form action="{{ route('forecast.contracts.delete', $contract->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this contract along with all its visit schedules? This action cannot be undone.')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-2 px-2" style="border-radius: 10px;" title="Delete Contract">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted fw-semibold">No service contracts registered in the system.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals Section (Rendered outside the table to prevent layout breakages in browsers) -->
    
    <!-- Modal Add Contract -->
    <div class="modal fade" id="modalAddContract" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-modern">
                <form action="{{ route('forecast.contracts.store') }}" method="POST">
                    @csrf
                    <div class="modal-header modal-header-modern">
                        <h5 class="modal-title"><i class="mdi mdi-file-document-plus-outline me-1"></i> Add Service Contract</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" for="modalAddContract_no_contract">Contract Number</label>
                            <input type="text" id="modalAddContract_no_contract" name="no_contract" class="form-control form-control-modern" placeholder="e.g. 001/P/CO/KII/2026" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" for="modalAddContract_id_client">Customer / Company</label>
                            <select id="modalAddContract_id_client" name="id_client" class="form-select form-control-modern" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->company }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" for="modalAddContract_date">Contract Start Date</label>
                            <input type="date" id="modalAddContract_date" name="date" class="form-control form-control-modern" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted" for="modalAddContract_type">Contract Type</label>
                            <select id="modalAddContract_type" name="type" class="form-select form-control-modern" required>
                                <option value="Order">Service Contract (Non-Tax / Order)</option>
                                <option value="Selling">Service Contract (Tax / Selling)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer p-3 border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-modern-primary px-4">Create Contract</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($contracts as $contract)
    <div class="modal fade" id="modalSchedule-{{ $contract->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-modern">
                <form action="{{ route('forecast.contracts.schedule', $contract->id) }}" method="POST">
                    @csrf
                    <div class="modal-header modal-header-modern">
                        <h5 class="modal-title"><i class="mdi mdi-calendar-clock me-1"></i> Configure Visits: {{ $contract->no_contract }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Customer / Company</label>
                                <input type="text" class="form-control form-control-modern" value="{{ $contract->client->company ?? ($contract->quotation?->pic?->client?->company ?? 'No Client') }}" disabled style="background-color: #f9fafb;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Contract Start Date</label>
                                <input type="text" class="form-control form-control-modern" value="{{ $contract->date ? \Carbon\Carbon::parse($contract->date)->format('d M Y') : '' }}" disabled style="background-color: #f9fafb;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold text-dark"><i class="mdi mdi-format-list-bulleted me-1"></i> Planned Visit Schedule</h6>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-auto-generate" data-contract-id="{{ $contract->id }}" data-start-date="{{ $contract->date }}" style="border-radius: 8px; font-weight: 600;">
                                    <i class="mdi mdi-flash-outline me-1"></i> Auto-Generate (Quarterly)
                                </button>
                                <button type="button" class="btn btn-sm btn-success btn-add-row" data-contract-id="{{ $contract->id }}" style="border-radius: 8px; font-weight: 600;">
                                    <i class="mdi mdi-plus me-1"></i> Add Row
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 12px;">
                            <table class="table table-hover align-middle m-0" id="tableVisits-{{ $contract->id }}">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2 text-center" style="width: 80px;">Visit #</th>
                                        <th class="py-2">Planned Date</th>
                                        <th class="py-2">Estimated Revenue (IDR)</th>
                                        <th class="py-2">Scope / Description</th>
                                        <th class="py-2 text-center" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="visit-rows-container">
                                    @forelse($contract->visitSchedules as $index => $visit)
                                    <tr class="visit-row">
                                        <td class="text-center align-middle fw-bold visit-num">{{ $visit->visit_number }}</td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm form-control-modern" name="visits[{{ $index }}][planned_date]" value="{{ \Carbon\Carbon::parse($visit->planned_date)->format('Y-m-d') }}" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm form-control-modern" name="visits[{{ $index }}][estimated_revenue]" value="{{ $visit->estimated_revenue }}" required min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm form-control-modern" name="visits[{{ $index }}][description]" value="{{ $visit->description }}">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" style="border-radius: 6px;"><i class="mdi mdi-delete-outline"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <!-- Default Empty Row -->
                                    <tr class="visit-row">
                                        <td class="text-center align-middle fw-bold visit-num">1</td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm form-control-modern" name="visits[0][planned_date]" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm form-control-modern" name="visits[0][estimated_revenue]" required min="0" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm form-control-modern" name="visits[0][description]" placeholder="PM1 / Regular Service">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" style="border-radius: 6px;"><i class="mdi mdi-delete-outline"></i></button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer p-3 border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Cancel</button>
                        <button type="submit" class="btn btn-modern-primary px-4">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('script')
<script>
    $(document).ready(function() {
        // Add row dynamically
        $(document).on('click', '.btn-add-row', function() {
            var cId = $(this).data('contract-id');
            var tableBody = $('#tableVisits-' + cId + ' .visit-rows-container');
            var rowCount = tableBody.find('.visit-row').length;
            
            var newRow = `
                <tr class="visit-row">
                    <td class="text-center align-middle fw-bold visit-num">${rowCount + 1}</td>
                    <td>
                        <input type="date" class="form-control form-control-sm form-control-modern" name="visits[${rowCount}][planned_date]" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm form-control-modern" name="visits[${rowCount}][estimated_revenue]" required min="0" placeholder="0">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm form-control-modern" name="visits[${rowCount}][description]" placeholder="PM1 / Regular Service">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" style="border-radius: 6px;"><i class="mdi mdi-delete-outline"></i></button>
                    </td>
                </tr>
            `;
            tableBody.append(newRow);
        });

        // Delete row
        $(document).on('click', '.btn-delete-row', function() {
            var tableBody = $(this).closest('.visit-rows-container');
            $(this).closest('.visit-row').remove();
            
            // Re-index row numbers and input names
            tableBody.find('.visit-row').each(function(index) {
                $(this).find('.visit-num').text(index + 1);
                $(this).find('input').each(function() {
                    var nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        var updatedName = nameAttr.replace(/visits\[\d+\]/, 'visits[' + index + ']');
                        $(this).attr('name', updatedName);
                    }
                });
            });
        });

        // Auto-generate quarterly schedule (4 visits spaced by 3 months)
        $(document).on('click', '.btn-auto-generate', function() {
            var cId = $(this).data('contract-id');
            var startDateStr = $(this).data('start-date');
            if(!startDateStr) {
                alert('Contract start date is not configured!');
                return;
            }
            
            var baseDate = new Date(startDateStr);
            var tableBody = $('#tableVisits-' + cId + ' .visit-rows-container');
            tableBody.empty(); // Clear existing rows
            
            for(var i = 0; i < 4; i++) {
                // Generate date spaced by 3 months
                var offsetMonths = (i + 1) * 3;
                var targetDate = new Date(baseDate.getTime());
                targetDate.setMonth(targetDate.getMonth() + offsetMonths);
                
                // Format YYYY-MM-DD
                var yyyy = targetDate.getFullYear();
                var mm = String(targetDate.getMonth() + 1).padStart(2, '0');
                var dd = String(targetDate.getDate()).padStart(2, '0');
                var dateStr = `${yyyy}-${mm}-${dd}`;
                
                var pmLabel = (i + 1) % 4 == 0 ? 'PM4' : ((i + 1) % 2 == 0 ? 'PM2' : 'PM1');
                
                var newRow = `
                    <tr class="visit-row">
                        <td class="text-center align-middle fw-bold visit-num">${i + 1}</td>
                        <td>
                            <input type="date" class="form-control form-control-sm form-control-modern" name="visits[${i}][planned_date]" value="${dateStr}" required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm form-control-modern" name="visits[${i}][estimated_revenue]" value="0" required min="0" placeholder="0">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm form-control-modern" name="visits[${i}][description]" value="${pmLabel} - Quarterly Service">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row" style="border-radius: 6px;"><i class="mdi mdi-delete-outline"></i></button>
                        </td>
                    </tr>
                `;
                tableBody.append(newRow);
            }
        });
    });
</script>
@endpush
@endsection
