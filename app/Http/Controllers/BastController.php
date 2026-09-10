<?php

namespace App\Http\Controllers;

use App\Models\Bast;
use App\Models\BastUnit;
use App\Models\KanbanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BastController extends Controller
{
    protected function guard()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin', 'Accounting', 'Finance Manager'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index()
    {
        $this->guard();

        $basts = Bast::with(['creator', 'units'])
            ->orderByDesc('id')
            ->get();

        return view('pages.accounting.bast.index', compact('basts'));
    }

    public function store(Request $request)
    {
        $this->guard();

        $request->validate([
            'id_kanban_task' => 'nullable|integer',
            'id_quotation' => 'nullable|integer',
            'type' => 'nullable|in:Default,Rental',
            'entity' => 'required|in:Reftech,Kojisha',
            'customer_name' => 'required|string|max:255',
            'work_title' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'work_date' => 'nullable|date',
            'rental_start_date' => 'nullable|date',
            'rental_end_date' => 'nullable|date',
            'test_running_result' => 'nullable|string',
            'units' => 'nullable|array',
            'units.*.unit_name' => 'required_with:units|string|max:255',
            'units.*.serial_no' => 'nullable|string|max:255',
            'units.*.qty' => 'nullable|integer|min:1',
        ]);

        $bast = DB::transaction(function () use ($request) {
            $entity = $request->input('entity', 'Reftech') ?: 'Reftech';
            $bast = Bast::create([
                'no_bast' => $this->generateNoBast($entity),
                'type' => $request->input('type', 'Default') ?: 'Default',
                'id_kanban_task' => $request->id_kanban_task ?: null,
                'id_quotation' => $request->id_quotation ?: null,
                'entity' => $entity,
                'customer_name' => $request->customer_name,
                'work_title' => $request->work_title,
                'po_number' => $request->po_number,
                'work_date' => $request->work_date ?: null,
                'rental_start_date' => $request->input('type') === 'Rental' ? ($request->rental_start_date ?: null) : null,
                'rental_end_date' => $request->input('type') === 'Rental' ? ($request->rental_end_date ?: null) : null,
                'test_running_result' => $request->test_running_result,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->input('units', []) as $index => $row) {
                if (empty($row['unit_name'])) {
                    continue;
                }
                BastUnit::create([
                    'id_bast' => $bast->id,
                    'unit_name' => $row['unit_name'],
                    'serial_no' => $row['serial_no'] ?? null,
                    'qty' => $row['qty'] ?? 1,
                    'position' => $index,
                ]);
            }

            return $bast;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BAST ' . $bast->no_bast . ' berhasil dibuat.',
                'bast' => [
                    'id' => $bast->id,
                    'no_bast' => $bast->no_bast,
                    'type' => $bast->type,
                    'show_link' => route('bast.show', $bast->id),
                    'print_link' => route('bast.print', $bast->id),
                ],
            ]);
        }

        return redirect()->route('bast.index')->with('success', 'BAST ' . $bast->no_bast . ' berhasil dibuat.');
    }

    public function editData($id)
    {
        $this->guard();

        $bast = Bast::with('units')->findOrFail($id);

        return response()->json([
            'success' => true,
            'bast' => [
                'id' => $bast->id,
                'no_bast' => $bast->no_bast,
                'type' => $bast->type ?? 'Default',
                'entity' => $bast->entity,
                'customer_name' => $bast->customer_name,
                'work_title' => $bast->work_title,
                'po_number' => $bast->po_number,
                'work_date' => $bast->work_date ? $bast->work_date->format('Y-m-d') : null,
                'rental_start_date' => $bast->rental_start_date ? $bast->rental_start_date->format('Y-m-d') : null,
                'rental_end_date' => $bast->rental_end_date ? $bast->rental_end_date->format('Y-m-d') : null,
                'test_running_result' => $bast->test_running_result,
                'units' => $bast->units->map(function ($u) {
                    return [
                        'unit_name' => $u->unit_name,
                        'serial_no' => $u->serial_no,
                        'qty' => $u->qty,
                    ];
                }),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->guard();

        $bast = Bast::findOrFail($id);

        $request->validate([
            'type' => 'nullable|in:Default,Rental',
            'entity' => 'required|in:Reftech,Kojisha',
            'customer_name' => 'required|string|max:255',
            'work_title' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'work_date' => 'nullable|date',
            'rental_start_date' => 'nullable|date',
            'rental_end_date' => 'nullable|date',
            'test_running_result' => 'nullable|string',
            'units' => 'nullable|array',
            'units.*.unit_name' => 'required_with:units|string|max:255',
            'units.*.serial_no' => 'nullable|string|max:255',
            'units.*.qty' => 'nullable|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $bast) {
            $bastType = $request->input('type', $bast->type ?: 'Default');
            $bast->update([
                'type' => $bastType,
                'entity' => $request->entity,
                'customer_name' => $request->customer_name,
                'work_title' => $request->work_title,
                'po_number' => $request->po_number,
                'work_date' => $request->work_date ?: null,
                'rental_start_date' => $bastType === 'Rental' ? ($request->rental_start_date ?: null) : null,
                'rental_end_date' => $bastType === 'Rental' ? ($request->rental_end_date ?: null) : null,
                'test_running_result' => $request->test_running_result,
            ]);

            $bast->units()->delete();
            foreach ($request->input('units', []) as $index => $row) {
                if (empty($row['unit_name'])) {
                    continue;
                }
                BastUnit::create([
                    'id_bast' => $bast->id,
                    'unit_name' => $row['unit_name'],
                    'serial_no' => $row['serial_no'] ?? null,
                    'qty' => $row['qty'] ?? 1,
                    'position' => $index,
                ]);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'BAST ' . $bast->no_bast . ' berhasil diperbarui.',
            ]);
        }

        return redirect()->route('bast.index')->with('success', 'BAST ' . $bast->no_bast . ' berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $this->guard();

        $bast = Bast::findOrFail($id);
        $bast->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'BAST berhasil dihapus.']);
        }

        return redirect()->route('bast.index')->with('success', 'BAST berhasil dihapus.');
    }

    public function show($id)
    {
        $this->guard();

        $bast = Bast::with(['creator', 'units', 'kanbanTask.pendingPo'])->findOrFail($id);

        return view('pages.accounting.bast.show', compact('bast'));
    }

    public function print($id)
    {
        $this->guard();

        $bast = Bast::with(['units', 'kanbanTask.pendingPo'])->findOrFail($id);

        return view('pages.accounting.bast.print', compact('bast'));
    }

    public function handSign(Request $request, $id)
    {
        $this->guard();

        $bast = Bast::findOrFail($id);
        if ($bast->entity === 'Kojisha') {
            $bast->sign = 'asset/sign/kojisha-nm.jpeg';
        } else {
            $bast->sign = 'asset/sign/reftech-nm.jpeg';
        }
        $bast->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hand sign berhasil ditambahkan pada BAST.',
                'sign' => asset($bast->sign),
            ]);
        }

        return back()->with('success', 'Hand sign berhasil ditambahkan.');
    }

    public function deleteHandSign(Request $request, $id)
    {
        $this->guard();

        $bast = Bast::findOrFail($id);
        $bast->sign = null;
        $bast->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hand sign berhasil dihapus dari BAST.',
            ]);
        }

        return back()->with('success', 'Hand sign berhasil dihapus.');
    }

    private function generateNoBast(string $entity = 'Reftech'): string
    {
        $year = now()->year;
        $code = ($entity === 'Kojisha') ? 'KII' : 'RJO';
        $last = Bast::where('no_bast', 'like', '%/BAST/' . $code . '/' . $year)
            ->orderByDesc('id')
            ->first();

        $seq = 1;
        if ($last && preg_match('/^(\d+)\//', $last->no_bast, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return str_pad($seq, 3, '0', STR_PAD_LEFT) . '/BAST/' . $code . '/' . $year;
    }
}
