<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function index()
     {
         $role = auth()->user()->role;

         if ($role == 'admin') {
             return view('dashboard');
         } elseif ($role == 'driver') {

             $orders = Order::with('user')
                 ->where('driver_id', auth()->id())
                 ->orderBy('created_at', 'desc')
                 ->get();

             return view('driver.dashboard', compact('orders'));
         } else {
             return view('home', ['orders' => Order::where('user_id', auth()->id())->latest()->get()]);
            }
     }
 }
