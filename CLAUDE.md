# CLAUDE.md — plugin DevTool

## Plugin này là gì

Bộ công cụ cho lập trình viên của SkillDo CMS v8. Hai phần:

1. **Terminal trong admin** — nút "Terminal" trên header bar (chỉ hiện với `Admin::isRoot()`), chạy các lệnh kiểu Artisan qua ajax. Đây là phần chính.
2. **Sidebar debug** — panel nổi ở footer admin & frontend: info, hooks, layouts, queries, router, setting.

Namespace PHP là `DevTool\*`. Class chính `DevTool` (không namespace, ở `index.php`) chỉ giữ config; plugin không có bảng DB riêng, cấu hình lưu ở option `devToolConfig`.

**Không có console runner thật** — mọi lệnh chạy qua `POST /admin/ajax` với `action=DevTool\Ajax\DevToolAjax::terminal`, tham số `command`. Không có CLI, không chạy được từ shell.

## File map

| Đường dẫn | Trách nhiệm |
|---|---|
| `index.php` | class `DevTool`: `config()` / `setConfig()` đọc-ghi `config('DevTool::theme.*')` + option `devToolConfig` |
| `plugin.json` | manifest; PSR-4 khai tay cho `DevTool\Commands` và `DevTool\Console` (2 thư mục này KHÔNG nằm trong danh sách lõi tự map) |
| `bootstrap/config.php` | gắn sidebar vào `cle_footer` + `admin_footer`, thêm nút Terminal qua filter `admin_theme_header_button` |
| `bootstrap/ajax.php` | đăng ký 7 ajax admin |
| `app/Ajax/DevToolAjax.php` | các handler ajax; `terminal()` khởi tạo `Kernel` và dispatch lệnh |
| `app/Commands/Kernel.php` | quét `app/Console`, so khớp token đầu của `signature` với lệnh người dùng gõ, gọi `run()` |
| `app/Commands/Command.php` | base class: `argument()`, `option()`, `line()`, `success()`, `error()`; `handle()` trả `self::SUCCESS` / `self::ERROR` |
| `app/Commands/SignatureParser.php` | parse `{arg} {arg?} {arg=default} {--opt} {--opt=default}` |
| `app/Commands/Message.php` | build chuỗi màu cho jquery-terminal (`[[;#00ee11;]text]`) |
| `app/Console/Command*.php` | 61 lệnh, mỗi file 1 lệnh — **tên file phải trùng tên class** vì Kernel suy class từ đường dẫn |
| `app/Supports/DevtoolHelper.php` | helper dùng chung cho các lệnh (xem bảng dưới) |
| `app/Supports/DevToolThemeSidebar.php` | render sidebar debug |
| `app/helpers/admin.php` | class `DevToolAdmin` — nút Terminal trên header admin |
| `sample/` | template cho các lệnh `make:*`; placeholder viết HOA_GẠCH_DƯỚI |
| `assets/js/devtool.js` | `TerminalDevTool.commands` là bảng autocomplete — **thêm/xoá lệnh PHP phải sửa file này** |

### DevtoolHelper — API dùng chung

| Method | Dùng khi |
|---|---|
| `pluginAlias($plugin)` | lấy namespace gốc plugin. **Luôn dùng cái này, đừng `Str::studly($id)`** — sicommerce có id `sicommerce` nhưng alias `Ecommerce` |
| `registerInactivePluginAutoload()` | trước khi đụng class của plugin CHƯA active (lõi chỉ map PSR-4 cho plugin đang active) |
| `registerJsonWidget($jsonPath, $group, $class, $relPath)` | ghi vào `elements/elements.json` hoặc `widget/widget.json` |
| `registerManifestCmsForm($manifestPath, 'fields'\|'popover', $key, $value, $createIfMissing)` | ghi `cms.form.fields` / `cms.form.popover` vào `plugin.json` / `theme-child.json` |
| `registerManifestMiddlewareAlias($manifestPath, $alias, $class, $createIfMissing)` | ghi `middlewares.aliases` |
| `linkAdminRouteFile($storage, $base, $routeFile)` | `require` file route module vào `routes/admin.php` |
| `getNamespaceAndClass($path)` | tách `Sub\Folder\name` thành namespace + tên class studly |

## Quy tắc bắt buộc khi thêm/sửa lệnh `make:*`

Đây là chỗ v7 → v8 sai nhiều nhất. Ba nguyên tắc:

**1. Lõi không quét thư mục nữa — phải ghi registry.** Tạo file class mà quên đăng ký thì nó không bao giờ được nạp:

| Loại | Registry | Đọc bởi |
|---|---|---|
| Element | `<theme\|theme-child\|plugin>/elements/elements.json` (`header`/`footer`/`general`) | `SkillDo\Cms\Element\ElementManager` |
| Widget | `<theme>/widget/widget.json` (`block`/`footer`/`sidebar`/`sidebar-list`/`sidebar-detail`) — **chỉ theme cha, không có theme-child** | `SkillDo\Cms\Widget\WidgetManager` |
| Form field | manifest `cms.form.fields.<tên>.class` | `PluginServiceProvider::buildCmsRegister` + `Loader` |
| Popover | manifest `cms.form.popover.<module>` | như trên |
| Middleware | manifest `middlewares.{aliases,groups,global}` | `Loader::registerMiddleware` |
| Taxonomy | Service class + `add_action('init', [X::class,'register'])` trong `bootstrap/` | — |
| Route | **chỉ** `routes/admin.php`, `routes/web.php`, `routes/api.php` | `CmsRouteServiceProvider` |

**2. PSR-4 của plugin/theme là danh sách cố định.** `Loader::resolvePrefixesPsr4` map `<Alias>\{Ajax,Cms,Controllers,Middlewares,Models,Modules,Providers,Services,Supports}` → `app/<Dir>`. Ngoài danh sách này phải khai `autoload.psr-4` trong manifest. `app/Macros` và `app/helpers` được **preload theo file**, không qua PSR-4 nên không đặt namespace.

**3. Theme cha hay theme con?** Không đồng nhất, phải tra từng loại:

- **theme-child** (`config('cms.theme.child')`): code PHP (`app/*`), element, route, database, bootstrap → dùng cho hầu hết `make:*:theme`.
- **theme cha** (`Theme::name()`): `widget/widget.json`, `language/` — `LanguageServiceProvider` chỉ `loadTranslationsFrom(Path::theme('language'))`, KHÔNG có theme-child, nên `make:lang:theme` ghi vào theme cha là đúng.

## Gotcha

- **Thư mục đã biến mất ở v8**: `theme-custom/`, `<theme>/core/`, `<theme>/Cms/`, `<theme>/theme-child/` (theme con giờ ngang hàng `views/theme-child`), `<plugin>/Cms/`, `<plugin>/core/`, `middleware/` ở root. Thấy đường dẫn nào trong số này là code v7 chưa migrate.
- **`Qr::` đã bị xoá** — v8 dùng Eloquent.
- **`Plugin` = `SkillDo\Cms\Plugin\Plugin`** (alias toàn cục), dùng trait `PluginHelper`. Có `Plugin::has()` / `Plugin::get()` / `Plugin::isActive()`; **không có `Plugin::is()`**, gọi sẽ throw `AppException`. Constructor là `new Plugin($id, array $manifest)` — đừng `new Plugin($name)`.
- **Không có `\SkillDo\Cache`** — là `SkillDo\Cache\Cache`. **`Template::minifyClear()` là method instance**, gọi qua `Theme::template()->minifyClear()`.
- **Custom validate rule không mở rộng được.** `RuleCollection` tự `new ValidatorFactory()` mỗi lần, đoạn scan class trong `ValidatorFactory` đã bị comment. Cần validate riêng thì dùng `Rule::make('Label')->custom(fn($v) => ...)`. Vì vậy `make:validate:*` đã bị gỡ — đừng thêm lại nếu chưa sửa lõi.
- **Ajax chỉ nhận POST tại `/admin/ajax`**; GET luôn trả "ajax action not found".
- **`Storage::disk('plugin')` và `disk('plugins')` cùng root** — cả hai đều hợp lệ, nhưng nên dùng `plugins` cho thống nhất.
- **`SignatureParser` không ép tham số bắt buộc**: thiếu arg thì `argument()` trả `false`, không báo lỗi. Mọi lệnh phải tự `preg_match` kiểm tra và ép `(string)` trước khi so khớp.
- **Log query ajax chưa có nguồn ghi**: `debugBarAjax` đọc `storage/uploads/devtool/query-log.txt` nhưng không chỗ nào ghi file này — panel Ajax sẽ luôn hiện trạng thái rỗng cho tới khi có logger.
- **`assets/js/devtool.js` KHÔNG qua bước build.** `DevToolThemeSidebar` nhúng thẳng bằng
  `file_get_contents`, nên sửa là ăn ngay — nó không nằm trong `cms:build:js`. Nhớ cập nhật bảng
  autocomplete `TerminalDevTool.commands` khi thêm/xoá lệnh hoặc option.
- **`cms:build:js` cần node + `javascript-obfuscator`.** Lệnh gộp bằng `matthiasmullie/minify` rồi gọi
  `node_modules/javascript-obfuscator` qua Symfony Process, cấu hình ở `obfuscator.config.json` gốc repo.
  Máy chủ web thường không có `node` trong PATH → khai `NODE_BIN` trong `.env`. Thiếu node thì các gói vẫn
  được ghi ở dạng đã rút gọn và lệnh chỉ cảnh báo. `--no-obfuscate` cho ra bản đọc được để dò lỗi.
  Bảng gói build ở `CommandCmsJsBuild::bundles()`; chỉ mã của mình được làm rối, thư viện ngoài để nguyên.
