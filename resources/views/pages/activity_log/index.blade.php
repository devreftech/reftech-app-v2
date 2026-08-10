@extends('layouts.sales.app')
@section('title', 'Activity Log & System Audit')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Activity Log & System Audit</h4>
            <span class="text-muted">Jejak aktivitas transaksi pengguna dan catatan error otomatis sistem.</span>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('activity-log.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipe Log</label>
                    <select name="type" class="form-select">
                        <option value="">-- Semua Tipe --</option>
                        <option value="activity" {{ request('type') == 'activity' ? 'selected' : '' }}>Aktivitas Transaksi</option>
                        <option value="error" {{ request('type') == 'error' ? 'selected' : '' }}>System Error 500</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pengguna (User)</label>
                    <select name="user_id" class="form-select">
                        <option value="">-- Semua User --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aksi (Action)</label>
                    <select name="action" class="form-select">
                        <option value="">-- Semua Aksi --</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Create (Tambah)</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Update (Edit)</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Delete (Hapus)</option>
                        <option value="error_500" {{ request('action') == 'error_500' ? 'selected' : '' }}>Error 500</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="mdi mdi-filter-outline me-1"></i> Filter</button>
                    <a href="{{ route('activity-log.index') }}" class="btn btn-label-secondary"><i class="mdi mdi-refresh me-1"></i> Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User / Akun</th>
                        <th>Tipe</th>
                        <th>Aksi</th>
                        <th>Deskripsi Aktivitas / Detail Error</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $log->created_at->format('d-m-Y H:i:s') }}</span>
                                <br><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($log->user->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-semibold">{{ $log->user->name }}</span>
                                            <br><small class="text-muted">{{ $log->user->role }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary">System / Guest</span>
                                @endif
                            </td>
                            <td>
                                @if($log->type == 'error')
                                    <span class="badge bg-label-danger"><i class="mdi mdi-alert-circle me-1"></i> System Error</span>
                                @else
                                    <span class="badge bg-label-success"><i class="mdi mdi-check-circle me-1"></i> Transaction</span>
                                @endif
                            </td>
                            <td>
                                @if($log->action == 'created')
                                    <span class="badge bg-primary">CREATED</span>
                                @elseif($log->action == 'updated')
                                    <span class="badge bg-info">UPDATED</span>
                                @elseif($log->action == 'deleted')
                                    <span class="badge bg-danger">DELETED</span>
                                @else
                                    <span class="badge bg-warning">{{ strtoupper($log->action) }}</span>
                                @endif
                            </td>
                            <td style="max-width: 400px; white-space: normal;">
                                <div>{{ $log->description }}</div>
                                @if($log->properties)
                                    <button class="btn btn-xs btn-outline-secondary mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#prop-{{ $log->id }}">
                                        Lihat Detail Data JSON
                                    </button>
                                    <div class="collapse mt-2" id="prop-{{ $log->id }}">
                                        <pre class="p-2 bg-dark text-white rounded font-monospace" style="font-size: 11px;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <code>{{ $log->ip_address ?? '-' }}</code>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data activity log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
