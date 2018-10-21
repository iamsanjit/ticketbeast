<?php

namespace App\Http\Controllers\Backstage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Concert;
use Illuminate\Support\Facades\Auth;

class PublishedConcertController extends Controller
{
    public function store()
    {
        $concert = Auth::user()->concerts()->findOrFail(request('concert_id'));
        abort_if($concert->isPublished(), 422);
        $concert->publish();
        return redirect()->route('backstage.concerts.index');
    }
}
