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
        $this->validate(request(), [
            'email' => 'required|email',
            'quantity' => 'required|integer|min:1',
            'token' => 'required'
        ]);

        $concert = Concert::published()->findOrFail($concertId);
        $this->paymentGateway->charge($concert->ticket_price * request('quantity'), request('token'));
        $concert->orderTickets(request('quantity'), request('email'));
        return response()->json([], 201);
    }
}
