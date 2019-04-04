<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>K-State Engineering Advising]</title>
    </head>
    <body role="document">
        <div>
            @yield('content')
        </div>
        @include('email.footer')
    </body>
</html>
