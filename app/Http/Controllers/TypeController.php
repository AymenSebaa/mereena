<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller {
	public function index(Request $request) {
		$type_id = $request->id;

		// Load subtypes count automatically
		$type = Type::with(['subTypes', 'parent'])
			->withCount('subTypes')
			->find($type_id);

		$data['type'] = $type;

		$data['types'] = $type
			? $type->subTypes()->withCount('subTypes')->get()
			: Type::with(['subTypes', 'parent'])
			->withCount('subTypes')
			->whereNull('type_id')
			->get();

		return view('types.index', $data);
	}

	public function upsert(Request $request) {
		$validator = Validator::make($request->all(), [
			'name' => ['required', 'string', 'max:255'],
		]);
		if ($validator->fails()) {
			return response()->json(['errors' => $validator->messages()], 422);
		}

		if ($request->id) {
			$type = Type::findOrFail($request->id);
			$message = 'Update Successfully';
		} else {
			$type = new Type();
			$message = 'Saved Successfully';
		}

		$type->type_id = v($request->type_id);
		$type->status = v($request->status);
		$type->name = v($request->name);
		$type->save();

		session()->flash('success', $message);
		return response()->json(['result' => $type]);
	}

	public function delete($id) {
		$type = Type::find($id);
		if ($type) {
			$type->delete();
		}
		return back()->with('success', 'Delete successfully');
	}
}
