@extends('layouts.master', ['wide' => true])

@section('title', 'Scheduler')

@section('content')

    <div id="schedule" class="schedule"> </div>

    <input type="hidden" id="planId" value="{{ $planId }}" />

@endsection

@section ('scripts')

    @parent

    <script src="{{ mix('js/scheduler.js') }}" defer="defer"></script>

@endsection

@section('styles')

    @parent

    <link rel="stylesheet" type="text/css" href="{{ mix('css/schedule.css') }}" />

@endsection

