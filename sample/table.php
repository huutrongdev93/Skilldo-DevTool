<?php
use SkillDo\Form\Form;
use SkillDo\Http\Request;
use SkillDo\Table\Columns\ColumnCheckbox;
use SkillDo\Table\Columns\ColumnEdit;
use SkillDo\Table\Columns\ColumnImage;
use SkillDo\Table\Columns\ColumnText;
use SkillDo\Table\SKDObjectTable;

class TABLE_CLASS_NAME extends SKDObjectTable {

    protected string $module = 'TABLE_NAME';

    protected mixed $model = '';

    function getColumns() {

        $this->_column_headers = [
            'cb'        => 'cb',
            'image'     => [
                'label'  => trans('table.image'),
                'column' => fn ($item, $args) => ColumnImage::make('image', $item, $args),
            ],
            'title'     => [
                'label'  => trans('table.title'),
                'column' => fn ($item, $args) => ColumnText::make('title', $item, $args)->title(),
            ],
            'order' => [
                'label'  => trans('table.order'),
                'column' => fn ($item, $args) => ColumnEdit::make('order', $item, $args),
            ],
            'public' => [
                'label' => trans('table.public'),
                'column' => fn ($item, $args) => ColumnCheckbox::make('public', $item, $args),
            ],
            'action' => trans('table.action')
        ];


        $this->_column_headers = apply_filters( "manage_TABLE_NAME_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        return apply_filters('table_TABLE_NAME_columns_action', [], $item);
    }

    function headerFilter(Form $form, Request $request)
    {
        return apply_filters('admin_TABLE_NAME_table_form_filter', $form);
    }

    function headerSearch(Form $form, Request $request): Form
    {
        return apply_filters('admin_TABLE_NAME_table_form_search', $form, $request);
    }

    public function queryFilter(Qr $query, \SkillDo\Http\Request $request): Qr
    {
        return $query;
    }

    public function queryDisplay(Qr $query, \SkillDo\Http\Request $request, $data = []): Qr
    {
        $query = parent::queryDisplay($query, $request, $data);

        $query
            ->orderBy('created', 'desc');

        return $query;
    }

    public function dataDisplay($objects)
    {
        return $objects;
    }
}