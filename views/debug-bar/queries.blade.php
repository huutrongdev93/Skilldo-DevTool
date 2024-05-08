<fieldset id="ci_profiler_queries">
    @if(!DEBUG)
        {!! Admin::alert('warning', 'Chế độ DEBUG đang tắt') !!}
    @else
        <legend style="color:#995300;">{{count($queries->queries)}} Query trong {!! $total !!}s</legend>
        <div class="queries">
            @foreach($queries->queries as $query)
                <div class="query">
                    <div class="query-sql">{!! $query['sql'] !!}</div>
                    <div class="query-time">{!! $query['time'] !!}</div>
                </div>
            @endforeach
        </div>
    @endif
</fieldset>

