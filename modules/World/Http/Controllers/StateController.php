<?php

namespace Modules\World\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\World\Models\State;

class StateController extends BaseCrudController {
    protected string $modelClass = State::class;
    protected string $viewPrefix = 'world::states';
    protected array $with = ['country.region.continent'];
    protected array $searchable = ['name', 'iso2'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        return [
            'name'       => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'iso2'       => 'nullable|string|max:10',
            'lat'        => 'nullable|numeric',
            'lng'        => 'nullable|numeric',
        ];
    }

    protected function label(): string {
        return 'State';
    }
}
