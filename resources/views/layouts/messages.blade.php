<script>
    $(document).ready(function() {
        toastr.options.timeOut = 5000;
        setTimeout(() => {
            @if (Session::has('errorMsg'))
            toastr.error('{{ Session::get('errorMsg') }}');
            @elseif(Session::has('successMsg'))
            toastr.success('{{ Session::get('successMsg') }}');
            @endif
        }, 1000);
    });
</script>
    