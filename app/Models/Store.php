<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'delivery_price',
        'type',
        'popular',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

public function orders() { return $this->hasMany(Order::class); }
public function products()
{
    return $this->hasMany(Product::class);
}
}
