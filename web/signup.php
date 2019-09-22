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
			<h1>Sign up</h1>
			<p>簡単5項目のユーザー登録でお金の貸し借りの管理を簡単に</p>
		</section>
		<section>
			<form v-on:submit="validation">
				<!-- <input-part :type="textType" :name="username" :model="inputs.username" :required="required" :err="err.usernameErr"></input-part>
				<input-part :type="passwordType" :name="password" :model="inputs.password" :required="required" :err="err.passwordErr"></input-part>
				<input-part :type="passwordType" :name="username" :model="inputs.username" :required="required" :err="err.usernameErr"></input-part> -->
				<div class="inputArea">
					<p>User name</p>
					<input type="text" name="username" v-model="inputs.username">
					<span>*required</span>  <span class="red_message">{{ err.usernameErr }}</span>
				</div>
				<div class="inputArea">
					<p>Password</p>
					<input type="password" name="password" v-model="inputs.password">
					<span>*required</span>  <span class="red_message">{{ err.passwordErr }}</span>
				</div>
				<div class="inputArea">
					<p>Password (check)</p>
					<input type="password" name="password_check" v-model="inputs.password_check">
					<span>*required</span>  <span class="red_message">{{ err.password_checkErr }}</span>
				</div>
				<div class="inputArea">
					<p>Email</p>
					<input type="email" name="email" v-model="inputs.email">
					<span>*required</span>  <span class="red_message">{{ err.emailErr }}</span>
				</div>
				<div class="inputArea">
					<p>Sex</p>
					<select type="select" name="sex" v-model="inputs.sex">
						<option value="male">Male</option>
						<option value="female">Female</option>
						<option value="other">Other</option>
					</select>
					<span>*required</span>  <span class="red_message">{{ err.sexErr }}</span>
				</div>
				<div class="buttonArea">
					<button type="submit" class="button">Sign Up</button>
				</div>
			</form>
		</section>
		<section v-if="submitted">
			<form method="post" action="<?php echo htmlspecialchars('confirm.php'); ?>">
				<div class="inputData">
					<p>Username: {{ response.username }}</p>
					<input type="hidden" name="username" v-model="response.username">
					<p>Password: {{ response.password }}</p>
					<input type="hidden" name="hash" v-model="response.hash">
					<p>Email: {{ response.email }}</p>
					<input type="hidden" name="email" v-model="response.email">
					<p>Sex: {{ response.sex }}</p>
					<input type="hidden" name="sex" v-model="response.sex">
				</div>
				<div class="buttonArea">
					<button type="submit">上記の内容で登録する</button>
				</div>
			</form>
		</section>
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
			sex: 'female'
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
		submitted: false
	},
	methods: {
		resetErr: function(){
			this.err.usernameErr = this.err.passwordErr = this.err.password_checkErr = this.err.emailErr = this.err.sexErr = null;
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