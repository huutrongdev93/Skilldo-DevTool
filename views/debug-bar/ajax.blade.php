<fieldset id="ci_profiler_ajax">
    <div class="ci_profiler_ajax_query">
        @if(!DEBUG)
            {!! Admin::alert('warning', 'Chế độ DEBUG đang tắt') !!}
        @endif
    </div>
    <div class="devTools-sidebar-button">
        <button id="devTools-debug-ajax" class="btn btn-red btn-block">{!! Admin::icon('save') !!} Load data</button>
    </div>
</fieldset>
