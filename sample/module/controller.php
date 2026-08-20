<?php

namespace NAMESPACE_CLASS_NAME\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class MODULE_CLASS_NAMEController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        Cms::setData('module', 'MODULE_KEY');
    }

    public function index(Request $request)
    {
        Cms::setData('table', new \NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME\MODULE_CLASS_NAMETable());

        return Cms::view('admin::resources/page-default/page-index', [
            'title' => 'Module List',
        ]);
    }

    public function add(Request $request)
    {
        Cms::setData('form', FormAdminHelper::getForm('MODULE_KEY'));

        return Cms::view('admin::resources/page-default/page-save');
    }

    public function edit(Request $request, $id)
    {
        $object = \NAMESPACE_CLASS_NAME\Models\MODULE_CLASS_NAME::find($id);

        if(!hasItems($object))
        {
            return Admin::pageNotFound();
        }

        Cms::setData('object', $object);

        Cms::setData('form', FormAdminHelper::getForm('MODULE_KEY', $object));

        return Cms::view('admin::resources/page-default/page-save');
    }
}