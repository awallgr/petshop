<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Andreas\StateMachine\Traits\HasStateMachine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use HasStateMachine;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_status_id',
        'uuid',
        'products',
        'address',
        'delivery_fee',
        'amount',
    ];

    /**
     * @return BelongsTo<OrderStatus, Order>
     */
    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'order_status_id');
    }
}
