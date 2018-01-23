<html>
<head>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<title>Stream Service Test Page</title>
</head>
<body>
<!-- same structure as player pages with infobox on Liquipedia -->
<div class="fo-nttax-infobox">

	<a href="parser.php" >parser.php</a> |
	<a href="afreecatvStreamList.php" >afreecatvStreamList.php</a> |
	<a href="http://terbets.id.lv/tl/api/api.php" >/api/api.php</a>
	<br /><br />

	<a rel="nofollow noopener" class="external text" href="https://www.afreecatv.com/asgasga" >
		<i class="lp-icon lp-twitch"></i>asgasga afreeca
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.afreecatv.com/yyy2222" >
		<i class="lp-icon lp-twitch"></i>yyy2222 afreeca
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.twitch.tv/medrybw" >
		<i class="lp-icon lp-twitch"></i>medryBW twitch
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.youtube.com/UCVeETS7uZTAARqvv2zssZCw" >
		<i class="lp-icon lp-twitch"></i>UCVeETS7uZTAARqvv2zssZCw youtube
	</a>
	<br />

	<a rel="nofollow noopener" class="external text" href="https://www.twitch.tv/test_twitch" >
		<i class="lp-icon lp-twitch"></i>test_twitch
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.youtube.com/test_youtube" >
		<i class="lp-icon lp-twitch"></i>test_youtube
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.dailymotion.com/test_dailymotion" >
		<i class="lp-icon lp-twitch"></i>test_dailymotion
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.smashcast.com/test_smashcast" >
		<i class="lp-icon lp-twitch"></i>test_smashcast
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.afreecatv.com/test_afreecatv" >
		<i class="lp-icon lp-twitch"></i>test_afreecatv
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.douyu.com/test_douyu" >
		<i class="lp-icon lp-twitch"></i>test_douyu
	</a> |
	<a rel="nofollow noopener" class="external text" href="https://www.huomao.com/test_huomao" >
		<i class="lp-icon lp-twitch"></i>test_huomao
	</a> |
</div>
<!-- display debug info here -->
<div id="debug">
</div>
<script type="text/javascript">
findLink("twitch.tv");
findLink("youtube.com");
findLink("dailymotion.com");
findLink("smashcast.com");
findLink("afreecatv.com");
findLink("douyu.com");
findLink("huomao.com");
/*
ToDo:
// don't run this script on all pages...
// stripping http(s):// part. Optimize and test carefully!
// blinking animation if stream is live - change it to something better!
// only finds the first link on the page
*/
function findLink(streamingService)
{
	if ($("div.fo-nttax-infobox").find('a[href*="' + streamingService + '"]').length)
	{
		console.log(streamingService + " link was found on this page");
		var streamLink = $("div.fo-nttax-infobox").find('a[href*="' + streamingService + '"]').attr("href");
		var userName = streamLink.replace(/^.*\/\/[^\/]+/, '');
		userName = userName.replace('/channel', '');
		userName = userName.replace('/', '');
		$.ajax({
		type: 'GET',
		dataType: 'json',
		url: 'http://terbets.id.lv/tl/api/'+ streamingService + "/" + userName,
		success: function(data){
			if (data.live === "true"){
				console.log(userName + " is live on " + streamingService);
				
				var $blinkingdiv = $( "div.fo-nttax-infobox > div > div:first" );
				var backgroundInterval = setInterval(function(){
					$blinkingdiv.toggleClass("awerti-blink-class1");
				},2000)
			}else
			{
				console.log(userName + " is offline on " + streamingService);
			}
		},
		error: function(textStatus, error){
			console.log(streamingService + " Error:" + textStatus + " " +  error);
		}
		});
	}
	else
	{
		console.log(streamingService + " link not found");
	}
}
</script>
</body>
</html>