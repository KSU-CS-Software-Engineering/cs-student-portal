{{--makes background color and shape around edges--}}
<div class="row bg-red rounded flowchart-header" id="error">
    {{--<b>Test: </b>--}}
    {{--calls the flowchart controller and prints an error message if rules are violated--}}
    <div>

        <p> <strong>  {{--@foreach ($planreqs as $planreq) {{$planreq}} @endforeach--}} </strong> </p>

        @if(count($CISreqs) > 0)
        <p> <strong> Courses missing: @foreach ($CISreqs as $CISreq) {{$CISreq['course_name']}} @endforeach </strong> </p>
        @endif

        @if(count($hours) > 0)
        <p> <strong> To many hours for: @foreach ($hours as $hour) {{$hour['name']}} @endforeach needs to be under 21. </strong> </p>
        @endif

        @if(count($prereqs) >0)
        <p> <strong> Prerequisities missing: </strong>@foreach  ($prereqs as $prereq) <br> {{$prereq}}  @endforeach </p>
        @endif

        @if(count($courseplacement) > 0)
        <p> <strong> Courses not offered in its current semester placement: </strong>@foreach ($courseplacement as $course) <br>{{$course}} @endforeach </p>
        @endif
    </div>


</div>
