<div class="devTools-sidebar {{ $setting['theme'] ?? 'dark' }} {{ $setting['layout'] ?? 'vertical' }}" style="display: none">
    <a class="devTools-btn-toggle" href="#"><i class="fad fa-cogs"></i></a>
    <a class="devTools-btn-terminal" href="#"><i class="fa-duotone fa-solid fa-square-terminal"></i></a>
    <div class="devTools-sidebar-body">
        <div class="devTools-header p-2">
            <div class="d-flex justify-content-between align-items-center mb-2 p-2">
                <h4 class="text-uppercase text-lg fw-bold">DEV TOOL</h4>
                <p class="mb-0">Debug Mode:
                    @if(config('app.debug'))
                        <span class="debug-status-label active">On</span>
                    @else
                        <span class="debug-status-label inactive">Off</span>
                    @endif
                </p>
            </div>
            <style>
                @keyframes neonGreen {
                    0%, 100% {
                        text-shadow: 0 0 5px #00ff6a, 0 0 10px #00ff6a, 0 0 20px #00ff6a;
                        color: #aaffcc;
                    }
                    50% {
                        text-shadow: 0 0 20px #00ff6a, 0 0 40px #00ff6a, 0 0 60px #00ff6a;
                        color: #00ff6a;
                    }
                }

                @keyframes neonRed {
                    0%, 100% {
                        text-shadow: 0 0 5px #ff4747, 0 0 10px #ff4747, 0 0 20px #ff4747;
                        color: #ffaaaa;
                    }
                    50% {
                        text-shadow: 0 0 20px #ff4747, 0 0 40px #ff4747, 0 0 60px #ff4747;
                        color: #ff4747;
                    }
                }

                .debug-status-label.active {
                    animation: neonGreen 1.5s infinite alternate;
                }

                .debug-status-label.inactive {
                    animation: neonRed 1.5s infinite alternate;
                }
            </style>

            <div class="devTools-btn-action d-flex">
                {!!
                    Admin::button('red', [
                        'text' => 'CSS/JS nén',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'minify',
                        'tooltip' => 'xóa các file css, js được nén'
                    ])
                !!}
                {!!
                    Admin::button('red', [
                        'text' => 'Xóa cache',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'cache',
                        'tooltip' => 'xóa cache'
                    ])
                !!}
                {!!
                    Admin::button('red', [
                        'text' => 'cache autoload',
                        'class' => 'devTools-btn-cache fw-bold text-uppercase',
                        'data-type' => 'autoload',
                        'tooltip' => 'xóa cache autoload'
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
                    <button class="nav-link active" id="devTools-btn-info" data-bs-toggle="tab" data-bs-target="#devTools-tab-info" type="button" role="tab">Thông Tin</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-layout" data-bs-toggle="tab" data-bs-target="#devTools-tab-layout" type="button" role="tab">Layout</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-hook" data-bs-toggle="tab" data-bs-target="#devTools-tab-hook" type="button" role="tab">Hook</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-queries" data-bs-toggle="tab" data-bs-target="#devTools-tab-queries" type="button" role="tab">Queries</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-router" data-bs-toggle="tab" data-bs-target="#devTools-tab-router" type="button" role="tab">Router</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-includes" data-bs-toggle="tab" data-bs-target="#devTools-tab-includes" type="button" role="tab">Includes</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="devTools-btn-setting" data-bs-toggle="tab" data-bs-target="#devTools-tab-setting" type="button" role="tab">Setting</button>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="devTools-tabs-content">
            <div class="tab-pane h-full fade show active" id="devTools-tab-info" role="tabpanel" tabindex="0">
                {!! \DevTool\Supports\DevToolThemeSidebar::tabInfo() !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-layout" role="tabpanel" tabindex="0">
                <form id="devTools-form-layout">
                    {!! \DevTool\Supports\DevToolThemeSidebar::tabLayout() !!}
                </form>
                <div class="devTools-sidebar-button">
                    <button form="devTools-form-layout" class="btn btn-red btn-block">{!! Admin::icon('save') !!} SAVE</button>
                </div>
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-hook" role="tabpanel" tabindex="0">
                {!! \DevTool\Supports\DevToolThemeSidebar::tabHook() !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-queries" role="tabpanel" tabindex="0">
                {!! \DevTool\Supports\DevToolThemeSidebar::tabQueries() !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-router" role="tabpanel" tabindex="0">
                {!! \DevTool\Supports\DevToolThemeSidebar::tabRouter() !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-includes" role="tabpanel" tabindex="0">
                {!! \DevTool\Supports\DevToolThemeSidebar::tabIncludes() !!}
            </div>
            <div class="tab-pane h-full fade" id="devTools-tab-setting" role="tabpanel" tabindex="0">
                {!! Plugin::partial('DevTool', 'sidebar/setting') !!}
            </div>
        </div>
    </div>
</div>
{!!
    Plugin::partial('DevTool', 'terminal', [
        'id' => 'terminal'
    ]);
!!}
<style>{!! $css !!}</style>
<script defer>{!! $js !!}</script>
