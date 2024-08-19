<?php
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

include_once 'MODULE_KEY.button.php';

Class AdminMODULE_CLASS_NAME {
    static function navigation(): void
    {
        AdminMenu::add('MODULE_KEY', 'MODULE_KEY', 'plugins/MODULE_KEY', [
            'callback' => 'AdminMODULE_CLASS_NAME::page', //function run
        ]);
    }
    static function page(Request $request, $params): void
    {

        $view = $params[0] ?? '';

        switch ($view) {
            case 'add':
                self::pageAdd($request);
                break;
            case 'edit':
                self::pageEdit($request, $params);
                break;
            default:
                self::pageList($request);
                break;
        }
    }
    static function pageList(Request $request): void
    {
        $table = new AdminMODULE_CLASS_NAMETable([
            'items' => [],
            'table' => 'MODULE_MODEL_TABLE',
            'model' => model('MODULE_MODEL_TABLE'),
            'module'=> 'MODULE_KEY',
        ]);

        Admin::view('components/page-default/page-index', [
            'module'    => 'MODULE_KEY',
            'name'      => trans(''),
            'table'     => $table,
            'tableId'     => 'admin_table_MODULE_KEY_list',
            'limitKey'    => 'admin_MODULE_KEY_limit',
            'ajax'        => 'AdminMODULE_CLASS_NAMEAjax::load',
        ]);
    }
    static function pageAdd(Request $request): void
    {
        Admin::creatForm('MODULE_KEY');

        Admin::view('components/page-default/page-save', [
            'module'  => 'MODULE_KEY',
            'object' => []
        ]);
    }
    static function pageEdit(Request $request, $params): void
    {
        $id     = (int)$params[1] ?? 0;

        $object = SkillDo\Model\MODULE_MODEL_NAME::get($id);

        if(have_posts($object)) {

            Admin::creatForm('MODULE_KEY', $object);

            Admin::view('components/page-default/page-save', [
                'module'  => 'MODULE_KEY',
                'object' => $object
            ]);
        }
    }
    /**
     * @throws Exception
     */
    static function form(FormAdmin $form): FormAdmin {
        $form->lang
            ->addGroup('info', trans('form.group.info'))
            ->addFieldLang('title', 'text', [
                'label' => '',
                'validations'   => [
                    Language::default() => Rule::make()->notEmpty()
                ]
            ])
            ->addFieldLang('excerpt', 'wysiwyg-short', ['label' => trans('form.info.excerpt')]);
        $form->right
            ->addGroup('media', trans('form.group.media'))
            ->addField('image', 'image', ['label' => trans('form.media.image')]);

        $form->right
            ->addGroup('seo', 'Seo')
            ->addField('slug', 'text', ['label' => 'Slug'])
            ->addField('seo_title', 'text', ['label' => 'Meta title'])
            ->addField('seo_keywords', 'text', ['label' => 'Meta Keyword'])
            ->addField('seo_description', 'textarea', ['label' => 'Meta Description']);
        return $form;
    }
    static function save($id, $insertData): int|SKD_Error
    {
        return SkillDo\Model\MODULE_MODEL_NAME::insert($insertData);
    }
}
add_action('init', 'AdminMODULE_CLASS_NAME::navigation');
add_filter('manage_MODULE_KEY_input', 'AdminMODULE_CLASS_NAME::form');
add_filter('form_submit_MODULE_KEY', 'AdminMODULE_CLASS_NAME::save', 10, 2);