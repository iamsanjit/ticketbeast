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
                        <h5>#0123456789</h2>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-12"><div class="border-top mt-3 mb-3"></div></div>
                    <div class="col-12">
                        <p class="font-weight-bold">Order Total: $69.98</p>
                        <p>Billed to card: **** **** **** 4242</p>
                    </div>
                
                    <div class="col-12">
                        <h5>Your Tickets</h5>
                        <div class="card ticket-card mt-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h4>No Warning</h4>
                                        <p>with Jane and John</p>
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
                                                <p class="font-weight-bold">Sunday, September 23, 2018</p>
                                                <p>Doors at 8:00PM</p>
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
                                        <span>DKHTRA</span>
                                    </div>
                                    <div class="col-12 col-md-6 text-right">
                                        <span>jane@example.com</span>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="card ticket-card mt-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h4>No Warning</h4>
                                        <p>with Jane and John</p>
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
                                                <p class="font-weight-bold">Sunday, September 23, 2018</p>
                                                <p>Doors at 8:00PM</p>
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
                                        <span>DKHTRA</span>
                                    </div>
                                    <div class="col-12 col-md-6 text-right">
                                        <span>jane@example.com</span>
                                    </div>
                                </div>    
                            </div>
                        </div>                 
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <p>Powered by TicketBeast</p>
        </div>
    </div>
@endsection