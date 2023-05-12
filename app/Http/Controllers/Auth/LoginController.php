<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::whereEmail($request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ["The credential are incorrect"],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('web-token')->plainTextToken;

        // return [
        //     "user" => $user,
        //     "token" => $token->plainTextToken
        // ];

        return (new UserResource($user))->additional(compact('token'));
    }
}
