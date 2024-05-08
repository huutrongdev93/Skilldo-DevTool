<div class="profiler-table">
    @if(!empty($routers))
        <div class="profiler-row">
            <div class="profiler-column profiler-column-5">[Name]/[Path]</div>
            <div class="profiler-column profiler-column-2"><span>[Method]</span></div>
            <div class="profiler-column profiler-column-3"><span>[Controller action]</span></div>
        </div>
        @foreach ($routers as $key => $route)
            <div class="profiler-row">
                <div class="profiler-column profiler-column-5">
                    <p class="mb-0 opacity-50">{{  $route['name']}}</p>
                    <p>{!! $route['fullPath'] !!}</p>
                </div>
                <div class="profiler-column profiler-column-2"><span>{!! $route['methods'] !!}</span></div>
                <div class="profiler-column profiler-column-3"><span>{!! $route['action'] !!}</span></div>
            </div>
        @endforeach
    @endif
</div>