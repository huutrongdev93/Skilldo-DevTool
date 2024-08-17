<?php
namespace SkillDo\DevTool\Commands;

class SignatureParser
{
    private string $signature;

    private array $arguments = [];

    private array $argumentsOptional = [];

    private array $options = [];

    public function __construct(string $signature)
    {
        $this->signature = $signature;

        $this->parseSignature();
    }

    protected function parseSignature(): void
    {
        // Phân tích signature để lấy danh sách arguments và options
        preg_match_all('/\{(\-\-)?(\w+)(\?)?(=)?(.*?)\}/', $this->signature, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {

            $defaultValue = ($match[4] == "=") ? $match[5] : false;

            if ($match[1] === '--')
            {
                $this->options[$match[2]] = $defaultValue;
            }
            else
            {
                $this->arguments[$match[2]] = $defaultValue;

                if($match[3] == '?') {
                    $this->argumentsOptional[] = $match[2];
                }
            }
        }
    }

    public function parseArguments(array $commandParams): void
    {
        $argumentsOrder = array_keys($this->arguments);

        while (!empty($commandParams)) {

            $arg = array_shift($commandParams);

            if (str_starts_with($arg, '--')) {

                // Handle options
                $optionParts = explode('=', substr($arg, 2), 2);

                $optionName = $optionParts[0];

                if (isset($optionParts[1])) {
                    // Case: --option=value
                    $optionValue = $optionParts[1];

                }
                elseif (!empty($commandParams) && !str_starts_with($commandParams[0], '--')) {
                    // Case: --option value (next argument is a value)
                    $optionValue = array_shift($commandParams);
                }
                else {
                    // Case: --option with no value (boolean true)
                    $optionValue = true;
                }

                $this->options[$optionName] = $optionValue;
            }
            else {

                // Xử lý arguments
                $argName = array_key_first($argumentsOrder) !== null ? $argumentsOrder[array_key_first($argumentsOrder)] : null;

                if ($argName !== null) {

                    $this->arguments[$argName] = $arg;

                    array_shift($argumentsOrder);
                }
                else
                {
                    // Handle additional arguments if any
                    $this->arguments['extra'][] = $arg;
                }
            }
        }
    }

    public function validateInput(array $commandParams): bool
    {
        if(empty($commandParams)) {

            $options = count(array_filter($this->options, fn($value) => $value === null));

            if(!empty($options)) {
                return false;
            }

            $arguments = count(array_filter($this->arguments, fn($value) => $value === null));

            if(!empty($arguments)) {
                return false;
            }

            return true;
        }

        $options = array_filter($this->options, fn($value) => $value === null);

        $arguments = array_filter($this->arguments, fn($value) => $value === null);

        $optionsParams = [];

        $argumentsParams = [];

        $argumentsOrder = array_keys($this->arguments);

        while (!empty($commandParams)) {

            $arg = array_shift($commandParams);

            if (str_starts_with($arg, '--')) {

                // Handle options
                $optionParts = explode('=', substr($arg, 2), 2);

                $optionName = $optionParts[0];

                if (isset($optionParts[1])) {
                    // Case: --option=value
                    $optionValue = $optionParts[1];
                }
                elseif (!empty($commandParams) && !str_starts_with($commandParams[0], '--')) {
                    // Case: --option value (next argument is a value)
                    $optionValue = array_shift($commandParams);
                }
                else {
                    // Case: --option with no value (boolean true)
                    $optionValue = true;
                }

                $optionsParams[$optionName] = $optionValue;
            }
            else {

                // Xử lý arguments
                $argName = array_key_first($argumentsOrder) !== null ? $argumentsOrder[array_key_first($argumentsOrder)] : null;

                if ($argName !== null) {

                    $argumentsParams[$argName] = $arg;

                    array_shift($argumentsOrder);
                }
                else
                {
                    // Handle additional arguments if any
                    $argumentsParams['extra'][] = $arg;
                }
            }
        }

        if(!empty($options)) {

            if(empty($optionsParams)) {
                return false;
            }

            $optionsParams = array_keys($optionsParams);

            $hasOption = false;

            foreach ($options as $optionKey => $optionValue) {
                if(in_array($optionKey, $optionsParams) !== false) {
                    $hasOption = true;
                    break;
                }
            }

            if(!$hasOption) {
                return false;
            }
        }

        if(!empty($arguments)) {

            if(empty($argumentsParams)) {

                $argumentsKey = array_keys($arguments);

                $argumentsKey = array_filter($argumentsKey, function ($argument) {
                    return !in_array($argument, $this->argumentsOptional);
                });

                return !have_posts($argumentsKey);
            }

            $argumentsParams = array_keys($argumentsParams);

            foreach ($arguments as $argumentKey => $argumentValue) {
                if(in_array($argumentKey, $argumentsParams) === false && !in_array($argumentKey, $this->argumentsOptional)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
