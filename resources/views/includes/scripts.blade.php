@section('scripts')

<script src="{{ mix('/js/manifest.js') }}" defer="defer"></script>
<script src="{{ mix('/js/vendor.js') }}" defer="defer"></script>
<script src="{{ mix('/js/app.js') }}" defer="defer"></script>

<script type="text/javascript">
  window.appInit = {
      controller: "{{ $controller }}",
      action: "{{ $action }}"
  };
</script>

@show
