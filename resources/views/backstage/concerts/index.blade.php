@extends('layouts.backstage')

@section('content')
    <div class="pt-3 pb-3 border-bottom bg-white">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h4 class="m-0 p-0">Concerts</h4>
                </div>
                <div class="col text-right">
                    <a href="{{route('backstage.concerts.create')}}" class="btn btn-success">Add Concert</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mt-5">
            <div class="col">
                <p class="text-muted"><strong>Published</strong></p>
            </div>
        </div>
        <div class="row mb-4">
            @foreach($concerts as $concert)
                @if($concert->isPublished())
                <div class="col-md-6 col-lg-4">
                    <div class="card pt-3 pb-3 pl-4 pr-4 m-2">
                        <h5 class="m-0"><strong>{{$concert->title}}</strong></h5>
                        <h6 class="text-muted m-0 mt-1">{{$concert->subtitle}}</h6>
                        <div class="media mt-2">
                            <div class="mr-1">
                                @icon('location', 'icon-sm icon-muted')
                            </div>
                            <div class="media-body">
                                {{$concert->venue}} - {{$concert->city}}, {{$concert->state}}
                            </div>
                        </div>
                        <div class="media mt-2">
                            <div class="mr-1">
                                @icon('calendar', 'icon-sm icon-muted')
                            </div>
                            <div class="media-body">
                                {{$concert->formatted_date}} @ {{$concert->formatted_time}}
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{route('concerts.show', $concert)}}" class="btn btn-md btn-light">Get Ticket Link</a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Drafts -->
        <div class="row mt-5">
            <div class="col">
                <p class="text-muted"><strong>Drafts</strong></p>
            </div>
        </div>
        <div class="row mb-5">
            @foreach($concerts as $concert)
                @if(!$concert->isPublished())
                <div class="col-md-6 col-lg-4">
                    <div class="card pt-3 pb-3 pl-4 pr-4 m-2">
                        <h5 class="m-0"><strong>{{$concert->title}}</strong></h5>
                        <h6 class="text-muted m-0 mt-1">{{$concert->subtitle}}</h6>
                        <div class="media mt-2">
                            <div class="mr-1">
                                @icon('location', 'icon-sm icon-muted')
                            </div>
                            <div class="media-body">
                                {{$concert->venue}} - {{$concert->city}}, {{$concert->state}}
                            </div>
                        </div>
                        <div class="media mt-2">
                            <div class="mr-1">
                                @icon('calendar', 'icon-sm icon-muted')
                            </div>
                            <div class="media-body">
                                {{$concert->formatted_date}} @ {{$concert->formatted_time}}
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{route('backstage.concerts.edit', $concert)}}" class="btn btn-md btn-light mr-2">Edit</a>
                            <a href="" class="btn btn-md btn-primary">Publish</a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection