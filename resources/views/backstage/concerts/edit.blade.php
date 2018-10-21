@extends('layouts.backstage')

@section('content')
<div class="pt-3 pb-3 border-bottom bg-white">
    <div class="container">
        <div class="row">
            <div class="col">
                <h4 class="m-0 p-0">Edit a concert</h4>
            </div>
        </div>
    </div>
</div>
<form action="{{route('backstage.concerts.patch', $concert)}}" method="post" >
    @method('PATCH')
    @csrf
    <!-- Concert details -->
    <div class="pt-4 pb-4 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <h5 class="mt-4"><strong>Concert details</strong></h5>
                    <p>Tell us who is playing</p>
                    <p>
                        Include headerline in concert name, use the subtitle section to 
                        list any opening bands and add any important informaiton to the 
                        description 
                    </p>    
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="card p-4">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control @inputfeedback('title')" placeholder="The header lines" value="{{old('title', $concert->title)}}">
                            @inputerrors('title')
                           
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control @inputfeedback('subtitle')" placeholder="with the openers (optional)" value="{{old('subtitle', $concert->subtitle)}}">
                            @inputerrors('subtitle')
                        </div>
                        <div class="form-group">
                            <label for="additional_information">Additional Information</label>
                            <textarea name="additional_information" class="form-control @inputfeedback('additional_information')" placeholder="This concert is 19+ (optional)">{{old('additional_informaiton', $concert->additional_information)}}</textarea>
                            @inputerrors('additional_information')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date & Time -->
    <div class="pt-4 pb-4 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <h5 class="mt-4"><strong>Date &amp; Time</strong></h5>
                    <p>
                        True meta head only care about concert. So make sure obscure openers,
                        so make sure they reach there on time 
                    </p>
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="card p-4">
                        <div class="form-row">
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" class="form-control @inputfeedback('date')" value="{{old('date', $concert->date->format('Y-m-d'))}}">
                                    @inputerrors('date')
                                </div>
                            </div>
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="time">Time</label>
                                    <input type="text" name="time" class="form-control @inputfeedback('time')" placeholder="8:00pm" value="{{old('time', $concert->date->format('H:m'))}}">
                                    @inputerrors('time')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Venue Information -->
    <div class="pt-4 pb-4 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <h5 class="mt-4"><strong>Venue Information</strong></h5>
                    <p>
                        Where is the show? Let attendees know the venue name and the address
                        so they can bring the mosh.  
                    </p>
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="card p-4">
                        <div class="form-group">
                            <label for="venue">Venue Name</label>
                            <input type="text" name="venue" class="form-control @inputfeedback('venue')" placeholder="The mosh pit"  value="{{old('venue', $concert->venue)}}">
                            @inputerrors('venue')
                        </div>
                        <div class="form-group">
                            <label for="venue_address">Street Address</label>
                            <input type="text" name="venue_address" class="form-control @inputfeedback('venue_address')" placeholder="123 Example Ave"  value="{{old('venue_address', $concert->venue_address)}}">
                            @inputerrors('venue_address')
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control @inputfeedback('city')" placeholder="Laraville" value="{{old('city', $concert->city)}}">
                                    @inputerrors('city')
                                </div>
                            </div>
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" name="state" class="form-control @inputfeedback('state')" placeholder="ON" value="{{old('state', $concert->state)}}">
                                    @inputerrors('state')
                                </div>
                            </div>
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="zip">Zip</label>
                                    <input type="text" name="zip" class="form-control @inputfeedback('zip')" placeholder="90527" value="{{old('zip', $concert->zip)}}">
                                    @inputerrors('zip')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets & Pricing -->
    <div class="pt-4 pb-4 border-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <h5 class="mt-4"><strong>Tickets &amp; Pricing</strong></h5>
                    <p>
                        Set you tickets price and availability, but don't forget, meta heads
                        are cheap so keep it reasonable.
                    </p>
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="card p-4">
                        <div class="form-row">
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="ticket_price">Price</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="text" class="form-control @inputfeedback('ticket_price')" name="ticket_price" placeholder="0.00" aria-label="Dollar amount (with dot and two decimal places)" value="{{old('ticket_price', number_format($concert->ticket_price/100, 2))}}">
                                    </div>
                                    @inputerrors('ticket_price')
                                </div>
                            </div>
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="ticket_quantity">Tickets Available</label>
                                    <input type="input" name="ticket_quantity" class="form-control @inputfeedback('ticket_quantity')" placeholder="250" value="{{old('ticket_quantity', $concert->ticket_quantity)}}">
                                    @inputerrors('ticket_quantity')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 pb-4 border-bottom text-right">
        <div class="container">
            <div class="row">
                <div class="col">
                    <button class="btn btn-primary" type="submit">Update Concert</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection