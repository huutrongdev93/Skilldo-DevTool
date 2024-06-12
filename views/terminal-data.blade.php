<link href="https://fonts.googleapis.com/css?family=Roboto+Mono&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.terminal/2.41.1/js/jquery.terminal.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.terminal/2.41.1/css/jquery.terminal.min.css" rel="stylesheet"/><script src="https://raw.githubusercontent.com/jcubic/jquery.terminal/2.41.1/js/prism.js"></script>
<script src="https://unpkg.com/js-polyfills/keyboard.js"></script>
<script src="https://cdn.jsdelivr.net/gh/jcubic/static/js/wcwidth.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.terminal/js/ascii_table.js"></script>
<script src="https://unpkg.com/jquery.terminal/js/autocomplete_menu.js"></script>

<div id="terminal-data"
     data-path-theme="{!! htmlentities(json_encode($themePaths)) !!}"
     data-plugins="{!! htmlentities(json_encode($plugins)) !!}"
></div>