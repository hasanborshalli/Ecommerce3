<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders')->withSum('orders as total_spent', 'total');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name',  'like', "%{$s}%")
                  ->orWhere('email',       'like', "%{$s}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $totalCustomers     = Customer::count();
        $newThisMonth       = Customer::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $customersWithOrders = Customer::has('orders')->count();

        return view('admin.customers.index', compact(
            'customers', 'totalCustomers', 'newThisMonth', 'customersWithOrders'
        ));
    }

    public function show(Customer $customer)
    {
        $customer->load('addresses');

        $orders = Order::where('customer_id', $customer->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalSpent    = Order::where('customer_id', $customer->id)->where('status', '!=', 'cancelled')->sum('total');
        $totalOrders   = Order::where('customer_id', $customer->id)->count();
        $reviews       = $customer->reviews()->with('product')->latest()->get();

        return view('admin.customers.show', compact(
            'customer', 'orders', 'totalSpent', 'totalOrders', 'reviews'
        ));
    }
}
