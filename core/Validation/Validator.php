<?php
namespace Core\Validation;

class Validator
{
    public static function make(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleStr) {
            $rulesArr = explode('|', $ruleStr);
            $value = $data[$field] ?? null;
            foreach ($rulesArr as $rule) {
                $params = null;
                if (strpos($rule, ':') !== false) {
                    [$rule, $params] = explode(':', $rule, 2);
                }
                switch ($rule) {
                    case 'required':
                        if (is_null($value) || $value === '') {
                            $errors[$field][] = 'This field is required.';
                        }
                        break;
                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Invalid email address.';
                        }
                        break;
                    case 'min':
                        if ($value && strlen($value) < (int)$params) {
                            $errors[$field][] = "Minimum $params characters.";
                        }
                        break;
                    case 'max':
                        if ($value && strlen($value) > (int)$params) {
                            $errors[$field][] = "Maximum $params characters.";
                        }
                        break;
                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = 'Must be numeric.';
                        }
                        break;
                }
            }
        }
        return $errors;
    }
}
