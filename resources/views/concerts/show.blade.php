@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col col-md-6">
                <div class="card concert-details-card">
                    <div class="card-section p-3">
                        <h1 class="text-center">{{ $concert->title }}</h1>
                        <h4 class="text-center">{{ $concert->subtitle }}</h4>
                        <div class="media mt-5">
                            <div class="mr-3">
                                @icon('calendar')
                            </div>
                            <div class="media-body">
                                <span class="lead">{{ $concert->formatted_date }}</span>
                            </div>
                        </div>
                        <div class="media mt-5">
                            <div class="mr-3">
                                @icon('time')
                            </div>
                            <div class="media-body">
                            <span class="lead">Doors at {{ $concert->formatted_start_time }}</span>
                            </div>
                        </div>
                        <div class="media mt-5">
                            <div class="mr-3">
                                @icon('currency-dollar')
                            </div>
                            <div class="media-body">
                                <span class="lead">{{ $concert->ticket_price_in_dollars }}</span>
                            </div>
                        </div>
                        <div class="media mt-5">
                            <div class="mr-3">
                                @icon('location')
                            </div>
                            <div class="media-body">
                                <h3 class="lead">{{ $concert->venue }}</h3>
                                {{ $concert->venue_address }}<br>
                                {{ $concert->city }}, {{ $concert->state }} {{ $concert->zip }}
                            </div>
                        </div>
                        <div class="media mt-5">
                            <div class="mr-3">
                                @icon('information-solid')
                            </div>
                            <div class="media-body">
                                <h3 class="lead">Additional Information</h3>
                                <p>{{ $concert->additional_information}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-section">
                        <ticket-checkout
                            :concert-id="{{ $concert->id }}"
                            concert-title="{{ $concert->title }}"
                            :price="{{ $concert->ticket_price }}"
                        ></ticket-checkout>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <p>Powered by TicketBeast</p>
        </div>
    </div>
</div>
@endsection

@push('beforeScripts')
<script src="https://checkout.stripe.com/checkout.js"></script>
@endpush
