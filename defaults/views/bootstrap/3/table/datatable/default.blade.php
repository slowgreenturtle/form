<div class="dataTable_wrapper">

    <div id="{{ $table->name('wrapper') }}" class="{{ $table->htmlClass('wrapper') }}">

        <div class="row">

            <div class="col-sm-12">
                <table class="{{ $table->htmlClass('table') }}"
                       id="{{$table->name()}}" role="grid" aria-describedby="{{ $table->name()}}_info">
                    <thead>

                    <tr class="{{ $table->htmlClass('row') }}" role="row">

                        @foreach($table->headers() as $header)

                            <th>
                                <span data-toggle="tooltip" data-placement="bottom"
                                      title="{{$header['tooltip']}}">

                                {{ $header['name'] }}

                                </span>
                            </th>

                        @endforeach

                    </tr>
                    </thead>
                    <tbody>
                    {!! $table->body() !!}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('scripts')

    <script>
        $(document).ready(function ()
        {

            var settings = @json($table->jsSettings());
            if (settings.ajax.data)
            {

                settings.ajax.data = function (d)
                {
                    {!! $table->getSetting('ajax.data') !!}
                };
            }

            $('#{{ $table->name() }}').DataTable(settings);

        });
    </script>
@endpush