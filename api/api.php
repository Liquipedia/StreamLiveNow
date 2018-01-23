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
		checkStreamStatusOnDouyu($channelName, $streamingService);
		break;
	case "huomao.com":
		checkStreamStatusOnHuomao($channelName, $streamingService);
		break;
	case "youtube.com":
		checkStreamStatusOnYoutube($channelName, $streamingService);
		break;
	case "facebook.com":
		checkStreamStatusOnFacebook($channelName, $streamingService);
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
	$API_KEY = 'AIzaSyB-rpirk39e2HoC1VxS6uTpM2jgKQuLp90';

	$channelInfo = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId='.$channelName.'&type=video&eventType=live&key='.$API_KEY;

	// echo $channelInfo;
	
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

function checkStreamStatusOnFacebook($channelName, $streamingService)
{
	/* PHP SDK v5.0.0 */
	/* make the API call */
	try {
	  // Returns a `Facebook\FacebookResponse` object
	  $response = $fb->get(
		'/{video-id}',
		'{access-token}'
	  );
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}
	$graphNode = $response->getGraphNode();
}

function checkStreamStatusOnDouyu($channelName, $streamingService)
{
	jsonResponse("false", $channelName, $streamingService);
}

function checkStreamStatusOnHuomao($channelName, $streamingService)
{
	jsonResponse("false", $channelName, $streamingService);
}

function jsonResponse($bool, $channelName, $streamingService)
{
	echo '{"live":"' . $bool . '","channel":"' . $channelName .  '","streaming_service":"' . $streamingService .  '"}';
}
?>