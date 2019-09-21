<!DOCTYPE html>
<html>
<?php
$title = "Profile";
include './modules/header.php';
if(empty($_SESSION['username'])){
	$url = "http://" . $_SERVER['HTTP_HOST'] . '/db_practice/index.php';
	header('Location: ' . $url);
}
require_once './model/userModel.php';
$usermodel = new UserModel();
$profile = $usermodel->getUserProfile($_SESSION['user_id']);
$url = $profile['pic'];

require_once './model/moneyModel.php';
$moneymodel = new MoneyModel();
$currency_list = $moneymodel->getCurrencyList();
?>
<body>
	<div id="wrapper" class="wrapper">
		<p class="alert" v-if="saved">変更を保存しました。</p>
		<form v-on:submit="submit" enctype="multipart/form-data">
			<div class="inputArea">
				<p>User name</p>
				<input type="text" name="username" v-model="inputs.username">
			</div>
			<div class="inputArea">
				<p>Icon</p>
				<!-- <input type="file" name="pic" v-on:change="selectedFile"> -->
				<img v-bind:src="icon">
			</div>
			<div class="inputArea">
				<p>Currency</p>
				<select v-model="inputs.currency">
					<?php
					foreach($currency_list as $currency_item){
						echo '<option value=' . $currency_item . '>' . $currency_item . '</option>';
					}
					?>
				</select>
			</div>
			<input type="hidden" name="tmp_token" v-model="inputs.tmp_token">
			<div class="buttonArea">
				<button class="button" type="submit" v-on:click="saving">Save</button>
			</div>
		</form>
		<div class="buttonArea">
			<a href="mypage.php"><button class="button" type="submit">My page top</button></a>
		</div>
	</div>
</body>
</html>

<script type="text/javascript">
new Vue({
	el: '#wrapper',
	data: {
		inputs:{
			username: '<?php echo $profile["username"]; ?>',
			currency: '<?php echo $profile["currency"]; ?>',
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		icon: '<?php echo $url; ?>',
		saved: false
	},
	methods: {
		// selectedFile: function(e){
		// 	let files = e.target.files;
		// 	console.log(files);
		// 	this.newicon = files[1];
		// },
		submit: function(e){
			let vm = this;
			axios.post('./controller/profile.php', vm.inputs)
			.then(function(res){
				console.log(res.data);
			})
			.catch(function(err){
				console.log(err);
			});
			e.preventDefault();
		},
		saving: function(){
			this.saved = true;
		}
	}
})
</script>