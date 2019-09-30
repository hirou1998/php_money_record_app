<?php

$data = file_get_contents('php://input');
$decoded = json_decode($data, JSON_UNESCAPED_UNICODE);

require_once '../model/moneyModel.php';
$moneyModel = new moneyModel();
$result = $moneyModel->getCurrencyList_new($decoded['input_word']);

echo json_encode($result, JSON_UNESCAPED_UNICODE);

?>