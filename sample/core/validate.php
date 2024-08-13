<?php
use SkillDo\Validate\MissingRequiredParameterException;
use SkillDo\Validate\Rule;

class VALIDATE_CLASS_NAME extends Rule
{
    protected string $key = '';

    public function init(string $param1, string $param2): array
    {
        return [
            'param1'    => $param1,
            'param2'    => $param2,
            'message'   => trans('') //error message
        ];
    }

    /**
     * @throws MissingRequiredParameterException
     */
    public function check($value): bool
    {
        $this->requireParameters(['param1', 'param2']);

        if(empty($value)) return false;

        return true;
    }
}