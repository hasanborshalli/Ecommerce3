<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    // ── Shared date range helper ─────────────────────────────────────

    private function dateRange(Request $request): array
    {
        $period = $request->get('period', '30');
        $from   = $request->filled('from')
            ? $request->from
            : now()->subDays((int) $period)->format('Y-m-d');
        $to     = $request->filled('to')
            ? $request->to
            : now()->format('Y-m-d');

        return [$from, $to, $period];
    }

    // ── Page views ───────────────────────────────────────────────────

    public function index()
    {
        return redirect()->route('admin.reports.sales');
    }

    public function sales(Request $request)
    {
        [$from, $to, $period] = $this->dateRange($request);

        $base = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        $totalOrders = $base->clone()->count();
        $summary = [
            'revenue'   => (float) $base->clone()->sum('total'),
            'orders'    => $totalOrders,
            'profit'    => (float) DB::table('orders')->where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->selectRaw('SUM(`total` - cost_total) as profit_sum')->value('profit_sum'),
            'avg_order' => $totalOrders > 0
                ? round((float) $base->clone()->sum('total') / $totalOrders, 2)
                : 0,
        ];

        $daily = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(`total`) as revenue'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(`total` - cost_total) as profit')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->toBase()
            ->get();

        return view('admin.reports.sales',
            compact('summary', 'daily', 'from', 'to', 'period'));
    }

    public function products(Request $request)
    {
        [$from, $to, $period] = $this->dateRange($request);

        $topProducts = OrderItem::select(
                'product_id', 'product_name',
                DB::raw('SUM(quantity) as units_sold'),
                DB::raw('SUM(line_total) as revenue'),
                DB::raw('SUM(line_cost) as cost'),
                DB::raw('SUM(line_profit) as profit')
            )
            ->whereHas('order', fn($q) => $q
                ->where('status', '!=', 'cancelled')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
            )
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reports.products',
            compact('topProducts', 'from', 'to', 'period'));
    }

    public function categories(Request $request)
    {
        [$from, $to, $period] = $this->dateRange($request);

        $catData = Category::with(['products' => function ($q) use ($from, $to) {
            $q->withSum(['orderItems as revenue' => function ($q2) use ($from, $to) {
                $q2->whereHas('order', fn($oq) => $oq
                    ->where('status', '!=', 'cancelled')
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                );
            }], 'line_total')
            ->withSum(['orderItems as profit' => function ($q2) use ($from, $to) {
                $q2->whereHas('order', fn($oq) => $oq
                    ->where('status', '!=', 'cancelled')
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                );
            }], 'line_profit');
        }])
        ->get()
        ->map(function ($cat) {
            $cat->total_revenue = (float) ($cat->products->sum('revenue') ?? 0);
            $cat->total_profit  = (float) ($cat->products->sum('profit')  ?? 0);
            return $cat;
        })
        ->sortByDesc('total_revenue');

        return view('admin.reports.categories',
            compact('catData', 'from', 'to', 'period'));
    }

    public function profit(Request $request)
    {
        [$from, $to, $period] = $this->dateRange($request);

        $orders = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $totals = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw('SUM(`total`) as revenue, SUM(cost_total) as cost, SUM(`total` - cost_total) as profit')
            ->toBase()
            ->first();

        return view('admin.reports.profit',
            compact('orders', 'totals', 'from', 'to', 'period'));
    }

    // ── CSV export (no package required) ────────────────────────────

    public function exportExcel(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $type = $request->get('type', 'sales');

        $filename = "report-{$type}-{$from}-to-{$to}.csv";
        $rows     = $this->buildCsvRows($type, $from, $to);

        $response = response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);

        return $response;
    }

    // ── PDF export (HTML → print-optimised view, browser prints) ────

    public function exportPdf(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $type = $request->get('type', 'sales');

        $data = $this->buildPdfData($type, $from, $to);

        return view('admin.reports.pdf', array_merge($data, [
            'type' => $type,
            'from' => $from,
            'to'   => $to,
        ]));
    }

    // ── Private data builders ────────────────────────────────────────

    private function buildCsvRows(string $type, string $from, string $to): array
    {
        return match ($type) {
            'products' => $this->csvProducts($from, $to),
            'profit'   => $this->csvProfit($from, $to),
            'categories' => $this->csvCategories($from, $to),
            default    => $this->csvSales($from, $to),
        };
    }

    private function csvSales(string $from, string $to): array
    {
        $rows   = [['Date', 'Orders', 'Revenue', 'Cost', 'Profit', 'Margin %']];
        $daily  = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(cost_total) as cost'),
                DB::raw('SUM(`total` - cost_total) as profit')
            )
            ->groupBy('date')->orderBy('date')->get();

        foreach ($daily as $d) {
            $margin  = $d->revenue > 0 ? round($d->profit / $d->revenue * 100, 1) : 0;
            $rows[] = [
                $d->date,
                $d->orders,
                number_format($d->revenue, 2),
                number_format($d->cost, 2),
                number_format($d->profit, 2),
                $margin . '%',
            ];
        }
        return $rows;
    }

    private function csvProducts(string $from, string $to): array
    {
        $rows = [['Product', 'Units Sold', 'Revenue', 'Cost', 'Profit', 'Margin %']];
        $data = OrderItem::select(
                'product_name',
                DB::raw('SUM(quantity) as units'),
                DB::raw('SUM(line_total) as revenue'),
                DB::raw('SUM(line_cost) as cost'),
                DB::raw('SUM(line_profit) as profit')
            )
            ->whereHas('order', fn($q) => $q
                ->where('status', '!=', 'cancelled')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
            )
            ->groupBy('product_name')->orderByDesc('revenue')->get();

        foreach ($data as $d) {
            $margin  = $d->revenue > 0 ? round($d->profit / $d->revenue * 100, 1) : 0;
            $rows[] = [
                $d->product_name,
                $d->units,
                number_format($d->revenue, 2),
                number_format($d->cost, 2),
                number_format($d->profit, 2),
                $margin . '%',
            ];
        }
        return $rows;
    }

    private function csvProfit(string $from, string $to): array
    {
        $rows = [['Order #', 'Customer', 'Date', 'Revenue', 'Cost', 'Profit', 'Margin %']];
        $data = Order::where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($data as $o) {
            $margin  = $o->total > 0 ? round($o->profit / $o->total * 100, 1) : 0;
            $rows[] = [
                $o->order_number,
                $o->customer_name,
                $o->created_at->format('Y-m-d'),
                number_format($o->total, 2),
                number_format($o->cost_total, 2),
                number_format($o->profit, 2),
                $margin . '%',
            ];
        }
        return $rows;
    }

    private function csvCategories(string $from, string $to): array
    {
        $rows = [['Category', 'Revenue', 'Profit', 'Margin %']];
        $data = OrderItem::select(
                DB::raw('products.category_id'),
                DB::raw('categories.name as category_name'),
                DB::raw('SUM(order_items.line_total) as revenue'),
                DB::raw('SUM(order_items.line_profit) as profit')
            )
            ->join('products',   'order_items.product_id',   '=', 'products.id')
            ->join('categories', 'products.category_id',     '=', 'categories.id')
            ->whereHas('order', fn($q) => $q
                ->where('status', '!=', 'cancelled')
                ->whereDate('created_at', '>=', $from)
                ->whereDate('created_at', '<=', $to)
            )
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('revenue')
            ->get();

        foreach ($data as $d) {
            $margin  = $d->revenue > 0 ? round($d->profit / $d->revenue * 100, 1) : 0;
            $rows[] = [$d->category_name, number_format($d->revenue, 2), number_format($d->profit, 2), $margin . '%'];
        }
        return $rows;
    }

    private function buildPdfData(string $type, string $from, string $to): array
    {
        return match ($type) {
            'products'   => ['rows' => $this->csvProducts($from, $to)],
            'profit'     => ['rows' => $this->csvProfit($from, $to)],
            'categories' => ['rows' => $this->csvCategories($from, $to)],
            default      => ['rows' => $this->csvSales($from, $to)],
        };
    }
}
