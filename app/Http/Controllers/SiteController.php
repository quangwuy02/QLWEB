<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        // $users = DB::table('Product')->get();
        //list 10 san pham dau tien
        $products_10first = DB::table('product')->take(10)->get();
        // list pro theo số lg bán
        $products_quantity = Product::select('name', DB::raw('SUM(quantity) as total'))
        ->join('transactions', 'product.id', '=', 'transactions.product_id')
        ->groupBy('product.id')
        ->orderBy('total', 'desc')
        ->take(10)
        ->get();
        // $product1 = Product::where('amount', '>', '700')->get();
        // list pro re nhat 
        $products_cheap = Product::orderBy('price', 'asc')
        ->take(10)
        ->get();
        //list pro dat nhat
        $products_expensive = Product::orderBy('price', 'desc')
        ->take(10)
        ->get();
        //list pro theo ngày up
        $products_datecreated = Product::select('products.*')
        // ->join('order_items', 'products.id', '=', 'order_items.product_id')
        ->orderBy('products.created_at', 'desc')
        // ->groupBy('products.id')
        ->take(10)
        ->get();
        return view('index');
    }
}
