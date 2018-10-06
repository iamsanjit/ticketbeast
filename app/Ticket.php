<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Facades\TicketCode;

class Ticket extends Model
{
    protected $guarded = [];
    
    public function claimFor($order)
    {
        $this->code = TicketCode::generate();
        $order->tickets()->save($this);
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function release()
    {
        $this->update(['reserved_at' => null]);
    }

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
