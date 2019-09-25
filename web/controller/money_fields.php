<?php
session_start();
$data = file_get_contents('php://input');
$decoded = json_decode($data, true);

var_dump($_SESSION['tmp_token'] != $decoded['tmp_token']);

if($_SERVER['REQUEST_METHOD'] != 'POST' || $_SESSION['tmp_token'] != $decoded['tmp_token']){
	$url = '../index.php';
	header('Location: ' . $url);
}else{
	$userid = $_SESSION['user_id'];
	$type = $decoded['type'];
	$person = $decoded['person'];
	$status = $decoded['status'];
	$amount = $decoded['amount'];
	$currency = $decoded['currency'];
	$comment = $decoded['comment'];
	$deadline = $decoded['deadline'];
	$date = $decoded['date'];

	if($status == "未清算"){
		$status_number = 0;
	}else{
		$status_number = 1;
	}

	require_once '../model/moneyModel.php';
	$moneymodel = new MoneyModel();

	$moneymodel->registerMoneyRecord($userid, $type, $person, $status, $amount, $currency, $comment, $deadline, $date, $status_number);

	echo json_encode($decoded, true);
}
?>