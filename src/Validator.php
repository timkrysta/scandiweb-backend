<?php

namespace Timkrysta;

use Timkrysta\DB;

/** 
 * -----------------------------------------------------------------------------
 * ------------------------ Available Validation Rules -------------------------
 * -----------------------------------------------------------------------------
 * Below is a list of all available validation rules and their function:
 * 
 * 
 * -----------------------------------------------------------------------------
 * required
 * The field under validation must be present in the input data and not empty. A field is "empty" if it meets one of the following criteria:
 * -----------------------------------------------------------------------------
 * required_if:anotherfield,value,...
 * The field under validation must be present and not empty if the anotherfield field is equal to any value.
 * -----------------------------------------------------------------------------
 * string
 * The field under validation must be a string.
 * -----------------------------------------------------------------------------
 * numeric
 * The field under validation must be numeric. (https://www.php.net/manual/en/function.is-numeric.php)
 * -----------------------------------------------------------------------------
 * array
 * The field under validation must be a PHP array.
 * -----------------------------------------------------------------------------
 * alpha_dash
 * The field under validation must be entirely Unicode alpha-numeric characters contained in \p{L}, \p{M}, \p{N}, as well as ASCII dashes (-) and ASCII underscores (_).
 * -----------------------------------------------------------------------------
 * between:min,max
 * The field under validation must have a size between the given min and max (inclusive). Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.
 * -----------------------------------------------------------------------------
 * in:foo,bar,...
 * The field under validation must be included in the given list of values.
 * -----------------------------------------------------------------------------
 * unique:table,column
 * The field under validation must not exist within the given database table.
 * -----------------------------------------------------------------------------
 * exists:table,column
 * The field under validation must exist in a given database table.
 * -----------------------------------------------------------------------------
 */
class Validator
{
    /**
     * Errors that were encoutered during validation
     */
    public array $errors = [];

    public function __construct(
        private array $data,
        private array $validationRules
    ) {
        foreach ($validationRules as $input => $rules) {
            $this->validateInput($input, $rules);
        }
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     */
    public function validated(): array
    {
        return array_diff_key(
            array_intersect_key($this->data, $this->validationRules),
            $this->errors
        );
    }
    
    /**
     * Validate single input for all validation rules it has
     *
     * @param  mixed $input
     * @param  array $rules
     * @return void
     */
    private function validateInput(mixed $input, array $rules): void
    {
        foreach ($rules as $rule) {
            $ruleParts = explode(':', $rule);
            $ruleName = $ruleParts[0];
            $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

            switch ($ruleName) {
                case 'required':
                    if (!isset($this->data[$input])) {
                        $this->errors[$input][] = "The {$input} field is required.";
                    }
                    break;
                case 'required_if':
                    $otherField = $ruleParams[0];
                    $otherValue = $ruleParams[1];

                    if (
                        isset($this->data[$otherField]) 
                        && $this->data[$otherField] === $otherValue 
                        && !isset($this->data[$input])
                    ) {
                        $this->errors[$input][] = "The {$input} field is required when {$otherField} is {$otherValue}.";
                    }
                    break;
                case 'string':
                    if (!isset($this->data[$input])) break;
                    if (!is_string($this->data[$input])) {
                        $this->errors[$input][] = "The {$input} must be a string.";
                    }
                    break;
                case 'numeric':
                    if (!isset($this->data[$input])) break;
                    if (!is_numeric($this->data[$input])) {
                        $this->errors[$input][] = "The {$input} must be a number.";
                    }
                    break;
                case 'array':
                    if (!isset($this->data[$input])) break;
                    if (!is_array($this->data[$input])) {
                        $this->errors[$input][] = "The {$input} must be an array.";
                    }
                    break;
                case 'alpha_dash':
                    if (!isset($this->data[$input])) break;
                    if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $this->data[$input])) {
                        $this->errors[$input][] = "The {$input} must only contain letters, numbers, dashes and underscores.";
                    }
                    break;
                case 'between':
                    if (!isset($this->data[$input])) break;
                    $min = $ruleParams[0];
                    $max = $ruleParams[1];

                    if (in_array('string', $rules)) {
                        $inputLength = strlen($this->data[$input]);
                        if ($inputLength < $min || $inputLength > $max) {
                            $this->errors[$input][] = "The {$input} must be between {$min} and {$max} characters.";
                        }
                    }
                    if (in_array('numeric', $rules)) {
                        if ($this->data[$input] < $min || $this->data[$input] > $max) {
                            $this->errors[$input][] = "The {$input} must be between {$min} and {$max}.";
                        }
                    }
                    break;
                case 'in':
                    if (!isset($this->data[$input])) break;
                    if (!in_array($this->data[$input], $ruleParams)) {
                        $this->errors[$input][] = "The selected {$input} is invaild.";
                    }
                    break;
                case 'unique':
                    if (!isset($this->data[$input])) break;
                    $result = $this->getRecordCount($input, $ruleParams);
                    if ($result > 0) {
                        $this->errors[$input][] = "The {$input} has already been taken.";
                    }
                    break;
                case 'exists':
                    if (!isset($this->data[$input])) break;
                    $result = $this->getRecordCount($input, $ruleParams);
                    if ($result <= 0) {
                        $this->errors[$input][] = "The selected {$input} is invaild.";
                    }
                    break;
            }
        }
    }
    
    /**
     * Get record count.
     *
     * @param  mixed $input
     * @param  mixed $ruleParams
     * @return int
     */
    private function getRecordCount(mixed $input, mixed $ruleParams): int
    {
        $table  = $ruleParams[0];
        $column = $ruleParams[1];
        $db = new DB();
        $result = $db->getRecordCount(
            "SELECT * FROM {$table} WHERE {$column} = ?;",
            's',
            [$this->data[$input]]
        );
        return $result;
    }
}
