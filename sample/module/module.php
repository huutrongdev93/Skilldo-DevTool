<?php
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

include_once 'MODULE_FOLDER.button.php';

Class MODULE_CLASS_NAMEAdmin {
    static function navigation(): void
    {
        AdminMenu::add('MODULE_FOLDER', 'MODULE_KEY', 'plugins/MODULE_FOLDER', [
            'callback' => 'MODULE_CLASS_NAMEAdmin::page', //function run
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
        $table = new MODULE_CLASS_NAME\Admin\Table();

        Admin::view('components/page-default/page-index', [
            'module'    => 'MODULE_KEY',
            'name'      => trans(''),
            'table'     => $table,
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
            ->text('title', [
                'label' => '',
                'validations'   => [
                    Language::default() => Rule::make()->notEmpty()
                ]
            ])
            ->wysiwygShort('excerpt', ['label' => trans('form.info.excerpt')]);
        $form->right
            ->addGroup('media', trans('form.group.media'))
            ->image('image', ['label' => trans('form.media.image')]);

        $form->right
            ->addGroup('seo', 'Seo')
            ->text('slug', ['label' => 'Slug'])
            ->text('seo_title', ['label' => 'Meta title'])
            ->text('seo_keywords', ['label' => 'Meta Keyword'])
            ->textarea('seo_description', ['label' => 'Meta Description']);
        return $form;
    }
    static function save($id, $insertData): int|SKD_Error
    {
        return SkillDo\Model\MODULE_MODEL_NAME::insert($insertData);
    }
}
add_action('init', 'MODULE_CLASS_NAMEAdmin::navigation');
add_filter('manage_MODULE_KEY_input', 'MODULE_CLASS_NAMEAdmin::form');
add_filter('form_submit_MODULE_KEY', 'MODULE_CLASS_NAMEAdmin::save', 10, 2);