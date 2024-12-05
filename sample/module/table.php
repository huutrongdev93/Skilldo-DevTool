<?php
namespace MODULE_CLASS_NAME\Admin;
use Admin;
use SkillDo\Form\Form;
use SkillDo\Http\Request;
use SkillDo\Table\Columns\ColumnCheckbox;
use SkillDo\Table\Columns\ColumnEdit;
use SkillDo\Table\Columns\ColumnImage;
use SkillDo\Table\Columns\ColumnText;
use SkillDo\Table\SKDObjectTable;
use Url;
use Qr;

class Table extends SKDObjectTable {

    protected string $module = 'MODULE_KEY';

    protected mixed $model = \SkillDo\Model\MODULE_MODEL_NAME::class;

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
            'href' => Url::admin('plugins/MODULE_FOLDER/edit/'.$item->id),
            'icon' => Admin::icon('edit')
        ]);

        $buttons[] = Admin::btnDelete([
            'id' => $item->id,
            'model' => 'SkillDo\Model\MODULE_MODEL_NAME',
            'description' => trans('message.page.confirmDelete', ['title' => html_escape($item->title)])
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

    public function queryFilter(Qr $query, Request $request): Qr
    {
        return $query;
    }

    public function queryDisplay(Qr $query, Request $request, $data = []): Qr
    {
        $query = parent::queryDisplay($query, $request, $data);

        return $query;
    }
}