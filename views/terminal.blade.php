<div class="devtool-terminal-wrapper">
    <div class="terminal-main {{ $id }}" data-php="{{PHP_VERSION}}"></div>
</div>


<style>
    .terminal-main .cmd,
    .terminal-main .cmd span,
    .terminal-main .cmd div ,
    .terminal-main.terminal,
    .terminal-main.terminal span,
    .terminal-main.terminal div
    {
        --background:#30353a;
        --font: Roboto Mono!important;
        line-height:25px!important;
    }
    .terminal-main, .terminal-main .terminal-scroller {
        height:100%;
    }
    .devtool-terminal-wrapper {
        position:relative;
        height:99%;
    }
    .terminal-main {
        position:absolute;
        top:0;
        left:0;
        width:100%;
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