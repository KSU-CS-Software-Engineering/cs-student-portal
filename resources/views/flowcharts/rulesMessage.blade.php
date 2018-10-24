{{--makes background color and shape around edges--}}
<div class="row bg-red rounded flowchart-header" id="error">
    {{--<b>Test: </b>--}}
    {{--calles the flowchart controller and prints an error message if rules are violated--}}
    <b>{{ \App\Http\Controllers\FlowchartsController:: CheckSemesterRules($plan)}}</b>
    <b>{{ \App\Http\Controllers\FlowchartsController:: CheckFourYearRules($plan)}}</b>

</div>