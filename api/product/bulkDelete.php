<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' .'vendor/autoload.php';

use Timkrysta\Api;
use Timkrysta\DB;
use Timkrysta\Validator;
use Timkrysta\Response;

Api::exitIfHttpMethodNotIn(['POST']);



# TODO(tim): this is very ugly kind of validation
$validationData = [];
$validationRules = [
    'ids' => ['required', 'array'],
];

if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    foreach ($_POST['ids'] as $index => $id) {
        $key = "ids.{$index}";
        $validationData[$key] = $id;
        $validationRules[$key] = ['numeric', 'exists:products,id'];
    }
    $validationData['ids'] = $_POST['ids'];
}

$validator = new Validator($validationData, $validationRules);

# TODO(tim): validation fails even if we pass X good ids to delete and one non existing
if ($validator->fails()) {
    Response::json([
        'message' => 'Validation Failed',
        'error' => $validator->errors,
    ], 422);
}




$db = new DB();
$values = DB::getQuestionMarksString(count($_POST['ids']));
$db->execute(
    "DELETE FROM products WHERE id IN ({$values})", 
    str_repeat('i', count($_POST['ids'])), 
    $_POST['ids']
);



Response::json([
    'message' => 'Success',
    'deleted_ids' => array_values(array_unique($_POST['ids'] ?? []))
], 200);

