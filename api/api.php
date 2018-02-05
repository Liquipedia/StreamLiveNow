<?php

require_once("../vendor/autoload.php");

$channelName = filter_var ( $_GET['channelName'], FILTER_SANITIZE_STRING);
$streamingService = filter_var ( $_GET['streamingService'], FILTER_SANITIZE_STRING);

switch($streamingService)
{
	case "afreecatv.com":
		checkStreamStatusOnAfreeca($channelName, $streamingService);
		break;	
	case "dailymotion.com":
		$api = "https://api.dailymotion.com/video/" . $channelName . "?fields=onair";
		checkStreamStatusOnDailymotion($api, $channelName, $streamingService);
		break;	
	case "douyu.com":
		checkStreamStatusOnDouyu($channelName, $streamingService);
		break;	
	case "facebook.com":
		checkStreamStatusOnFacebook($channelName, $streamingService);
		break;		
	case "garena.live":
		checkStreamStatusOnGarena($channelName, $streamingService);
		break;		
	case "huomao.com":
		checkStreamStatusOnHuomao($channelName, $streamingService);
		break;		
	case "smashcast.com":
		$api="https://api.smashcast.tv/media/status/";
		checkStreamStatusOnSmashcast($api, $channelName, $streamingService);
		break;		
	case "twitch.tv":
		$api = 'https://api.twitch.tv/kraken/streams/';
		checkStreamStatusOnTwitch($api, $channelName, $streamingService);
		break;
	case "youtube.com":
		checkStreamStatusOnYoutube($channelName, $streamingService);
		break;
	default:
		echo '{"live" : "false", "error" : "streaming service not recognized"}';
		LogError("Streaming service not recognized: " . $streamingService . "\n");
		break;
}

function checkStreamStatusOnAfreeca($channelName, $streamingService){
	try {
		$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		if ($pdo->connect_error){
			LogError("DB connection failed " . $pdo->connect_error . "\n");
			die;
		}

		$sql = "SELECT * FROM afreecatv_streams WHERE name = '" . $channelName . "'";
		$result = $pdo->query($sql);
		if ($result->rowCount() > 0) {
			jsonResponse("true", $channelName, $streamingService);
		} else{
			jsonResponse("false", $channelName, $streamingService);
		}
	} catch (PDOException $e) {
		LogError("Database exception: " . $e->getMessage() . "\n");
		die;
	}
	$pdo = null;
}

function checkStreamStatusOnDailymotion($api, $channelName, $streamingService){
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $api
	));
	$response = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($response, true);
	if ($json['onair'] === true)
	{
		jsonResponse("true", $channelName, $streamingService);
	}
	else
	{
		jsonResponse("false", $channelName, $streamingService);
	}
}

function checkStreamStatusOnDouyu($channelName, $streamingService){
	jsonResponse("false", $channelName, $streamingService);
}

function checkStreamStatusOnFacebook($channelName, $streamingService){
	echo '{"live" : "false", "error" : "Facebook service not implemented yet"}';
	exit;
}

function checkStreamStatusOnGarena($channelName, $streamingService){
	echo '{"live" : "false", "error" : "Garena.live service not implemented yet"}';
	exit;

	$url = "https://garena.live/api/channel_stream_get";
	$data = array('channel_id' => 437340);
	$options = array('http' => array(
		'method'  => 'POST',
		'content' => http_build_query($data)
	));
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	echo "<pre>";
	print_r($result);
	echo "</pre>";
	// 391550 1585175
	$url = "https://garena.live/api/channel_info_get";
	$data = array('channel_id' => 437340);
	$options = array('http' => array(
		'method'  => 'POST',
		'content' => http_build_query($data)
	));
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	echo "<pre>";
	print_r($result);
	echo "</pre>";
	
	jsonResponse("false", $channelName, $streamingService);
}

function checkStreamStatusOnHuomao($channelName, $streamingService){
	jsonResponse("false", $channelName, $streamingService);
}

function checkStreamStatusOnSmashcast($api, $channelName, $streamingService){
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $api . $channelName
	));
	$response = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($response, true);
	if ($json['media_is_live'] === "1")
	{
		jsonResponse("true", $channelName, $streamingService);
	}
	else
	{
		jsonResponse("false", $channelName, $streamingService);
	}
}

function checkStreamStatusOnTwitch($api, $channelName, $streamingService){
	$clientId = 'iunn9vjy5ffoaowyf6bx5nxp6ttjsw';
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_HTTPHEADER => array(
		'Client-ID: ' . $clientId
	),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $api . $channelName
	));
	$response = curl_exec($ch);
	curl_close($ch);
	$json = json_decode($response, true);
	if ($json['stream']['stream_type']==="live")
	{
		jsonResponse("true", $channelName, $streamingService);
	}
	else
	{
		jsonResponse("false", $channelName, $streamingService);
	}
}

function checkStreamStatusOnYoutube($channelName, $streamingService){
	$API_KEY = 'AIzaSyB-rpirk39e2HoC1VxS6uTpM2jgKQuLp90';

	$channelInfo = "https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=" . $channelName . "&type=video&eventType=live&key=" . $API_KEY;
	
	$extractInfo = file_get_contents($channelInfo);
	$extractInfo = str_replace('},]',"}]",$extractInfo);
	$showInfo = json_decode($extractInfo, true);

	if($showInfo['pageInfo']['totalResults'] == 0)
	{
		jsonResponse("false", $channelName, $streamingService);
	}else{
		jsonResponse("true", $channelName, $streamingService);
	}
}

function jsonResponse($bool, $channelName, $streamingService){
	echo '{"live":"' . $bool . '","channel":"' . $channelName .  '","streaming_service":"' . $streamingService .  '"}';
}

?>