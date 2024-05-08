<fieldset id="ci_profiler_files">
    @if(have_posts($files))
        <div class="profiler-table">
            <div class="profiler-row">
                <div class="profiler-column">
                    <p><b>Root path:</b> {!! FCPATH !!}</p>
                </div>
            </div>
            @foreach ($files as $key => $val)
                <div class="profiler-row">
                    <div class="profiler-column">
                        <p>{!! preg_replace("/\/.*\//", "", str_replace(FCPATH, '', $val)) !!}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</fieldset>