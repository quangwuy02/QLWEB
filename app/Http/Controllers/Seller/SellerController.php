<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class SellerController extends Controller
{
    public function Dashboard() 
    {
	$user = Auth::user();
	$totalOrders = DB::table('orders')
    		->join('products', 'products.id', '=', 'orders.product_id')
    		->select(DB::raw('COUNT(*) as total_orders'))
    		->where('seller_id', '=', $user->id)
    		->first();	
    $accountBalance = User::where('id', $user->id)->value('balance');					
	$revenue = DB::table('orders')
    		->join('products', 'products.id', '=', 'orders.product_id')
    		->select(DB::raw('SUM(price) as revenue_seller'))
    		->where('seller_id', '=', $user->id)
    		->first();
    return view('', compact('totalOrders','accountBalance','revenue'));

    }
}
