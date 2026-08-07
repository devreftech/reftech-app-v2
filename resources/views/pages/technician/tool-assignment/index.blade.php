@extends('layouts.sales.app')
@section('title', 'Management Tools per Teknisi')
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center py-3 mb-1">
        <h4 class="fw-bold mb-0">
            Management Tools per Teknisi
        </h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
            <i class="mdi mdi-plus"></i> Add Technician
        </button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Teknisi</th>
                                <th>Code</th>
                                <th>Total Tools Aktif</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($technicians as $technician)
                                <tr>
                                    <td>{{ $technician->name }}</td>
                                    <td>{{ $technician->code ?? '-' }}</td>
                                    <td>{{ $technician->tools_assigned_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('tool-assignment.show', $technician->id) }}"
                                            class="btn btn-sm btn-primary">Kelola Tools</a>
                                        <form action="{{ route('tool-assignment.remove-technician', $technician->id) }}"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Hapus {{ $technician->name }} dari daftar Tool Assignment? Tools yang sudah di-assign tidak akan terhapus.');">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada teknisi yang ditambahkan. Klik "Add
                                        Technician" untuk mulai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Add Technician --}}
    <form action="{{ route('tool-assignment.add-technician') }}" method="post">
        @csrf
        <div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Technician</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select class="form-select" name="user_id" required>
                                <option value="" disabled selected>-- Pilih User --</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Semua user bisa ditambahkan, tidak terbatas role
                                Technician.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection()
