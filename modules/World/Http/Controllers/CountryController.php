<?php

namespace Modules\World\Http\Controllers;

use App\Http\Controllers\BaseCrudController;
use Modules\World\Models\Country;

class CountryController extends BaseCrudController {
    protected string $modelClass = Country::class;
    protected string $viewPrefix = 'world::countries';
    protected array $with = ['region.continent'];
    protected array $searchable = ['name', 'iso2', 'iso3', 'phone_code', 'currency'];
    protected array $orderBy = ['name' => 'asc'];

    protected function rules(): array {
        return [
            'name'       => 'required|string|max:255',
            'region_id'  => 'required|exists:regions,id',
            'iso2'       => 'required|string|size:2',
            'iso3'       => 'required|string|size:3',
            'phone_code' => 'nullable|string',
            'currency'   => 'nullable|string',
            'emoji'      => 'nullable|string|max:4',
            'lat'        => 'nullable|numeric',
            'lng'        => 'nullable|numeric',
        ];
    }

    protected function label(): string {
        return 'Country';
    }
}
