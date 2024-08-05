<?php
use SkillDo\Form\InputBuilder;

class FormFieldClassName extends InputBuilder {

    function __construct($args = [], mixed $value = null) {

        parent::__construct($args, $value);

        $this->type = '';
    }

    public function output(): static
    {
        $this->output .= form_input($this->attributes(true));

        return $this;
    }
}