<?php
namespace FORM_FIELD_NAMESPACE;

use SkillDo\Cms\Form\InputBuilder;

/**
 * Field đã được khai báo trong manifest (plugin.json / theme.json):
 *
 * "cms": { "form": { "fields": { "FORM_FIELD_TYPE": { "class": "..." } } } }
 *
 * Sau khi khai báo, dùng trong form: $form->FORM_FIELD_TYPE('name', ['label' => '...']);
 */
class FORM_FIELD_CLASS_NAME extends InputBuilder {

    function __construct($args = [], mixed $value = null) {

        parent::__construct($args, $value);

        $this->type = 'FORM_FIELD_TYPE';
    }

    public function output(): static
    {
        $this->output .= form_input($this->attributes(true));

        return $this;
    }
}
