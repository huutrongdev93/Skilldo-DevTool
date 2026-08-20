@if(empty($ajax))
    {!! Admin::alert('warning', 'Chưa có log query ajax nào trong storage/uploads/devtool/query-log.txt') !!}
@else
    @foreach($ajax as $action => $queries)
        <legend class="p-3" style="color:#995300;">{{ $action }} — {{ count($queries) }} query</legend>
        <div class="queries">
            @foreach($queries as $query)
                <div class="query">
                    <div class="query-sql">{!! $query['sql'] !!}</div>
                    <div class="query-time">{!! $query['time'] !!}</div>
                </div>
            @endforeach
        </div>
    @endforeach
@endif
