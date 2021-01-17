<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends ApiController
{
    public function user(Request $request)
    {
        $user = $this->guard()->user();

        return response()->json($user);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw new UnauthorizedHttpException('auth', 'errors.auth.invalid_data');
        }

        $permissions = [
            'roles'  => $user->roles()->pluck('name'),
            'rights' => $user->permissions()->pluck('name')
        ];

        return [
            'token' => $user->createToken('login')->plainTextToken,
            'permissions' => $permissions
        ];
    }

    public function guard()
    {
        return Auth::guard();
    }
}
