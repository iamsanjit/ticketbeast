<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'TicketBeast')</title>
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        @include('scripts.app')
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>
        @stack('beforeScripts')
        <script src="{{ mix('js/app.js') }}"></script>
        @stack('afterScripts')
        {{ svg_spritesheet() }}
    </body>
</html>
