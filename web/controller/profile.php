<?php
session_start();
$data = file_get_contents('php://input');
$decoded = json_decode($data, true);

if($_SERVER['REQUEST_METHOD'] != 'POST' || $_SESSION['tmp_token'] != $decoded['tmp_token']){
	$url = "http://" . $_SERVER['HTTP_HOST'] . '/db_practice/index.php';
	header('Location: ' . $url);
}else{
	$userid = $_SESSION['user_id'];
	require_once '../model/userModel.php';
	$usermodel = new UserModel();

	$usermodel->saveUserProfile($decoded['username'], $decoded['currency'], $userid);

	$result = $usermodel->getUserProfile($userid);

	$_SESSION['username'] = $result['username'];

	$list = array(
		'username' => $result['username'],
		'pic' => $result['pic'],
		'currency' => $result['currency']
	);

	echo json_encode($list, true);
}
?>