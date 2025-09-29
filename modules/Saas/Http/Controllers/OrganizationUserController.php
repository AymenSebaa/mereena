<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Models\OrganizationUser;

class OrganizationUserController extends Controller {
    public function index() {
        $orgUsers = OrganizationUser::with(['organization', 'user'])->get();
        return view('saas::organization_users.index', compact('orgUsers'));
    }

    public function upsert(Request $request) {
        $id = $request->id;

        $validated = $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'user_id'         => 'required|exists:users,id',
            'role'            => 'required|string|max:50',
        ]);

        $orgUser = OrganizationUser::updateOrCreate(['id' => $id], $validated);

        return response()->json([
            'result' => true,
            'message' => $id ? 'Organization User updated successfully' : 'Organization User created successfully',
            'data' => $orgUser,
        ]);
    }

    public function delete($id) {
        if ($orgUser = OrganizationUser::find($id)) $orgUser->delete();

        return response()->json(['result' => true, 'message' => 'Organization User deleted']);
    }
}
