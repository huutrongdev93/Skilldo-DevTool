<div class="hook-list" data-hooks="{!! htmlentities(json_encode($listHook)) !!}">
    @if(have_posts($listHook))
        @foreach ($listHook as $hookGroup)
            <div class="box">
                <div class="header"><h4 class="text-uppercase">{!! $hookGroup['name'] !!}</h4></div>
                @foreach ($hookGroup['list'] as $hookName)
                <div class="hook-group ps-2 pe-2 mt-2">
                    <h4 class="hook-header" data-id="#{{$hookName}}"><i class="fad fa-brackets"></i> {{$hookName}}</h4>
                    <div class="hook-detail" id="{{$hookName}}">
                        @if(isset($globalFilter[$hookName]) && have_posts($globalFilter[$hookName]))
                            @foreach ($globalFilter[$hookName]->callbacks as $position => $functions)
                                @foreach ($functions as $name => $function)
                                    @if(is_string($function['function']))
                                    <a>
                                        <i class="fad fa-directions"></i>
                                        {{$function['function']}}
                                        <span class="hook-position">{{$position}}</span>
                                    </a>
                                    @elseif($function['function'] instanceof \Closure)
                                        <a>
                                            <i class="fad fa-directions"></i>
                                            Closure
                                            <span class="hook-position">{{$position}}</span>
                                        </a>
                                    @else
                                    <a>
                                        <i class="fad fa-directions"></i>
                                        class::{{get_class($function['function'][0])}} - Function:: {{$function['function'][1]}}
                                        <span class="hook-position">{{$position}}</span>
                                    </a>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
<script>
	$(function () {
		$('.hook-header').click(function () {
			let id = $(this).attr('data-id');
			$(id).toggleClass('in');
		});
	})
</script>
