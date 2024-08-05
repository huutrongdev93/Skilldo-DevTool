<?php

use SkillDo\Widget\WidgetSidebar;
use SkillDo\Widget\WidgetField;

class WIDGET_CLASS_NAME extends WidgetSidebar {

    function __construct() {
        parent::__construct('WIDGET_CLASS', '(style WIDGET_STYLE_NUMBER)');
        $this->setTags('WIDGET_TAG');
        $this->assets('assets/WIDGET_FILE_STYLE.less');
        $this->assets('assets/WIDGET_FILE_JS.js');
    }

    public function form(): void
    {
        $this->tabs('generate')->adds(function (WidgetField $form) {
            //Tab thông tin
        });

        $this->tabs('style')->adds(function (WidgetField $form) {
            //Tab style
        });

        $this->tabs('advanced')->adds(function (WidgetField $form) {
            //Tab nâng cao
        });

        parent::form();
    }

    public function widget(): void
    {
        Theme::view($this->getDir().'views/view', [
            'id'        => $this->id,
            'name'      => $this->name,
            'header'    => ThemeSidebar::heading($this->name, $this->options->heading),
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