@extends('layouts.masterwide')

@section('title', 'Scheduler')

@section('content')

    <div id="schedule" class="schedule"> </div>

@endsection

@section ('scripts')

    @parent

    <script src="js/scheduler.js"></script>

    @endsection

@section('styles')
    @parent
        <link href="css/schedule.css" rel="stylesheet">

    @endsection

