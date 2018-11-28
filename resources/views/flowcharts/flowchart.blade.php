@extends('layouts.masterwide')

@section('title', 'Flowcharts - Manage Flowchart')

@section('content')

@include('flowcharts._flowchart', ['plan' => $plan, 'link' => false])


<div id="flowchart"></div>

<div id="course-modal"></div>
{{--@include('flowcharts._courseform')--}}

<input type="hidden" id="id" value="{{$plan->id}}">
<input type="hidden" id="student_id" value="{{$plan->student_id}}">

@endsection
