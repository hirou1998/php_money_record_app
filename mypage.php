<!DOCTYPE html>
<html>
<?php
$title = 'mypage';
include './modules/header.php';
if(empty($_SESSION['username'])){
	$url = "http://" . $_SERVER['HTTP_HOST'] . '/db_practice/index.php';
	header('Location: ' . $url);
}

require_once './model/userModel.php';
$usermodel = new UserModel();
$tmp_token = $usermodel->createToken(30);
$_SESSION['tmp_token'] = $tmp_token;
$profile = $usermodel->getUserProfile($userid);

require_once './model/moneyModel.php';
$moneymodel = new MoneyModel();
$records = $moneymodel->getAllRecordsBasedOnPerson($userid);
$personsLIst = $moneymodel->getPersonsList($userid);
?>
<body>
	<div id="wrapper" class="wrapper">
		<section class="top_content">
			<h1>My Page</h1>
			<article class="profile">
				<div class="profile_flex">
					<div class="left_content">
						<img class="user_pic" src="<?php echo $profile['pic']; ?>" alt="<?php echo $username; ?>">
					</div>
					<div class="right_content">
						<p class="profile_text"><?php echo $username; ?></p>
						<p class="profile_text"><span>currency: </span><?php echo $profile['currency']; ?></p>
					</div>
				</div>
				<div class="buttonArea">
					<button><a href="profile.php"><p>編集</p></a></button>
				</div>
			</article>
		</section>
		<section>
			<article>
				<?php foreach ($records as $key => $record) { ?>
					<ul class="record_block">
						<li class="record_block_top"><span class="person_name"><?php echo $personsLIst[$key]; ?></span>
						<?php $total = array_pop($record); ?>
						<span class="total">合計 <span class="total_amount"><?php echo $total; ?></span> <?php echo $profile['currency']; ?></span></li>
						<?php foreach ($record as $key => $item) { ?>
							<?php 
							$total = array_pop($item); 
							?>
							<ul class="top_row">						
							<?php if($key == 0){ ?>
								<?php foreach ($item as $key => $value) { ?>
								<li>
									<span class="title <?php echo $key . '_title'; ?>"><?php echo $key; ?></span>
									<span class="<?php echo $key . '_item'; ?>"><?php echo $value; ?></span>
								<?php } ?>
							<?php }else{ ?>
								<?php foreach ($item as $key => $value) { ?>
									<span><?php echo $value; ?></span>
								</li>
								<?php } ?>
							<?php } ?>
							</ul>
						<?php } ?>
					</ul>
				<?php } ?>
			</article>
		</section>
	</div>
</body>
</html>