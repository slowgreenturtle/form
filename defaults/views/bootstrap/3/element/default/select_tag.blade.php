<div class="{{ $div_classes }}" id="{{ $div_name}}">
    {!! $label !!}
    {{ $form_element }}
</div>


@section('scripts')


    <script>
        $(document).ready(function ()
        {

            $('.select2-multiple').select2();

        });
    </script>


@endsection