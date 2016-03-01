# Using Third-party Web Services
Francisco Couto and Tiago Guerreiro

SOA (Service Oriented Architecture) is a software achitecture type that models the several system components by the functionalities they implement, as services (Web Services). The implementation of complex applications involves interconnecting several components by calling these services. 

Web Services can be seen as APIs that allow for this interconnection between heterogeneous software applications in the web by using the HTTP protocol.

In this tutorial, we show examples of using the Flickr API to search for photos, and Google Places to search for nearby points of interest. A brief challenge, that composes both APIS, is presented in the end. 

# The Flickr Example 

This tutorial aims to introduce the reader to using an external web service in this case to access Flickr accounts and data therein. It  uses the [Flickr API](http://www.flickr.com/services/api/) which is a powerful way to interact with Flickr accounts. With the API, you can read almost all the data associated with pictures and sets. You can also upload pictures through the API and change/add picture information.

## Getting an API key

Some APIs require developers to request a key to use the API. Getting an API Key from Flickr is straightforward and can be done at the [https://www.flickr.com/services/apps/create/apply/]("Flickr App Garden"). If you don't have one, you will need to create a Yahoo account. If you didn't create an account before, you will be provided with a temporary Flickr API key in class. However, it should not be used outside of the class; you should create your own at the cost of making too many requests with the provided account and reaching the limit of requests for the _difcul_ account.

## Looking through the API

The next step in using an API is looking through the [API Documentation](https://www.flickr.com/services/api/) to understand which methods and parameters are available. By looking at the [Flickr API Documentation](https://www.flickr.com/services/api/), we can already understand how powerful it is and the variety of possibilities available. Click the method _flickr.photos.search_ and inspect the number of parameters available to customize a call to this method. Also, look at a possible response, in XML. 

## Understanding a service call and response

Now let's try to use the API ourselves. A call to a web service is an HTTP request to that service available in the remove Web server. As such, before doing it programatically, we can test an API by using the web browser to perform the HTTP request. 

As an example, let's use the Flickr API to search for photos about Cristiano Ronaldo. To do so, we will use the method _flickr.photos.search_, with the search string (_text_) as being "Cristiano Ronaldo". Without forgetting the ```api_key```, always required, we compose the following URL:

```
https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=fd3fabe4e9d94ca725df1de71b8d285b&text=cristiano+ronaldo
```

The order of the arguments is not relevant; arguments are separated by _&_; whitespaces are replaced by _+_; _=_ is used to give a value to each argument.

The response provided is as follows:
```
<rsp stat="ok">
    <photos page="1" pages="255" perpage="100" total="25442">
        <photo id="24744703083" owner="131210812@N08" secret="fc8a0bb173" server="1474" farm="2" title="colorful CR7😍😍😍 #cr7 #cristiano #ronaldo #realmadrid #Portugal #instapic #instagood" ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="25072741560" owner="134461758@N07" secret="8399731ca5" server="1689" farm="2" title="Ritual-ritual Unik CR7 Sebelum Bertanding" ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="25364658605" owner="131210812@N08" secret="1908120432" server="1630" farm="2" title="Throwback to when I met Cristiano Ronaldo at Bernabeu Stadium." ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="24731677023" owner="131210812@N08" secret="22e58796e2" server="1682" farm="2" title="Remembering that one time I got to go to a Real Madrid game and see Cristiano Ronaldo! #spain #madrid #realmadrid #cristianoronaldo #ronaldo #solucky #blessed #hestiny #bestseatsinthehouse #bale" ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="25237351112" owner="136415076@N05" secret="f42906bb42" server="1475" farm="2" title="Cristiano Ronaldo Akui Barca Lebih Baik Di Musim Ini Dari Pada Real Madrid" ispublic="1" isfriend="0" isfamily="0"/>
....
```

The response format can be changed by using the _format_ argument, as the following examples shows:

```
https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=fd3fabe4e9d94ca725df1de71b8d285b&text=cristiano+ronaldo&format=json
```

In the response, we receive a set of photos, identified by the photo _id_, and two other numbers, _farm-id_ and _secret_. These numbers enable us to access the image associated with each photo in the set. Flickr stores several versions, different sizes, of each photo, and all of them have a static URL. This URL is composed as follows:

```
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}_[mstzb].jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{o-secret}_o.(jpg|gif|png)
```

The values displayed inside brackets are available from the photo search response above. _mstzb_ are the options relative to the size of the desired photo. For example, for a medium sized photo, one would use the letter _m_. As an example, looking at the example response above, for the "Cristiano Ronaldo" query, if we want to access the first image, we would use the URL:

```
https://farm2.staticflickr.com/1474/24744703083_fc8a0bb173_m.jpg
```

For more information about image URLs, please refer to https://www.flickr.com/services/api/misc.urls.html.

## Using the Flickr API programatically

Now that we already understand how to perform a call to a method in the API and the response, let's do the same programatically. The first step is to build the URL to call:

```
<?php
# build the API URL to call
$params = array(
        'api_key'=> 'YOUR_API_KEY',
        'method'=> 'flickr.photos.search',
        'text'=> 'Cristiano+Ronaldo',
        'format'=> 'php_serial',
        );

$encoded_params = array();
foreach ($params as $k => $v){
  $encoded_params[] = urlencode($k).'='.urlencode($v);
}
$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
```

The variable ```$url``` is now ready to be called. If you want to verify the contents of your variable you can use ```echo $url;``` or ```print_r($url);```.

To call the API, we will use the method ```file_get_contents``` which reads the content of a file into a string. In this case, it will _read_ the contents of the URL into a response string. This response is formatted according to the request, in this case ```php_serial```, which means that the response comes serialized. If, for example, the response was requested in _json_, we could use the function ```json_decode``` to decode the response to a PHP array. The following code calls the API and decodes the serialized response into a response array:

```
# call the API and decode the response
$rsp = file_get_contents($url);
$rsp_obj = unserialize($rsp);
```

The response, in XML, would be something similar to:

```
<rsp stat="ok">
    <photos page="1" pages="255" perpage="100" total="25442">
        <photo id="24744703083" owner="131210812@N08" secret="fc8a0bb173" server="1474" farm="2" title="colorful CR7😍😍😍 #cr7 #cristiano #ronaldo #realmadrid #Portugal #instapic #instagood" ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="25072741560" owner="134461758@N07" secret="8399731ca5" server="1689" farm="2" title="Ritual-ritual Unik CR7 Sebelum Bertanding" ispublic="1" isfriend="0" isfamily="0"/>
        <photo id="25364658605" owner="131210812@N08" secret="1908120432" server="1630" farm="2" title="Throwback to when I met Cristiano Ronaldo at Bernabeu Stadium." ispublic="1" isfriend="0" isfamily="0"/>
        ...
    </photos>
</rsp>
```

Given that the response was unserialized, in this case from ```php_serial``` to an array, we can now test our response and navigate through the response hierarchy. In the following example, we process each photo and echo a thumbnail and link to the full image to the client:

```
# display the photo thumbnails with links to the images
if ($rsp_obj['stat'] == 'ok'){
    $photos = $rsp_obj["photos"]["photo"];

    foreach($photos as $photo) {

        $farm              = $photo['farm'];
        $server            = $photo['server'];
        $photo_id          = $photo['id'];
        $secret            = $photo['secret'];
        $photo_title       = $photo['title'];

        $partial_img_name = 'http://farm'.$photo['farm'].'.static.flickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'];

        echo '<a href="'.$partial_img_name.'_b.jpg"><img src="'.$partial_img_name.'_t.jpg" alt="'.$photo['title'].'" /></a>
        ';
     }

} else {
        echo "Error getting photos";
   }
?>
```

Check the result and try different arguments and response formats. Try to use the method _flickr.photos.getRecent_. With this example and the API documentation you are now able to easily use the Flickr services. 

## The Google Places Example

Among several other services, Google makes available the Places API which enables us to get information, and also photos, about places near a location or related to a certain search string. 

As with Flickr, to use the Places API you will have to get an API key. This is done in the [Google Places API webpage](https://developers.google.com/places/web-service/get-api-key).

By inspecting the API documentation in the same website, we can identify how to search Google Places for a search string or a type of place in particular. As an example, to search using just a query string, we could call the following URL:

```https://maps.googleapis.com/maps/api/place/textsearch/xml?query=monuments+in+Portugal&key=YOUR_API_KEY
```

The following code prints a list of "monuments in Portugal" as returned by the Google Places API:

```
<?php
# build the API URL to call
$params = array(
        'key'=> 'AIzaSyBJu4F8tX7Aur2PetH6xAqLQ_mnka8BV7w',
        'query'=> 'monuments+in+Portugal',
        );

$encoded_params = array();
foreach ($params as $k => $v){
  $encoded_params[] = urlencode($k).'='.urlencode($v);
}

$url = "https://maps.googleapis.com/maps/api/place/textsearch/json?".implode('&', $encoded_params);

# call the API and decode the response
$rsp = file_get_contents($url);
$rsp_obj = json_decode($rsp, true);
$results = $rsp_obj["results"];

echo '<ul>
';

foreach($results as $result) {
    echo '<li>'.$result['name'].'</li>';
 }
echo '</ul>
'
?>
```

## Challenge

Now that you are able to search for places and to retrieve photos from those places, develop a PHP script that is able to query Google Places about "Gardens" in Lisbon and present pictures about those Gardens, as retrieved from Flickr.


