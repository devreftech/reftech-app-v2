@extends('layouts.sales.app')
@section('title', 'Tools - ' . $technician->name)
@section('content')
    <style>
        #toolMasterList .btn-pick-tool-master:last-child {
            border-bottom: 0;
        }

        #toolMasterList .btn-pick-tool-master:hover {
            background: #f6f6f7;
        }
    </style>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Management Tools /</span> {{ $technician->name }}
    </h4>
    <p class="text-muted mb-4">{{ $technician->code ?? '-' }}</p>

    <a href="{{ route('tool-assignment.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali ke Daftar Teknisi
    </a>
    <button type="button" class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addToolModal">
        <i class="mdi mdi-plus"></i> Tambah Tools
    </button>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Foto Awal</th>
                        <th>Nama Tools</th>
                        <th>Qty</th>
                        <th>Tanggal Serah Terima</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tools as $tool)
                        <tr>
                            <td>
                                @if ($tool->foto_awal)
                                    <img src="{{ asset($tool->foto_awal) }}" alt="foto"
                                        style="width:50px;height:50px;object-fit:cover;border-radius:6px;">
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $tool->toolsMaster->nama_tools ?? '-' }}</td>
                            <td>{{ $tool->qty }}</td>
                            <td>{{ \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d M Y') }}</td>
                            <td>
                                @if ($tool->status_tools == 'Aktif')
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-secondary">Retired</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-inline-block">
                                    <a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end m-0">
                                        <li><a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editTool-{{ $tool->id }}">Edit</a></li>
                                        <li><a href="javascript:;" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#transferTool-{{ $tool->id }}">Transfer PIC</a></li>
                                        <div class="dropdown-divider"></div>
                                        <li>
                                            <form action="{{ route('tool-assignment.retire', $tool->id) }}" method="post"
                                                onsubmit="return confirm('{{ $tool->status_tools == 'Aktif' ? 'Retired-kan' : 'Aktifkan kembali' }} tools ini?');">
                                                @csrf
                                                <button type="submit"
                                                    class="dropdown-item {{ $tool->status_tools == 'Aktif' ? 'text-danger' : '' }}">
                                                    {{ $tool->status_tools == 'Aktif' ? 'Retired-kan' : 'Aktifkan Kembali' }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada tools yang di-assign ke teknisi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: Tambah Tools --}}
    <form id="addToolForm" action="{{ route('tool-assignment.store', $technician->id) }}" method="post"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_tools_master" id="id_tools_master_input">
        <div class="modal fade" id="addToolModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Tools untuk {{ $technician->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Step 1: pilih jenis tools --}}
                        <div id="toolPickStep">
                            <input type="text" class="form-control mb-3" id="searchToolMaster"
                                placeholder="Cari Nama Tools / Kategori...">
                            <div id="toolMasterList" style="max-height:350px; overflow-y:auto; border:1px solid #d8d8dd; border-radius:.5rem; overflow-x:hidden;">
                                @forelse ($toolMasters as $master)
                                    <button type="button" class="btn-pick-tool-master"
                                        data-id="{{ $master->id }}" data-name="{{ $master->nama_tools }}"
                                        data-search="{{ strtolower($master->nama_tools . ' ' . $master->kategori) }}"
                                        style="display:block; width:100%; text-align:left; background:#fff; border:0; border-bottom:1px solid #eaeaec; padding:.7rem 1rem; cursor:pointer;">
                                        <div style="display:flex; justify-content:space-between;">
                                            <strong>{{ $master->nama_tools }}</strong>
                                            @if ($master->kategori)
                                                <small class="text-muted">{{ $master->kategori }}</small>
                                            @endif
                                        </div>
                                        @if ($master->spesifikasi)
                                            <div class="text-muted" style="font-size:12px;">
                                                {{ Str::limit($master->spesifikasi, 100) }}</div>
                                        @endif
                                    </button>
                                @empty
                                    <p class="text-muted text-center py-3 mb-0">Belum ada Master Tools. Tambahkan
                                        dulu di menu Tool Master.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Step 2: detail serah terima --}}
                        <div id="toolFormStep" class="d-none">
                            <div
                                class="alert alert-light border d-flex justify-content-between align-items-center py-2 mb-3">
                                <div><i class="mdi mdi-check-circle text-success me-1"></i>
                                    <strong id="pickedToolName"></strong>
                                </div>
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary btn-change-tool-master">Ganti</button>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Serah Terima</label>
                                <input type="date" class="form-control" name="tanggal_serah_terima"
                                    value="{{ now()->format('Y-m-d') }}" required>
                                <small class="text-muted">Bisa dimundurkan kalau tools ini sudah lama dipegang
                                    teknisi.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Serah Terima (Baseline)</label>
                                <input type="file" class="form-control" name="foto_awal" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" name="desc" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary d-none" id="btnSubmitTool">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Modal per baris: Edit & Transfer --}}
    @foreach ($tools as $tool)
        <form action="{{ route('tool-assignment.update', $tool->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="modal fade" id="editTool-{{ $tool->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit {{ $tool->toolsMaster->nama_tools ?? '-' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" name="qty" min="1"
                                    value="{{ $tool->qty }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Serah Terima</label>
                                <input type="date" class="form-control" name="tanggal_serah_terima"
                                    value="{{ \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ganti Foto Serah Terima (opsional)</label>
                                <input type="file" class="form-control" name="foto_awal" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea class="form-control" name="desc" rows="2">{{ $tool->desc }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('tool-assignment.transfer', $tool->id) }}" method="post">
            @csrf
            <div class="modal fade" id="transferTool-{{ $tool->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Transfer PIC — {{ $tool->toolsMaster->nama_tools ?? '-' }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Pindahkan ke Teknisi</label>
                                <select class="form-select" name="id_pic" required>
                                    <option value="" disabled selected>-- Pilih Teknisi --</option>
                                    @foreach ($otherTechnicians as $other)
                                        <option value="{{ $other->id }}">{{ $other->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Transfer</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endforeach

    @push('script')
        <script>
            function showToolPickStep() {
                $('#toolPickStep').removeClass('d-none');
                $('#toolFormStep').addClass('d-none');
                $('#btnSubmitTool').addClass('d-none');
            }

            function showToolFormStep(name) {
                $('#toolPickStep').addClass('d-none');
                $('#toolFormStep').removeClass('d-none');
                $('#btnSubmitTool').removeClass('d-none');
                $('#pickedToolName').text(name);
            }

            $(function() {
                $('#addToolModal').on('show.bs.modal', function() {
                    $('#addToolForm')[0].reset();
                    $('#id_tools_master_input').val('');
                    $('#searchToolMaster').val('');
                    $('#toolMasterList .btn-pick-tool-master').show();
                    showToolPickStep();
                });

                $('#searchToolMaster').on('keyup', function() {
                    var kw = $(this).val().toLowerCase();
                    $('#toolMasterList .btn-pick-tool-master').each(function() {
                        var text = $(this).data('search') || '';
                        $(this).toggle(text.toString().indexOf(kw) !== -1);
                    });
                });

                $(document).on('click', '.btn-pick-tool-master', function() {
                    $('#id_tools_master_input').val($(this).data('id'));
                    showToolFormStep($(this).data('name'));
                });

                $(document).on('click', '.btn-change-tool-master', function() {
                    showToolPickStep();
                });

                $('#addToolForm').on('submit', function(e) {
                    if (!$('#id_tools_master_input').val()) {
                        e.preventDefault();
                        showToolPickStep();
                    }
                });
            });
        </script>
    @endpush
@endsection()
