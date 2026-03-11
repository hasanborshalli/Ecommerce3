<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::withCount('uses');

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'all'      => Coupon::count(),
            'active'   => Coupon::where('is_active', true)->count(),
            'expired'  => Coupon::where('expires_at', '<', now())->count(),
        ];

        return view('admin.coupons.index', compact('coupons', 'counts'));
    }

    public function create()
    {
        return view('admin.coupons.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);
        Coupon::create($validated);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon->id);
        $coupon->update($validated);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    private function validateCoupon(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'code'              => 'required|string|max:50|unique:coupons,code' . ($excludeId ? ",{$excludeId}" : ''),
            'type'              => 'required|in:percentage,fixed,free_shipping',
            'value'             => 'required_unless:type,free_shipping|nullable|numeric|min:0',
            'min_order_amount'   => 'nullable|numeric|min:0',
            'max_uses'          => 'nullable|integer|min:1',
            'expires_at'        => 'nullable|date',
            'is_active'         => 'nullable|boolean',
            'description'       => 'nullable|string|max:200',
        ]);

        $data['code']      = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);
        if ($data['type'] === 'free_shipping') {
            $data['value'] = 0;
        }

        return $data;
    }
}
