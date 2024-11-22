<link href="https://fonts.googleapis.com/css?family=Roboto+Mono&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.terminal/2.41.1/js/jquery.terminal.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.terminal/2.41.1/css/jquery.terminal.min.css" rel="stylesheet"/>
<script src="https://raw.githubusercontent.com/jcubic/jquery.terminal/2.41.1/js/prism.js"></script>
<script src="{!! Url::base('views/plugins/DevTool/assets/terminal/keyboard.js') !!}"></script>
<script src="https://cdn.jsdelivr.net/gh/jcubic/static/js/wcwidth.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.terminal/js/ascii_table.js"></script>
<script src="{!! Url::base('views/plugins/DevTool/assets/terminal/autocomplete_menu.js') !!}"></script>

<div id="terminal-data"
     data-cms-version="{{ Cms::version() }}"
     data-path-theme="{!! htmlentities(json_encode($themePaths)) !!}"
     data-plugins="{!! htmlentities(json_encode($plugins)) !!}"
></div>

<div class="devtool-terminal-wrapper">
    <div class="terminal-mask"></div>
    <div class="terminal-main {{ $id }}" data-php="{{PHP_VERSION}}"></div>
</div>

<style>
    .devtool-terminal-wrapper {
        display: none;
    }
    .devtool-terminal-wrapper.open {
        position: fixed;
        top:0;
        left:0;
        height:100vh;
        width:100%;
        z-index: 99999;
        display: block;
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(4px);
    }
    .terminal-mask {
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100vh;
        background:#30353a;
        opacity: 0.9;
    }

    .terminal-main .cmd,
    .terminal-main .cmd span,
    .terminal-main .cmd div ,
    .terminal-main.terminal,
    .terminal-main.terminal span,
    .terminal-main.terminal div
    {
        --background:transparent !important;
        --font: Roboto Mono!important;
        --color:#fff!important;
        line-height:25px!important;
    }
    .terminal-main, .terminal-main .terminal-scroller {
        height:100%;
    }
    .terminal-main {
        max-width:100%;
        width:1000px;
        margin:0 auto;
    }
    .terminal-main ul {
        list-style: none;
        margin: 0;
        padding: 0;
        float: left;
        position: absolute;
        top: 14px;
        left: 0;
    }
    .terminal-main li {
        cursor: pointer;
        white-space: nowrap;
    }
    .terminal-main li:hover {
        background: #aaa;
        color: #000;
    }
    .terminal-main .cursor-wrapper {
        position: relative;
    }
    /* this can be removed in version 2.1.0 */
    .cmd [role="presentation"] {
        overflow: visible;
    }
</style>