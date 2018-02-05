<?php

require_once("vendor/autoload.php");

//$api = 'http://terbets.id.lv/tl/1.js';
//$api = 'http://terbets.id.lv/tl/2.js';
$api ="http://live.afreecatv.com/afreeca/broad_list_api.php";

$curl_log = fopen(dirname(__FILE__).'/curl_log.txt', 'a');
$ch = curl_init();
curl_setopt_array($ch, array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_VERBOSE => true,							
	CURLOPT_STDERR => $curl_log, 								
	CURLOPT_URL => $api
));
$response = curl_exec($ch);
$output= fread($curl_log, 2048);
fclose($curl_log);
curl_close($ch);

// convert to utf-8, lose all Korean symbols in the process
// convert the js array into a valid json array
// remove js array definition
// remove trailing ;
// convert single ticks to double ticks
// remove slashes
$response = mb_convert_encoding($response, 'UTF-8', 'UTF-8'); 
$old = array("var oBroadListData = ", 	"}};", 	"'", "\\");
$new = array("", 						"}}", 	'"', "");
$response = str_replace($old, $new, $response);
$json = json_decode($response, true);
// log error
// echo json_last_error();

echo "<pre>";
//print_r($json);
echo "</pre>";

$aa = [];
$AfreecaLiveStreamList = arraySearch('LIVE', $json['CHANNEL']['REAL_BROAD'],$aa);

echo "<pre>";
print_r($AfreecaLiveStreamList);
echo "</pre>";



// check if we retrieved stream list. If we did not, abandon.
// error log


// 	remove all existing streams in DB
//	add new streams in DB
try {
	$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
	if ($pdo->connect_error){
		error_log("Connection failed " . $conn->connect_error . "\n", 3, __DIR__ . "/error_log.txt");
		die;
	}

	$sql = "DELETE FROM afreecatv_streams WHERE 1";
	$pdo->query($sql);
	
	$sql = 'INSERT INTO afreecatv_streams (name) VALUES ';
	$insertQuery = array();
	$insertData = array();
	foreach ($AfreecaLiveStreamList as $row) {
		$insertQuery[] = '(?)';
		$insertData[] = $row;
	}
	if (!empty($insertQuery)) {
		$sql .= implode(', ', $insertQuery);
		$stmt = $pdo->prepare($sql);
		$stmt->execute($insertData);
	}
	echo "New records added successfully to DB.";
} catch (PDOException $e) {
	error_log("Database exception: " . $e->getMessage() . "\n", 3, __DIR__ . "/error_log.txt");
	die;
}
$pdo = null;

// add all user ids into array
function arraySearch($searchCriteria, $array, $resultArray) {
   foreach ($array as $key => $val) {
       //if ($val['content_type'] === $searchCriteria) {
	   //if ($val['total_view_cnt'] > 0) {  
		   array_push($resultArray, $val['user_id']);
		   //array_push($resultArray, $val['total_view_cnt']);
       //}
   }
   return $resultArray;
}

?>