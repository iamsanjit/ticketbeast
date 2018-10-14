<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotEnoughTicketsException;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date'];

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function reserveTickets($ticketQuantity, $email)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($ticketQuantity > $tickets->count()) {
            throw new NotEnoughTicketsException;
        }

        $tickets->each(function ($ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
        return $this;
    }

    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->whereEmail($customerEmail)->count() > 0;
    }

    public function orderFor($customerEmail)
    {
        return $this->orders()->whereEmail($customerEmail)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
