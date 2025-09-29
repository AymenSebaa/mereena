<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Models\Invoice;

class InvoiceController extends Controller {
    public function index() {
        $invoices = Invoice::all();
        return view('saas::invoices.index', compact('invoices'));
    }

    public function upsert(Request $request) {
        $id = $request->id;
        
        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'amount'          => 'required|numeric|min:0',
            'status'          => 'required|string|max:50',
            'due_date'        => 'nullable|date',
        ]);

        $invoice = Invoice::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'result' => true,
            'message' => $id ? 'Invoice updated successfully' : 'Invoice created successfully',
            'data' => $invoice,
        ]);
    }

    public function delete($id) {
        if ($invoice = Invoice::find($id)) $invoice->delete();

        return response()->json(['result' => true, 'message' => 'Invoice deleted']);
    }

}
