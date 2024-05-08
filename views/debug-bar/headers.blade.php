<fieldset id="ci_profiler_headers">
    <div class="profiler-table">
        @foreach (['HTTP_ACCEPT', 'HTTP_USER_AGENT', 'HTTP_CONNECTION', 'SERVER_PORT', 'SERVER_NAME', 'REMOTE_ADDR', 'SERVER_SOFTWARE', 'HTTP_ACCEPT_LANGUAGE', 'SCRIPT_NAME', 'REQUEST_METHOD',' HTTP_HOST', 'REMOTE_HOST', 'CONTENT_TYPE', 'SERVER_PROTOCOL', 'QUERY_STRING', 'HTTP_ACCEPT_ENCODING', 'HTTP_X_FORWARDED_FOR'] as $header)
        <div class="profiler-row">
            <div class="profiler-column profiler-column-4">{{$header}}</div>
            <div class="profiler-column profiler-column-6"><span>{!! (isset($_SERVER[$header])) ? $_SERVER[$header] : '' !!}</span></div>
        </div>
        @endforeach
    </div>
</fieldset>