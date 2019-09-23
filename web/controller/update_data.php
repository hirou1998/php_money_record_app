<?php
session_start();
$data = file_get_contents('php://input');
$decoded = json_decode($data, true);
require_once '../model/moneyModel.php';
$moneymodel = new MoneyModel();

//var_dump($_SESSION['tmp_token'] != $decoded['tmp_token']);

if($_SERVER['REQUEST_METHOD'] != 'POST' || $_SESSION['tmp_token'] != $decoded['tmp_token']){
	//CSRF

	$url = '../index.php';
	header('Location: ' . $url);

}elseif ($decoded['updated'] == true) {
	//when user updates data

	$id = $decoded['id'];
	$type = $decoded['type'];
	$person = $decoded['person'];
	$status = $decoded['status'];
	$amount = $decoded['amount'];
	$currency = $decoded['currency'];
	$comment = $decoded['comment'];
	$deadline = $decoded['deadline'];
	$moneymodel->updateMoneyRecord($type, $person, $status, $amount, $currency, $comment, $deadline, $id);

}elseif($decoded['delete'] == true){
	//when user deletes data

	$id = $decoded['record_id'];
	$moneymodel->deleteMoneyRecord($id);

}else{
	//when this file is called for the first time

	$record_id = $decoded['record_id'];
	$result = $moneymodel->getMoneyRecordById($record_id);
	echo json_encode($result, true);

}
?>