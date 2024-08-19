<?php

class AdminMODULE_CLASS_NAMEButton
{
    const MODULE = 'MODULE_KEY';
    /**
     * Thêm buttons action cho header của table
     * @param $buttons
     * @return array
     */
    static function tableHeaderButton($buttons): array
    {
        $buttons['add'] = Admin::button('add', [
            'href' => 'admin/plugins/'.static::MODULE.'/add',
            'text' => trans('button.add')
        ]);

        $buttons['reload'] = Admin::button('reload');

        return $buttons;
    }

    /**
     * Thêm button cho page thêm mới edit
     * @param $module
     * @return void
     */
    static function formButton($module): void
    {
        $barUrl = Url::admin('plugins/'.static::MODULE);
        $buttons = [];
        $buttons[] = Admin::button('save', ['type' => 'submit', 'data-redirect' => $barUrl]);
        $buttons[] = Admin::button('back', ['href' => $barUrl]);
        $buttons = apply_filters(static::MODULE.'_form_buttons', $buttons);

        Admin::view('include/form/form-action', ['buttons' => $buttons, 'module' => $module]);
    }
}

add_filter('table_MODULE_KEY_header_buttons', 'AdminMODULE_CLASS_NAMEButton::tableHeaderButton');
add_action('form_MODULE_KEY_action_button', 'AdminMODULE_CLASS_NAMEButton::formButton');