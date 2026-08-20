<?php

use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Element\Element;
use SkillDo\Cms\Support\Theme;

class ELEMENT_CLASS_NAME extends Element
{
    function __construct()
    {
        parent::__construct('ELEMENT_CLASS_NAME', 'ELEMENT_LABEL (style ELEMENT_STYLE_NUMBER)');

        $this->assets('assets/ELEMENT_FILE_STYLE.less');
        $this->assets('assets/ELEMENT_FILE_JS.js');
    }

    public function icon(): string
    {
        return 'icon-box';
    }

    public function category(): string
    {
        return 'basic';
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
