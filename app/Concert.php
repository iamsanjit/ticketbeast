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

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderTickets($ticketQuantity, $email)
    {
        $tickets = $this->findTickets($ticketQuantity);
        return $this->createOrder($tickets, $email);
    }

    public function findTickets($ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($ticketQuantity > $tickets->count()) throw new NotEnoughTicketsException;
        return $tickets;
    }

    public function reserveTickets($ticketQuantity)
    {
        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();
        if ($ticketQuantity > $tickets->count()) throw new NotEnoughTicketsException;
        $tickets->each(function ($ticket) {
            $ticket->reserve();
        });
        // return $tickets;
        return new Reservation($tickets);
    }

    public function createOrder($tickets, $email)
    {
        return Order::forTickets($tickets, $email, $tickets->sum('price'));
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
}
