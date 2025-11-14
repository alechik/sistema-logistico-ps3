<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userTypes = UserType::all();
        return response()->json(['data' => $userTypes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:user_types',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userType = UserType::create([
            'name' => $request->name,
        ]);

        return response()->json(['data' => $userType], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserType $userType)
    {
        return response()->json(['data' => $userType], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserType $userType)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('user_types')->ignore($userType->id),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prevent updating default user types
        $defaultTypes = ['Administrator', 'Manager', 'Employee'];
        if (in_array($userType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden modificar los tipos de usuario predeterminados.'
            ], 422);
        }

        $userType->update([
            'name' => $request->name,
        ]);

        return response()->json(['data' => $userType], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserType $userType)
    {
        // Prevent deleting default user types
        $defaultTypes = ['Administrator', 'Manager', 'Employee'];
        if (in_array($userType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden eliminar los tipos de usuario predeterminados.'
            ], 422);
        }

        // Check if any user is using this user type
        if ($userType->users()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de usuario porque hay usuarios asignados a él.'
            ], 422);
        }

        $userType->delete();
        return response()->json(['message' => 'Tipo de usuario eliminado correctamente'], 200);
    }
}
