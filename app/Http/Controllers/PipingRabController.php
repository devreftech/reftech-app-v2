<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pic;
use App\Models\PipingMaterial;
use App\Models\PipingRab;
use App\Models\PipingRabItem;
use App\Models\PipingRabSection;
use App\Models\Supplier;
use App\Models\UnitQuotation;
use App\Models\UnitQuotationDetail;
use App\Models\UnitQuotationOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PipingRabController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = PipingRab::with(['client', 'sales', 'sections.items'])
            ->where('is_latest', true)
            ->orderBy('id', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_rab', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('location_plant', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%");
                  });
            });
        }

        $rabs = $query->paginate(20)->withQueryString();

        $stats = [
            'total'               => PipingRab::where('is_latest', true)->count(),
            'draft'               => PipingRab::where('is_latest', true)->where('status', 'Draft')->count(),
            'approved'            => PipingRab::where('is_latest', true)->where('status', 'Approved')->count(),
            'converted'           => PipingRab::where('is_latest', true)->where('status', 'Converted')->count(),
            'total_selling_value' => PipingRab::where('is_latest', true)->sum('total_selling_price'),
        ];

        return view('pages.piping.rab.index', compact('rabs', 'stats', 'status', 'search'));
    }

    public function create()
    {
        $noRab = PipingRab::generateNoRab();
        $clients = Client::orderBy('company', 'asc')->get(['id', 'company', 'address']);
        $salesList = User::whereIn('id', [1, 2, 3, 4, 32])->orderBy('name', 'asc')->get(['id', 'name']);
        $suppliers = Supplier::orderBy('supplier', 'asc')->get(['id', 'supplier']);
        $materials = PipingMaterial::with(['vendorPrices.supplier'])->orderBy('category')->orderBy('item_name')->get();

        return view('pages.piping.rab.create', compact('noRab', 'clients', 'salesList', 'suppliers', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rab'         => 'required|string|unique:piping_rabs,no_rab',
            'id_client'      => 'nullable|exists:client,id',
            'id_pic'         => 'nullable|exists:pic,id',
            'id_sales'       => 'nullable|exists:users,id',
            'project_name'   => 'required|string|max:255',
            'location_plant' => 'nullable|string|max:255',
            'rab_date'       => 'required|date',
            'notes'          => 'nullable|string',
            'sections'       => 'required|array|min:1',
            'sections.*.name' => 'required|string|max:255',
            'sections.*.items' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $grandHpp = 0;
            $grandSelling = 0;

            $rab = PipingRab::create([
                'no_rab'              => $validated['no_rab'],
                'id_client'           => $validated['id_client'] ?? null,
                'id_pic'              => $validated['id_pic'] ?? null,
                'id_sales'            => $validated['id_sales'] ?? Auth::id(),
                'id_admin'            => Auth::id(),
                'project_name'        => $validated['project_name'],
                'location_plant'      => $validated['location_plant'] ?? null,
                'rab_date'            => $validated['rab_date'],
                'revision_number'     => 0,
                'is_latest'           => true,
                'status'              => 'Draft',
                'total_hpp'           => 0,
                'total_margin'        => 0,
                'total_selling_price' => 0,
                'notes'               => $validated['notes'] ?? null,
            ]);

            $rab->root_id = $rab->id;
            $rab->save();

            foreach ($request->sections as $secIndex => $secData) {
                $sectionHpp = 0;
                $sectionSelling = 0;

                $section = PipingRabSection::create([
                    'id_piping_rab'          => $rab->id,
                    'section_name'           => $secData['name'] ?? 'Section ' . ($secIndex + 1),
                    'sort_order'             => $secIndex,
                    'subtotal_hpp'           => 0,
                    'subtotal_selling_price' => 0,
                ]);

                if (!empty($secData['items']) && is_array($secData['items'])) {
                    foreach ($secData['items'] as $itemIndex => $item) {
                        $rawHpp = str_replace(['.', ' ', 'Rp'], '', (string)($item['unit_price_hpp'] ?? '0'));
                        $rawHpp = str_replace(',', '.', $rawHpp);
                        $hpp = (float) $rawHpp;

                        $rawQty = str_replace(['.', ' '], ['', ''], (string)($item['calculated_qty'] ?? '1'));
                        $rawQty = str_replace(',', '.', (string)($item['calculated_qty'] ?? '1'));
                        $qty = (float) $rawQty;

                        $marginType = $item['margin_type'] ?? 'percent';
                        $marginVal = (float) ($item['margin_value'] ?? 0);

                        $rawSell = str_replace(['.', ' ', 'Rp'], '', (string)($item['unit_selling_price'] ?? '0'));
                        $rawSell = str_replace(',', '.', $rawSell);
                        $unitSell = (float) $rawSell;

                        if ($unitSell <= 0) {
                            if ($marginType === 'percent') {
                                $unitSell = $hpp + ($hpp * ($marginVal / 100));
                            } else {
                                $unitSell = $hpp + $marginVal;
                            }
                        }

                        $itemTotalHpp = $qty * $hpp;
                        $itemTotalSell = $qty * $unitSell;

                        PipingRabItem::create([
                            'id_piping_rab_section' => $section->id,
                            'id_piping_material'    => !empty($item['id_piping_material']) ? $item['id_piping_material'] : null,
                            'item_type'             => $item['item_type'] ?? 'material',
                            'item_name'             => $item['item_name'] ?? 'Item',
                            'size'                  => $item['size'] ?? null,
                            'spec'                  => $item['spec'] ?? null,
                            'unit'                  => $item['unit'] ?? 'Pcs',
                            'input_length_meter'    => !empty($item['input_length_meter']) ? (float)$item['input_length_meter'] : null,
                            'length_per_unit'       => !empty($item['length_per_unit']) ? (float)$item['length_per_unit'] : 6.00,
                            'waste_percent'         => isset($item['waste_percent']) ? (float)$item['waste_percent'] : 0,
                            'calculated_qty'        => $qty,
                            'unit_price_hpp'        => $hpp,
                            'id_supplier'           => !empty($item['id_supplier']) ? $item['id_supplier'] : null,
                            'margin_type'           => $marginType,
                            'margin_value'          => $marginVal,
                            'unit_selling_price'    => $unitSell,
                            'total_hpp'             => $itemTotalHpp,
                            'total_selling_price'   => $itemTotalSell,
                            'notes'                 => $item['notes'] ?? null,
                            'sort_order'            => $itemIndex,
                        ]);

                        $sectionHpp += $itemTotalHpp;
                        $sectionSelling += $itemTotalSell;
                    }
                }

                $section->update([
                    'subtotal_hpp'           => $sectionHpp,
                    'subtotal_selling_price' => $sectionSelling,
                ]);

                $grandHpp += $sectionHpp;
                $grandSelling += $sectionSelling;
            }

            $rab->update([
                'total_hpp'           => $grandHpp,
                'total_margin'        => $grandSelling - $grandHpp,
                'total_selling_price' => $grandSelling,
            ]);

            DB::commit();
            return redirect()->route('piping-rab.show', $rab->id)->with('success', 'RAB Piping Proyek berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat RAB: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $rab = PipingRab::with([
            'client',
            'pic',
            'sales',
            'admin',
            'convertedQuotation',
            'sections.items.supplier',
            'sections.items.material'
        ])->findOrFail($id);

        $revisions = $rab->revisions();

        return view('pages.piping.rab.show', compact('rab', 'revisions'));
    }

    public function edit($id)
    {
        $rab = PipingRab::with(['sections.items'])->findOrFail($id);
        $clients = Client::orderBy('company', 'asc')->get(['id', 'company', 'address']);
        $salesList = User::whereIn('id', [1, 2, 3, 4, 32])->orderBy('name', 'asc')->get(['id', 'name']);
        $suppliers = Supplier::orderBy('supplier', 'asc')->get(['id', 'supplier']);
        $materials = PipingMaterial::with(['vendorPrices.supplier'])->orderBy('category')->orderBy('item_name')->get();
        $pics = $rab->id_client ? Pic::where('id_client', $rab->id_client)->get() : [];

        return view('pages.piping.rab.edit', compact('rab', 'clients', 'salesList', 'suppliers', 'materials', 'pics'));
    }

    public function update(Request $request, $id)
    {
        $rab = PipingRab::findOrFail($id);

        $validated = $request->validate([
            'project_name'   => 'required|string|max:255',
            'id_client'      => 'nullable|exists:client,id',
            'id_pic'         => 'nullable|exists:pic,id',
            'id_sales'       => 'nullable|exists:users,id',
            'location_plant' => 'nullable|string|max:255',
            'rab_date'       => 'required|date',
            'notes'          => 'nullable|string',
            'status'         => 'required|in:Draft,Reviewed,Approved,Converted',
            'sections'       => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $grandHpp = 0;
            $grandSelling = 0;

            // Delete old sections (cascades to items)
            $rab->sections()->delete();

            foreach ($request->sections as $secIndex => $secData) {
                $sectionHpp = 0;
                $sectionSelling = 0;

                $section = PipingRabSection::create([
                    'id_piping_rab'          => $rab->id,
                    'section_name'           => $secData['name'] ?? 'Section ' . ($secIndex + 1),
                    'sort_order'             => $secIndex,
                    'subtotal_hpp'           => 0,
                    'subtotal_selling_price' => 0,
                ]);

                if (!empty($secData['items']) && is_array($secData['items'])) {
                    foreach ($secData['items'] as $itemIndex => $item) {
                        $rawHpp = str_replace(['.', ' ', 'Rp'], '', (string)($item['unit_price_hpp'] ?? '0'));
                        $rawHpp = str_replace(',', '.', $rawHpp);
                        $hpp = (float) $rawHpp;

                        $rawQty = str_replace(['.', ' '], ['', ''], (string)($item['calculated_qty'] ?? '1'));
                        $rawQty = str_replace(',', '.', (string)($item['calculated_qty'] ?? '1'));
                        $qty = (float) $rawQty;

                        $marginType = $item['margin_type'] ?? 'percent';
                        $marginVal = (float) ($item['margin_value'] ?? 0);

                        $rawSell = str_replace(['.', ' ', 'Rp'], '', (string)($item['unit_selling_price'] ?? '0'));
                        $rawSell = str_replace(',', '.', $rawSell);
                        $unitSell = (float) $rawSell;

                        if ($unitSell <= 0) {
                            if ($marginType === 'percent') {
                                $unitSell = $hpp + ($hpp * ($marginVal / 100));
                            } else {
                                $unitSell = $hpp + $marginVal;
                            }
                        }

                        $itemTotalHpp = $qty * $hpp;
                        $itemTotalSell = $qty * $unitSell;

                        PipingRabItem::create([
                            'id_piping_rab_section' => $section->id,
                            'id_piping_material'    => !empty($item['id_piping_material']) ? $item['id_piping_material'] : null,
                            'item_type'             => $item['item_type'] ?? 'material',
                            'item_name'             => $item['item_name'] ?? 'Item',
                            'size'                  => $item['size'] ?? null,
                            'spec'                  => $item['spec'] ?? null,
                            'unit'                  => $item['unit'] ?? 'Pcs',
                            'input_length_meter'    => !empty($item['input_length_meter']) ? (float)$item['input_length_meter'] : null,
                            'length_per_unit'       => !empty($item['length_per_unit']) ? (float)$item['length_per_unit'] : 6.00,
                            'waste_percent'         => isset($item['waste_percent']) ? (float)$item['waste_percent'] : 0,
                            'calculated_qty'        => $qty,
                            'unit_price_hpp'        => $hpp,
                            'id_supplier'           => !empty($item['id_supplier']) ? $item['id_supplier'] : null,
                            'margin_type'           => $marginType,
                            'margin_value'          => $marginVal,
                            'unit_selling_price'    => $unitSell,
                            'total_hpp'             => $itemTotalHpp,
                            'total_selling_price'   => $itemTotalSell,
                            'notes'                 => $item['notes'] ?? null,
                            'sort_order'            => $itemIndex,
                        ]);

                        $sectionHpp += $itemTotalHpp;
                        $sectionSelling += $itemTotalSell;
                    }
                }

                $section->update([
                    'subtotal_hpp'           => $sectionHpp,
                    'subtotal_selling_price' => $sectionSelling,
                ]);

                $grandHpp += $sectionHpp;
                $grandSelling += $sectionSelling;
            }

            $rab->update([
                'id_client'           => $validated['id_client'] ?? null,
                'id_pic'              => $validated['id_pic'] ?? null,
                'id_sales'            => $validated['id_sales'] ?? $rab->id_sales,
                'project_name'        => $validated['project_name'],
                'location_plant'      => $validated['location_plant'] ?? null,
                'rab_date'            => $validated['rab_date'],
                'status'              => $validated['status'],
                'total_hpp'           => $grandHpp,
                'total_margin'        => $grandSelling - $grandHpp,
                'total_selling_price' => $grandSelling,
                'notes'               => $validated['notes'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('piping-rab.show', $rab->id)->with('success', 'RAB Piping berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update RAB: ' . $e->getMessage());
        }
    }

    public function revise($id)
    {
        $sourceRab = PipingRab::with(['sections.items'])->findOrFail($id);

        DB::beginTransaction();
        try {
            // Set all old versions to is_latest = false
            $rootId = $sourceRab->root_id ?: $sourceRab->id;
            PipingRab::where('id', $rootId)->orWhere('root_id', $rootId)->update(['is_latest' => false]);

            $newRevisionNumber = $sourceRab->revision_number + 1;
            $baseNo = preg_replace('/-R\d+$/', '', $sourceRab->no_rab);
            $newNoRab = $baseNo . '-R' . $newRevisionNumber;

            $newRab = $sourceRab->replicate();
            $newRab->no_rab = $newNoRab;
            $newRab->revision_number = $newRevisionNumber;
            $newRab->root_id = $rootId;
            $newRab->is_latest = true;
            $newRab->status = 'Draft';
            $newRab->converted_quotation_id = null;
            $newRab->created_at = now();
            $newRab->updated_at = now();
            $newRab->save();

            // Clone sections and items
            foreach ($sourceRab->sections as $sec) {
                $newSec = $sec->replicate();
                $newSec->id_piping_rab = $newRab->id;
                $newSec->save();

                foreach ($sec->items as $item) {
                    $newItem = $item->replicate();
                    $newItem->id_piping_rab_section = $newSec->id;
                    $newItem->save();
                }
            }

            DB::commit();
            return redirect()->route('piping-rab.edit', $newRab->id)->with('success', "Revisi baru ({$newNoRab}) berhasil dibuat. Silakan sesuaikan data.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat revisi: ' . $e->getMessage());
        }
    }

    public function convertToQuotation(Request $request, $id)
    {
        $rab = PipingRab::with(['client', 'pic', 'sections.items'])->findOrFail($id);

        $validated = $request->validate([
            'conversion_mode' => 'required|in:lumpsum,breakdown',
            'tax'             => 'required|in:1,0', // 1=PPN 11%, 0=Non PPN
            'validity'        => 'nullable|string',
            'payment'         => 'nullable|string',
            'delivery_process'=> 'nullable|string',
            'warranty'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate quotation number (Format Smart Quote Reftech)
            $year = date('Y');
            $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
            $month = $romanMonths[(int)date('m')];
            $count = UnitQuotation::whereYear('date', $year)->count() + 1;
            $noQuote = sprintf("%03d/Q-PIP/REF/%s/%s", $count, $month, $year);

            $subtotal = (float) $rab->total_selling_price;
            $hasTax = (bool) $validated['tax'];
            $taxAmount = $hasTax ? round($subtotal * 0.11) : 0;
            $grandTotal = $subtotal + $taxAmount;

            $quotation = UnitQuotation::create([
                'id_piping_rab'    => $rab->id,
                'revision_number'  => 0,
                'is_latest'        => true,
                'id_client'        => $rab->id_client,
                'id_pic'           => $rab->id_pic,
                'id_sales'         => $rab->id_sales ?: Auth::id(),
                'no_quote'         => $noQuote,
                'attn'             => $rab->pic ? $rab->pic->name : '-',
                'date'             => now()->toDateString(),
                'expired_date'     => now()->addDays(14)->toDateString(),
                'title'            => $rab->project_name . ($rab->location_plant ? ' (' . $rab->location_plant . ')' : ''),
                'hide_title'       => false,
                'type'             => 'Piping Project',
                'subtotal'         => $subtotal,
                'diskon'           => 0,
                'tax'              => $hasTax,
                'tax_amount'       => $taxAmount,
                'total'            => $grandTotal,
                'validity'         => $validated['validity'] ?? '14 (empat belas) hari kalender',
                'payment'          => $validated['payment'] ?? 'DP 30% saat PO, Pelunasan 70% setelah BAST',
                'delivery_process' => $validated['delivery_process'] ?? 'Estimasi 2-3 minggu setelah material siap di lokasi',
                'warranty'         => $validated['warranty'] ?? 'Garansi kebocoran & instalasi selama 6 bulan',
                'status'           => 'Draft',
            ]);

            $quotation->root_id = $quotation->id;
            $quotation->save();

            // Create Option 1
            $option = UnitQuotationOption::create([
                'id_unit_quotation' => $quotation->id,
                'title'             => 'Opsi 1 (Penawaran Utama)',
                'subtotal'          => $subtotal,
                'diskon'            => 0,
                'diskon_type'       => 'percent',
                'tax'               => $hasTax,
                'tax_amount'        => $taxAmount,
                'shipping'          => 0,
                'total'             => $grandTotal,
                'sort_order'        => 1,
            ]);

            if ($validated['conversion_mode'] === 'lumpsum') {
                // Mode Lump Sum per Section (Head Title Section -> Item Penawaran)
                $sort = 1;
                foreach ($rab->sections as $sec) {
                    $desc = trim($sec->section_name);
                    if (!str_starts_with(strtolower($desc), 'pekerjaan') && !str_starts_with(strtolower($desc), 'instalasi') && !str_starts_with(strtolower($desc), 'pengadaan') && !str_starts_with(strtolower($desc), 'supply')) {
                        $desc = "Pekerjaan " . $desc;
                    }
                    if ($rab->location_plant && !str_contains(strtolower($desc), strtolower($rab->location_plant))) {
                        $desc .= " (" . $rab->location_plant . ")";
                    }

                    UnitQuotationDetail::create([
                        'id_unit_quotation' => $quotation->id,
                        'id_option'         => $option->id,
                        'type'              => 'piping',
                        'label'             => 'Piping Installation Package',
                        'description'       => $desc,
                        'qty'               => 1,
                        'info_qty'          => 'Lot',
                        'price'             => $sec->subtotal_selling_price,
                        'disc'              => 0,
                        'amount'            => $sec->subtotal_selling_price,
                        'sort_order'        => $sort++,
                    ]);
                }
            } else {
                // Mode Breakdown Detail
                $sort = 1;
                foreach ($rab->sections as $sec) {
                    foreach ($sec->items as $item) {
                        $desc = $item->item_name;
                        if ($item->size) $desc .= ' ' . $item->size;
                        if ($item->spec) $desc .= ' (' . $item->spec . ')';
                        if ($item->input_length_meter) $desc .= ' [Total ' . $item->input_length_meter . ' Meter]';

                        UnitQuotationDetail::create([
                            'id_unit_quotation' => $quotation->id,
                            'id_option'         => $option->id,
                            'type'              => 'piping',
                            'label'             => ucfirst($item->item_type),
                            'description'       => $desc,
                            'qty'               => $item->calculated_qty,
                            'info_qty'          => $item->unit,
                            'price'             => $item->unit_selling_price,
                            'disc'              => 0,
                            'amount'            => $item->total_selling_price,
                            'sort_order'        => $sort++,
                        ]);
                    }
                }
            }

            // Update status RAB
            $rab->update([
                'status'                 => 'Converted',
                'converted_quotation_id' => $quotation->id,
            ]);

            DB::commit();
            return redirect()->route('unit-quotation.show', $quotation->id)
                ->with('success', "Berhasil convert RAB ke Smart Quote ({$noQuote})! Sales dapat menyesuaikan penawaran ini.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal convert ke Quotation: ' . $e->getMessage());
        }
    }
}
