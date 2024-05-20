<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class UserController extends ResponseController
{
    
    public function index()
    {
        $users = DB::table('users')->get();
 
        return $users;
    }

    public function getAll()
    {
        $users = User::all();
        
        //consultar bd, traer productos y regresarlos
        return $this->sendResponse($users,'User register succesfully.');

    }

    public function create(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'lastnameF' => $request->lastnameF,
            'lastnameM' => $request->lastnameM,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'c_password' => Hash::make($request->c_password),
            'phoneNum' => $request->phoneNum,
            'payMeth' => $request->payMeth,
            'role' => $request->role,
        ]);
        return $this->sendResponse($user, 'User registered successfully.');

    }

    public function byId($userId)
    {
        $user = User::find($userId);
        return $this->sendResponse($user, 'User register successfully.');

    }


    public function update(Request $request)
    {
        $userId = $request->id;
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        $user->fill($request->except('password', 'c_password'));
        
        if ($request->filled('password') && $request->password !== $user->password) {
            $user->password = Hash::make($request->password);
        }
    
        if ($request->filled('c_password') && $request->c_password !== $user->c_password) {
            $user->c_password = Hash::make($request->c_password);
        }
        
        $user->phoneNum = $request->phoneNum;
        $user->payMeth = $request->payMeth;
        $user->role = $request->role;
        
        try {
            $user->save();
            return response()->json(['message' => 'Usuario actualizado con éxito', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el usuario', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $user = User::where('id', $id)
        ->delete();
        return $this->sendResponse($id,'Deleted');
    }

    // public function token(){
    //     return csrf_token();
    // }
}
