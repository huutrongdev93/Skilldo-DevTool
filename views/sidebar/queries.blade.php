<div class="devtool-mini-tabs devTools-queries">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="devTools-queries-btn-main" data-bs-toggle="tab" data-bs-target="#devTools-queries-tab-main" type="button" role="tab">Queries</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="devTools-queries-btn-ajax" data-bs-toggle="tab" data-bs-target="#devTools-queries-tab-ajax" type="button" role="tab">Ajax</button>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane h-full fade show active" id="devTools-queries-tab-main" role="tabpanel" tabindex="0">
            @if(!config('app.debug'))
                {!! Admin::alert('warning', 'Chế độ DEBUG đang tắt') !!}
            @else
                <legend class="p-3" style="color:#995300;">{{count($queries)}} Query trong {!! $total !!}s</legend>
                <div class="queries">
                    @foreach($queries as $query)
                        <div class="query">
                            <div class="query-sql">{!! $query['sql'] !!}</div>
                            <div class="query-time">{!! $query['time'] !!}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="tab-pane h-full fade" id="devTools-queries-tab-ajax" role="tabpanel" tabindex="0">

        </div>
    </div>
</div>