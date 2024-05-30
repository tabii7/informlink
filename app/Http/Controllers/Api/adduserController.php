<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class adduserController extends Controller
{
    public function adduser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'required|unique:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return response()->json(['success' => 'User added successfully.', 'user' => $user], 201);
    }
}
