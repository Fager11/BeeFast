<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;

class OrderController extends Controller
{
    
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $orders = Order::with(['user','items.product','driver'])->latest()->get();
        } elseif ($user->role == 'driver') {
            $orders = Order::with(['user','items.product'])
                        ->where('driver_id', $user->id)->latest()->get();
        } else {
            $orders = Order::with(['items.product','driver'])
                        ->where('user_id', $user->id)->latest()->get();
        }

        $drivers = User::where('role','driver')->get();
        return view('orders.index', compact('orders','drivers'));
    }

    
    public function show(Order $order)
    {
        $order->load(['user','items.product','driver']);
        $drivers = User::where('role','driver')->get();
        return view('orders.show', compact('order','drivers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $status = $request->status;
    
       
        if($status == 'in_progress' && !$order->driver_id){
            return back()->with('error','يجب تعيين سائق قبل قبول الطلب');
        }
    
        if (auth()->user()->role == 'driver' && $status == 'cancelled') {
            return back()->with('error','السائق لا يمكنه إلغاء الطلب كليًا');
        }
    
       
        if($status == 'cancelled' && $order->status != 'pending'){
            return back()->with('error','لا يمكن إلغاء الطلب بعد قبوله');
        }
    
        $order->status = $status;
        $order->save();
    
        $order->notifyStatus($status);
    
        return back()->with('success','تم تحديث حالة الطلب');
    }
    
    
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        $order = Order::findOrFail($id);
        $order->driver_id = $request->driver_id;
        $order->status = 'in_progress'; 
        $order->save();

        $data = [
            'order_id' => $order->id,
            'status'   => $order->status,
            'user_name'=> $order->user->name,
            'total'    => $order->total,
        ];

       
        $order->driver->notify(new \App\Notifications\OrderStatusNotification($data));

        
        $order->notifyStatus($order->status);

        return back()->with('success','تم تعيين السائق وإشعاره');
    }
    public function myOrders()
{
    $orders = Order::with(['items.product','store','driver'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

    return view('orders.user_orders', compact('orders'));
}

public function cannotCancel($id)
{
    return redirect()->back()->with('error', 'لا يمكنك إلغاء الطلب بعد الآن.');
}


}
