<!DOCTYPE html>
<html>
<?php
$title = "Profile";
include './modules/header.php';
if(empty($_SESSION['username'])){
	$url = '../index.php';
	header('Location: ' . $url);
}
require_once './model/userModel.php';
$usermodel = new UserModel();
$profile = $usermodel->getUserProfile($_SESSION['user_id']);
$url = $profile['pic'];

require_once './model/moneyModel.php';
$moneymodel = new MoneyModel();
//$currency_list = $moneymodel->getCurrencyList();
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
				<div class="inputArea">
					<input type="text" name="input_word" v-composition-model="input_word">
				</div>
				<select v-model="inputs.currency">
					<option value="item.country_name" size=suggestion.length v-for="item in suggestion" style="height: 20vw;">
						<span>{{item.country_name}}</span>
						<span>{{item.currency_name}}</span>
					</option>
				</select>
				<!-- <ul style="height: 20vw; overflow: scroll;">
					<li v-for="item in suggestion">
						<span>{{item.country_name}}</span>
						<span>{{item.currency_name}}</span>
					</li>
				</ul> -->
			</div>
			<p>{{inputs.currency}}</p>
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

<script type="text/javascript" src="./js/loading.js"></script>
<script type="text/javascript">

function vCompositionModelUpdate (el, { value, expression }, vnode) {
  // data書き換え
  vnode.context[expression] = el.value;
  app.showSuggestion();
}

Vue.directive('composition-model', {
  bind: function (el, binding, vnode) {
    el.value = binding.value
    el.addEventListener('keyup', () => vCompositionModelUpdate(el, binding, vnode));
    el.addEventListener('compositionend', () => vCompositionModelUpdate(el, binding, vnode));
  },
  // dataが直接書き換わったときの対応
  update: function (el, { value }) {
    el.value = value
  }
});

var app = new Vue({
	el: '#wrapper',
	data: {
		inputs:{
			username: '<?php echo $profile["username"]; ?>',
			currency: '<?php echo $profile["currency"]; ?>',
			tmp_token: '<?php echo $_SESSION["tmp_token"]; ?>'
		},
		input_word: null,
		icon: '<?php echo $url; ?>',
		saved: false,
		suggestion: [
			{
				country_name: "なし",
				currency_name: "なし"
			}
		]
	},
	methods: {
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
		},
		showSuggestion: function(){
			let vm = this;
			axios.post('./controller/show_suggestion.php', {
				input_word: vm.input_word
			})
			.then(function(res){
				//console.log(res.data);
				vm.suggestion.splice(1);
				for(num in res.data){
					if(num == 0){
						vm.$set(vm.suggestion[num], 'country_name', res.data[num]['country_name']);
						vm.$set(vm.suggestion[num], 'currency_name', res.data[num]['currency_name']);
						//console.log(vm.suggestion);
					}else{
						vm.suggestion.push(res.data[num]);
					}
					vm.suggestion.push(res.data);
				}
			})
			.catch(function(err){
				console.log(err);
			});
		}
	}
})
</script>