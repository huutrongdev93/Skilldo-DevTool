<?php
add_action('admin_navigation', [\NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME\MODULE_CLASS_NAMEService::class, 'navigation']);

/*
|--------------------------------------------------------------------------
| Hook Form
|--------------------------------------------------------------------------
| Form::fields - thêm điều chỉnh field trong form add và edit
| Form::buttons - thêm điều chỉnh buttons action trong form add và edit
*/
add_filter('manage_MODULE_KEY_input', [\NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME\MODULE_CLASS_NAMEForm::class, 'fields']);
add_filter('manage_MODULE_KEY_input', [\NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME\MODULE_CLASS_NAMEForm::class, 'buttons']);