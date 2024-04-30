<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const PENDING = 'pending';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'ammount',
        'shipping_address',
        'order_email',
        'order_status'
    ];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
