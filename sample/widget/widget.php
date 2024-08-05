<?php
use SkillDo\Widget\Widget;
use SkillDo\Widget\WidgetField;

class WIDGET_CLASS_NAME extends Widget {

    function __construct()
    {
        parent::__construct('WIDGET_CLASS', ' (style WIDGET_STYLE_NUMBER)');
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
            'header'   => ThemeWidget::heading($this->name, $this->options->heading),
        ]);
    }

    public function cssBuilder(): string
    {
        return $this->cssBuild();
    }

    public function default(): void
    {
    }
}
Widget::add('WIDGET_CLASS_NAME');