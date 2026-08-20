<?php
namespace DevTool\Supports;

use SkillDo\Cms\Plugin\PluginStore;
use Illuminate\Support\Str;

class DevtoolHelper
{
    /**
     * Các thư mục con trong app/ được lõi tự map PSR-4 cho plugin
     * (xem SkillDo\Cms\Loader::resolvePrefixesPsr4)
     */
    const PLUGIN_PSR4_DIRS = [
        'Ajax', 'Controllers', 'Cms', 'Middlewares',
        'Providers', 'Supports', 'Models', 'Modules', 'Services',
    ];

    /**
     * Đăng ký autoload cho các plugin CHƯA kích hoạt.
     *
     * Lõi chỉ dựng bản đồ PSR-4 cho plugin đang active, nên khi kích hoạt
     * plugin từ terminal các class trong plugin đó (ActivatorService...)
     * chưa thể autoload. Hàm này bù lại đúng như Admin\Ajax\PluginsAjax::active.
     */
    static function registerInactivePluginAutoload(): void
    {
        static $registered = false;

        if($registered) return;

        $registered = true;

        spl_autoload_register(function ($class)
        {
            $prefixesPsr4 = [];

            foreach (PluginStore::get('list') as $plugin)
            {
                if($plugin->checkActive()) continue;

                $manifest = $plugin->getManifest();

                $psr4 = $manifest['autoload']['psr-4'] ?? [];

                foreach ($psr4 as $prefix => $paths)
                {
                    $paths = is_array($paths) ? $paths : [$paths];

                    $psr4[$prefix] = array_map(fn($path) => $plugin->getPath().DIRECTORY_SEPARATOR.$path, $paths);
                }

                $alias = (!empty($manifest['autoload']['alias'])) ? $manifest['autoload']['alias'] : Str::studly($plugin->getId());

                foreach (self::PLUGIN_PSR4_DIRS as $dir)
                {
                    $psr4[$alias.'\\'.$dir.'\\'][] = $plugin->getPath().DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
                }

                $prefixesPsr4 = array_merge($prefixesPsr4, $psr4);
            }

            foreach ($prefixesPsr4 as $prefix => $baseDirs)
            {
                if (strncmp($class, $prefix, strlen($prefix)) !== 0) continue;

                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))).'.php';

                foreach ($baseDirs as $baseDir)
                {
                    $file = $baseDir.$relativePath;

                    if (is_file($file))
                    {
                        require_once $file;
                        return;
                    }
                }
            }
        });
    }

    /**
     * Nối file route của module vào routes/admin.php.
     *
     * Lõi (SkillDo\Cms\Providers\CmsRouteServiceProvider) chỉ nạp đúng 3 file
     * routes/admin.php, routes/web.php, routes/api.php của plugin/theme.
     * Vì vậy file route riêng của module phải được require lại từ admin.php,
     * nếu không toàn bộ route của module sẽ không tồn tại.
     *
     * @param mixed  $storage    Storage disk chứa plugin/theme
     * @param string $base       Thư mục gốc plugin/theme trên disk đó
     * @param string $routeFile  Tên file route của module, vd: admin-branch.php
     */
    static function linkAdminRouteFile(mixed $storage, string $base, string $routeFile): bool
    {
        $path = $base.'/routes/admin.php';

        $require = "require __DIR__ . '/".$routeFile."';";

        if(!$storage->has($path))
        {
            return (bool) $storage->put($path, "<?php\n\n".$require."\n");
        }

        $content = $storage->get($path);

        if(str_contains($content, $routeFile))
        {
            return true;
        }

        return (bool) $storage->put($path, rtrim($content, "\n")."\n\n".$require."\n");
    }

    /**
     * Đăng ký một class vào file json registry (elements.json / widget.json).
     *
     * Từ v8 element và widget KHÔNG còn được quét theo thư mục nữa, lõi chỉ nạp
     * các class được khai báo trong registry:
     *  - SkillDo\Cms\Element\ElementManager : <theme|theme-child|plugin>/elements/elements.json
     *  - SkillDo\Cms\Widget\WidgetManager   : <theme>/widget/widget.json
     * Tạo file class mà quên ghi vào đây thì element/widget sẽ không bao giờ hiện.
     *
     * @param string $jsonPath     Đường dẫn tuyệt đối tới file json
     * @param string $group        Nhóm trong json (general/header/footer, block/sidebar...)
     * @param string $class        Tên class
     * @param string $relativePath Đường dẫn file class, tính từ gốc theme/plugin
     */
    static function registerJsonWidget(string $jsonPath, string $group, string $class, string $relativePath): bool
    {
        $data = [];

        if(file_exists($jsonPath))
        {
            $data = json_decode(file_get_contents($jsonPath), true);

            if(!is_array($data))
            {
                return false;
            }
        }

        $data[$group][$class] = ['path' => str_replace('\\', '/', $relativePath)];

        $dir = dirname($jsonPath);

        if(!is_dir($dir))
        {
            mkdir($dir, 0775, true);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return file_put_contents($jsonPath, $json."\n") !== false;
    }

    /**
     * Namespace gốc của plugin: lấy autoload.alias, không có thì suy ra từ id.
     * Phải khớp đúng với SkillDo\Cms\Loader::resolvePrefixesPsr4, nếu tự
     * Str::studly(id) trong khi manifest khai alias khác (vd sicommerce =>
     * Ecommerce) thì class sinh ra sẽ không autoload được.
     */
    static function pluginAlias($plugin): string
    {
        $manifest = $plugin->getManifest();

        return (!empty($manifest['autoload']['alias']))
            ? $manifest['autoload']['alias']
            : Str::studly($plugin->getId());
    }

    /**
     * Khai báo form field / popover vào manifest (plugin.json hoặc theme.json).
     *
     * v8 không còn quét thư mục để tìm field/popover nữa:
     *  - plugin: SkillDo\Cms\Providers\PluginServiceProvider::buildCmsRegister
     *  - theme : SkillDo\Cms\Loader (khối cmsAliases)
     * cả hai đều đọc từ khoá cms.form.fields / cms.form.popover.
     *
     * @param string $manifestPath Đường dẫn tuyệt đối tới plugin.json / theme.json
     * @param string $section      'fields' hoặc 'popover'
     * @param string $key          Tên field (fields) hoặc module key (popover)
     * @param mixed  $value        ['class' => ...] với fields, tên class với popover
     */
    static function registerManifestCmsForm(string $manifestPath, string $section, string $key, mixed $value, bool $createIfMissing = false): bool
    {
        $data = [];

        if(file_exists($manifestPath))
        {
            $data = json_decode(file_get_contents($manifestPath), true);

            if(!is_array($data))
            {
                return false;
            }
        }
        else if(!$createIfMissing)
        {
            return false;
        }

        $data['cms']['form'][$section][$key] = $value;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return file_put_contents($manifestPath, $json."\n") !== false;
    }

    /**
     * Khai báo alias middleware vào manifest (plugin.json hoặc theme.json).
     *
     * SkillDo\Cms\Loader::registerMiddleware đọc từ khoá middlewares.aliases
     * để gọi $router->aliasMiddleware(). Middleware không khai báo ở đây
     * thì không dùng được trong route.
     */
    static function registerManifestMiddlewareAlias(string $manifestPath, string $alias, string $class, bool $createIfMissing = false): bool
    {
        $data = [];

        if(file_exists($manifestPath))
        {
            $data = json_decode(file_get_contents($manifestPath), true);

            if(!is_array($data))
            {
                return false;
            }
        }
        else if(!$createIfMissing)
        {
            return false;
        }

        $data['middlewares']['aliases'][$alias] = $class;

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return file_put_contents($manifestPath, $json."\n") !== false;
    }

    static function makeDirTreeFromNamespace(string $namespace, string $basePath): array
    {
        $parts = explode('\\', $namespace);

        $folders = [];

        $currentPath = rtrim($basePath, DIRECTORY_SEPARATOR);

        foreach ($parts as $part)
        {
            $currentPath .= DIRECTORY_SEPARATOR . $part;

            $folders[] = $currentPath;
        }

        foreach ($folders as $folder)
        {
            if (!is_dir($folder))
            {
                mkdir($folder, 0775, true);
            }
        }

        return $folders;
    }

    static function getNamespaceAndClass(string $path): array
    {
        $path = trim(str_replace('/', '\\', $path));

        $namespace = '';

        if (str_contains($path, '\\'))
        {
            $parts = explode('\\', $path);
            $fileNamePart = array_pop($parts); // phần cuối là tên file
            $namespace = implode('\\', array_map([Str::class, 'studly'], $parts));
        }
        else
        {
            $fileNamePart = $path;
        }

        $fileName = implode('', array_map([Str::class, 'studly'], preg_split('/[-_]/', $fileNamePart)));

        return [
            'namespace' => $namespace,
            'filename'  => $fileName,
        ];
    }

    static function highlightSql($sql): array|string|null
    {
        $highlighted = preg_replace(
            ['/\bselect\b/', '/\bfrom\b/', '/\bwhere\b/', '/\band\b/', '/\bOR\b/', '/\bjoin\b/', '/\bINNER JOIN\b/', '/\bLEFT JOIN\b/', '/\bRIGHT JOIN\b/', '/\border by\b/', '/\bgroup by\b/', '/\blimit\b/'],
            ['<span class="highlight-keyword">SELECT</span>', '<span class="highlight-keyword">FROM</span>', '<span class="highlight-keyword">WHERE</span>', '<span class="highlight-keyword">AND</span>', '<span class="highlight-keyword">OR</span>', '<span class="highlight-keyword">JOIN</span>', '<span class="highlight-keyword">INNER JOIN</span>', '<span class="highlight-keyword">LEFT JOIN</span>', '<span class="highlight-keyword">RIGHT JOIN</span>', '<span class="highlight-keyword">ORDER BY</span>', '<span class="highlight-keyword">GROUP BY</span>', '<span class="highlight-keyword">LIMIT</span>'],
            $sql
        );

        // Highlight chuỗi trong dấu ``
        return preg_replace('/`([^`]+)`/', '<span class="highlight-variable">`$1`</span>', $highlighted);
    }

    static function interpolateQuery($query, array $params): array|string|null
    {
        $keys = [];
        $values = $params;

        //build a regular expression for each parameter
        foreach ($params as $key => $value)
        {
            if (is_string($key))
            {
                $keys[] = "/:" . $key . "/";
            } else
            {
                $keys[] = '/[?]/';
            }

            if (is_string($value))
                $values[$key] = "'" . $value . "'";

            if (is_array($value))
                $values[$key] = implode(',', $value);

            if (is_null($value))
                $values[$key] = 'NULL';
        }

        $query = preg_replace($keys, $values, $query, 1, $count);

        return $query;
    }

    static function highlightMethod($string): array|string|null
    {
        return preg_replace(
            [
                '/\bPOST\b/',
                '/\bGET\b/',
                '/\bDELETE\b/',
                '/\bPUT\b/',
                '/\bPATH\b/',
                '/\bHEAD\b/'
            ],
            [
                '<span class="highlight-method-post">POST</span>',
                '<span class="highlight-method-get">GET</span>',
                '<span class="highlight-method-delete">DELETE</span>',
                '<span class="highlight-method-put">PUT</span>',
                '<span class="highlight-method-path">PATH</span>',
                '<span class="highlight-method-head">HEAD</span>',
            ],
            $string
        );
    }

    static function highlightParams($string): array|string|null
    {
        return preg_replace('/{([^}]+)}/', '<span class="highlight-method-post">{$1}</span>', $string);
    }
}