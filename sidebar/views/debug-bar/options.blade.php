<fieldset id="ci_profiler_options">
    <div class="profiler-table">
        @if(have_posts($options))
            @foreach ($options as $key => $val)
                <div class="profiler-row">
                    <div class="profiler-column profiler-column-4">
                        <p>{!! $key !!}</p>
                    </div>
                    <div class="profiler-column profiler-column-6">
                        <p>{!! (is_string($val) || is_numeric($val)) ? $val : show_r($val) !!}</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</fieldset>