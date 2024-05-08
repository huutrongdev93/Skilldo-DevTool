<div class="profiler-table p-3">
    <p>Chế độ </p>
    {!! Admin::button('white', [
        'icon'      => '<i class="fa-duotone fa-moon-cloud"></i>',
        'text'   => 'Tối',
        'class'     => ['p-1', 'devTools-btn-setting devTools-btn-theme'],
        'data-theme'   => 'dark',
    ]) !!}
    {!! Admin::button('white', [
        'icon'      => '<i class="fa-duotone fa-cloud-sun"></i>',
        'text'   => 'Sáng',
        'class'     => ['p-1', 'devTools-btn-setting devTools-btn-theme'],
        'data-theme'   => 'light',
    ]) !!}

    <p class="mt-3">Hiển thị</p>

    {!! Admin::button('white', [
        'icon'          => '<i class="fa-duotone fa-sidebar-flip"></i>',
        'text'          => 'Dọc',
        'class'         => ['p-1', 'devTools-btn-setting devTools-btn-layout'],
        'data-layout'   => 'vertical-right',
    ]) !!}
    {!! Admin::button('white', [
        'icon'          => '<i class="fa-duotone fa-window"></i>',
        'text'          => 'Ngang',
        'class'         => ['p-1', 'devTools-btn-setting devTools-btn-layout'],
        'data-layout'   => 'horizontal',
    ]) !!}
</div>