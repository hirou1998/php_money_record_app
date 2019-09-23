<?php 
session_start();
$isLoggedIn = (isset($_SESSION['username'])) ? true : false;
if($isLoggedIn){
	$username = $_SESSION['username'];
	$userid = $_SESSION['user_id'];
}
$url = $_SERVER['REQUEST_URI'];
?>
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">
	<link rel="stylesheet" type="text/css" href="./css/style_pc.css" media="screen and (min-width: 801px)">
	<link rel="stylesheet" type="text/css" href="./css/style_sp.css" media="screen and (max-width: 800px)">
	<link href="https://fonts.googleapis.com/css?family=Kosugi+Maru&display=swap" rel="stylesheet">
	<style type="text/css">body{font-family: 'Kosugi Maru', sans-serif;}</style>
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js"></script>
	<script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
</head>
<nav id="navigation" class="nav_none">
	<ul>
		<li><a href="index.php">Money Record</a></li>
		<?php
			if($isLoggedIn){
				if(strpos($url, 'mypage')){
					//echo "<li class='nav_button'><a href='information.php'>Change user info</a></li>";
					echo "<li class='nav_button'><a href='logout.php'>Log out</a></li>";
				}
				if(strpos($url, 'logout')){
					echo "<li class='nav_button'><a href='mypage.php'>My page</a></li>";
				}
				if(strpos($url, 'index')){
					echo "<li class='nav_button'><a href='mypage.php'>My page</a></li>";
					echo "<li class='nav_button'><a href='logout.php'>Log out</a></li>";
				}
				echo "<li class='username'><p>$username さん</p></li>";
			}else{
			}
		?>
	</ul>
</nav>

<div id="loading" class="loading">
	<div class="loading_content">
		<span class="circle first_circle"></span>
		<span class="circle second_circle"></span>
		<span class="circle third_circle"></span>
	</div>
</div>