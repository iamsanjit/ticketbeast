@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col col-md-8 p-3">
                <div class="row">
                    <div class="col col-md-6">
                        <h2>Order Summary</h2>
                    </div>
                    <div class="col col-md-6 text-right">
                        <h5><a href="{{ url('/orders/'.$order->confirmation_number) }}">{{ $order->confirmation_number }}</a></h2>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-12"><div class="border-top mt-3 mb-3"></div></div>
                    <div class="col-12">
                        <p class="font-weight-bold">Order Total: ${{ number_format($order->amount / 100, 2)}}</p>
                        <p>Billed to card: **** **** **** {{$order->card_last_four}}</p>
                    </div>
                
                    <div class="col-12">
                        <h5>Your Tickets</h5>
                        @foreach($order->tickets as $ticket)
                        <div class="card ticket-card mt-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h4>{{$ticket->concert->title}}</h4>
                                        <p>{{$ticket->concert->subtitle}}</p>
                                    </div>
                                    <div class="col-12 col-md-6 text-right">
                                        <p>General Admission</p>
                                        <p>Admit one</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-7">
                                        <div class="media">
                                            <div class="mr-3">
                                                @icon('calendar')
                                            </div>
                                            <div class="media-body">
                                                <time datetime="{{$ticket->concert->date->format('Y-m-d H:i')}}">
                                                    <p class="font-weight-bold">{{$ticket->concert->date->format('l, F d, Y')}}</p>
                                                    <p>Doors at {{$ticket->concert->date->format('h:ia')}}</p>
                                                </time>
                                            </div>
                                        </div>
                                    </div>   
                                     <div class="col-12 col-md-5">
                                        <div class="media">
                                            <div class="mr-3">
                                                @icon('location')
                                            </div>
                                            <div class="media-body">
                                                <h3 class="lead">The Galaxy Grand</h3>
                                                1397 Steeles Ave. W<br>
                                                North york, ON, M3N 2G2
                                            </div>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <span>{{ $ticket->code }}</span>
                                    </div>
                                    <div class="col-12 col-md-6 text-right">
                                        <span>{{$order->email}}</span>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        @endforeach;
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <p>Powered by TicketBeast</p>
        </div>
    </div>
@endsection