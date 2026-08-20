<div class="profiler-table">
    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">Controller</p>
        </div>
        <div class="profiler-column profiler-column-7"><span>{!! show_r($router) !!}</span></div>
    </div>

    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">Theme</p>
        </div>
        <div class="profiler-column profiler-column-7">
            {!! $template->name() !!}
        </div>
    </div>
    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">Page key</p>
        </div>
        <div class="profiler-column profiler-column-7">
            {!! $template->getPage() !!}
        </div>
    </div>
    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">Layout</p>
        </div>
        <div class="profiler-column profiler-column-7">
            {!! $template->getLayout() !!}
        </div>
    </div>
    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">View</p>
        </div>
        <div class="profiler-column profiler-column-7">
            {!! $template->getView() !!}
        </div>
    </div>
    @if(!empty($module))
        <div class="profiler-row">
            <div class="profiler-column profiler-column-3">
                <p class="mb-0">Module</p>
            </div>
            <div class="profiler-column profiler-column-7">
                {!! $module !!}
            </div>
        </div>
    @endif


    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">Ram (memory) </p>
        </div>
        <div class="profiler-column profiler-column-7"><span>{!! $memory !!}</span></div>
    </div>

    <div class="profiler-row">
        <div class="profiler-column profiler-column-3">
            <p class="mb-0">CPU load</p>
            <p class="mb-0 opacity-50">(CLI Linux only)</p>
        </div>
        <div class="profiler-column profiler-column-7"><span>{!! $cpuLoad !!}</span></div>
    </div>
</div>