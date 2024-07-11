<?php
class DevToolTerminalPage {

    static function button($buttons)
    {
        if(Admin::isRoot()) {
            $buttons['terminal'] = Admin::button('red', [
                'icon' => '<i class="fa-duotone fa-rectangle-terminal"></i>',
                'text' => 'Terminal',
                'class' => 'devTools-btn-terminal'
            ]);
        }

        return $buttons;
    }
}

add_filter('admin_theme_header_button', 'DevToolTerminalPage::button');