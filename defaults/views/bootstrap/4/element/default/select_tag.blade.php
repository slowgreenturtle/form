<div class="{{ $div_classes }}" id="{{ $div_name}}">
    {!! $label !!}
    {{ $form_element }}
</div>

<script>
    $(document).ready(function ()
    {

        $('.select2-multiple').select2();

    });
</script>
