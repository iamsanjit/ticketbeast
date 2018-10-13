@extends('layouts.backstage')

@section('title')
    Create a concert
@endsection

@section('content')
<form>

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
                            <input type="text" name="title" class="form-control" placeholder="The header lines">
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="with the openers (optional)">
                        </div>
                        <div class="form-group">
                            <label for="additional_information">Additional Information</label>
                            <textarea name="additional_information" class="form-control" placeholder="This concert is 19+ (optional)"></textarea>
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
                                    <input type="date" name="date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="time">Time</label>
                                    <input type="time" name="time" class="form-control">
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
                            <input type="text" name="venue" class="form-control" placeholder="The mosh pit">
                        </div>
                        <div class="form-group">
                            <label for="venue">Street Address</label>
                            <input type="text" name="venue" class="form-control" placeholder="123 Example Ave">
                        </div>
                        <div class="form-row">
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="Laraville">
                                </div>
                            </div>
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="state">State</label>
                                    <input type="text" name="state" class="form-control" placeholder="ON">
                                </div>
                            </div>
                            <div class="col-md-4">                        
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <input type="text" name="province" class="form-control" placeholder="90527">
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
                                        <input type="text" class="form-control" name="ticket_price" placeholder="0.00" aria-label="Dollar amount (with dot and two decimal places)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">                        
                                <div class="form-group">
                                    <label for="tickets_available">Tickets Available</label>
                                    <input type="input" name="tickets_available" class="form-control" placeholder="250">
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
                    <button class="btn btn-primary" type="submit">Add Concert</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection