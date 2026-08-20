<?php
namespace NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME;


use SkillDo\Cms\Menu\AdminMenu;

Class MODULE_CLASS_NAMEService
{
    static function navigation(): void
    {
        AdminMenu::add('MODULE_KEY', 'MODULE_KEY', route('admin.MODULE_KEY.index'), [
            'icon' => '<i class="fa fa-list"></i>',
        ]);
    }
}
