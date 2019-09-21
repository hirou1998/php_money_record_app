<?php
require_once 'dao.php';

class UserModel{
	
	private $dao = null;

	public function __construct(){
		$this->dao = new Dao();
	}

	public function testInput($data){
		$data = trim($data);
		$data = stripcslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	public function passwordHash($password){
		$hash = password_hash($password, PASSWORD_BCRYPT);
		return $hash;
	}

	public function createToken($length){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);
		$result = '';
		for ($i = 0; $i < $length; $i++){
			$index = rand(0, $count-1);
			$result .= mb_substr($chars, $index, 1);
		}
		return $result;
	}

	public function hidePassword($password){
		$hiddenPassword = '';
		for ($i = 0; $i < strlen($password); $i++){
			$hiddenPassword .= "●";
		}
		return $hiddenPassword;
	}

	public function checkIfUsernameValid($username){
		$sql = 'SELECT username FROM user WHERE username = :username';
		$arr = array(
			':username' => $username
		);
		$result = $this->dao->select($sql, $arr);
		if(!$result){
			$message = null;
		}else{
			$message = 'This username is already used';
		}

		return $message;
	}

	public function checkIfEmailValid($email){
		$sql = 'SELECT username FROM user WHERE email = :email';
		$arr = array(
			':email' => $email
		);
		$result = $this->dao->select($sql, $arr);
		if(!$result){
			$message = null;
		}else{
			$message = 'This email is already used';
		}

		return $message;
	}

	public function insertUser($username, $password, $email, $sex){
		$sql = 'INSERT INTO user (username, password, email, sex)
				VALUES (:username, :password, :email, :sex)';
		$arr = array(
			':username' => $username,
			':password' => $password,
			':email' => $email,
			':sex' => $sex
		);
		$this->dao->insert($sql, $arr);
	}

	public function loginUser($username, $password){
		$sql = 'SELECT * FROM user WHERE username = :username';
		$arr = array(
			':username' => $username
		);
		$result = $this->dao->select($sql, $arr);
		if(!$result){
			$message = 'username か password が正しくありません。';
		}
		if(!password_verify($password, $result['password'])){
			$message = 'username か password が正しくありません。';
		}
		session_start();
		$_SESSION['username'] = $result['username'];
		$_SESSION['user_id'] = $result['user_id'];
		$_SESSION['login'] = 'yes';

		return $message;
	}

	public function getUserProfile($userid){
		$sql = 'SELECT * FROM user WHERE user_id = :userid';
		$arr = array(
			':userid' => $userid
		);
		$result = $this->dao->select($sql, $arr);

		return $result;
	}

	public function saveUserProfile($username, $currency, $userid){
		$sql = 'UPDATE user SET username = :username, currency = :currency WHERE user_id = :userid';
		$arr = array(
			':username' => $username,
			':currency' => $currency,
			':userid' => $userid
		);
		$this->dao->update($sql, $arr);
	}
}

?>