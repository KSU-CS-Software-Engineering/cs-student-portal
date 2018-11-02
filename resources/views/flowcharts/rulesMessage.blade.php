{{--makes background color and shape around edges--}}
<div class="row bg-red rounded flowchart-header" id="error">
    {{--<b>Test: </b>--}}
    {{--calls the flowchart controller and prints an error message if rules are violated--}}
    <div>

        <p> <strong>  @foreach ($planreqs as $planreq) {{$planreq}} @endforeach </strong> </p>
        <p> <strong> Courses missing: @foreach ($CISreqs as $CISreq) {{$CISreq['course_name']}} @endforeach </strong> </p>
        <p> <strong> Hours message: @foreach ($hours as $hour) {{$hour['credits']}} @endforeach </strong> </p>
        <p> <strong> Prerequisities missing: @foreach ($prereqs as $prereq) {{$prereq}} @endforeach </strong> </p>

    </div>

</div>