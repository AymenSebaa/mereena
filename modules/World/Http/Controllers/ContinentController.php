<?php

namespace Modules\World\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\World\Models\Continent;

class ContinentController extends BaseCrudController {
    protected string $modelClass = Continent::class;
    protected string $viewPrefix = 'world::continents';
    protected array $searchable = ['name'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    protected function label(): string {
        return 'Continent';
    }
}
