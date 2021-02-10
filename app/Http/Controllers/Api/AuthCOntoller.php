<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthCOntoller extends Controller
{
    public function register(Request $req){

    $validate = Validator::make($req->all(),[
    'name' => 'required|max:255',
    'email' => 'required|email:unique',
    'password' => 'required|confirmed|min:6'
    ]);

    $user = User::create([
    'name' => $req->name,
    'email' => $req->email,
    'password' => Hash::make($req->password)
    ]);
    \auth()->attempt($req->only('email','password'));

    return $this->getToken($user);
    }

    public function getToken(User $user){
    $tokenResult = $user->createToken("Access Token");
    $token =$tokenResult->token;
    $token->expires_at = Carbon::now()->addWeeks(1);
    $token->save();

    return \response()->json([
        'success' => true,
        'accessToken' =>$tokenResult->accessToken,
        'tokenType' => 'Bearer',
        'ExpiredToken' => Carbon::parse($token->expires_at)->toDateTimeString(),
        'message' => 'authorize'
    ],200);


    }
}
