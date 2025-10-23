<?php
namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{

    //--------------- Function Login ----------------\\

    public function getAccessToken(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = request(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $userStatus = Auth::User()->statut;
            if ($userStatus === 0) {
                return response()->json([
                    'message' => 'This user not active',
                    'status' => 'NotActive',
                ]);
            }

        } else {
            return response()->json([
                'message' => 'Incorrect Login',
                'status' => false,
            ]);
        }

        $user = auth()->user();
        $tokenResult = $user->createToken('Access Token');
        $token = $tokenResult->token;
        $this->setCookie('Stocky_token', $tokenResult->accessToken);

        $tmp = Auth::User();

        $first_role = $tmp->roles->first();
        $permissions = [];
        if ($first_role && $first_role->permissions)
            $permissions = $first_role->permissions->pluck('name');

        return response()->json([
            'Stocky_token' => $tokenResult->accessToken,
            'warehouse' => $tmp->warehouse,
            'username' => $tmp->username,
            'email' => $tmp->email,
            'id' => $tmp->id,
            'role_id' => $tmp->role_id,
            'role_name' => $first_role->name,
            'status' => true,
            'permissions' => $permissions,
        ]);
    }

    //--------------- Function Logout ----------------\\

    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user()->token();
            $user->revoke();
            $this->destroyCookie('Stocky_token');
            return response()->json('success');
        }

    }

}
