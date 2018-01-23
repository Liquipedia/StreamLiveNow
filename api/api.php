<?php

require_once("../vendor/autoload.php");

$channelName = filter_var ( $_GET['channelName'], FILTER_SANITIZE_STRING);
$streamingService = filter_var ( $_GET['streamingService'], FILTER_SANITIZE_STRING);

switch($streamingService)
{
	case "twitch.tv":
		$api = 'https://api.twitch.tv/kraken/streams/';
		checkStreamStatusOnTwitch($api, $channelName, $streamingService);
		break;
	case "smashcast.com":
		$api="https://api.smashcast.tv/media/status/";
		checkStreamStatusOnSmashcast($api, $channelName, $streamingService);
		break;
	case "dailymotion.com":
		$api = "https://api.dailymotion.com/video/" . $channelName . "?fields=onair";
		checkStreamStatusOnDailymotion($api, $channelName, $streamingService);
		break;
	case "afreecatv.com":
		checkStreamStatusOnAfreeca($channelName, $streamingService);
		break;
	case "douyu.com":
		jsonResponse("false", $channelName, $streamingService);
		break;
	case "huomao.com":
		jsonResponse("false", $channelName, $streamingService);
		break;
	case "youtube.com":
		checkStreamStatusOnYoutube($channelName, $streamingService);
		break;
	default:
		echo '{"live" : "false", "error" : "streaming service not recognized"}';
		// log an error?
		break;
}

function checkStreamStatusOnSmashcast($api, $channelName, $streamingService)
{
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

function checkStreamStatusOnDailymotion($api, $channelName, $streamingService)
{
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

function checkStreamStatusOnTwitch($api, $channelName, $streamingService)
{
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

function checkStreamStatusOnAfreeca($channelName, $streamingService)
{
	
	try {
		$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
		$opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		$pdo = new PDO($dsn, DB_USER, DB_PASS, $opt);
		if ($pdo->connect_error){
			//die("Database connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM afreecatv_streams WHERE name = '" . $channelName . "'";
		$result = $pdo->query($sql);
		
		if ($result->rowCount() > 0) {
			jsonResponse("true", $channelName, $streamingService);
		} else{
			jsonResponse("false", $channelName, $streamingService);
		}
	} catch (PDOException $e) {
		//echo 'Database exception: ',  $e->getMessage(), "\n";
	}
	$pdo = null;
}

function checkStreamStatusOnYoutube($channelName, $streamingService)
{
	
	
	if (1 === 0)
	{
		jsonResponse("true", $channelName, $streamingService);
	}
	else
	{
		jsonResponse("false", $channelName, $streamingService);
	}
}

function jsonResponse($bool, $channelName, $streamingService)
{
	echo '{"live":"' . $bool . '","channel":"' . $channelName .  '","streaming_service":"' . $streamingService .  '"}';
}
?>