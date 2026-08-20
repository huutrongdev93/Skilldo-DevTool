<?php
class DevToolAdmin
{
    static function headerBar($buttons)
    {
        if(Admin::isRoot())
        {
            $buttons['terminal'] = Admin::button('red', [
                'icon' => '<i class="fa-duotone fa-rectangle-terminal"></i>',
                'text' => 'Terminal',
                'class' => 'devTools-btn-terminal'
            ]);
        }

        return $buttons;
    }
}