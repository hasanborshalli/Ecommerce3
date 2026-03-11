<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountDashboardController extends Controller
{
    // ── Dashboard overview ────────────────────────────────────

    public function index()
    {
        $customer = $this->customer();

        $totalOrders    = Order::where('customer_id', $customer->id)->count();
        $totalSpent     = Order::where('customer_id', $customer->id)
                            ->whereIn('status', ['delivered','shipped','processing','confirmed','pending'])
                            ->sum('total');
        $totalAddresses = CustomerAddress::where('customer_id', $customer->id)->count();
        $recentOrders   = Order::where('customer_id', $customer->id)
                            ->with('items')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('account.dashboard', compact(
            'customer', 'totalOrders', 'totalSpent', 'totalAddresses', 'recentOrders'
        ));
    }

    // ── Order history ─────────────────────────────────────────

    public function orders()
    {
        $customer = $this->customer();

        $orders = Order::where('customer_id', $customer->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('customer', 'orders'));
    }

    public function orderDetail(Order $order)
    {
        // Ensure the order belongs to this customer
        if ($order->customer_id !== session('customer_id')) {
            abort(403);
        }

        $order->load(['items.product']);
        $customer = $this->customer();

        return view('account.order-detail', compact('order', 'customer'));
    }

    // ── Addresses ─────────────────────────────────────────────

    public function addresses()
    {
        $customer  = $this->customer();
        $addresses = CustomerAddress::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get();

        return view('account.addresses', compact('customer', 'addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $this->validateAddress($request);
        $customerId = session('customer_id');

        // If first address or flagged as default, unset others first
        $isFirst = !CustomerAddress::where('customer_id', $customerId)->exists();
        if ($isFirst || $request->boolean('is_default')) {
            CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        CustomerAddress::create(array_merge($validated, ['customer_id' => $customerId]));

        return redirect()->route('account.addresses')
            ->with('success', 'Address saved.');
    }

    public function updateAddress(Request $request, CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        $validated = $this->validateAddress($request);

        if ($request->boolean('is_default')) {
            CustomerAddress::where('customer_id', $address->customer_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $address->update($validated);

        return redirect()->route('account.addresses')
            ->with('success', 'Address updated.');
    }

    public function destroyAddress(CustomerAddress $address)
    {
        $this->authorizeAddress($address);

        if ($address->is_default) {
            // Promote next address to default
            CustomerAddress::where('customer_id', $address->customer_id)
                ->where('id', '!=', $address->id)
                ->oldest()
                ->first()
                ?->update(['is_default' => true]);
        }

        $address->delete();

        return redirect()->route('account.addresses')
            ->with('success', 'Address removed.');
    }

    public function setDefaultAddress(CustomerAddress $address)
    {
        $this->authorizeAddress($address);
        $address->makeDefault();

        return redirect()->route('account.addresses')
            ->with('success', 'Default address updated.');
    }

    // ── Profile ───────────────────────────────────────────────

    public function profile()
    {
        $customer = $this->customer();
        return view('account.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->customer();
        $action   = $request->input('action', 'profile');

        if ($action === 'password') {
            return $this->changePassword($request, $customer);
        }

        // Personal details
        $validated = $request->validate([
            'first_name' => 'required|string|max:60',
            'last_name'  => 'required|string|max:60',
            'email'      => 'required|email|max:150|unique:customers,email,' . $customer->id,
            'phone'      => 'nullable|string|max:30',
        ]);

        $customer->update($validated);

        // Refresh session data
        session([
            'customer_name'       => $customer->fresh()->full_name,
            'customer_first_name' => $customer->fresh()->first_name,
            'customer_email'      => $customer->fresh()->email,
            'customer_phone'      => $customer->fresh()->phone,
        ]);

        return redirect()->route('account.profile')
            ->with('success', 'Profile updated.');
    }

    private function changePassword(Request $request, Customer $customer): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $customer->update(['password' => Hash::make($request->new_password)]);

        return redirect()->route('account.profile')
            ->with('success', 'Password changed successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────

    private function customer(): Customer
    {
        return Customer::findOrFail(session('customer_id'));
    }

    private function authorizeAddress(CustomerAddress $address): void
    {
        if ($address->customer_id !== session('customer_id')) {
            abort(403);
        }
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label'         => 'nullable|string|max:50',
            'full_name'     => 'nullable|string|max:100',
            'phone'         => 'nullable|string|max:30',
            'address_line1' => 'required|string|max:150',
            'address_line2' => 'nullable|string|max:150',
            'city'          => 'required|string|max:100',
            'state'         => 'nullable|string|max:100',
            'postal_code'   => 'nullable|string|max:20',
            'country'       => 'nullable|string|max:80',
            'is_default'    => 'nullable|boolean',
        ]);
    }
}
