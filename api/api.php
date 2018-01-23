<?php
/*
API request examples
website.com/api.php?streamingService=twitch&channelName=medrybw
website.com/api.php?streamingService=afreecatv&channelName=cksgmldbs
website.com/twitch/medrybw
website.com/afreecatv/cksgmldbs

Done:
// .htaccess pretty URL
// input validation
// caching - outside the scope of this service. Varnish should take care of that

ToDo:
// store afreecatv links in DB, but info from other streams is retrieved via API or cached on the fly.
// log request info, to make future easier
// evaluate performance of this script. Is it too slow to handle all the requests?
// defense against improper use? Someone making too many requests?
// return live: false if varnish cache is not working. I mean, what happens if varnish stops working properly? Too many requests would be made to twitch/smashcast/dailymotion/youtube APIs then. Hmm...
// check if $_GET params are empty
*/

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
		jsonResponse("false", $channelName, $streamingService);
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
	// check stream status in DB
	if (1 === 1)
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