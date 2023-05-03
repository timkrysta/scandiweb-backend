<?php

namespace Timkrysta;

use Timkrysta\DB;


class Validator {
    public array $errors = [];

    public function __construct(
        private array $requestData,
        private array $validationRules
    ) {
        foreach ($validationRules as $input => $rules) {
            foreach ($rules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];
        
                switch ($ruleName) {
                    case 'required':
                        if (!isset($_POST[$input])) {
                            $this->errors[$input][] = "Pole {$input} jest wymagane.";
                        }
                        break;
                    case 'required_if':
                        $otherField = $ruleParams[0];
                        $otherValue = $ruleParams[1];
        
                        if (isset($_POST[$otherField]) && $_POST[$otherField] === $otherValue && !isset($_POST[$input])) {
                            $this->errors[$input][] = "Pole {$input} jest wymagane gdy {$otherField} ma wartość {$otherValue}.";
                        }
                        break;
                    case 'string':
                        if (!isset($_POST[$input])) break;
                        if (!is_string($_POST[$input])) {
                            $this->errors[$input][] = "Pole {$input} musi być ciągiem znaków.";
                        }
                        break;
                    case 'numeric':
                        if (!isset($_POST[$input])) break;
                        if (!is_numeric($_POST[$input])) {
                            $this->errors[$input][] = "Pole {$input} musi być liczbą.";
                        }
                        break;
                    case 'alpha_dash':
                        if (!isset($_POST[$input])) break;
                        if (!preg_match('/^[\p{L}\p{N}_-]+$/u', $_POST[$input])) {
                            $this->errors[$input][] = "Pole {$input} może zawierać jedynie litery, cyfry i myślniki.";
                        }
                        break;
                    case 'between':
                        if (!isset($_POST[$input])) break;
                        $min = $ruleParams[0];
                        $max = $ruleParams[1];
                        
                        if (in_array('string', $rules)) {
                            $inputLength = strlen($_POST[$input]);
                            if ($inputLength < $min || $inputLength > $max) {
                                $this->errors[$input][] = "Pole {$input} musi zawierać się w granicach {$min} - {$max} znaków.";
                            }
                        }
                        if (in_array('numeric', $rules)) {
                            if ($_POST[$input] < $min || $_POST[$input] > $max) {
                                $this->errors[$input][] = "Pole {$input} musi zawierać się w granicach {$min} - {$max} znaków.";
                            }
                        }
                        break;
                    case 'in':
                        if (!isset($_POST[$input])) break;
                        if (!in_array($_POST[$input], $ruleParams)) {
                            $this->errors[$input][] = "Zaznaczony element {$input} jest nieprawidłowy.";
                        }
                        break;
                    case 'unique':
                        if (!isset($_POST[$input])) break;
                        $table  = $ruleParams[0];
                        $column = $ruleParams[1];
                        $db = new DB('localhost', 'root', '', 'web_developer_test_assignment');
                        $result = $db->getRecordCount(
                            "SELECT * FROM {$table} WHERE {$column} = ?;", 
                            's', 
                            [$_POST[$input]]
                        );
                        if ($result > 0) {
                            $this->errors[$input][] = "Taki {$input} już występuje.";
                        }
                        break;
                }
            }
        }
    }
    public function fails(): bool
    {
        return count($this->errors) > 0;
    }
}