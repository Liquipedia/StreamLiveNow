# StreamLiveNow

Small module/plugin for Liquipedia

## What it does?
Wiki pages that have an Infobox can feature links to streaming services. 
If the player or organization is live, a notification will be shown on the wiki page.	

## Architecture
![Very simple and general architecture graph](https://github.com/XeroCodeIT/StreamLiveNow/blob/master/architecture.png)

## Files
* index.php - 
* afreecatvStreamList.php - 
* parser.php - 
* api.php - provides GET method, call examples:
```
/api/api.php?streamingService=twitch&channelName=medrybw
/api/api.php?streamingService=afreecatv&channelName=cksgmldbs
/api/twitch.com/medrybw
/api/afreecatv.com/cksgmldbs
```

## TODO:

* Youtube support
* Huomao support
* Douyu support
* log request info, to make future easier
* evaluate performance of this script. Is it too slow to handle all the requests?
* defense against improper use? Someone making too many requests?
* return live: false if varnish cache is not working. I mean, what happens if varnish stops working properly? Too many requests would be made to twitch/smashcast/dailymotion/youtube APIs then. Hmm...
* check if $_GET params are empty
* error logging and management

## Credits


## License
* All original code is under the GPLv3