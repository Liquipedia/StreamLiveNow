// Helper function to display JavaScript value on HTML page.
function showResponse(response) {
    var responseString = JSON.stringify(response, '', 2);
    document.getElementById('info_youtube').innerHTML += responseString;
}
// Called automatically when JavaScript client library is loaded.
function onClientLoad() {
    gapi.client.load('youtube', 'v3', onYouTubeApiLoad);
}
// Called automatically when YouTube API interface is loaded.
function onYouTubeApiLoad() {
    // my own API key...
    gapi.client.setApiKey('AIzaSyCR5In4DZaTP6IEZQ0r1JceuvluJRzQNLE');
    search();
}
function search() {
    // Use the JavaScript client library to create a search.list() API call.
    var request = gapi.client.youtube.search.list({
        part: 'snippet',
        type: 'video',
        eventType: 'live',
        channelId: 'UCfi2CYWmz5f7gxSaJ-y7nVg',
    });
    
    // Send the request to the API server,
    // and invoke onSearchResponse() with the response.
    request.execute(onSearchResponse);
}
// Called automatically with the response of the YouTube API request.
function onSearchResponse(response) {
    showResponse(response);
}