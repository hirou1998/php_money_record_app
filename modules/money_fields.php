<div id="money_form">
	<form v-on:submit="submit">
		<div class="inputArea">
			<p>type</p>
			<select type="select" name="type" v-model="inputs.type">
				<option value="lend">貸し</option>
				<option value="borrow">借り</option>
			</select>
			<span>*required</span>  <span class="red_message">{{ err.typeErr }}</span>
		</div>
		<div class="inputArea">
			<p>Person</p>
			<!-- <div>
				<input type="radio" name="choose" value="select" v-model="inputs.choose" v-on:change="changeOption">選択する
				<input type="radio" name="choose" value="register" v-model=	"inputs.choose" v-on:change="changeOption">新規入力する
			</div>
			<p>{{select}}</p> -->
			<div>
				<!-- <p v-if="select">this function is not available yet</p> -->
				<input type="text" name="person" v-model="inputs.person">
			</div>
			<span>*required</span>  <span class="red_message">{{ err.personErr }}</span>
		</div>
		<div class="inputArea">
			<p>Status</p>
			<select v-model="inputs.status">
				<option value="unsettled">未清算</option>
				<option value="settled">清算済</option>
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
			<input type="text" name="currency" v-model="inputs.currency">
		</div>
		<div class="inputArea">
			<p>Comment</p>
			<textarea name="comment" v-model="inputs.comment"></textarea>
		</div>
		<div class="inputArea">
			<p>Deadline</p>
			<input type="date" name="deadline" v-model="inputs.deadline">
		</div>
		<div class="buttonArea">
			<button type="submit" class="button">Register</button>
		</div>
	</form>
</div>

<script type="application/javascript">
new Vue({
	el: '#money_form',
	data: {
		inputs: {
			type: 'lend',
			choose: 'select',
			person: 'hiro',
			status: 'unsettled',
			amount: 500,
			currency: '<?php echo $profile['currency']; ?>',
			comment: null,
			deadline: null
		},
		err: {
			typeErr: null,
			personErr: null,
			statusErr: null,
			amountErr: null
		},
		select: true,
		isErr: false
	},
	methods: {
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
			if(this.type == ''){
				this.typeErr = 'Please choose Type';
				this.isErr = true;
			}
			if(this.person == ''){
				this.personErr = 'Please choose or input Person name';
				this.isErr = true;
			}
			if(this.status == ''){
				this.statusErr = 'Please select Status';
				this.isErr = true;
			}
			if(this.amount == ''){
				this.amountErr = 'Please input Amount of money';
				this.isErr = true;
			}
			if(this.isErr == false){
				let vm = this;
				axios.post('../controller/money_fields.php', vm.inputs)
				.then(function(res){
					console.log(res.data);
				})
				.catch(function(err){
					console.log(err);
				});
			}
			e.preventDefault();
		}
	}
});
</script>