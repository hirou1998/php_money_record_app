<?php

if($_SERVER['REQUEST_METHOD'] == "POST"){

	require_once '../model/userModel.php';
	$usermodel = new UserModel;

	$data = file_get_contents('php://input');
	$decoded = json_decode($data, true);
	$username = $usermodel->testInput($decoded['username']);
	$password = $usermodel->testInput($decoded['password']);

	$message = $usermodel->loginUser($username, $password);

	if($message == null){
		$loggedIn = true;
	}else{
		$loggedIn = false;
	}

	$data = array(
		'message' => $message,
		'loggedIn' => $loggedIn
	);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
	die("ログイン画面からアクセスしてください。");
}


?>