<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $suppliers = Supplier::when($search, function ($q, $s) {
            $q->where('nama_supplier', 'like', "%{$s}%")
              ->orWhere('telepon', 'like', "%{$s}%");
        })->latest()->paginate(10);

        $totalSupplier = Supplier::count();

        return view('supplier.index', compact('suppliers', 'search', 'totalSupplier'));
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('supplier')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('supplier')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('supplier')->with('success', 'Supplier berhasil dihapus.');
    }
}
