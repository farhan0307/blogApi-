<?php

namespace App\Http\Controllers;

use App\Http\Requests\registerRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    //
    
    public function UserRegistration(registerRequest $request)
    {
       
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => "user",
            'status' => $request->input('status')
        ]);
        return response()->json(["user" => $user, 'message' => 'User registered'], 201);
    }
   
    public function UserLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');
        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $user = auth()->user();
        $token = $user->createToken('Token')->plainTextToken;  // token
       
        return response()->json(['user' => $user, 'access_token' => $token]);
    }
    

    public function verifyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "User Not Registerd"], 404);
        }

        if ($user->role == "admin") {
            return response()->json(["error" => "Can't change this data"], 403);
        }

        if (!$user) {
            return response()->json(["error" => "No User"], 404);
        }
        if ($user->status == "approved") {
            return response()->json(["error" => "Already verify"], 400);
        }
        $user->status = "approved";
        $user->save();
        return response()->json(["message" => "User verfied"]);
    }
}
