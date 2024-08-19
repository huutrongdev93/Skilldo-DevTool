<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Http\Request;

class AdminMODULE_CLASS_NAMEAjax {

    #[NoReturn]
    static function load(Request $request): void
    {
        if($request->isMethod('post')) {

            $page    = $request->input('page');

            $limit   = (empty($request->input('limit'))) ? 16 : (int)$request->input('limit');

            $keyword = $request->input('keyword');

            $recordsTotal   = $request->input('recordsTotal');

            $args = Qr::set();

            $args = apply_filters('admin_MODULE_KEY_controllers_index_args_before_count', $args);

            if(!is_numeric($recordsTotal)) {
                $recordsTotal = apply_filters('admin_MODULE_KEY_controllers_index_count', SkillDo\Model\MODULE_MODEL_NAME::count($args), $args);
            }

            # [List data]
            $args
                ->limit($limit)
                ->offset(($page - 1)*$limit)
                ->orderBy('order')
                ->orderBy('created', 'desc');

            $args = apply_filters('admin_MODULE_KEY_controllers_index_args', $args);

            $objects = apply_filters('admin_MODULE_KEY_controllers_index_objects', SkillDo\Model\MODULE_MODEL_NAME::gets($args), $args);

            $args = [
                'items' => $objects,
                'table' => 'MODULE_MODEL_TABLE',
                'model' => model('MODULE_MODEL_TABLE'),
                'module'=> 'MODULE_KEY',
            ];

            $table = new AdminMODULE_CLASS_NAMETable($args);
            $table->setTrash(true);
            $table->get_columns();
            ob_start();
            $table->display_rows_or_message();
            $html = ob_get_contents();
            ob_end_clean();

            /**
             * Bulk Actions
             */
            $buttonsBulkAction = apply_filters('table_MODULE_KEY_bulk_action_buttons', []);

            $bulkAction = Admin::partial('include/table/header/bulk-action-buttons', [
                'actionList' => $buttonsBulkAction
            ]);

            $result['data'] = [
                'html'          => base64_encode($html),
                'bulkAction'    => base64_encode($bulkAction),
            ];

            $result['pagination'] = [
                'limit' => $limit,
                'total' => $recordsTotal,
                'page'  => (int)$page,
            ];

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }
}

Ajax::admin('AdminMODULE_CLASS_NAMEAjax::load');