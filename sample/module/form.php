<?php
namespace NAMESPACE_CLASS_NAME\Modules\Admin\MODULE_CLASS_NAME;

use SkillDo\Cms\FormAdmin\FormAdmin;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Language;
use SkillDo\Validate\Rule;

class MODULE_CLASS_NAMEForm
{
    static function fields(FormAdmin $form): FormAdmin
    {
        $form->setModel(\NAMESPACE_CLASS_NAME\Models\MODULE_CLASS_NAME::class);

        $form->lang()
            ->addGroup('info', trans('form.group.info'))
            ->text('title', [
                'label' => '',
                'validations'   => [
                    Language::default() => Rule::make()->notEmpty()
                ]
            ])
            ->wysiwygShort('excerpt', ['label' => trans('form.info.excerpt')]);

        $form->right()
            ->addGroup('media', trans('form.group.media'))
            ->image('image', ['label' => trans('form.media.image')]);

        $form->right()
            ->addGroup('seo', 'Seo')
            ->text('slug', ['label' => 'Slug'])
            ->text('seo_title', ['label' => 'Meta title'])
            ->text('seo_keywords', ['label' => 'Meta Keyword'])
            ->textarea('seo_description', ['label' => 'Meta Description']);

        return $form;
    }

    static function buttons(FormAdmin $form): FormAdmin
    {
        $buttons = [];

        if(Admin::isPage('MODULE_KEY_add'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('back', [
                'href' => route('admin.MODULE_KEY.index'),
                'class' => ['btn-back-to-redirect']
            ]);
        }

        if(Admin::isPage('MODULE_KEY_edit'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('add', ['href' => route('admin.MODULE_KEY.add'), 'text' => '', 'tooltip' => 'Thêm mới']);
            $buttons[] = Admin::button('back', [
                'href' => route('admin.MODULE_KEY.index'),
                'text' => '',
                'tooltip' => 'Quay lại',
                'class' => ['btn-back-to-redirect']
            ]);
        }

        $buttons = apply_filters('MODULE_KEY_form_buttons', $buttons);

        $form->setButtons($buttons);

        return $form;
    }
}
