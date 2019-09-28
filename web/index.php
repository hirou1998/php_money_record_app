<!DOCTYPE html>
<html>
<?php
date_default_timezone_set('Asia/Tokyo');
//require('../vendor/autoload.php');
$refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
if(strpos($refer, 'logout') != false){
	session_start();
	session_destroy();
	$logout = true;
}else{

	//var_dump("uessepoirjbsebp@s");
}
$title = "TOP";
include './modules/header.php';

require_once './model/userModel.php';
$usermodel = new UserModel();

$tmp_token = $usermodel->createToken(30);
$_SESSION['tmp_token'] = $tmp_token;
$profile = $usermodel->getUserProfile($userid);

require_once './model/moneyModel.php';
$moneymodel = new MoneyModel();
$money = $moneymodel->getMoneyRecord($userid);
$currency_list = $moneymodel->getCurrencyList();
$personsList = $moneymodel->getPersonsList($userid);
$ids = json_encode($moneymodel->getRecordId($userid));
?>
<body>
	<?php if (empty($_SESSION['username'])){ ?>
	<div id="wrapper" class="wrapper wrapper_none top_wrapper">
	<?php }else{ ?>
	<div id="wrapper" class="wrapper wrapper_none">
	<?php } ?>
		<?php if($logout){ ?>
			<p class="alert" v-if="notDeleted">ログアウトしました。<span v-on:click="deleteMessage">×</span></p>
		<?php } ?>

		<section class="top_content">
			<div class="top_main">
				<h1>Money Record</h1>
				<p class="copy">世界中のどこでも、<br>お金の貸し借りをラクラク管理<br>どんな通貨の貸し借りもMY通貨に自動計算</p>
			</div>
		</section>

		<?php
		if(!empty($_SESSION['username'])){ ?>

			<section>
				<article>
					<p class="register_button" v-on:click="showInputArea" v-if="closed">お金の貸し借りを記録する</p>
					<transition name="fade">
						<div v-if="closed == false" class="money_fields">
							<?php// include './modules/money_fields.php'; ?>
							<form v-on:submit="submit">
								<div class="checkArea">
									<input id="radio_borrow" class="record_type_choice" type="radio" name="type" value="貸し" v-model="inputs.type">
									<label class="check_label record_type" for="radio_borrow">貸し</label>
									<input id="radio_lend" class="record_type_choice" type="radio" name="type" value="借り" v-model="inputs.type">
									<label class="check_label record_type" for="radio_lend">借り</label>
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span>
											<?php include './modules/svg/person_icon.php'; ?>
										</span>
									相手</p>
									<div class="checkArea">
										<input id="radio1" type="radio" name="choose" value="select" v-model="inputs.choose" v-on:change="changeOption">
										<label class="check_label" for="radio1">履歴から選択する</label>
										<input id="radio2" type="radio" name="choose" value="register" v-model=	"inputs.choose" v-on:change="changeOption">
										<label class="check_label" for="radio2">新規入力する</label>
									</div>
									<div>
										<select v-if="select" v-model="inputs.person">
											<?php
											foreach ($personsList as $person) {
												echo '<option value =' . $person . '>' . $person . '</option>';
											}
											?>
										</select>
										<input type="text" name="person" v-model="inputs.person" v-else placeholder="相手の名前を入力してください。"> 
									</div>
									<span>*必須</span>  <span class="red_message">{{ err.personErr }}</span>
								</div>
								<div class="checkArea">
									<input id="radio_unsettled" type="radio" name="status" value="未清算" v-model="inputs.status">
									<label class="check_label" for="radio_unsettled">未清算</label>
									<input id="radio_settled" type="radio" name="status" value="清算済" v-model="inputs.status">
									<label class="check_label" for="radio_settled">清算済</label>
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span>
											<?php include './modules/svg/amount_icon.php'; ?>
										</span>
									金額</p>
									<input type="number" name="amount" v-model="inputs.amount">
									<span>*必須</span>  <span class="red_message">{{ err.amountErr }}</span>
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span><?php include './modules/svg/currency_icon.php'; ?></span>
									通貨</p>
									<select type="select" name="currency" v-model="inputs.currency">
										<?php
										foreach($currency_list as $currency_item){
											echo '<option value=' . $currency_item . '>' . $currency_item . '</option>';
										}
										?>
									</select>
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span>
											<?php include './modules/svg/comment_icon.php'; ?>
										</span>
									内容</p>
									<textarea name="comment" v-model="inputs.comment"></textarea>
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span>
											<?php include './modules/svg/deadline_icon.php'; ?>
										</span>
									締切</p>
									<input type="date" name="deadline" v-model="inputs.deadline">
								</div>
								<div class="inputArea">
									<p class="input_item">
										<span>
											<?php include './modules/svg/reg_date_icon.php'; ?>
										</span>
									登録日</p>
									<input type="date" name="date" v-model="inputs.date">
									<span>*必須</span>  <span class="red_message">{{ err.dateErr }}</span>
								</div>
								<input type="hidden" name="tmp_token" v-model="inputs.tmp_token">
								<div class="buttonArea">
									<button type="submit" class="button">登録</button>
								</div>
							</form>
						</div>
					</transition>
					<p class="register_button" v-on:click="closeInputArea" v-if="closed == false">入力欄を閉じる</p>
				</article>

				<a href="./mypage.php"><p class="register_button">貸し借りの合計を相手ごとに確認する</p></a>

				<article class="money_record">
					<?php if($money){ ?>

						<?php foreach($money as $num => $item){ ?>
						<form v-on:submit.prevent="updateData(<?php echo $num; ?>)">

							<?php if($item['type'] == "貸し"){ ?>
							<ul class="record_list lend">
							<?php }else{ ?>
							<ul class="record_list borrow">
							<?php } ?>

								<input type="hidden" name="record_id" v-model="postData.record_id[<?php echo $num; ?>]">
								<li class="single_value">
									<span class="value type"><?php echo $item['type']; ?></span>
								</li>
								<li>
									<span class="key">
										<span>
											<?php include './modules/svg/person_icon.php'; ?>
										</span>
									相手</span>
									<span class="value"><?php echo $item['person']; ?></span>
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

							<!-- <?php foreach($item as $key => $value){ ?>
								<?php if($key == 'id'){ ?>
									<input type="hidden" name="record_id" v-model="postData.record_id[<?php echo $num; ?>]">
								<?php }else{ ?>
									<li>
										<span class="key"><?php echo $key; ?></span>
										<?php if($value != null){ ?>
											<span class="value"><?php echo $value; ?></span>
										<?php }else{ ?>
											<span class="value">-</span>
										<?php } ?>
									</li>
								<?php } ?>
							<?php } ?> -->
							<li class="buttons">
								<button class="edit_button" type="submit" v-on:click="showEditModal">編集</button>
								<button class="delete_button" type="submit" v-on:click="showDeleteModal">削除</button>
							</li>
							</ul>
						</form>
						<?php } ?>

						<transition name="modal">
							<div v-if="editModalShow" class="modal_over_lay" v-on:click="closeModal">
								<div class="close_button">×</div>
								<div class="modal_content" v-on:click.stop >
									<api-loading v-if="loading"></api-loading>
									<form v-on:submit.prevent="sendUpdate" v-else>
										<div class="checkArea">
											<input id="radio_borrow" class="record_type_choice" type="radio" name="type" value="貸し" v-model="edit.type">
											<label class="check_label record_type" for="radio_borrow">貸し</label>
											<input id="radio_lend" class="record_type_choice" type="radio" name="type" value="借り" v-model="edit.type">
											<label class="check_label record_type" for="radio_lend">借り</label>
										</div>
										<div class="inputArea">
											<p class="input_item">
												<span>
													<?php include './modules/svg/person_icon.php'; ?>
												</span>
											相手</p>
											<div class="checkArea">
												<input id="radio1" type="radio" name="choose" value="select" v-model="inputs.choose" v-on:change="changeOption">
												<label class="check_label" for="radio1">履歴から選択する</label>
												<input id="radio2" type="radio" name="choose" value="register" v-model=	"inputs.choose" v-on:change="changeOption">
												<label class="check_label" for="radio2">新規入力する</label>
											</div>
											<div>
												<select v-if="select" v-model="edit.person">
													<?php
													foreach ($personsList as $person) {
														echo '<option value =' . $person . '>' . $person . '</option>';
													}
													?>
												</select>
												<input type="text" name="person" v-model="edit.person" v-else placeholder="相手の名前を入力してください。"> 
											</div>
											<span>*必須</span>  <span class="red_message">{{ err.personErr }}</span>
										</div>
										<div class="checkArea">
											<input id="radio_unsettled" type="radio" name="status" value="未清算" v-model="edit.status">
											<label class="check_label" for="radio_unsettled">未清算</label>
											<input id="radio_settled" type="radio" name="status" value="清算済" v-model="edit.status">
											<label class="check_label" for="radio_settled">清算済</label>
										</div>
										<div class="inputArea">
											<p class="input_item">
												<span>
													<?php include './modules/svg/amount_icon.php'; ?>
												</span>
											金額</p>
											<input type="number" name="amount" v-model="edit.amount">
											<span>*必須</span>  <span class="red_message">{{ err.amountErr }}</span>
										</div>
										<div class="inputArea">
											<p class="input_item">
												<span><?php include './modules/svg/currency_icon.php'; ?></span>
											通貨</p>
											<select type="select" name="currency" v-model="edit.currency">
												<?php
												foreach($currency_list as $currency_item){
													echo '<option value=' . $currency_item . '>' . $currency_item . '</option>';
												}
												?>
											</select>
										</div>
										<div class="inputArea">
											<p class="input_item">
												<span>
													<?php include './modules/svg/comment_icon.php'; ?>
												</span>
											内容</p>
											<textarea name="comment" v-model="edit.comment"></textarea>
										</div>
										<div class="inputArea">
											<p class="input_item">
												<span>
													<?php include './modules/svg/deadline_icon.php'; ?>
												</span>
											締切</p>
											<input type="date" name="deadline" v-model="edit.deadline">
										</div>
										<input type="hidden" name="isUpdate" v-model="edit.updated">
										<div class="buttonArea">
											<button class="button" type="submit" v-on:click.stop >変更を保存</button>
										</div>
									</form>
								</div>
							</div>
						</transition>

						<transition name="modal">
							<div v-if="deleteModalShow" class="modal_over_lay" v-on:click="closeModal">
								<div class="close_button">×</div>
								<div class="modal_content" v-on:click.stop >
									<api-loading v-if="loading"></api-loading>
									<form v-on:submit.prevent="sendDelete" v-else>
										<p class="alert">本当に削除してもいいですか?</p>
										<ul class="record_list">
											<li class="single_value">
												<span class="type value">{{edit.type}}</span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/person_icon.php'; ?></span>
												相手</span>
												<span class="value">{{edit.person}}</span>
											</li>
											<!-- <li>
												<span class="status">{{edit.status}}</span>
											</li> -->
											<li>
												<span class="key">
													<span><?php include './modules/svg/amount_icon.php'; ?></span>
												金額</span>
												<span class="value">{{edit.amount}}</span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/currency_icon.php'; ?></span>
												通貨</span>
												<span class="value">{{edit.currency}}</span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/comment_icon.php'; ?></span>
												内容</span>
												<span class="value">{{edit.comment}}</span>
											</li>
											<li>
												<span class="key">
													<span><?php include './modules/svg/deadline_icon.php'; ?></span>
												締切</span>
												<span class="value">{{edit.deadline}}</span>
											</li>
										</ul>
										<div class="buttonArea">
											<button class="button" type="submit">削除</button>
										</div>
									</form>
								</div>
							</div>
						</transition>

					<?php } else {?>

						<p>お金の貸し借りはまだありません。</p>

					<?php }?>

				</article>
			</section>

		<?php }else{
			echo '<section class="button_area">
					<a href="login.php"><div class="top_button signin_button"><p>ログイン</p></div></a>
					<a href="signup.php"><div class="top_button signup_button"><p>ユーザー登録</p></div></a>
				  </section>';
		}
		?>
	</div>
</body>
</html>
<script type="text/javascript" src="./js/loading.js"></script>
<script type="text/javascript">
new Vue({
	el: '#wrapper',
	data:{
		notDeleted: true,
		closed: true,
		inputs: {
			type: '貸し',
			choose: 'select',
			person: null,
			status: '未清算',
			amount: null,
			currency: '<?php echo $profile["currency"]; ?>',
			comment: null,
			deadline: null,
			date: '<?php echo date("Y-m-d"); ?>',
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		err: {
			typeErr: null,
			personErr: null,
			statusErr: null,
			amountErr: null,
			dateErr: null
		},
		select: true,
		isErr: false,
		edit: {
			id: null,
			type: null,
			person: null,
			status: null,
			amount: null,
			currency: null,
			comment: null,
			deadline: null,
			updated: true,
			delete: false,
			archive: false,
			tmp_token: '<?php echo $_SESSION['tmp_token']; ?>'
		},
		postData: {
			record_id: <?php echo $ids; ?>,
			tmp_token: '<?php echo $_SESSION['tmp_token']; ?>'
		},
		editModalShow: false,
		deleteModalShow: false,
		current_record_id: null,
		loading: false
	},
	methods:{
		deleteMessage: function(){
			this.notDeleted = false;
		},
		showInputArea: function(){
			this.closed = false;
		},
		closeInputArea: function(){
			this.closed = true;
		},
		changeOption: function(e){
			if(e.target.value == 'select'){
				this.select = true;
			}else{
				this.select = false;
			}
		},
		resetErr: function(){
			this.typeErr = this.personErr = this.statusErr = this.amountErr = null;
			this.isErr = false;
		},
		submit: function(e){
			this.resetErr();
			if(this.inputs.type == null){
				this.err.typeErr = 'Please choose Type';
				this.isErr = true;
			}
			if(this.inputs.person == null){
				this.err.personErr = 'Please choose or input Person name';
				this.isErr = true;
			}
			if(this.inputs.status == null){
				this.err.statusErr = 'Please select Status';
				this.isErr = true;
			}
			if(this.inputs.amount == null){
				this.err.amountErr = 'Please input Amount of money';
				this.isErr = true;
			}
			if(this.isErr == false){
				let vm = this;
				axios.post('./controller/money_fields.php', vm.inputs)
				.then(function(res){
					console.log(res.data);
					let url = location.href;
					location.href = url;
				})
				.catch(function(err){
					console.log(err);
				});
			}
			e.preventDefault();
		},
		showDeleteModal: function(){
			this.deleteModalShow = true;
			this.editModalShow = false;
		},
		showEditModal: function(){
			this.editModalShow = true;
			this.deleteModalShow = false;
		},
		updateData: function(num){
			let vm = this;
			this.current_record_id = this.postData.record_id[num];
			this.loading = true;
			axios.post('./controller/update_data.php', {
				record_id: vm.postData.record_id[num],
				tmp_token: vm.postData.tmp_token,
				updated: false,
				delete: false,
				archive: false
			})
			.then(function(res){
				console.log(res.data);
				for(key in res.data){
					vm.$set(vm.edit, key, res.data[key]);
				}
				vm.loading = false;
			})
			.catch(function(err){
				console.log(err);
			});
		},
		sendUpdate: function(){
			let vm = this;
			this.loading = true;
			axios.post('./controller/update_data.php', vm.edit)
			.then(function(res){
				console.log(res.data);
				vm.loading = false;
				let url = location.href;
				location.href = url;
			})
			.catch(function(err){
				console.log(err);
			});
		},
		sendDelete: function(){
			let vm = this;
			this.loading = true;
			axios.post('./controller/update_data.php', {
				record_id: vm.current_record_id,
				tmp_token: vm.postData.tmp_token,
				updated: false,
				delete: true,
				archive: false
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
		},
		closeModal: function(){
			this.editModalShow = false;
			this.deleteModalShow = false;
		}
	}
})
</script>