<?php
namespace NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME;

use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Table\Columns\ColumnEdit;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class MODULE_CLASS_NAMETable extends SKDObjectTable {

    protected string $module = 'MODULE_KEY';

    protected mixed $model = \NAMESPACE_CLASS_NAME\Models\MODULE_CLASS_NAME::class;

    function getColumns() {

        $this->_column_headers = [
            'cb'        => 'cb',
            /*'image'     => [
                'label'  => trans('table.image'),
                'column' => fn ($item, $args) => ColumnImage::make('image', $item, $args),
            ],*/
            'title'     => [
                'label'  => trans('table.title'),
                'column' => fn ($item, $args) => ColumnText::make('title', $item, $args),
            ],
            'order' => [
                'label'  => trans('table.order'),
                'column' => fn ($item, $args) => ColumnEdit::make('order', $item, $args),
            ],
            /*'public' => [
                'label' => trans('table.public'),
                'column' => fn ($item, $args) => ColumnCheckbox::make('public', $item, $args),
            ],*/
            'action' => trans('table.action')
        ];

        $this->_column_headers = apply_filters( "manage_MODULE_KEY_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $buttons[] = Admin::button('blue', [
            'href' => route('admin.MODULE_KEY.edit', [$item->id]),
            'icon' => Admin::icon('edit')
        ]);

        $buttons[] = Admin::btnDelete([
            'id' => $item->id,
            'model' => $this->model,
            'description' => trans('admin::message.page.confirmDelete', ['title' => html_escape($item->title)])
        ]);

        return apply_filters('table_MODULE_KEY_columns_action', $buttons, $item);
    }

    function headerFilter(Form $form, Request $request)
    {
        return apply_filters('admin_MODULE_KEY_table_form_filter', $form);
    }

    function headerSearch(Form $form, Request $request): Form
    {
        return apply_filters('admin_MODULE_KEY_table_form_search', $form, $request);
    }

    function headerButton(): array
    {
        $buttons['add'] = Admin::button('add', [
            'href' => route('admin.MODULE_KEY.add'),
            'text' => trans('button.add')
        ]);

        $buttons['reload'] = Admin::button('reload');

        return $buttons;
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        return $query;
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        $query = parent::queryDisplay($query, $request, $data);

        return $query;
    }
}