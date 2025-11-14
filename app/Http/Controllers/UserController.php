<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('userType')->get();
        return response()->json(['data' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type_id' => ['required', 'exists:user_types,id'],
            'status' => 'boolean',
            'lastname' => 'nullable|string|max:255',
            'cellphone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type_id' => $request->user_type_id,
            'status' => $request->status ?? true,
            'cellphone' => $request->cellphone,
        ]);

        return response()->json(['data' => $user->load('userType')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json(['data' => $user->load('userType')], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'user_type_id' => 'sometimes|required|exists:user_types,id',
            'status' => 'sometimes|boolean',
            'lastname' => 'nullable|string|max:255',
            'cellphone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->only([
            'name', 'email', 'user_type_id', 'status', 'lastname', 'cellphone'
        ]);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['data' => $user->load('userType')], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the last admin
        if ($user->userType->name === 'Administrator' && 
            User::whereHas('userType', function($q) {
                $q->where('name', 'Administrator');
            })->count() <= 1) {
            return response()->json([
                'message' => 'No se puede eliminar el último administrador.'
            ], 422);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}
