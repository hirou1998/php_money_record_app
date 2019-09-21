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
				<div class="buttonArea profile_edit_button">
					<button><a href="profile.php"><p>編集</p></a></button>
				</div>
			</article>
		</section>
		<section>
			<a href="./index.php"　class="register_operate_records"><p class="register_button">お金の貸し借りを記録・編集・削除する</p></a>
			<article>
				<?php foreach ($records as $num => $record) { ?>
					<ul class="record_block">
						<li class="record_block_top"><span class="person_name"><?php echo $personsLIst[$num]; ?></span>
							<?php $total = array_pop($record); ?>
							<span class="total">合計
							<?php if($total > 0){ ?>
								<span class="total_amount amount_plus"><?php echo $total; ?></span>
							<?php }else{ ?>
								<span class="total_amount amount_minus"><?php echo $total; ?></span>
							<?php } ?> 
							<?php echo $profile['currency']; ?></span>
						</li>
						<div class="see_more_button" v-on:click="showMoreRecords('<?php echo $personsLIst[$num]; ?>')" v-if="recordsVisibility.<?php echo $personsLIst[$num]; ?> == false">すべて表示</div>
						<?php foreach ($record as $key => $item) { ?>
							<?php if($key == 0){ ?>
								<ul class="record_list">
								<?php foreach($item as $key => $value){ ?>						
									<li><span class="key"><?php echo $key; ?></span><span class="value"><?php echo $value; ?></span></li>
								<?php } ?>
								</ul>
							<?php }else{ ?>
								<transition name="fade">
									<ul class="record_list" v-if="recordsVisibility.<?php echo $personsLIst[$num]; ?>">	
									<?php foreach($item as $key => $value){ ?>					
										<li><span class="key"><?php echo $key; ?></span><span class="value"><?php echo $value; ?></span></li>
									<?php } ?>
									</ul>
								</transition>
							<?php } ?>
						<?php } ?>
					</ul>
				<?php } ?>
			</article>
		</section>
	</div>
</body>
</html>

<script type="text/javascript">
new Vue({
	el: '#wrapper',
	data: {
		recordsVisibility: {
			<?php foreach ($personsLIst as $key => $value){ ?>
				<?php echo $value; ?>: false,
			<?php } ?>
		}
	},
	methods: {
		showMoreRecords: function(person){
			this.$set(this.recordsVisibility, person, true);
		}
	}
})
</script>