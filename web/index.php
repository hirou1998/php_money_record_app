<!DOCTYPE html>
<html>
<?php
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
	<div id="wrapper" class="wrapper wrapper_none">
		<?php if($logout){ ?>
			<p class="alert" v-if="notDeleted">ログアウトしました。<span v-on:click="deleteMessage">×</span></p>
		<?php } ?>

		<section class="top_content">
			<h1>Money Record</h1>
			<p>世界中のどこでも、お金の貸し借りをラクラク管理<br>どんな通貨の貸し借りもMY通貨に自動計算</p>
		</section>

		<?php
		if(!empty($_SESSION['username'])){ ?>

			<section>
				<p class="register_button" v-on:click="showInputArea" v-if="closed">お金の貸し借りを記録する</p>
				<transition name="fade">
					<div v-if="closed == false" class="money_fields">
						<?php// include './modules/money_fields.php'; ?>
						<form v-on:submit="submit">
							<div class="inputArea">
								<p>type</p>
								<select type="select" name="type" v-model="inputs.type">
									<option value="貸し">貸し</option>
									<option value="借り">借り</option>
								</select>
								<span>*required</span>  <span class="red_message">{{ err.typeErr }}</span>
							</div>
							<div class="inputArea">
								<p>Person</p>
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
									<input type="text" name="person" v-model="inputs.person" v-else placeholder="Enter person's name"> 
								</div>
								<span>*required</span>  <span class="red_message">{{ err.personErr }}</span>
							</div>
							<div class="inputArea">
								<p>Status</p>
								<select v-model="inputs.status">
									<option value="未清算">未清算</option>
									<option value="清算済">清算済</option>
								</select>
								<span>*required</span>  <span class="red_message">{{ err.statusErr }}</span>
							</div>
							<div class="inputArea">
								<p>Amount</p>
								<input type="number" name="amount" v-model="inputs.amount">
								<span>*required</span>  <span class="red_message">{{ err.amountErr }}</span>
							</div>
							<div class="inputArea">
								<p>Currency</p>
								<select type="select" name="currency" v-model="inputs.currency">
									<?php
									foreach($currency_list as $currency_item){
										echo '<option value=' . $currency_item . '>' . $currency_item . '</option>';
									}
									?>
								</select>
							</div>
							<div class="inputArea">
								<p>Comment</p>
								<textarea name="comment" v-model="inputs.comment"></textarea>
							</div>
							<div class="inputArea">
								<p>Deadline</p>
								<input type="date" name="deadline" v-model="inputs.deadline">
							</div>
							<div class="inputArea">
								<p>Date</p>
								<input type="date" name="date" v-model="inputs.date">
								<span>*required</span>  <span class="red_message">{{ err.dateErr }}</span>
							</div>
							<input type="hidden" name="tmp_token" v-model="inputs.tmp_token">
							<div class="buttonArea">
								<button type="submit" class="button">登録</button>
							</div>
						</form>
					</div>
				</transition>
				<p class="register_button" v-on:click="closeInputArea" v-if="closed == false">入力欄を閉じる</p>
				<article class="money_record">
					<?php if($money){ ?>

						<?php foreach($money as $num => $item){ ?>
						<form v-on:submit.prevent="updateData(<?php echo $num; ?>)">

							<?php if($item['type'] == "貸し"){ ?>
							<ul class="record_list lend">
							<?php }else{ ?>
							<ul class="record_list borrow">
							<?php } ?>

							<?php foreach($item as $key => $value){ ?>
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
							<?php } ?>
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
									<form v-on:submit.prevent="sendUpdate">
										<div class="inputArea">
											<p>type</p>
											<select type="select" name="type" v-model="edit.type">
												<option value="貸し">貸し</option>
												<option value="借り">借り</option>
											</select>
										</div>
										<div class="inputArea">
											<p>Person</p>
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
												<input type="text" name="person" v-model="edit.person" v-else placeholder="Enter person's name"> 
											</div>
										</div>
										<div class="inputArea">
											<p>Status</p>
											<select v-model="edit.status">
												<option value="未清算">未清算</option>
												<option value="清算済">清算済</option>
											</select>
										</div>
										<div class="inputArea">
											<p>Amount</p>
											<input type="number" name="amount" v-model="edit.amount">
										</div>
										<div class="inputArea">
											<p>Currency</p>
											<select type="select" name="currency" v-model="edit.currency">
												<?php
												foreach($currency_list as $currency_item){
													echo '<option value=' . $currency_item . '>' . $currency_item . '</option>';
												}
												?>
											</select>
										</div>
										<div class="inputArea">
											<p>Comment</p>
											<textarea name="comment" v-model="edit.comment"></textarea>
										</div>
										<div class="inputArea">
											<p>Deadline</p>
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
									<form v-on:submit.prevent="sendDelete">
										<p class="alert">本当に削除してもいいですか?</p>
										<ul class="record_list">
											<li><span class="key">Type</span><span>{{edit.type}}</span></li>
											<li><span class="key">Person</span><span>{{edit.person}}</span></li>
											<li><span class="key">Status</span><span>{{edit.status}}</span></li>
											<li><span class="key">Amount</span><span>{{edit.amount}}</span></li>
											<li><span class="key">Currency</span><span>{{edit.currency}}</span></li>
											<li><span class="key">Comment</span><span>{{edit.comment}}</span></li>
											<li><span class="key">Deadline</span><span>{{edit.deadline}}</span></li>
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
					<a href="login.php"><div class="top_button signin_button"><p>Sign In</p></div></a>
					<a href="signup.php"><div class="top_button signup_button"><p>Sign Up</p></div></a>
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
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		postData: {
			record_id: <?php echo $ids; ?>,
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		editModalShow: false,
		deleteModalShow: false,
		current_record_id: null
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
			axios.post('./controller/update_data.php', {
				record_id: vm.postData.record_id[num],
				tmp_token: vm.postData.tmp_token,
				updated: false,
				delete: false
			})
			.then(function(res){
				console.log(res.data);
				for(key in res.data){
					vm.$set(vm.edit, key, res.data[key]);
				}
			})
			.catch(function(err){
				console.log(err);
			});
		},
		sendUpdate: function(){
			let vm = this;
			axios.post('./controller/update_data.php', vm.edit)
			.then(function(res){
				console.log(res.data);
				let url = location.href;
				location.href = url;
			})
			.catch(function(err){
				console.log(err);
			});
		},
		sendDelete: function(){
			let vm = this;
			axios.post('./controller/update_data.php', {
				record_id: vm.current_record_id,
				tmp_token: vm.postData.tmp_token,
				updated: false,
				delete: true
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
		closeModal: function(){
			this.editModalShow = false;
			this.deleteModalShow = false;
		}
	}
})
</script>