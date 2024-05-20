<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use App\Models\Supplier;

class SupplierController extends ResponseController
{
    public function getAll()
    {
        $supplier = Supplier::all();
        return $this->sendResponse($supplier, 'Suppliers.');
    }

    public function create(Request $request)
    {
        $supplier = Supplier::create([
            'name' => $request->name,
            'address' => $request->address,
            'phoneNum' => $request->phoneNum,

        ]);
        $supplier->save();
        return $this->sendResponse($supplier, 'Supplier register succesfully.');
    }

    public function byId($supplierid)
    {
        $supplier = Supplier::where('id', $supplierid)->first();
        return $this->sendResponse($supplier, 'Supplier data.');
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $supplier = Supplier::find($request->id);
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->phoneNum = $request->phoneNum;

        $supplier->save();
        return $request;
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::where('id', $id)
            ->delete();
        return $this->sendResponse($id, 'Supplier drop');
    }
}
