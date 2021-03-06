# StreamLiveNow

Small module/plugin for Liquipedia

## Demo
http://terbets.id.lv/tl/

## What it does?
Wiki pages that have an Infobox can feature links to streaming services. 
If the player or organization is live, a notification will be shown on the wiki page.	

## Architecture
How we check if the stream is live.
![Very simple and general architecture graph](https://github.com/XeroCodeIT/StreamLiveNow/blob/master/architecture.png)

## Files
* index.php - for testing only, 
* afreecatvStreamList.php - displays the current list of AfreecaTV streams, from the database
* parser.php - reads afreeca stream list and puts stream names in database
* /StreamArrays/ provides files to test parser.php.
* /api/api.php - provides GET method, call examples:
```
/api/api.php?streamingService=twitch&channelName=medrybw
/api/api.php?streamingService=afreecatv&channelName=cksgmldbs
/api/twitch.com/medrybw
/api/afreecatv.com/cksgmldbs
```

### Error logging:
* curl_log.txt - messages from connecting to afreecatv API
* error_log - unmanaged system error messages
* error_log.txt - managed app error messages

## TODO:
* use a hook to load the javascript only on pages with an infobox.
* separate link finding in html from querying the API.
* change js to find more than 1 link per streaming service (for example, if there are 2 twitch links, only 1 will be checked)
* Garena support
* Huomao support
* Douyu support
* Facebook support
* unit tests
* code comments
* proper error logging and management
* blinking animation if stream is live - change it to something better!
* filter AfreecaTV stream list to only include streams from liquipedia pages. But it's really low priority, since 4000 entries in database is not much. And having 50 links would not offer any noticeable advantage.
* evaluate performance of this script. Is it too slow to handle all the requests?
* defense against improper use? Someone making too many requests?
* return live: false if varnish cache is not working. I mean, what happens if varnish stops working properly? Too many requests would be made to twitch/smashcast/dailymotion/youtube APIs then. Hmm...

## Dependencies:
facebook/graph-sdk": "5.6.1"
phpdocumentor/phpdocumentor": "2.*"

## Credits

## License
* To be decided