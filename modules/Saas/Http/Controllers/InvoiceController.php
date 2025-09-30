<?php

namespace Modules\Saas\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\Saas\Models\Invoice;

class InvoiceController extends BaseCrudController {
    protected string $modelClass = Invoice::class;
    protected string $viewPrefix = 'saas::invoices';

    protected function rules(): array {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'amount'          => 'required|numeric|min:0',
            'status'          => 'required|string|max:50',
            'due_date'        => 'nullable|date',
        ];
    }

    protected function label(): string {
        return 'Invoice';
    }
}
