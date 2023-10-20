<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $token = auth('api')->attempt($request->all('email','password')); // do guards em Config\auth.php
        if($token)
            return response()->json(['token' => $token]);
        else
            return response()->json(['erro' => 'Utilizador ou password inválidos'],403);

    }
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Efetuado o logout com sucesso']);
    }

    public function refresh()
    {
        return response()->json(['token' => auth('api')->refresh()]);
    }
    public function me()
    {
        return response()->json(auth()->user());
    }
}
