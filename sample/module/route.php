<?php

use SkillDo\Support\Facades\Route;

Route::middleware('auth:admin')
    ->prefix('admin/MODULE_KEY')
    ->group(function ()
    {
        $controller = \NAMESPACE_CLASS_NAME\Controllers\Admin\MODULE_CLASS_NAMEController::class;

        Route::match(['get', 'post'], '/', $controller.'@index')->name('admin.MODULE_KEY.index');
        Route::match(['get', 'post'], '/add', $controller.'@add')->name('admin.MODULE_KEY.add');
        Route::match(['get', 'post'], '/edit/{id}', $controller.'@edit')->where('id', '[0-9]+')->name('admin.MODULE_KEY.edit');
    });
