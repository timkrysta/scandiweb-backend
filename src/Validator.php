<?php

namespace Timkrysta;

use Timkrysta\DB;


class Validator {
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

    

    private function validateInput($input, $rules)
    {
        foreach ($rules as $rule) {
            $ruleParts = explode(':', $rule);
            $ruleName = $ruleParts[0];
            $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];
    
            switch ($ruleName) {
                case 'required':
                    if (!isset($this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} jest wymagane.";
                    }
                    break;
                case 'required_if':
                    $otherField = $ruleParams[0];
                    $otherValue = $ruleParams[1];
    
                    if (isset($this->data[$otherField]) && $this->data[$otherField] === $otherValue && !isset($this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} jest wymagane gdy {$otherField} ma wartość {$otherValue}.";
                    }
                    break;
                case 'string':
                    if (!isset($this->data[$input])) break;
                    if (!is_string($this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} musi być ciągiem znaków.";
                    }
                    break;
                case 'numeric':
                    if (!isset($this->data[$input])) break;
                    if (!is_numeric($this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} musi być liczbą.";
                    }
                    break;
                case 'array':
                    if (!isset($this->data[$input])) break;
                    if (!is_array($this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} musi być tablicą.";
                    }
                    break;
                case 'alpha_dash':
                    if (!isset($this->data[$input])) break;
                    if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $this->data[$input])) {
                        $this->errors[$input][] = "Pole {$input} może zawierać jedynie litery, cyfry i myślniki.";
                    }
                    break;
                case 'between':
                    if (!isset($this->data[$input])) break;
                    $min = $ruleParams[0];
                    $max = $ruleParams[1];
                    
                    if (in_array('string', $rules)) {
                        $inputLength = strlen($this->data[$input]);
                        if ($inputLength < $min || $inputLength > $max) {
                            $this->errors[$input][] = "Pole {$input} musi zawierać się w granicach {$min} - {$max} znaków.";
                        }
                    }
                    if (in_array('numeric', $rules)) {
                        if ($this->data[$input] < $min || $this->data[$input] > $max) {
                            $this->errors[$input][] = "Pole {$input} musi zawierać się w granicach {$min} - {$max} znaków.";
                        }
                    }
                    break;
                case 'in':
                    if (!isset($this->data[$input])) break;
                    if (!in_array($this->data[$input], $ruleParams)) {
                        $this->errors[$input][] = "Zaznaczony element {$input} jest nieprawidłowy.";
                    }
                    break;
                case 'unique':
                    if (!isset($this->data[$input])) break;
                    $result = $this->getRecordCount($input, $ruleParams);
                    if ($result > 0) {
                        $this->errors[$input][] = "Taki {$input} już występuje.";
                    }
                    break;
                case 'exists':
                    if (!isset($this->data[$input])) break;
                    $result = $this->getRecordCount($input, $ruleParams);
                    if ($result <= 0) {
                        $this->errors[$input][] = "Zaznaczone pole {$input} jest nieprawidłowe.";
                    }
                    break;
            }
        }
    }

    private function getRecordCount($input, $ruleParams)
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