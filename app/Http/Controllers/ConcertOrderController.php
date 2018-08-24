<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;
use App\Billing\PaymentFailedException;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;

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
        $this->validate(request(), [
            'email' => 'required|email',
            'quantity' => 'required|integer|min:1',
            'token' => 'required'
        ]);
        
        try {

            $tickets = $concert->findTickets(request('quantity'));
            $this->paymentGateway->charge($tickets->sum('price'), request('token'));
            $order = Order::forTickets($tickets, request('email'), $tickets->sum('price'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
