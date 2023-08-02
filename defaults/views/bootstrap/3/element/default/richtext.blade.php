@include('input')

@push('scripts')
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <script>

        var options = {
            debug: 'info',
            modules: {
                toolbar: '#toolbar'
            },
            placeholder: 'Compose an epic...',
            readOnly: true,
            theme: 'snow'
        };

        var editor = new Quill('#editor', options);
    </script>
@endpush