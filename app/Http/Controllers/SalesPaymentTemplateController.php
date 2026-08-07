<?php

namespace App\Http\Controllers;

use App\Models\SalesPaymentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesPaymentTemplateController extends Controller
{
    /**
     * Get all payment templates for the current logged-in sales user.
     */
    public function index(Request $request)
    {
        $salesId = Auth::id();
        $templates = SalesPaymentTemplate::with('client')
            ->where('id_sales', $salesId)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $templates,
        ]);
    }

    /**
     * Store a new payment template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'payment_term' => 'required|string',
            'client_ids'   => 'nullable|array',
            'is_default'   => 'nullable|boolean',
        ]);

        $salesId   = Auth::id();
        $isDefault = $request->boolean('is_default');
        $clientIds = is_array($request->client_ids) ? array_map('intval', array_filter($request->client_ids)) : [];

        if ($isDefault) {
            SalesPaymentTemplate::where('id_sales', $salesId)->update(['is_default' => false]);
        }

        $template = SalesPaymentTemplate::create([
            'id_sales'     => $salesId,
            'id_client'    => count($clientIds) > 0 ? $clientIds[0] : null,
            'client_ids'   => count($clientIds) > 0 ? array_values($clientIds) : null,
            'name'         => $request->name,
            'payment_term' => $request->payment_term,
            'is_default'   => $isDefault,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment template berhasil dibuat.',
            'data'    => $template->load('client'),
        ]);
    }

    /**
     * Update an existing payment template.
     */
    public function update(Request $request, $id)
    {
        $template = SalesPaymentTemplate::where('id_sales', Auth::id())->findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'payment_term' => 'required|string',
            'client_ids'   => 'nullable|array',
            'is_default'   => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');
        $clientIds = is_array($request->client_ids) ? array_map('intval', array_filter($request->client_ids)) : [];

        if ($isDefault) {
            SalesPaymentTemplate::where('id_sales', Auth::id())->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $template->update([
            'id_client'    => count($clientIds) > 0 ? $clientIds[0] : null,
            'client_ids'   => count($clientIds) > 0 ? array_values($clientIds) : null,
            'name'         => $request->name,
            'payment_term' => $request->payment_term,
            'is_default'   => $isDefault,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment template berhasil diperbarui.',
            'data'    => $template->load('client'),
        ]);
    }

    /**
     * Delete a payment template.
     */
    public function destroy($id)
    {
        $template = SalesPaymentTemplate::where('id_sales', Auth::id())->findOrFail($id);
        $template->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment template berhasil dihapus.',
        ]);
    }

    /**
     * Set a template as default.
     */
    public function setDefault($id)
    {
        $salesId = Auth::id();
        SalesPaymentTemplate::where('id_sales', $salesId)->update(['is_default' => false]);

        $template = SalesPaymentTemplate::where('id_sales', $salesId)->findOrFail($id);
        $template->update(['is_default' => true]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Template default berhasil diubah.',
        ]);
    }
}
