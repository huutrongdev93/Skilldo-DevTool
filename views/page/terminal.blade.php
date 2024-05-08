<div class="h-full terminal-page" style="min-height: 100vh">
    {!! Plugin::partial('DevTool', 'views/terminal', [
        'id' => 'terminal',
    ]); !!}
</div>
<style>
    .terminal-page .devtool-terminal-wrapper {
        min-height: 90vh;
        padding: 20px;
    }
    .terminal-page .terminal {
        padding: 20px;
        border-radius: 10px;
    }
</style>