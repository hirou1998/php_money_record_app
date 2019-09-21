<!DOCTYPE html>
<html>
<?php
$title = "Confirm";
include './modules/header.php';
?>
<body>

</body>
</html>
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	require_once './model/userModel.php';
	$usermodel = new usermodel();

	$username = $_POST['username'];
	$password = $_POST['hash'];
	$email = $_POST['email'];
	$sex = $_POST['sex'];

	if($usermodel->insertUser($username, $password, $email, $sex) == null){
		$usermodel->loginUser($username, $password);
	}else{
		die("予期せぬエラーが発生しました。");
	}

}else{
	die('このページは表示できません。');
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Confirm</title>
</head>
<body>
	<div id="wrapper" class="wrapper">
		<section>
			<p>ユーザー登録が完了しました。マイページにリダイレクトします。</p>
		</section>
	</div>
</body>
</html>
<script type="text/javascript">
setTimeout(function(){
	location.href = 'mypage.php';
}, 3000);
</script>