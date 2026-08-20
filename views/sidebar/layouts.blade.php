@foreach($pages as $pageKey => $page)
    <div class="box mb-2">
        <div class="box-header"><h4 class="box-title">{!! $page['name'] !!}</h4></div>
        <div class="m-2">
            <div class="box-content row">
                @foreach ($layouts as $layoutKey => $layout)
                    @if($layout['type'] == $page['type'])
                        {!! Theme::resources('admin/theme-layout/layout-item', [
                            'layout'     => $layout,
                            'pageKey'    => $pageKey,
                            'layoutKey'  => $layoutKey,
                            'active' 	 => $page['layout']
                        ]) !!}
                    @endif
                @endforeach
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endforeach