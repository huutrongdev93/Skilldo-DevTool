<?php
use Illuminate\Database\Capsule\Manager as DB;

class DebugBar {

    protected string $view = 'views/debug-bar';

    protected array $_available_sections = [
        'info',
        'request',
        'queries',
        'ajax',
        'headers',
        'session',
        'options',
        'files',
        'view'
    ];

    protected int $_query_toggle_count = 25;

    protected array $_compile_ = [];

    protected $CI;

    public function __construct($config = array())
    {
        $this->CI =& get_instance();

        if (isset($config['query_toggle_count']))
        {
            $this->_query_toggle_count = (int) $config['query_toggle_count'];
            unset($config['query_toggle_count']);
        }

        // default all sections to display
        foreach ($this->_available_sections as $section)
        {
            if ( ! isset($config[$section]))
            {
                $this->_compile_[$section] = TRUE;
            }
        }

        $this->set_sections($config);
    }

    public function set_sections(mixed $config): void
    {
        foreach ($config as $method => $enable)
        {
            if (in_array($method, $this->_available_sections))
            {
                $this->_compile_[$method] = $enable !== FALSE;
            }
        }
    }

    private function human_filesize($bytes): string
    {
        $sz = 'BKMGTP';

        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f", $bytes / pow(1024, $factor)) . @$sz[(int)$factor];
    }

    protected function _compile_info(): array|string
    {
        $profile = array();

        foreach ($this->CI->benchmark->marker as $key => $val)
        {
            // We match the "end" marker so that the list ends
            // up in the order that it was defined
            if (preg_match("/(.+?)_end/i", $key, $match))
            {
                if (isset($this->CI->benchmark->marker[$match[1].'_end']) AND isset($this->CI->benchmark->marker[$match[1].'_start']))
                {
                    $profile[$match[1]] = $this->CI->benchmark->elapsed_time($match[1].'_start', $key);
                }
            }
        }

        return Plugin::partial('DevTool', $this->view.'/info', [
            'profile'   => $profile,
            'class'     => $this->CI->router->fetch_class(),
            'method'    => $this->CI->router->fetch_method(),
            'ram'       => (($usage = memory_get_usage()) != '' ? $this->human_filesize($usage) . ' ' : $this->CI->lang->line('profiler_no_memory'))
        ]);
    }

    protected function _compile_queries(): string
    {
        $databaseQuery = DB::getQueryLog();

        $total = 0;

        $this->CI->load->helper('text');

        $dbs = (object)['queries' => [], 'database' => CLE_DBNAME];

        if (have_posts($databaseQuery)) {

            foreach ($databaseQuery as $q)
            {
                $time = number_format($q['time']/1000, 4);

                $total += $q['time']/1000;

                $query = DevtoolHelper::interpolateQuery($q['query'], $q['bindings']);

                $dbs->queries[] = ['sql' => DevtoolHelper::highlightSql($query), 'time' => $time];
            }
        }

        return Plugin::partial('DevTool',$this->view. '/queries', ['queries' => $dbs, 'total' => $total]);
    }

    protected function _compile_ajax(): string
    {
        if(file_exists('uploads/devtool/query-log.txt')) {
            unlink('uploads/devtool/query-log.txt');
        }

        return Plugin::partial('DevTool',$this->view. '/ajax');
    }

    protected function _compile_request(): string
    {
        $output  = "\n\n";
        $output .= '<fieldset id="ci_profiler_request">';
        $output .= "\n";
        $output .= '<p style="color:#cd6e00;">GET</p>';
        ob_start();
        dump(request()->query->all());
        $output .= ob_get_contents();
        ob_end_clean();

        $output .= '<p style="color:#cd6e00;">POST</p>';
        ob_start();
        dump(request()->request->all());
        $output .= ob_get_contents();
        ob_end_clean();

        $output .= '<p style="color:#cd6e00;">FILES</p>';
        ob_start();
        dump(request()->allFiles());
        $output .= ob_get_contents();
        ob_end_clean();

        $output .= "</fieldset>";

        return $output;
    }

    protected function _compile_headers(): string
    {
        return Plugin::partial('DevTool',$this->view. '/headers');
    }

    protected function _compile_session(): string
    {
        return Plugin::partial('DevTool',$this->view. '/session', ['session' => session()->all()]);
    }

    protected function _compile_options(): string
    {

        $options = Option::getAll();

        foreach ($options as $key => $value) {

            if(Str::isSerialized($value)) {
                $options->$key = unserialize($value);
            }
        }

        return Plugin::partial('DevTool',$this->view. '/options', ['options' => $options]);
    }

    protected function _compile_files(): string
    {
        $files = get_included_files();

        sort($files);

        return Plugin::partial('DevTool',$this->view. '/files', ['files' => $files]);
    }

    protected function _compile_view(): string
    {
        $_ci_cached_vars = $this->CI->load->get_vars();

        return Plugin::partial('DevTool',$this->view.'/views', ['data' => $_ci_cached_vars]);
    }

    /**
     * Run the Profiler
     *
     * @return	string
     */
    public function run(): string
    {
        $output = '<div id="codeigniter_wrap_profiler">';
        $output .= $this->addDebugBar();
        $output .= "<div id='codeigniter_profiler'>";
        $fields_displayed = 0;

        foreach ($this->_available_sections as $section)
        {
            if ($this->_compile_[$section] !== FALSE)
            {
                $func = "_compile_{$section}";
                $output .= $this->{$func}();
                $fields_displayed++;
            }
        }

        if ($fields_displayed == 0)
        {
            $output .= '<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee">'.$this->CI->lang->line('profiler_no_profiles').'</p>';
        }

        $output .= '</div>';
        $output .= '</div>';
        //add script
        $output .= '
        <script>
        var cookie_fn = {
            set: function(name,value,days) {
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime()+(days*24*60*60*1000));
                    var expires = "; expires="+date.toGMTString();
                } else
                    var expires = "";
                document.cookie = name+"="+value+expires+"; path=/";
            },
            get: function(name) {
                return(document.cookie.match(\'(^|; )\'+name+\'=([^;]*)\')||0)[2];
            },
            del: function(name) {
                this.set(name,"",-1);
            },
            check: function(name) {
                var isset = this.get(name);
                return (isset);
            },
        };
        var ci_profiler_wrap = document.getElementById("codeigniter_wrap_profiler");
        var ci_profiler = document.getElementById("codeigniter_profiler");
        var ci_profiler_bar = document.getElementById("codeigniter_profiler_debug_bar");
        var ci_profiler_height = ci_profiler_wrap.offsetHeight;
        var ci_profiler_fn = {
            defaultHeight: 230,
            triggerZoom: false,
            // triggerClose: false,
            triggerClose: Boolean(parseInt(cookie_fn.get(\'ci_profile_close\'))),
            resizeBody: function() {
                document.body.style.marginBottom = ci_profiler_wrap.offsetHeight + "px";
            },
            restoreBody: function(section) {
                cookie_fn.set(\'ci_profile_section\', section, 1);
                if(this.triggerClose) {
                    this.zoomBar();
                }
                // this.scrollSection(section);
                this.showSection(section);
            },
            scrollSection: function(section) {
                // using scrollIntoView
                // document.getElementById(section).scrollIntoView(true);
                var ci_section = document.getElementById(section);
                if(ci_section) {
                    ci_section.scrollIntoView(true);
                }
            },
            showSection: function(section) {
                // using display mode
                this.hideSection(0);
                var ci_section = document.getElementById(section);
                if(ci_section) {
                    ci_section.style.display = "block";
                }
            },
            hideSection: function(startFrom) {
                var elements = document.getElementsByTagName("fieldset");
                for(var i=startFrom; i<elements.length; i++) {
                    elements[i].style.display = "none";
                }
            },
        };
        // ci_profiler_fn.hideSection(1);
        // ci_profiler_fn.resizeBody();
        // ci_profiler_fn.minimalBar();
        if(Boolean(parseInt(cookie_fn.get(\'ci_profile_close\')))) {
            ci_profiler_wrap.style.height = "31px";
        }
        var last_section = cookie_fn.get(\'ci_profile_section\') || \'ci_profiler_benchmarks\';
        ci_profiler_fn.showSection(last_section);
        </script>';
        return $output;
    }

    protected function addDebugBar(): string
    {
        $debugBar = '<div id="codeigniter_profiler_debug_bar">';
        $debugBar .= $this->debugBarTab();
        $debugBar .= '</div>';
        return $debugBar;
    }

    protected function debugBarTab(): string
    {
        $tab = '';
        foreach ($this->_available_sections as $section)
            if ($this->_compile_[$section] !== FALSE)
                $tab .= $this->debugTabs($section);
        return $tab;
    }

    protected function debugTabs($section): string
    {
        $tab = '<div id="codeigniter_profiler_debug_bar_tab">';
        $tab .= '<span id="codeigniter_profiler_title"><a href="javascript:void(0);" onclick="ci_profiler_fn.restoreBody(\'ci_profiler_' . $section . '\');">' . $section . '</a></span>';
        $tab .= '</div>';
        return $tab;
    }
}