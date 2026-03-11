<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchaseOrders')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:150',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:300',
            'notes'        => 'nullable|string|max:500',
            'is_active'    => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier added.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:150',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:300',
            'notes'        => 'nullable|string|max:500',
            'is_active'    => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->count() > 0) {
            return back()->with('error', 'Cannot delete — supplier has purchase orders.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted.');
    }
}
