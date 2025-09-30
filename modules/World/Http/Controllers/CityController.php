<?php

namespace Modules\World\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\World\Models\City;

class CityController extends BaseCrudController {
    protected string $modelClass = City::class;
    protected string $viewPrefix = 'world::cities';
    protected array $with = ['state.country.region.continent'];
    protected array $searchable = ['name', 'zip_code'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        return [
            'name'     => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'zip_code' => 'nullable|string|max:20',
            'lat'      => 'nullable|numeric',
            'lng'      => 'nullable|numeric',
        ];
    }

    protected function label(): string {
        return 'City';
    }
}
