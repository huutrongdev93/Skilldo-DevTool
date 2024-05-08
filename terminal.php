<?php
class DevToolTerminalPage {
    static function register(): void
    {
        $args = [
            'callback' => 'DevToolTerminalPage::render',
            'position' => 'theme',
            'hidden' => true
        ];
        AdminMenu::add('devtool-terminal', 'Terminal', 'plugins?page=devtool-terminal', $args);
    }

    static function button($buttons)
    {
        $buttons['terminal'] = Admin::button('red', [
            'icon' => '<i class="fa-duotone fa-rectangle-terminal"></i>',
            'text' => 'Terminal',
            'href' => Url::admin('plugins?page=devtool-terminal'),
        ]);

        return $buttons;
    }

    static function render(): void {

        Plugin::view('DevTool', 'views/page/terminal');
    }
}

add_action('admin_init', 'DevToolTerminalPage::register');
add_filter('admin_theme_header_button', 'DevToolTerminalPage::button');