<?php
require_once './userModel.php';
$usermodel = new UserModel();
//$dao->createTable("CREATE TABLE user_profile(user_id INT(6) UNSIGNED PRIMARY KEY, pic VARCHAR(225), currency VARCHAR(225) DEFAULT 'JPY')");
//$dao->insertUser("test-a", "testa", "a@gail.com", "female");
require_once './dao.php';
$dao = new Dao();
//$dao->createTable("CREATE TABLE money_record (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id iNT(6) NOT NULL, type VARCHAR(10) NOT NULL, person VARCHAR(225) NOT NULL, status VARCHAR(225) NOT NULL, amount INT(225) NOT NULL, comment VARCHAR(225), deadline DATE)");
//$dao->createTable("CREATE TABLE countries_currency (id INT(225) UNSIGNED AUTO_INCREMENT PRIMARY KEY, country_name VARCHAR(225) NOT NULL, currency_name VARCHAR(225) NOT NULL)");
//var_dump($dao->select("SELECT * FROM user WHERE username = :username", array(':username' => 'yamada')));
require_once './moneyModel.php';
$moneymodel = new MoneyModel;
//$moneymodel->registerMoneyRecord(1, '借り', '木内', '未清算', 320, 'JPY', 'ごはん', NULL);

// require_once './phpQuery-onefile.php';
// $html = file_get_contents('https://www.iban.jp/currency-codes');
// $data = array();
// $doc = phpQuery::newDocument($html);
// foreach($doc["table tbody tr"] as $key => $value){
// 	if($key == 0){
// 		continue;
// 	}elseif($key < 139){
// 		continue;
// 	}else{
// 		$country_name = pq($value)->find("td:eq(0)")->text();
// 		$currency_name = pq($value)->find("td:eq(2)")->text();
// 		if($country_name && $currency_name){
// 			// $dao->insert("INSERT INTO countries_currency (country_name, currency_name)
// 			// 			VALUES(:country_name, :currency_name);", $arr = array(
// 			// 															":country_name" => $country_name,
// 			// 															":currency_name" => $currency_name
// 			// 																)
// 			// 		);
// 			//$dao->insertCountries($country_name, $currency_name);
// 			array_push($data, [$country_name, $currency_name]);
// 		}
// 	}
// }
//$data = $dao->select("SELECT * FROM countries_currency WHERE country_name = ':country_name';", array(":country_name" => "バングラデシュ"));
//var_dump($data);
?>