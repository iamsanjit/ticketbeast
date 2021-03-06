<?php

namespace App\Http\Controllers;

use App\Concert;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;
use App\Billing\PaymentFailedException;
use App\Exceptions\NotEnoughTicketsException;
use App\Order;
use App\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationEmail;

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
            'payment_token' => 'required'
        ]);
        
        try {
            $reservation = $concert->reserveTickets(request('quantity'), request('email'));
            $order = $reservation->complete($this->paymentGateway, request('payment_token'));

            Mail::to($order->email)->send(new OrderConfirmationEmail($order));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            $reservation->cancel();
            return response()->json(['error' => 'Payment Failed'], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json(['error' => 'Not Enough Tickets'], 422);
        }
    }
}
