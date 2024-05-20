<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
//use Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Laravel\Passport\Token;


class RegisterController extends ResponseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lastnameF' => 'required',
            'lastnameM' => 'required',
            'address' => 'required',
            'email' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'phoneNum' => 'required',
            'payMeth' => 'required',
            'role' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'Validation Error',
                $validator->errors()
            );
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $input['c_password'] = bcrypt($input['c_password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] = $user->name;
        $success['role'] = $user->role;
        $success['id'] = $user->id;

        return $this->sendResponse($success, 'User register succesfully.');
    }

    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('token')->accessToken;
            $success['name'] = $user->name;
            $success['role'] = $user->role;
            $success['id'] = $user->id;
            return response()->json([
                'token' => $token,
                'name' => $user->name,
                'role' => $user->role,
                'id' => $user->id,
                'messagge' => 'Login succesful'
            ], 200);
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::user()->token()->revoke();
        return ('Su sesión ha expirado con exito');
    }

    public function admin()
    {
        $user = Auth::user();
        if ($user->role == 1) {
            return ("Eres un admin");
        } else {
            return ("No eres admin, fuchi wakale");
        }
    }
}
