<!DOCTYPE html>
<html>
<?php
$title = 'mypage';
include './modules/header.php';
if(empty($_SESSION['username'])){
	$url = '../index.php';
	header('Location: ' . $url);
}

require_once './model/userModel.php';
$usermodel = new UserModel();
$tmp_token = $usermodel->createToken(30);
$_SESSION['tmp_token'] = $tmp_token;
$profile = $usermodel->getUserProfile($userid);

require_once './model/moneyModel.php';
$moneymodel = new MoneyModel();
$records = $moneymodel->getAllRecordsBasedOnPerson($userid, $profile['currency']);
$personsLIst = $moneymodel->getPersonsList($userid);
$idList = json_encode($moneymodel->makeIdListBasedOnPerson($userid), JSON_UNESCAPED_UNICODE);
?>
<body>
	<div id="wrapper" class="wrapper">
		<section>
		</section>
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
						<?php foreach ($record as $index => $item) { ?>
							<?php if($index == 0){ ?>

								<form v-on:submit.prevent="changeToSettled(idList.<?php echo $personsLIst[$num]; ?>[<?php echo $index; ?>])">
									<?php if($item['status'] == "未清算"){ ?>
										<?php if($item['type'] == "借り"){ ?>
										<ul class="record_list borrow">
										<?php }else{ ?>
										<ul class="record_list lend">
										<?php } ?>
									<?php }else{ ?>
										<ul class="record_list settled">
									<?php } ?>
											<input type="hidden" name="record_id" v-model="idList.<?php echo $personsLIst[$num]; ?>[<?php echo $index; ?>]">
											<li class="single_value">
												<span class="value type"><?php echo $item['type']; ?></span>
											</li>
											<li>
												<span class="key">
													<span>
														<?php include './modules/svg/amount_icon.php'; ?>
													</span>
												金額</span>
												<span class="value"><?php echo $item['amount']; ?></span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/currency_icon.php'; ?></span>
												通貨</span>
												<span class="value"><?php echo $item['currency']; ?></span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/comment_icon.php'; ?></span>
												内容</span>
												<span class="value"><?php echo $item['comment']; ?></span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/deadline_icon.php'; ?></span>
												締切</span>
												<span class="value"><?php echo $item['deadline']; ?></span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/reg_date_icon.php'; ?></span>
												登録日</span>
												<span class="value"><?php echo $item['reg_date']; ?></span>
											</li>
										<?php if($item['status'] == "未清算"){ ?>
											<li class="settled_button mypage_button"><button type="submit" v-on:click="showSettledModal">清算済にする</button></li>
										<?php }else{ ?>
											<li class="delete_button mypage_button"><button type="submit" v-on:click="showDeleteModal">アーカイブする</button></li>
										<?php } ?>
									</ul>
								</form>
							<?php }else{ ?>
								<transition name="fade">
									<form v-on:submit.prevent="changeToSettled(idList.<?php echo $personsLIst[$num]; ?>[<?php echo $index; ?>])">
										<?php if($item['status'] == "未清算"){ ?>
											<?php if($item['type'] == "借り"){ ?>
												<ul class="record_list borrow" v-if="recordsVisibility.<?php echo $personsLIst[$num]; ?>">
												<?php }else{ ?>
												<ul class="record_list lend" v-if="recordsVisibility.<?php echo $personsLIst[$num]; ?>">
												<?php } ?>	
											<?php }else{ ?>
											<ul class="record_list settled" v-if="recordsVisibility.<?php echo $personsLIst[$num]; ?>">
										<?php } ?>
												<input type="hidden" name="record_id" v-model="idList.<?php echo $personsLIst[$num]; ?>[<?php echo $index; ?>]">
												<li class="single_value">
													<span class="value type"><?php echo $item['type']; ?></span>
												</li>
												<li>
													<span class="key">
														<span>
															<?php include './modules/svg/amount_icon.php'; ?>
														</span>
													金額</span>
													<span class="value"><?php echo $item['amount']; ?></span>
												</li>
												<li>
													<span class="key">
														<span><?php include './modules/svg/currency_icon.php'; ?></span>
													通貨</span>
													<span class="value"><?php echo $item['currency']; ?></span>
												</li>
												<li>
													<span class="key">
														<span><?php include './modules/svg/comment_icon.php'; ?></span>
													内容</span>
													<span class="value"><?php echo $item['comment']; ?></span>
												</li>
												<li>
													<span class="key">
														<span><?php include './modules/svg/deadline_icon.php'; ?></span>
													締切</span>
													<span class="value"><?php echo $item['deadline']; ?></span>
												</li>
												<li>
													<span class="key">
														<span><?php include './modules/svg/reg_date_icon.php'; ?></span>
													登録日</span>
													<span class="value"><?php echo $item['reg_date']; ?></span>
												</li>
											<?php if($item['status'] == "未清算"){ ?>
												<li class="settled_button mypage_button"><button type="submit" v-on:click="showSettledModal">清算済にする</button></li>
											<?php }else{ ?>
												<li class="delete_button mypage_button"><button type="submit" v-on:click="showDeleteModal">アーカイブする</button></li>
											<?php } ?>
										</ul>
									</form>
								</transition>
							<?php } ?>
						<?php } ?>
					</ul>
				<?php } ?>
			</article>
			<transition name="modal">
				<div v-if="settledModalShow" class="modal_over_lay" v-on:click="closeModal">
					<div class="close_button">×</div>
					<div class="modal_content" v-on:click.stop >
						<api-loading v-if="loading"></api-loading>
						<form v-on:submit.prevent="sendChange" v-else>
							<p class="alert">以下のデータを清算済にしていいですか?</p>
							<ul class="record_list">
								<li class="single_value">
									<span class="type value">{{preview.type}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/person_icon.php'; ?></span>
									相手</span>
									<span class="value">{{preview.person}}</span>
								</li>
								<!-- <li>
									<span class="status">{{edit.status}}</span>
								</li> -->
								<li>
									<span class="key">
										<span><?php include './modules/svg/amount_icon.php'; ?></span>
									金額</span>
									<span class="value">{{preview.amount}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/currency_icon.php'; ?></span>
									通貨</span>
									<span class="value">{{preview.currency}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/comment_icon.php'; ?></span>
									内容</span>
									<span class="value">{{preview.comment}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/deadline_icon.php'; ?></span>
									締切</span>
									<span class="value">{{preview.deadline}}</span>
								</li>
							</ul>
							<div class="buttonArea">
								<button class="button" type="submit">変更</button>
							</div>
						</form>
					</div>
				</div>
			</transition>

			<transition name="modal">
				'<div v-if="deleteModalShow" class="modal_over_lay" v-on:click="closeModal">
					<div class="close_button">×</div>
					<div class="modal_content" v-on:click.stop >
						<api-loading v-if="loading"></api-loading>
						<form v-on:submit.prevent="sendArchive" v-else>
							<p class="alert">アーカイブしてもいいですか?</p>
							<ul class="record_list">
								<li class="single_value">
									<span class="type value">{{preview.type}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/person_icon.php'; ?></span>
									相手</span>
									<span class="value">{{preview.person}}</span>
								</li>
								<!-- <li>
									<span class="status">{{edit.status}}</span>
								</li> -->
								<li>
									<span class="key">
										<span><?php include './modules/svg/amount_icon.php'; ?></span>
									金額</span>
									<span class="value">{{preview.amount}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/currency_icon.php'; ?></span>
									通貨</span>
									<span class="value">{{preview.currency}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/comment_icon.php'; ?></span>
									内容</span>
									<span class="value">{{preview.comment}}</span>
								</li>
								<li>
									<span class="key">
										<span><?php include './modules/svg/deadline_icon.php'; ?></span>
									締切</span>
									<span class="value">{{preview.deadline}}</span>
								</li>
							</ul>
							<div class="buttonArea">
								<button class="button" type="submit">アーカイブ</button>
							</div>
						</form>
					</div>
				</div>
			</transition>'
		</section>
	</div>
</body>
</html>

<script type="text/javascript" src="./js/loading.js"></script>
<script type="text/javascript">
new Vue({
	el: '#wrapper',
	data: {
		recordsVisibility: {
			<?php foreach ($personsLIst as $key => $value){ ?>
				<?php echo $value; ?>: false,
			<?php } ?>
		},
		tmp_token: '<?php echo $_SESSION['tmp_token']; ?>',
		settledModalShow: false,
		deleteModalShow: false,
		preview: {
			id: null,
			type: null,
			person: null,
			status: null,
			amount: null,
			currency: null,
			comment: null,
			deadline: null
		},
		loading: false,
		idList: 
			<?php echo $idList; ?>
	},
	methods: {
		showMoreRecords: function(person){
			this.$set(this.recordsVisibility, person, true);
		},
		changeToSettled: function(record_id){
			let vm = this;
			this.loading = true;
			axios.post('./controller/change_to_settled.php', {
				id: record_id,
				tmp_token: vm.tmp_token,
				change: false
			})
			.then(function(res){
				console.log(res.data);
				for(key in res.data){
					vm.$set(vm.preview, key, res.data[key]);
				}
				vm.loading = false;
			})
			.catch(function(err){
				console.log(err);
			});
		},
		showSettledModal: function(){
			this.settledModalShow = true;
			this.deleteModalShow = false;
		},
		showDeleteModal: function(){
			this.settledModalShow = false;
			this.deleteModalShow = true;
		},
		closeModal: function(){
			this.settledModalShow = false;
			this.deleteModalShow = false;
		},
		sendChange: function(){
			let vm = this;
			axios.post('./controller/change_to_settled.php', {
				id: vm.preview.id,
				tmp_token: vm.tmp_token,
				change: true
			})
			.then(function(res){
				console.log(res.data);
				let url = location.href;
				location.href = url;
			})
			.catch(function(err){
				console.log(err);
			});
		},
		sendArchive: function(){
			let vm = this;
			axios.post('./controller/update_data.php', {
				record_id: vm.preview.id,
				tmp_token: vm.tmp_token,
				updated: false,
				delete: false,
				archive: true
			})
			.then(function(res){
				console.log(res.data);
				vm.loading = false;
				let url = location.href;
				location.href = url;
			})
			.catch(function(err){
				console.log(err);
			});
		}
	}
})
</script>