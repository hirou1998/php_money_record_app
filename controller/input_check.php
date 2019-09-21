<?php
if ($_SERVER['REQUEST_METHOD'] != "POST"){

	$url = "http://" . $_SERVER['HTTP_HOST'] . '/db_practice/signup.php';
	header('Location: ' . $url);
	echo "ユーザー登録画面で入力してください。";

}else{

	require_once '../model/userModel.php';

	$data = file_get_contents('php://input');

	$decoded = json_decode($data, true);

	$usermodel = new usermodel();

	$username = $usermodel->testInput($decoded['username']);
	$duplicated_username = $usermodel->checkIfUsernameValid($username);
	$email = $usermodel->testInput($decoded['email']);
	$duplicated_email = $usermodel->checkIfEmailValid($email);

	if($duplicated_username != null || $duplicated_email != null){
		$data = array(
			'usernameErr' => $duplicated_username,
			'emailErr' => $duplicated_email
		);
		echo json_encode($data, true);
	}else{
		$password = $usermodel->testInput($decoded['password']);
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$password = $usermodel->hidePassword($password);
		$sex = $usermodel->testInput($decoded['sex']);

		$data = array(
			'username' => $username,
			'password' => $password,
			'hash' => $hash,
			'email' => $email,
			'sex' => $sex
		);
		echo json_encode($data, true);
	}

}
?>