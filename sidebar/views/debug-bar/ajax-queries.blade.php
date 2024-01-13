@if(have_posts($ajax))
    @foreach ($ajax as $ajaxName => $queries)
        <div class="box">
            <div class="header"><h4 class="text-uppercase">{!! $ajaxName !!}</h4></div>
            <div class="queries">
                @foreach($queries as $query)
                    <div class="query">
                        <div class="query-sql">{!! $query['sql'] !!}</div>
                        <div class="query-time">{!! $query['time'] !!}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif