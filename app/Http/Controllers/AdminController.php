<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $bookCount = Book::count();
        $orderCount = Order::count();
        $userCount = User::count();
        $revenue = Order::where(function ($query) {
            $query->where(function ($subQuery) {
                $subQuery->where('order_type', 'online')
                    ->where('status', Order::STATUS_DA_GIAO_DICH_THANH_CONG);
            })->orWhere(function ($subQuery) {
                $subQuery->where('order_type', 'offline')
                    ->where('status', Order::STATUS_DA_NHAN_HANG);
            });
        })->sum('total_amount');

        $monthlyRevenue = Order::selectRaw("YEAR(order_date) as year, MONTH(order_date) as month")
            ->selectRaw("SUM(CASE WHEN order_type = 'online' AND status = '" . Order::STATUS_DA_GIAO_DICH_THANH_CONG . "' THEN total_amount ELSE 0 END) as online_revenue")
            ->selectRaw("SUM(CASE WHEN order_type = 'offline' AND status = '" . Order::STATUS_DA_NHAN_HANG . "' THEN total_amount ELSE 0 END) as offline_revenue")
            ->groupByRaw('YEAR(order_date), MONTH(order_date)')
            ->orderByRaw('YEAR(order_date) desc, MONTH(order_date) desc')
            ->get();

        $yearlyRevenue = Order::selectRaw("YEAR(order_date) as year")
            ->selectRaw("SUM(CASE WHEN order_type = 'online' AND status = '" . Order::STATUS_DA_GIAO_DICH_THANH_CONG . "' THEN total_amount ELSE 0 END) as online_revenue")
            ->selectRaw("SUM(CASE WHEN order_type = 'offline' AND status = '" . Order::STATUS_DA_NHAN_HANG . "' THEN total_amount ELSE 0 END) as offline_revenue")
            ->groupByRaw('YEAR(order_date)')
            ->orderByRaw('YEAR(order_date) desc')
            ->get();

        $bestsellers = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('books', 'books.id', '=', 'order_items.book_id')
            ->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('orders.order_type', 'online')
                        ->where('orders.status', Order::STATUS_DA_GIAO_DICH_THANH_CONG);
                })->orWhere(function ($subQuery) {
                    $subQuery->where('orders.order_type', 'offline')
                        ->where('orders.status', Order::STATUS_DA_NHAN_HANG);
                });
            })
            ->select('books.title', 'books.book_type')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->groupBy('books.id', 'books.title', 'books.book_type')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return view('admin.index', compact(
            'bookCount',
            'orderCount',
            'userCount',
            'revenue',
            'monthlyRevenue',
            'yearlyRevenue',
            'bestsellers'
        ));
    }
}
