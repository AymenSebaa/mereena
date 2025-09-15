<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller {
    // List all roles
    public function index() {
        return response()->json(Role::all());
    }

    // Show one role
    public function show($id) {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    // Create new role
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return response()->json($role, 201);
    }

    // Update role
    public function update(Request $request, $id) {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    // Delete role
    public function destroy($id) {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted']);
    }
}
