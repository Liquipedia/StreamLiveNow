<?php

require_once("vendor/autoload.php");

/*
AfreecaTV channel list parser
Get Live stream list:
http://live.afreecatv.com/afreeca/broad_list_api.php

make it a valid array, remove "var oBroadListData = "
json_encode
filter out live streams
then (maybe?) filter out streams linked on liquipedia. If not, there will be too many irrelevant links
store info in DB
add cron job
escaped unicode symbols in the array, do something about that...
Korean specific symbols

display initial channel count
display LIVE channel count
*/

//$api = 'http://terbets.id.lv/tl/1.js';
//$api = 'http://terbets.id.lv/tl/2.js';
$api ="http://live.afreecatv.com/afreeca/broad_list_api.php";

// add to file instead of losing content all the time
$fp = fopen(dirname(__FILE__).'/errorlog.txt', 'w');
$ch = curl_init();
curl_setopt_array($ch, array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_VERBOSE => true,							
	CURLOPT_STDERR => $fp, 								
	CURLOPT_URL => $api
));
$response = curl_exec($ch);
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



echo "<pre>";
//print_r($json);
echo "</pre>";



$aa = [];
$AfreecaLiveStreamList = arraySearch('LIVE', $json['CHANNEL']['REAL_BROAD'],$aa);

echo "<pre>";
print_r($AfreecaLiveStreamList);
echo "</pre>";







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
		// log errror
		die("Connection failed: " . $conn->connect_error);
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
	echo "New records added successfully";
} catch (PDOException $e) {
    echo 'Database exception: ',  $e->getMessage(), "\n";
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

//echo json_last_error();
?>