<?php

use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Theme;
use SkillDo\Cms\Widget\Widget;
use Theme\Supports\ThemeSidebar;

class WIDGET_CLASS_NAME extends Widget {

    function __construct() {
        parent::__construct('WIDGET_CLASS', '(style WIDGET_STYLE_NUMBER)');
        $this->setTags('WIDGET_TAG');
        $this->assets('assets/WIDGET_FILE_STYLE.less');
        $this->assets('assets/WIDGET_FILE_JS.js');
    }

    public function form(): void
    {
        $this->tabs('generate')->adds(function (Form $form) {
            //Tab thông tin
        });

        $this->tabs('style')->adds(function (Form $form) {
            //Tab style
        });

        $this->tabs('advanced')->adds(function (Form $form) {
            //Tab nâng cao
        });

        parent::form();
    }

    public function widget(): void
    {
        Theme::view($this->getDir().'views/view', [
            'id'        => $this->id,
            'name'      => $this->name,
            'header'    => ThemeSidebar::heading($this->name, $this->options->heading ?? [], '.js_'.$this->key.'_'.$this->id),
            'classId'   => $this->getClassId(),
        ]);
    }

    public function cssBuilder(): string
    {
        return $this->cssBuild();
    }

    public function default(): void
    {
    }

    public function update($new_instance, $old_instance )
    {
        return $new_instance;
    }
}
