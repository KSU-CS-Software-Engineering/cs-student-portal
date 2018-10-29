{{--makes background color and shape around edges--}}
<div class="row bg-red rounded flowchart-header" id="error">
    {{--<b>Test: </b>--}}
    {{--calls the flowchart controller and prints an error message if rules are violated--}}
    <div>
        {{ \App\Http\Controllers\FlowchartsController:: CheckHoursRules($plan)}}
        {{ \App\Http\Controllers\FlowchartsController:: CheckPreReqRules($plan)}}
        {{ \App\Http\Controllers\FlowchartsController:: CheckGradPlanRules($plan)}}
        {{ \App\Http\Controllers\FlowchartsController:: CheckCisReqRules($plan)}}
    </div>

</div>