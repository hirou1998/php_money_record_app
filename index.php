<!DOCTYPE html>
<html>
<?php
if(strpos($_SERVER['HTTP_REFERER'], 'logout') != false){
	session_start();
	session_destroy();
	$logout = true;
}else{
	require_once './model/userModel.php';
	$usermodel = new UserModel();
	$title = "TOP";
	$tmp_token = $usermodel->createToken(30);
	$_SESSION['tmp_token'] = $tmp_token;
	$profile = $usermodel->getUserProfile($userid);

	require_once './model/moneyModel.php';
	$moneymodel = new MoneyModel();
	$money = $moneymodel->getMoneyRecord($userid);
	$currency_list = $moneymodel->getCurrencyList();
	$personsList = $moneymodel->getPersonsList($userid);
}
include './modules/header.php';
 ?>
<body>
	<div id="wrapper" class="wrapper">
		<?php
		if($logout){
			echo '<p class="alert" v-if="notDeleted">ログアウトしました。<span v-on:click="deleteMessage">×</span></p>';
		}
		?>
		<section class="top_content">
			<h1>Money Record</h1>
			<p>世界中のどこでも、お金の貸し借りをラクラク管理</p>
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
						<form v-on:submit.prevent="deleting">
						<?php foreach($money as $item){ ?>
							<ul class="record_list">
							<?php foreach($item as $key => $value){ ?>
								<li>
									<span class="key"><?php echo $key; ?></span>
									<?php if($value != null){ ?>
										<span class="value"><?php echo $value; ?></span>
									<?php }else{ ?>
										<span class="value">-</span>
									<?php } ?>
								</li>
							<?php }?>
							<li class="buttons"><span class="edit_button">編集</span><span class="delete_button">削除</span></li>
							<!-- <transition name="fade">
								<button type="submit" v-if="deleteButton">本当に削除していいですか?</button>
							</transition> -->
							</ul>
						<?php } ?>
						<transition name="modal">
							<div v-if="modalShow">
								<button type="submit">削除していいですか?</button>
							</div>
						</transition>
						</form>
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
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		err: {
			typeErr: null,
			personErr: null,
			statusErr: null,
			amountErr: null
		},
		select: true,
		isErr: false,
		edit: {
			type: null,
			person: null,
			status: null,
			amount: null,
			currency: null,
			comment: null,
			deadline: null
		}
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
		showDeleteButton: function(){
			this.deleteButton = true;
		}
	}
})
</script>