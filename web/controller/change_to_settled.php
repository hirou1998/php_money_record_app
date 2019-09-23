<?php

session_start();
$data = file_get_contents('php://input');
$decoded = json_decode($data, true);
require_once '../model/moneyModel.php';
$moneymodel = new MoneyModel();

if($_SERVER['REQUEST_METHOD'] != 'POST' || $_SESSION['tmp_token'] != $decoded['tmp_token']){
	$url = '../index.php';
	header('Location: ' . $url);
}elseif($decoded['change'] == true){
	$id = $decoded['id'];
	$moneymodel->changeToSettled($id);
	echo "変更しました";
}else{
	$id = $decoded['id'];
	//var_dump($decoded);
	$result = $moneymodel->getMoneyRecordById($id);
	echo json_encode($result, true);
}

?>