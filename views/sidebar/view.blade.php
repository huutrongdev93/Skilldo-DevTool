<div class="devTools-sidebar {{ $setting['theme'] ?? 'dark' }} {{ $setting['layout'] ?? 'vertical' }}" style="display: none">
    <a class="devTools-btn-toggle" href="#"><i class="fad fa-cogs"></i></a>
    <a class="devTools-btn-terminal" href="#"><i class="fa-duotone fa-solid fa-square-terminal"></i></a>
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
                        'text' => 'Xóa cache',
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
                    <button class="nav-link" id="devTools-btn-setting" data-bs-toggle="tab" data-bs-target="#devTools-tab-setting" type="button" role="tab">Setting</button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="devTools-tabs-content">
            <div class="tab-pane h-full fade show active" id="devTools-tab-layout" role="tabpanel" tabindex="0">
                <form id="devTools-form-layout">
                    {!! Theme::partial('admin/theme-layout/html/layout-content', [
                        'layoutList' => Theme_Layout::list()
                    ]) !!}
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
            <div class="tab-pane h-full fade" id="devTools-tab-setting" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'views/sidebar/setting') !!}
            </div>
        </div>
    </div>
</div>
{!!
    Plugin::partial('DevTool', 'views/terminal', [
        'id' => 'terminal',
        'themePaths' => $themePaths,
        'plugins' => $plugins
    ]);
!!}
<style>{!! $css !!}</style>
<script defer>{!! $js !!}</script>
