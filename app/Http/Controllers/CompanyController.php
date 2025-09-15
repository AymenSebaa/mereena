<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Type;
use Illuminate\Http\Request;

class CompanyController extends Controller {

    // List all companies
    public function index() {
        $data['companies'] = Company::with('buses')->get();
        $data['types'] = Type::where('name', 'Vehicules')->first()->subTypes;

        return view('companies.index', $data);
    }

    public function live(Request $request) {
        $companies = Company::with('buses')->get();

        return response()->json($companies);
    }


    // Upsert company (create or update)
    public function upsert(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = Company::updateOrCreate(
            ['name' => $request->input('name')],
            []
        );

        return response()->json($company);
    }


    // Show single company with buses
    public function show(Company $company) {
        return $company->load('buses');
    }
}
