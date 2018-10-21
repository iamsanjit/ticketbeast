<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'TicketBeast')</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <style>
            .zondicon.icon-sm {
                width: 0.7rem;
                height: 0.7rem;
            }

            .zondicon.icon-muted {
                color: #6c757d !important;
                fill: #6c757d !important;
            }
        </style>
        @include('scripts.app')
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/backstage/concerts/new') }}">
                            <img src="/img/checkout-icon.png" width="30" height="30" alt="">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav ml-auto">
                            <a class="nav-item nav-link active" href="#">Logout<span class="sr-only">(current)</span></a>
                        </div>
                    </div>
                </div>  
            </nav>

            @yield('content')

            <div class="bg-dark pt-5 pb-5">
                <div class="container">
                    <div class="row">
                        <div class="col text-center text-light">Ticketbeast</div>
                    </div>
                </div>
            </div>

        </div>
        @stack('beforeScripts')
        <script src="{{ mix('js/app.js') }}"></script>
        @stack('afterScripts')
        {{ svg_spritesheet() }}
    </body>
</html>