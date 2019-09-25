<!DOCTYPE html>
<html>
<?php
$title = "Log in";
include './modules/header.php';
?>
<body>
	<div id="wrapper" class="wrapper">
		<section class="top_content">
			<h1>ログイン</h1>
		</section>
		<section>
			<form v-on:submit="validation">
				<div class="inputArea">
					<p>ユーザー名</p>
					<input type="text" name="username" v-model="inputs.username">
					<span>*必須 {{errs.usernameErr}}</span>
				</div>
				<div class="inputArea">
					<p>パスワード</p>
					<input type="password" name="password" v-model="inputs.password">
					<span>*必須 {{errs.passwordErr}}</span>
				</div>
				<div class="buttonArea">
					<button class="button" type="submit">ログイン</button>
				</div>
			</form>
		</section>
		<p>{{ loggedIn }}</p>
		<p>{{ message }}</p>
	</div>
</body>
</html>
<script type="text/javascript" src="./js/loading.js"></script>
<script type="text/javascript">
new Vue({
	el: "#wrapper",
	data: {
		inputs: {
			username: null,
			password: null
		},
		errs: {
			usernameErr: null,
			passwordErr: null
		},
		isErr: false,
		loggedIn: null,
		message: null
	},
	methods: {
		validation: function(e){
			this.errs.usernameErr = this.errs.passwordErr = null;
			this.isErr = false;
			this.message = this.loggedIn = null;
			if(!this.inputs.username){
				this.errs.usernameErr = "ユーザー名が必要です。";
				this.isErr = true;
			}
			if(!this.inputs.password){
				this.errs.passwordErr = "パスワードが必要です。";
				this.isErr = true;
			}
			if(this.isErr == false){
				let vm = this;
				axios.post('./controller/login_check.php', vm.inputs)
				.then(function(res){
					console.log(res.data);
					if(res.data['message'] == null){
					location.href = 'mypage.php';
					}else{
						vm.message = res.data['message'];
					}
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