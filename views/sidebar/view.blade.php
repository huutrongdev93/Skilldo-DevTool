{!! Plugin::partial('DevTool', 'views/terminal-data', ['themePaths' => $themePaths, 'plugins' => $plugins]) !!}
<div class="devTools-sidebar {{ $setting['theme'] ?? 'dark' }} {{ $setting['layout'] ?? 'horizontal' }}" style="display: none">
    <a class="devTools-btn-toggle" href="#"><i class="fad fa-cogs"></i></a>
    <div class="devTools-sidebar-body">
        <div class="devTools-header p-2">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="text-uppercase text-lg fw-bold">DEV TOOL</h4>
                <div class="devTools-theme"></div>
            </div>
            <div class="devTools-btn-action d-flex">
                {!!
                    Admin::button('red', [
                        'text' => 'cache css',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'css'
                    ])
                !!}
                {!!
                    Admin::button('red', [
                        'text' => 'cache js',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'js'
                    ])
                !!}
                {!!
                    Admin::button('red', [
                        'text' => 'Xóa tất cả cache',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'cache'
                    ])
                !!}
                {!!
                    Admin::button('white', [
                        'text' => 'Đóng',
                        'class' => 'devTools-btn-close fw-bold text-uppercase',
                    ])
                !!}
            </div>
        </div>
        <div class="devTools-tabs sticky-top">
            <ul class="nav nav-tabs" id="devTools-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="devTools-btn-layout" data-bs-toggle="tab" data-bs-target="#devTools-tab-layout" type="button" role="tab">Layout</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-hook" data-bs-toggle="tab" data-bs-target="#devTools-tab-hook" type="button" role="tab">Hook</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-debug" data-bs-toggle="tab" data-bs-target="#devTools-tab-debug" type="button" role="tab">Debug Bar</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-router" data-bs-toggle="tab" data-bs-target="#devTools-tab-router" type="button" role="tab">Router</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-terminal" data-bs-toggle="tab" data-bs-target="#devTools-tab-terminal" type="button" role="tab">Terminal</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-setting" data-bs-toggle="tab" data-bs-target="#devTools-tab-setting" type="button" role="tab">Setting</button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="devTools-tabs-content">
            <div class="tab-pane h-full fade show active" id="devTools-tab-layout" role="tabpanel" tabindex="0">
                <form id="devTools-form-layout">
                    @do_action('setting_sidebar_template')
                </form>
                <div class="devTools-sidebar-button">
                    <button form="devTools-form-layout" class="btn btn-red btn-block">{!! Admin::icon('save') !!} SAVE</button>
                </div>
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-hook" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'views/sidebar/hooks', ['globalFilter' => $globalFilter, 'listHook' => $listHook]) !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-debug" role="tabpanel" tabindex="0">
                {!! $debugBar !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-router" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'views/sidebar/router', ['routers' => $routers]) !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-terminal" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'views/sidebar/terminal') !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-setting" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'views/sidebar/setting') !!}
            </div>
        </div>
    </div>
</div>
<style>
    {!! $css !!}
</style>
<script defer>
    {!! $js !!}
</script>
