<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Deposit;
use App\Models\UpgradeRequest;

class AdminController extends Controller
{
    public function Dashboard()
    {
        $totalUsers = User::where('rights', '<', 9)->count();
        // $user = Auth::user();
        // $accountBalance = User::where('id', $user->id)->value('balance');
        $depositRequests = Deposit::where('status', 0)->count();
        $listDeposit = Deposit::all();

        $sellerReq = UpgradeRequest::all()->count();

        $newUsers = User::take(5)->orderBy('created_at')->get();
        return view('admin.dashboard', compact('totalUsers', 'depositRequests', 'listDeposit', 'sellerReq', 'newUsers'));
    }
}
