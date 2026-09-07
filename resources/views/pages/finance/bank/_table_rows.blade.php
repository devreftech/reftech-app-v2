<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr style="font-size: 12px;">
                <th class="text-uppercase fw-bold text-muted py-3 px-3">Nama Bank &amp; Rekening</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3">Entitas &amp; Atas Nama</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3">Keterangan / Tipe</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">Saldo Awal</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">Saldo Terkini / Berjalan</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Status</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Riwayat</th>
                <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody style="font-size: 13px;">
            @forelse ($bankList as $item)
                <tr>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-initial rounded {{ ($item->entity ?? 'Reftech') == 'Kojisha' ? 'bg-label-warning text-warning' : 'bg-label-primary text-primary' }} fw-bold">
                                    {{ strtoupper(substr($item->bank, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block fs-6">{{ $item->bank }}</span>
                                <span class="font-monospace text-muted small"><i class="mdi mdi-credit-card-outline me-1"></i>{{ $item->no_rek }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-1 mb-1">
                            @if(($item->entity ?? 'Reftech') == 'Kojisha')
                                <span class="badge bg-label-warning rounded-pill px-2 py-0.5" style="font-size: 11px;">
                                    <i class="mdi mdi-office-building me-1"></i> KOJISHA
                                </span>
                            @else
                                <span class="badge bg-label-info rounded-pill px-2 py-0.5" style="font-size: 11px;">
                                    <i class="mdi mdi-domain me-1"></i> REFTECH
                                </span>
                            @endif
                        </div>
                        <span class="fw-semibold text-dark d-block" style="font-size: 12.5px;">{{ $item->atas_nama ?: 'PT. Refrigerasi Teknik Indonesia' }}</span>
                        <small class="text-muted">{{ $item->branch ? 'KCP ' . $item->branch : '-' }}</small>
                    </td>
                    <td class="px-3">
                        @if($item->is_petty_cash)
                            <span class="badge bg-label-primary rounded-pill px-2 py-0.5 mb-1" style="font-size: 10.5px;">
                                <i class="mdi mdi-cash-register me-1"></i> Kas Kecil (Petty Cash)
                            </span>
                            @if($item->pic)
                                <div class="text-primary small fw-semibold" style="font-size: 11.5px;">
                                    <i class="mdi mdi-account-tie me-1"></i> PIC: {{ $item->pic->name }}
                                </div>
                            @endif
                            @if($item->plafond > 0)
                                <div class="text-muted small" style="font-size: 10.5px;">
                                    Plafon: Rp {{ number_format($item->plafond, 0, ',', '.') }}
                                </div>
                            @endif
                        @endif
                        @if($item->description)
                            @if(stripos($item->description, 'PPN') !== false && stripos($item->description, 'Non-PPN') === false)
                                <span class="badge bg-label-success rounded-pill px-2 py-0.5 mb-1" style="font-size: 10.5px;">
                                    <i class="mdi mdi-receipt-text-check-outline me-1"></i> PPN
                                </span>
                            @elseif(stripos($item->description, 'Non-PPN') !== false)
                                <span class="badge bg-label-secondary rounded-pill px-2 py-0.5 mb-1" style="font-size: 10.5px;">
                                    <i class="mdi mdi-receipt-text-outline me-1"></i> Non-PPN
                                </span>
                            @endif
                            <div class="text-muted small" style="max-width: 220px; line-height: 1.3;">{{ $item->description }}</div>
                        @elseif(!$item->is_petty_cash)
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="px-3 text-end text-muted fw-semibold">
                        Rp {{ number_format($item->initial_balance ?: 0, 0, ',', '.') }}
                    </td>
                    <td class="px-3 text-end">
                        <h6 class="fw-bolder {{ $item->saldo >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                            Rp {{ number_format($item->saldo, 0, ',', '.') }}
                        </h6>
                    </td>
                    <td class="px-3 text-center">
                        @if($item->is_active)
                            <span class="badge bg-label-success rounded-pill px-2.5 py-1">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Aktif
                            </span>
                        @else
                            <span class="badge bg-label-secondary rounded-pill px-2.5 py-1">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-3 text-center">
                        <span class="badge bg-label-info rounded-pill px-2.5 py-1">
                            {{ $item->total_tx_count }} Mutasi
                        </span>
                    </td>
                    <td class="px-3 text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('bank.statement', $item->id) }}" class="btn btn-xs btn-label-primary rounded-pill px-2.5" title="Lihat Rekening Koran / Buku Bank">
                                <i class="mdi mdi-book-open-outline me-1"></i> Buku Bank
                            </a>
                            <button type="button" class="btn btn-xs btn-label-warning rounded-pill px-2" title="Edit Bank"
                                data-bs-toggle="modal" data-bs-target="#editBankModal-{{ $item->id }}">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <form action="{{ route('bank.toggle_status', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $item->is_active ? 'btn-label-secondary' : 'btn-label-success' }} rounded-pill px-2" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="mdi {{ $item->is_active ? 'mdi-eye-off-outline' : 'mdi-eye-outline' }}"></i>
                                </button>
                            </form>
                            @if($item->total_tx_count == 0)
                                <form action="{{ route('bank.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening {{ $item->bank }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-label-danger rounded-pill px-2" title="Hapus">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <em>Belum ada rekening bank yang terdaftar pada tab ini.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

