<!DOCTYPE html>
<html>
<?php
$title = "Sign up";
include './modules/header.php';
if(isset($_SESSION['login'])){
	$url = "http://" . $_SERVER['HTTP_HOST'] . '/db_practice/mypage.php';
	header('Location: ' . $url);
}
?>
<body>
	<div id="wrapper" class="wrapper">
		<section class="top_content">
			<h1>ユーザー登録</h1>
			<p>簡単5項目のユーザー登録でお金の貸し借りの管理を簡単に</p>
			<p>パスワードは暗号化して登録しています。</p>
		</section>
		<section>
			<form v-on:submit="validation">
				<div class="inputArea">
					<p>ユーザー名</p>
					<input type="text" name="username" v-model="inputs.username">
					<span>*必須</span>  <span class="red_message">{{ err.usernameErr }}</span>
				</div>
				<div class="inputArea">
					<p>パスワード</p>
					<input type="password" name="password" v-model="inputs.password">
					<span>*必須</span>  <span class="red_message">{{ err.passwordErr }}</span>
				</div>
				<div class="inputArea">
					<p>パスワード (確認用)</p>
					<input type="password" name="password_check" v-model="inputs.password_check">
					<span>*必須</span>  <span class="red_message">{{ err.password_checkErr }}</span>
				</div>
				<div class="inputArea">
					<p>メールアドレス</p>
					<input type="email" name="email" v-model="inputs.email">
					<span>*必須</span>  <span class="red_message">{{ err.emailErr }}</span>
				</div>
				<div class="checkArea inputArea">
					<p>性別</p>
					<input id="radio_male" type="radio" name="sex" value="male" v-model="inputs.sex" v-on:change="changeOption">
					<label class="check_label" for="radio_male">男性</label>
					<input id="radio_female" type="radio" name="sex" value="female" v-model="inputs.sex" v-on:change="changeOption">
					<label class="check_label" for="radio_female">女性</label>
					<input id="radio_other" type="radio" name="sex" value="other" v-model="inputs.sex" v-on:change="changeOption">
					<label class="check_label" for="radio_other">その他</label>
					<!-- <select type="select" name="sex" v-model="inputs.sex">
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="other">Other</option>
					</select> -->
					<span>*必須</span>  <span class="red_message">{{ err.sexErr }}</span>
				</div>
				<div class="buttonArea">
					<button type="submit" class="button">ユーザー登録</button>
				</div>
			</form>
		</section>
		<section v-if="submitted">
			<form method="post" action="<?php echo htmlspecialchars('confirm.php'); ?>">
				<div class="inputData">
					<p>ユーザー名: {{ response.username }}</p>
					<input type="hidden" name="username" v-model="response.username">
					<p>パスワード: {{ response.password }}</p>
					<input type="hidden" name="hash" v-model="response.hash">
					<p>メールアドレス: {{ response.email }}</p>
					<input type="hidden" name="email" v-model="response.email">
					<p>性別: {{ response.sex }}</p>
					<input type="hidden" name="sex" v-model="response.sex">
				</div>
				<div class="buttonArea">
					<button type="submit">上記の内容で登録する</button>
				</div>
			</form>
		</section>
		<api-loading v-if="loading"></api-loading>
	</div>
</body>
</html>

<script type="text/javascript" src="./js/loading.js"></script>
<script type="text/javascript">
// Vue.component('input-part', {
// 	props: {
// 		type: String,
// 		name: String,
// 		model: String,
// 		required: String,
// 		err: String
// 	},
// 	template: '<div class="inputArea"><p>{{ name }}</p><input type="type" name="name" v-model="model"><span>{{ required }}</span><span class="red_message"> {{ err }}</span></div>'
// })

new Vue({
	el: "#wrapper",
	data: {
		// textType: 'text',
		// passwordType: 'password',
		// username: 'username',
		// password: 'password',
		// password_check: 'password_check',
		// email: 'email',
		// sex: 'sex',
		// required: '*required',
		inputs: {
			username: null,
			password: null,
			password_check: null,
			email: null,
			sex: 'male'
		},
		err: {
			usernameErr: null,
			passwordErr: null,
			password_checkErr: null,
			emailErr: null,
			sexErr: null
		},
		isErr: false,
		response: {
			username: "",
			password: "",
			hash: "",
			email: "",
			sex: ""
		},
		submitted: false,
		select: true,
		loading: false
	},
	methods: {
		resetErr: function(){
			this.err.usernameErr = this.err.passwordErr = this.err.password_checkErr = this.err.emailErr = this.err.sexErr = null;
		},
		changeOption: function(e){
			if(e.target.value == 'select'){
				this.select = true;
			}else{
				this.select = false;
			}
		},
		validation: function(e){
			this.isErr = false;
			this.resetErr();
			if(this.inputs.username == null){
				this.err.usernameErr = "User name is required.";
				this.isErr = true;
			}
			if(this.inputs.password == null){
				this.err.passwordErr = "Password is required";
				this.isErr = true;
			}
			if(this.inputs.password_check == null || this.inputs.password != this.inputs.password_check){
				this.err.password_checkErr = "Password and password_check are not the same";
				this.password_check = null;
				this.isErr = true;
			}
			if(this.inputs.email == null){
				this.err.emailErr = "Email is required";
				this.isErr = true;
			}
			if(this.inputs.sex == null){
				this.err.sexErr = "Choose your sex";
				this.isErr = true;
			}
			if(this.isErr == false){
				let vm = this;
				this.loading = true;
				axios.post('./controller/input_check.php', vm.inputs)
				.then(function(res){
					if(res.data['usernameErr'] != null || res.data['emailErr'] != null){
						for(key in res.data){
							vm.$set(vm.err, key, res.data[key]);
						}
					}else{
						for(key in res.data){
							vm.$set(vm.response, key, res.data[key]);
						}
						vm.submitted = true;
					}
					vm.loading = false;
				})
				.catch(function(err){
					console.log(err);
				});
			}
			e.preventDefault();
		}
	}
})
</script>