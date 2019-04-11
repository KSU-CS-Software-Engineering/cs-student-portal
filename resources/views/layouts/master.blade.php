<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('includes.head')
        <title>K-State Engineering Advising - @yield('title')</title>
    </head>
    <body role="document">
        @include('includes.navbar')
        <div class="{{ empty($wide) ? 'container' : 'container-fluid' }}" role="main">
            <div id="message">
              @if(session('message') !== null)
                <input type="hidden" id="message_flash" value="{{ Session::pull('message') }}">
                <input type="hidden" id="message_type_flash" value="{{ Session::pull('type') }}">
              @endif
            </div>
            @yield('content')
        </div>

        @include('includes.footer')

        @include('includes.scripts')
    </body>
</html>
