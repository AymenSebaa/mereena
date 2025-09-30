<?php

namespace Modules\World\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\World\Models\Region;

class RegionController extends BaseCrudController {
    protected string $modelClass = Region::class;
    protected string $viewPrefix = 'world::regions';
    protected array $with = ['continent'];
    protected array $searchable = ['name', 'm49_code'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        return [
            'name'         => 'required|string|max:255',
            'm49_code'     => 'required|integer',
            'continent_id' => 'required|exists:continents,id',
        ];
    }

    protected function label(): string {
        return 'Region';
    }
}
