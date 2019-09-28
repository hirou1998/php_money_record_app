<?php
require_once 'dao.php';
require_once 'userModel.php';

class MoneyModel{

	private $dao = null;
	private $uermodel = null;
	private $currency_list = array('JPY', 'USD', 'EUR', 'SEK', 'KRW', 'CNY', 'HKD');
	// private $exchange_rate_list_f = array(
	// 	"JPY" => array(
	// 		"USD" => [107.55, '2019-09-28 00:56:37'],
	// 		"EUR" => 118.52,
	// 		"SEK" => 11.08
	// 	),
	// 	"USD" => array(
	// 		"JPY" => 0.0092,
	// 		"EUR" => 1.102,
	// 		"SEK" => 0.103
	// 	),
	// 	"EUR" => array(
	// 		"JPY" => 0.0084,
	// 		"USD" => 0.907,
	// 		"SEK" => 0.093,
	// 	),
	// 	"SEK" => array(
	// 		"JPY" => 0.09,
	// 		"USD" => 9.70,
	// 		"EUR" => 10.693
	// 	)
	// );
	private $exchange_rate_list = array();

	public function __construct(){
		$this->dao = new Dao();
		$this->usermodel = new UserModel();
	}

	public function getCurrencyList(){
		return $this->currency_list;
	}

	public function registerMoneyRecord($userid, $type, $person, $status, $amount, $currency, $comment, $deadline, $date, $status_number){
		$sql = 'INSERT INTO money_record (user_id, type, person, status, amount, currency, comment, deadline, reg_date, status_number)
				VALUES (:userid, :type, :person, :status, :amount, :currency, :comment, :deadline, :reg_date, :status_number)';
		$arr = array(
			':userid' => $userid,
			':type' => $type,
			':person' => $person,
			':status' => $status,
			':amount' => $amount,
			':currency' => $currency,
			':comment' => $comment,
			':deadline' => $deadline,
			':reg_date' => $date,
			':status_number' => $status_number
		);
		$this->dao->insert($sql, $arr);
	}

	public function getMoneyRecord($userid){
		$sql = "SELECT id, type, person, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE user_id = :userid AND archive = :archive ORDER BY id DESC" ;
		$arr = array(
			':userid' => $userid,
			':archive' => 0
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function getMoneyRecordById($id){
		$sql = "SELECT id, type, person, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE id = :id";
		$arr = array(
			':id' => $id
		);
		$result = $this->dao->select($sql, $arr);

		return $result;
	}

	public function getPersonsList($userid){
		$sql = "SELECT person FROM money_record WHERE user_id = :userid";
		$arr = array(
			':userid' => $userid
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		$personsList = array();
		foreach($result as $item){
			$newperson = $item['person'];
			if(in_array($newperson, $personsList) == false){
				array_push($personsList, $item['person']);
			}else{
				continue;
			}
		}

		return $personsList;
	}

	public function getRecordId($userid){
		$sql = 'SELECT id FROM money_record WHERE user_id = :userid AND archive = :archive ORDER BY id DESC';
		$arr = array(
			':userid' => $userid,
			':archive' => 0
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		$ids = array();
		foreach ($result as $item) {
			array_push($ids, $item['id']);
		}

		return $ids;
	}

	public function getRecordIdBasedOnPerson($userid, $person){
		$sql = 'SELECT id FROM money_record WHERE user_id = :userid AND person = :person AND archive = :archive ORDER BY status_number ASC, id DESC';
		$arr = array(
			':userid' => $userid,
			':person' => $person,
			':archive' => 0
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function makeIdListBasedOnPerson($userid){
		$personsList = $this->getPersonsList($userid);
		$idList = array();
		foreach ($personsList as $person) {
			$result = $this->getRecordIdBasedOnPerson($userid, $person);
			$arr = array();
			foreach ($result as $item) {
				foreach ($item as $key => $value) {
					array_push($arr, $value);
				}
			}
			$idList = $idList + array($person => $arr);
		}

		return $idList;
	}

	public function getRecordBasedOnPerson($userid, $person){
		$sql = 'SELECT id, type, status, amount, currency, comment, deadline, reg_date FROM money_record WHERE user_id = :userid AND person = :person AND archive = :archive ORDER BY status_number ASC, id DESC';
		$arr = array(
			':userid' => $userid,
			':person' => $person,
			':archive' => 0
		);
		$result = $this->dao->selectMultipulData($sql, $arr);

		return $result;
	}

	public function calculationTotalAmount($result, $myCurrency){
		$totalAmount = 0;
		foreach ($result as $item) {	
			if($item['status'] == '未清算'){
				//exchange
				if($item['currency'] == $myCurrency){
					if($item['type'] == '貸し'){
						$amount =intval($item['amount']);
						$totalAmount += $amount;
					}else{
						$amount =intval($item['amount']);
						$totalAmount -= $amount;
					}
				}else{
					$exchange_rate = $this->calculateExchange($myCurrency, $item['currency']);
					//var_dump($exchange_rate);
					if($item['type'] == '貸し'){
						$amount =intval($item['amount'] * $exchange_rate);
						$totalAmount += $amount;
					}else{
						$amount =intval($item['amount']) * $exchange_rate;
						$totalAmount -= $amount;
					}
				}
			}else{
				continue;
			}
		}
	
		return round($totalAmount, 2);
	}

	public function getAllRecordsBasedOnPerson($userid, $myCurrency){
		$personsList = $this->getPersonsList($userid);
		$result = array();
		foreach ($personsList as $person) {
			$record = $this->getRecordBasedOnPerson($userid, $person);
			array_push($result, $record);
		}

		$data = array();
		foreach ($result as $item) {
			$total = $this->calculationTotalAmount($item, $myCurrency);
			$item = $item + array('total' => $total);
			array_push($data, $item);
		}

		return $data;
	}

	public function updateMoneyRecord($type, $person, $status, $amount, $currency, $comment, $deadline, $id){
		$sql = "UPDATE money_record SET type = :type, person = :person, status = :status, amount = :amount, currency = :currency, comment = :comment, deadline = :deadline WHERE id = :id";
		$arr = array(
			':type' => $type,
			':person' => $person,
			':status' => $status,
			':amount' => $amount,
			':currency' => $currency,
			':comment' => $comment,
			':deadline' => $deadline,
			':id' => $id
		);
		$this->dao->update($sql, $arr);
	}

	public function deleteMoneyRecord($id){
		$sql = "DELETE FROM money_record WHERE id = :id";
		$arr = array(
			':id' => $id
		);
		$this->dao->delete($sql, $arr);
	}

	public function calculateExchange($myCurrency, $recordCurrency){
		$exchange_rate_list = $this->exchange_rate_list;
		//var_dump($exchange_rate_list);

		if(!empty($exchange_rate_list)){

			//var_dump("not empty");

			if(array_key_exists($myCurrency, $exchange_rate_list)){

				$my_currency_exchange_rate = $exchange_rate_list[$myCurrency];
				//var_dump("mycyrrency in array");

				if(array_key_exists($recordCurrency, $my_currency_exchange_rate)){

					date_default_timezone_set("Europe/London");
					$current_time = date('Y-m-d H:i:s');
					$last_executed_time = $my_currency_exchange_rate[$recordCurrency][1];
					$time_diff = abs(strtotime($current_time) - strtotime($last_executed_time)) / (60 * 60);
					//var_dump("record currency in array");

					if($time_diff > 3){

						$result = $this->callApiToGetExchangeRate($myCurrency, $recordCurrency);
						foreach ($result as $key => $value) {
							$my_currency_exchange_rate[$recordCurrency][$key] = $value;	
						}
						$exchange_rate = $my_currency_exchange_rate[$recordCurrency][0];
						//var_dump("three hours passed");

					}else{

						$exchange_rate = $my_currency_exchange_rate[$recordCurrency][0];
						//var_dump("widtin three hours since last api was call");

					}

				}else{

					$result = $this->callApiToGetExchangeRate($myCurrency, $recordCurrency);
					$my_currency_exchange_rate = $my_currency_exchange_rate + array($recordCurrency => array($result[0], $result[1]));
					$exchange_rate = $my_currency_exchange_rate[$recordCurrency][0];
					//var_dump("record curreny not in array");

				}

			}else{

				$result = $this->callApiToGetExchangeRate($myCurrency, $recordCurrency);
				$arr = array($myCurrency => array($recordCurrency => array($result[0], $result[1])));
				$exchange_rate_list = $exchange_rate_list + $arr;
				$exchange_rate = $exchange_rate_list[$myCurrency][$recordCurrency][0];
				//var_dump("my currency not in array");	

			}

		}else{

			$result = $this->callApiToGetExchangeRate($myCurrency, $recordCurrency);
			$arr = array($myCurrency => array($recordCurrency => array($result[0], $result[1])));
			$this->exchange_rate_list = $arr;
			$exchange_rate = $result[0];
			//var_dump($exchange_rate_list);
			//var_dump("first");

		}

		//var_dump($exchange_rate_list);
		return $exchange_rate;
	}

	public function callApiToGetExchangeRate($myCurrency, $recordCurrency){
		$key = $recordCurrency . '_' . $myCurrency;
		$api = 'https://free.currconv.com/api/v7/convert?q=' . $key . '&compact=ultra&apiKey=f603ebb5d1a5a0b6e8c0';
		$result = json_decode(file_get_contents($api), true);
		$new_exchange_rate = $result[$key];

		$result = json_decode(file_get_contents('https://free.currconv.com/others/usage?apiKey=f603ebb5d1a5a0b6e8c0'),true);
		$date = substr($result["timestamp"], 0,  strpos($result["timestamp"], 'T'));
		$time = substr($result["timestamp"], 11, 8);
		$date_time = $date . ' ' . $time;
		//var_dump($date_time);

		return $result = array($new_exchange_rate, $date_time);
	}

	public function changeToSettled($id){
		$sql = "UPDATE money_record SET status = :status, status_number = :status_number WHERE id = :id";
		$arr = array(
			":status" => "清算済",
			":status_number" => 1,
			":id" => $id
		);
		$this->dao->update($sql, $arr);
	}

	public function archiveRecord($id){
		$sql = "UPDATE money_record SET archive = :archive WHERE id = :id";
		$arr = array(
			":archive" => 1,
			":id" => $id
		);
		$this->dao->update($sql, $arr);
	}

}

?>