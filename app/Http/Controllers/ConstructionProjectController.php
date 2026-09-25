<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\DetailExpense;
use App\Models\Expense;
use App\Models\FixedAsset;
use App\Models\FixedAssetConstructionCost;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConstructionProjectController extends Controller
{
    /**
     * Tampilkan daftar catatan pengeluaran proyek pembangunan konstruksi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isManager = in_array($user?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager', 'Project Manager']) || $user?->isDeveloper() || $user?->id == 3;

        if (!$isManager && !$user?->hasConstructionProjectAccess()) {
            abort(403, 'Anda tidak memiliki akses ke modul Proyek Konstruksi.');
        }

        // Semua aset gedung / bangunan yang ada
        $allBuildings = FixedAsset::where('type', 'Bangunan')
            ->orderByRaw("FIELD(status_bangunan, 'construction', 'operational')")
            ->orderByDesc('id')
            ->get();

        $buildings = $isManager
            ? $allBuildings
            : $allBuildings->filter(function ($bld) use ($user) {
                $pics = $bld->pic_construction_ids ?? [];
                return is_array($pics) && in_array($user->id, $pics);
            });

        if ($buildings->isEmpty()) {
            return view('pages.finance.construction-project.index', [
                'buildings' => collect(),
                'selectedBuilding' => null,
                'costs' => collect(),
                'totalProjectCost' => 0,
                'totalMaterialCost' => 0,
                'totalJasaCost' => 0,
                'totalSewaCost' => 0,
                'unmappedCount' => 0,
                'accounts' => collect(),
                'suppliers' => collect(),
                'purchaseOrders' => collect(),
            ]);
        }

        // Tentukan gedung yang dipilih
        $buildingId = $request->query('building_id');
        $selectedBuilding = $buildingId
            ? $buildings->firstWhere('id', $buildingId)
            : ($buildings->firstWhere('status_bangunan', 'construction') ?? $buildings->first());

        if (!$selectedBuilding) {
            $selectedBuilding = $buildings->first();
        }

        // Query pengeluaran untuk gedung terpilih
        $query = FixedAssetConstructionCost::with(['supplier', 'pengeluaran', 'beban', 'creator', 'fixedAsset', 'purchaseOrder'])
            ->where('fixed_asset_id', $selectedBuilding->id);

        if ($request->filled('kategori')) {
            $query->where('kategori_biaya', $request->kategori);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_item', 'like', "%{$q}%")
                    ->orWhere('payee', 'like', "%{$q}%")
                    ->orWhere('no_bukti', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        $allCosts = FixedAssetConstructionCost::where('fixed_asset_id', $selectedBuilding->id)->get();
        $totalProjectCost = $allCosts->sum('total_biaya');
        $totalMaterialCost = $allCosts->where('kategori_biaya', 'material')->sum('total_biaya');
        $totalJasaCost = $allCosts->where('kategori_biaya', 'jasa')->sum('total_biaya');
        $totalSewaCost = $allCosts->whereIn('kategori_biaya', ['sewa', 'operasional'])->sum('total_biaya');
        $unmappedCount = $allCosts->whereNull('id_pengeluaran')->count();

        $costs = $query->orderByDesc('tanggal')->orderByDesc('id')->paginate(30)->withQueryString();

        $accounts = Account::orderBy('code')->get();
        $suppliers = Supplier::orderBy('supplier')->get();
        $purchaseOrders = PurchaseOrder::with(['supplier', 'detail'])->orderByDesc('id')->get();

        return view('pages.finance.construction-project.index', compact(
            'buildings',
            'selectedBuilding',
            'costs',
            'totalProjectCost',
            'totalMaterialCost',
            'totalJasaCost',
            'totalSewaCost',
            'unmappedCount',
            'accounts',
            'suppliers',
            'purchaseOrders'
        ));
    }

    /**
     * Simpan catatan pengeluaran proyek baru (Mendukung Multi-Item per 1 Nota).
     */
    public function store(Request $request)
    {
        $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_asset,id',
            'tanggal' => 'required|date',
            'payee' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:supplier,id',
            'no_bukti' => 'nullable|string|max:100',
            'foto_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
            'items' => 'nullable|array|min:1',
            'items.*.nama_item' => 'required_with:items|string|max:255',
            'items.*.kategori_biaya' => 'required_with:items|in:material,jasa,sewa,operasional',
            'items.*.total_biaya' => 'required_with:items',
        ]);

        $fixed = FixedAsset::findOrFail($request->fixed_asset_id);

        $user = Auth::user();
        $isManager = in_array($user?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']);
        $pics = $fixed->pic_construction_ids ?? [];
        if (!$isManager && (!is_array($pics) || !in_array($user->id, $pics))) {
            abort(403, 'Anda tidak memiliki akses sebagai PIC penginput untuk proyek pembangunan gedung ini.');
        }

        // Upload foto / nota fisik sekali (digunakan untuk semua item dalam struk ini)
        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = 'nota_proyek_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/construction_costs');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $fotoPath = 'uploads/construction_costs/' . $filename;
        }

        $items = $request->input('items', []);
        $totalNota = 0;
        $totalMaterialAddition = 0;
        $itemsCount = 0;

        if (!empty($items) && is_array($items)) {
            // PROSES MULTI-ITEM DALAM 1 TRANSAKSI NOTA
            foreach ($items as $item) {
                if (empty($item['nama_item'])) {
                    continue;
                }

                $totalBiaya = (float) str_replace(['.', ','], ['', '.'], (string) ($item['total_biaya'] ?? '0'));
                $qty = !empty($item['qty']) ? (float) $item['qty'] : 1.0;
                $hargaSatuan = 0;
                if (!empty($item['harga_satuan'])) {
                    $hargaSatuan = (float) str_replace(['.', ','], ['', '.'], (string) $item['harga_satuan']);
                } else {
                    $hargaSatuan = $qty > 0 ? ($totalBiaya / $qty) : $totalBiaya;
                }

                $kategori = $item['kategori_biaya'] ?? 'material';
                if ($kategori === 'material') {
                    $totalMaterialAddition += $totalBiaya;
                }
                $totalNota += $totalBiaya;

                FixedAssetConstructionCost::create([
                    'fixed_asset_id' => $fixed->id,
                    'purchase_order_id' => $request->purchase_order_id ?: null,
                    'tanggal' => $request->tanggal,
                    'kategori_biaya' => $kategori,
                    'nama_item' => $item['nama_item'],
                    'supplier_id' => $request->supplier_id ?: null,
                    'payee' => $request->payee,
                    'qty' => $qty,
                    'satuan' => $item['satuan'] ?? 'ls',
                    'harga_satuan' => $hargaSatuan,
                    'total_biaya' => $totalBiaya,
                    'no_bukti' => $request->no_bukti,
                    'foto_bukti' => $fotoPath,
                    'catatan' => $request->catatan,
                    'created_by' => Auth::id(),
                ]);

                $itemsCount++;
            }
        } else {
            // FALLBACK SINGLE ITEM
            $totalBiaya = (float) str_replace(['.', ','], ['', '.'], (string) $request->total_biaya);
            $qty = $request->qty ? (float) $request->qty : 1.0;
            $hargaSatuan = 0;
            if ($request->harga_satuan) {
                $hargaSatuan = (float) str_replace(['.', ','], ['', '.'], (string) $request->harga_satuan);
            } else {
                $hargaSatuan = $qty > 0 ? ($totalBiaya / $qty) : $totalBiaya;
            }

            if ($request->kategori_biaya === 'material') {
                $totalMaterialAddition += $totalBiaya;
            }
            $totalNota += $totalBiaya;

            FixedAssetConstructionCost::create([
                'fixed_asset_id' => $fixed->id,
                'purchase_order_id' => $request->purchase_order_id ?: null,
                'tanggal' => $request->tanggal,
                'kategori_biaya' => $request->kategori_biaya ?: 'material',
                'nama_item' => $request->nama_item,
                'supplier_id' => $request->supplier_id ?: null,
                'payee' => $request->payee,
                'qty' => $qty,
                'satuan' => $request->satuan,
                'harga_satuan' => $hargaSatuan,
                'total_biaya' => $totalBiaya,
                'no_bukti' => $request->no_bukti,
                'foto_bukti' => $fotoPath,
                'catatan' => $request->catatan,
                'created_by' => Auth::id(),
            ]);

            $itemsCount = 1;
        }

        // Tambahkan akumulasi belanja material ke Nilai Perolehan Aktiva Bangunan
        if ($totalMaterialAddition > 0) {
            $fixed->total = (float) $fixed->total + $totalMaterialAddition;
            $fixed->save();
        }

        return redirect()->route('proyek-konstruksi.index', ['building_id' => $fixed->id])
            ->with('success', "Berhasil mencatat {$itemsCount} item pengeluaran dengan total nominal Rp " . number_format($totalNota, 0, ',', '.') . ".");
    }

    /**
     * Simpan pengeluaran proyek yang dihubungkan langsung dari Purchase Order (PO).
     */
    public function linkPo(Request $request)
    {
        $request->validate([
            'fixed_asset_id' => 'required|exists:fixed_asset,id',
            'purchase_order_id' => 'required|exists:purchase_order,id',
            'tanggal' => 'required|date',
            'kategori_biaya' => 'required|in:material,jasa,sewa,operasional',
            'nama_item' => 'required|string|max:255',
            'total_biaya' => 'required',
            'foto_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $fixed = FixedAsset::findOrFail($request->fixed_asset_id);

        $user = Auth::user();
        $isManager = in_array($user?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']);
        $pics = $fixed->pic_construction_ids ?? [];
        if (!$isManager && (!is_array($pics) || !in_array($user->id, $pics))) {
            abort(403, 'Anda tidak memiliki akses sebagai PIC penginput untuk proyek pembangunan gedung ini.');
        }

        $po = PurchaseOrder::with('supplier')->findOrFail($request->purchase_order_id);
        $totalBiaya = (float) str_replace(['.', ','], ['', '.'], (string) $request->total_biaya);
        $qty = $request->qty ? (float) $request->qty : 1.0;
        $hargaSatuan = $request->harga_satuan ? (float) str_replace(['.', ','], ['', '.'], (string) $request->harga_satuan) : ($qty > 0 ? ($totalBiaya / $qty) : $totalBiaya);

        // Upload foto / nota jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = 'nota_po_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/construction_costs');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $fotoPath = 'uploads/construction_costs/' . $filename;
        }

        // Jika Material -> Otomatis menambah Nilai Perolehan Aktiva Bangunan
        if ($request->kategori_biaya === 'material') {
            $fixed->total = (float) $fixed->total + $totalBiaya;
            $fixed->save();
        }

        FixedAssetConstructionCost::create([
            'fixed_asset_id' => $fixed->id,
            'purchase_order_id' => $po->id,
            'tanggal' => $request->tanggal,
            'kategori_biaya' => $request->kategori_biaya,
            'nama_item' => $request->nama_item,
            'supplier_id' => $request->supplier_id ?: $po->id_supplier,
            'payee' => $request->payee ?: ('PO: ' . $po->no_po . ($po->supplier ? ' - ' . $po->supplier->supplier : '')),
            'qty' => $qty,
            'satuan' => $request->satuan ?: 'ls',
            'harga_satuan' => $hargaSatuan,
            'total_biaya' => $totalBiaya,
            'no_bukti' => $request->no_bukti ?: $po->no_po,
            'foto_bukti' => $fotoPath,
            'catatan' => $request->catatan ?: ($po->note ?: 'Dihubungkan dari Purchase Order ' . $po->no_po),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('proyek-konstruksi.index', ['building_id' => $fixed->id])
            ->with('success', "Pengeluaran dari Purchase Order '{$po->no_po}' sebesar Rp " . number_format($totalBiaya, 0, ',', '.') . " berhasil dihubungkan ke proyek.");
    }

    /**
     * Update catatan pengeluaran proyek.
     */
    public function update(Request $request, $id)
    {
        $cost = FixedAssetConstructionCost::findOrFail($id);
        $fixed = FixedAsset::findOrFail($cost->fixed_asset_id);

        $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_order,id',
            'tanggal' => 'required|date',
            'kategori_biaya' => 'required|in:material,jasa,sewa,operasional',
            'nama_item' => 'required|string|max:255',
            'qty' => 'nullable|numeric|min:0.01',
            'satuan' => 'nullable|string|max:50',
            'harga_satuan' => 'nullable',
            'total_biaya' => 'required',
            'payee' => 'nullable|string|max:255',
            'foto_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $newTotalBiaya = (float) str_replace(['.', ','], ['', '.'], (string) $request->total_biaya);
        $qty = $request->qty ? (float) $request->qty : 1.0;

        $hargaSatuan = 0;
        if ($request->harga_satuan) {
            $hargaSatuan = (float) str_replace(['.', ','], ['', '.'], (string) $request->harga_satuan);
        } else {
            $hargaSatuan = $qty > 0 ? ($newTotalBiaya / $qty) : $newTotalBiaya;
        }

        // Adjust Nilai Perolehan Aktiva Tetap jika kategori material
        $oldWasMaterial = $cost->kategori_biaya === 'material';
        $newIsMaterial = $request->kategori_biaya === 'material';

        if ($oldWasMaterial && $newIsMaterial) {
            $diff = $newTotalBiaya - (float) $cost->total_biaya;
            $fixed->total = max(0, (float) $fixed->total + $diff);
            $fixed->save();
        } elseif ($oldWasMaterial && !$newIsMaterial) {
            $fixed->total = max(0, (float) $fixed->total - (float) $cost->total_biaya);
            $fixed->save();
        } elseif (!$oldWasMaterial && $newIsMaterial) {
            $fixed->total = (float) $fixed->total + $newTotalBiaya;
            $fixed->save();
        }

        // Update foto jika ada
        if ($request->hasFile('foto_bukti')) {
            if ($cost->foto_bukti && file_exists(public_path($cost->foto_bukti))) {
                @unlink(public_path($cost->foto_bukti));
            }
            $file = $request->file('foto_bukti');
            $filename = 'nota_proyek_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/construction_costs');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $cost->foto_bukti = 'uploads/construction_costs/' . $filename;
        }

        if ($request->has('purchase_order_id')) {
            $cost->purchase_order_id = $request->purchase_order_id ?: null;
        }
        $cost->tanggal = $request->tanggal;
        $cost->kategori_biaya = $request->kategori_biaya;
        $cost->nama_item = $request->nama_item;
        $cost->supplier_id = $request->supplier_id ?: null;
        $cost->payee = $request->payee;
        $cost->qty = $qty;
        $cost->satuan = $request->satuan;
        $cost->harga_satuan = $hargaSatuan;
        $cost->total_biaya = $newTotalBiaya;
        $cost->no_bukti = $request->no_bukti ?: $cost->no_bukti;
        $cost->catatan = $request->catatan;
        $cost->save();

        return redirect()->back()->with('success', 'Catatan pengeluaran proyek berhasil diperbarui.');
    }

    /**
     * Mapping Akun Kas/Bank & Beban COA (Khusus Finance, Accounting, Admin, Developer).
     */
    public function mapCoa(Request $request, $id)
    {
        $cost = FixedAssetConstructionCost::findOrFail($id);

        $request->validate([
            'id_pengeluaran' => 'nullable|exists:account,id',
            'id_beban' => 'nullable|exists:account,id',
            'no_bukti' => 'nullable|string|max:100',
        ]);

        $cost->id_pengeluaran = $request->id_pengeluaran ?: null;
        $cost->id_beban = $request->id_beban ?: null;
        if ($request->no_bukti) {
            $cost->no_bukti = $request->no_bukti;
        }

        // Jika Jasa / Sewa / Operasional dan ada akun kas/bank, sync ke modul Expense
        if (in_array($cost->kategori_biaya, ['jasa', 'sewa', 'operasional']) && $cost->id_pengeluaran) {
            $fixed = $cost->fixedAsset;
            $kodeRef = 'EXP-BGN-' . str_pad($cost->id, 4, '0', STR_PAD_LEFT);

            $expense = $cost->expense_id ? Expense::find($cost->expense_id) : null;
            if (!$expense) {
                $expense = new Expense();
                $expense->code = $kodeRef;
                $expense->created_by = Auth::id();
            }

            $expense->date = $cost->tanggal ? $cost->tanggal->format('Y-m-d') : date('Y-m-d');
            $expense->id_account = $cost->id_pengeluaran;
            $expense->payee = $cost->payee ?: ($fixed ? "Proyek {$fixed->desc}" : 'Proyek Bangunan');
            $expense->desc = "Proyek {$fixed?->desc}: {$cost->nama_item}";
            $expense->total = $cost->total_biaya;
            $expense->save();

            $dExpense = DetailExpense::where('id_expense', $expense->id)->first();
            if (!$dExpense) {
                $dExpense = new DetailExpense();
                $dExpense->id_expense = $expense->id;
            }
            $dExpense->desc = $cost->nama_item . ($cost->catatan ? " ({$cost->catatan})" : '');
            $dExpense->price = $cost->total_biaya;
            $dExpense->id_account = $cost->id_beban ?: ($fixed?->id_beban ?: $cost->id_pengeluaran);
            $dExpense->qty = 1;
            $dExpense->save();

            $cost->expense_id = $expense->id;
        }

        $cost->save();

        return redirect()->back()->with('success', 'Pemetaan Akun Akuntansi (COA) dan sumber dana berhasil disimpan.');
    }

    /**
     * Hapus catatan pengeluaran proyek.
     */
    public function destroy($id)
    {
        $cost = FixedAssetConstructionCost::findOrFail($id);
        $fixed = FixedAsset::findOrFail($cost->fixed_asset_id);

        if ($cost->kategori_biaya === 'material') {
            $fixed->total = max(0, (float) $fixed->total - (float) $cost->total_biaya);
            $fixed->save();
        }

        if ($cost->expense_id) {
            DetailExpense::where('id_expense', $cost->expense_id)->delete();
            Expense::where('id', $cost->expense_id)->delete();
        }

        if ($cost->foto_bukti && file_exists(public_path($cost->foto_bukti))) {
            @unlink(public_path($cost->foto_bukti));
        }

        $cost->delete();

        return redirect()->back()->with('success', 'Catatan pengeluaran proyek berhasil dihapus.');
    }
}
