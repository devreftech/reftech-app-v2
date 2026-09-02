<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Schematic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchematicController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $clientId = $request->query('client_id');
        $status = $request->query('status');

        $query = Schematic::with(['client', 'creator'])
            ->orderBy('updated_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('schematic_number', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('company', 'like', "%{$search}%");
                  });
            });
        }

        if ($type) {
            $query->where('diagram_type', $type);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $schematics = $query->paginate(12)->withQueryString();

        $stats = [
            'total'         => Schematic::count(),
            'refrigeration' => Schematic::where('diagram_type', 'Refrigeration System')->count(),
            'compressed'    => Schematic::where('diagram_type', 'Compressed Air System')->count(),
            'pid'           => Schematic::where('diagram_type', 'Piping & Instrumentation (P&ID)')->count(),
        ];

        $clients = Client::orderBy('company')->get(['id', 'company']);

        return view('pages.schematics.index', compact('schematics', 'stats', 'search', 'type', 'clientId', 'status', 'clients'));
    }

    public function create()
    {
        $schematicNumber = Schematic::generateNumber();
        $clients = Client::orderBy('company')->get(['id', 'company']);
        $schematic = new Schematic([
            'schematic_number' => $schematicNumber,
            'title' => 'Skematik Proyek Baru',
            'diagram_type' => 'Refrigeration System',
            'status' => 'Draft',
        ]);

        return view('pages.schematics.editor', compact('schematic', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'schematic_number' => 'nullable|string|max:50',
            'client_id'        => 'nullable|exists:client,id',
            'project_name'     => 'nullable|string|max:255',
            'diagram_type'     => 'required|string|max:100',
            'canvas_data'      => 'nullable|string',
            'preview_image'    => 'nullable|string',
            'description'      => 'nullable|string',
            'status'           => 'nullable|string|max:50',
        ]);

        if (empty($validated['schematic_number'])) {
            $validated['schematic_number'] = Schematic::generateNumber();
        }

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        $schematic = Schematic::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Skematik diagram berhasil disimpan!',
                'redirect_url' => route('schematics.edit', $schematic->id),
                'schematic' => $schematic,
            ]);
        }

        return redirect()->route('schematics.edit', $schematic->id)
            ->with('success', 'Skematik diagram berhasil disimpan!');
    }

    public function edit($id)
    {
        $schematic = Schematic::with(['client', 'creator'])->findOrFail($id);
        $clients = Client::orderBy('company')->get(['id', 'company']);

        return view('pages.schematics.editor', compact('schematic', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $schematic = Schematic::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'client_id'        => 'nullable|exists:client,id',
            'project_name'     => 'nullable|string|max:255',
            'diagram_type'     => 'required|string|max:100',
            'canvas_data'      => 'nullable|string',
            'preview_image'    => 'nullable|string',
            'description'      => 'nullable|string',
            'status'           => 'nullable|string|max:50',
        ]);

        $validated['updated_by'] = Auth::id();

        $schematic->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perubahan skematik diagram berhasil disimpan!',
                'schematic' => $schematic,
            ]);
        }

        return redirect()->back()->with('success', 'Perubahan skematik diagram berhasil disimpan!');
    }

    public function destroy($id)
    {
        $schematic = Schematic::findOrFail($id);
        $schematic->delete();

        return redirect()->route('schematics.index')
            ->with('success', 'Skematik diagram berhasil dihapus.');
    }

    public function duplicate($id)
    {
        $original = Schematic::findOrFail($id);
        
        $clone = $original->replicate();
        $clone->schematic_number = Schematic::generateNumber();
        $clone->title = $original->title . ' (Copy)';
        $clone->status = 'Draft';
        $clone->created_by = Auth::id();
        $clone->updated_by = Auth::id();
        $clone->save();

        return redirect()->route('schematics.edit', $clone->id)
            ->with('success', 'Skematik diagram berhasil diduplikasi!');
    }
}
