<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\OrderStatusNotification;
use App\Models\User;
class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'driver_id',
        'subtotal', 'discount', 'delivery_price', 'total',
        'status', 'estimated_delivery_time','address', 'latitude', 'longitude'
    ];
public function user() { return $this->belongsTo(User::class); }
public function store() { return $this->belongsTo(Store::class); }
public function driver() { return $this->belongsTo(User::class, 'driver_id'); }
public function items() { return $this->hasMany(OrderItem::class); }
public function notifyStatus($status)
{
    $data = [
        'order_id' => $this->id,
        'status'   => $status,
        'user_name'=> $this->user->name,
        'total'    => $this->total,
    ];


    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new OrderStatusNotification($data));
    }

    if ($this->user) {
        $this->user->notify(new OrderStatusNotification($data));
    }

    if ($status === 'assigned' && $this->driver) {
        $driverData = $data;
        $driverData['driver_assigned'] = true;
        $this->driver->notify(new OrderStatusNotification($driverData));
    }
}
}
