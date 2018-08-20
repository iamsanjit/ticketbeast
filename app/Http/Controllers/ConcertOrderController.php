<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;

class ConcertOrderController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);
        $ticketQuantity = request('quantity');
        $email = request('email');
        $token = request('token');
        $totalCharges = $concert->ticket_price * $ticketQuantity;
        $this->paymentGateway->charge($totalCharges, $token);

        $order = $concert->orders()->create(['email' => $email]);

        foreach(range(1, $ticketQuantity) as $i) {
            $order->tickets()->create();
        }

        return response()->json([], 201);
    }
}
