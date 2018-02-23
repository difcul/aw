# Using Web Services
Tiago Guerreiro and Francisco Couto

Web Services can be seen as APIs that allow for this interconnection between heterogeneous software applications in the web by using the HTTP protocol.

In this tutorial, we show examples of using the PubMed API and Flickr API.

## PubMed API

In the previous module you downloaded the list of PubMed Ids for given disease by using the URL https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=Asthma&retmax=10&retmode=xml
that invokes a NCBI's  web service (https://www.ncbi.nlm.nih.gov/home/develop/api/), more specifically the _Searching a Database_ method.
By looking at its documentation (https://www.ncbi.nlm.nih.gov/books/NBK25499/#chapter4.ESearch) you can better understand which methods and parameters are available. 

Note that the order of the parameters in the URL is not relevant; arguments are separated by _&_; whitespaces are replaced by _+_; _=_ is used to give a value to each argument.

A call to a web service is an HTTP request to that service available in the remote Web server. As such, we can test an API by using the web browser to perform the HTTP request,
the _curl_ tool, or any other programming language that lets you open URLs. 

### EFetch

We will use now the EFetch method (https://www.ncbi.nlm.nih.gov/books/NBK25499/#chapter4.EFetch) to get the titles of the titles. 

For example, check the XML output of the following call to get the data about the PubMed Id 29462659:

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29462659&retmode=text&rettype=xml" 
```

To get only the title and use the _grep_ tool, and _sed_ to remove the XML tags and trim the whitespaces:

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29462659&retmode=text&rettype=xml" | egrep "<ArticleTitle>" | sed -e "s/<[^>]*>//g" -e "s/^ *//" -e "s/ *$//'"
```

Create a file named _getPubMedTitles.sh_ with the previous command, but replace 29462659 by _$1_ so we can use any PubMed Id as input, i.e :
```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=$1&retmode=text&rettype=xml" | egrep "<ArticleTitle>" | sed -e "s/<[^>]*>//g" -e "s/^ *//" -e "s/ *$//"
```

Now add permissions to execute the script, and execute it:

```
chmod 755 ./getPubMedTitles.sh
./getPubMedTitles.sh 29462659
```

The EFetch method allows to get data from multiple Ids by separating them by a comma, for example try:

```
./getPubMedTitles.sh "29462659,29461895" 
```

Create this input list by applying the _tr_ tool the PubMed Ids used in_Asthma.txt_ file created in a previous module (type ```man tr``` to know more about _tr_):  
```
tr '\n' ',' < Asthma.txt
```

Now execute the script using this list as input and saving the result to a file:
```
./getPubMedTitles.sh $(tr '\n' ',' < Asthma.txt)  > AsthmaTitles.txt
```

### Web Application with titles

Get the file _mywebapp.php_ created in a previous module and add the following PHP code:
```
$filename = $_GET['disease']."Titles.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$titles = explode("\n",$contents);
fclose($handle);

$c=array_combine($links,$titles);

foreach ($c as $key => $value) {
  echo '<a href="' . $key . '">' . $value . '</a></br>'; 
}
```

Open the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_ (hit refresh) and check the results.

Now try for different diseases, but do not forget to run the shell scripts before.


## Flickr API

The [Flickr API](http://www.flickr.com/services/api/) is a powerful way to interact with Flickr accounts. 
With the API, you can read almost all the data associated with pictures and sets. You can also upload pictures through the API and change/add picture information.

### Getting an API key

Some APIs require developers to request a key to use the API. Getting an API Key from Flickr is straightforward and can be done at the ["Flickr App Garden"](https://www.flickr.com/services/apps/create/apply/). If you don't have one, you will need to create a Yahoo account. If you didn't create an account before, you will be provided with a temporary Flickr API key in class. However, it should not be used outside of the class; you should create your own at the cost of making too many requests with the provided account and reaching the limit of requests for the _difcul_ account.

### Photos Search

Check the method _flickr.photos.search_ (https://www.flickr.com/services/api/flickr.photos.search.html) and inspect the number of parameters available to customize a call to this method. 
Also, look at a possible response, in XML. 

To search for 10 public photos about Asthma test the following call using the search string (_text_) as being "Asthma". 
Do not forget to replace the ```api_key```, always required.

```
curl https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=YOUR_API_KEY&text=Asthma&per_page=10&privacy_filter=1
```

The response provided should similar to:
```
<rsp stat="ok">
<photos page="1" pages="217" perpage="100" total="21634">
<photo id="25529403237" owner="156441279@N02" secret="81693a20ef" server="4665" farm="5" title="Pediatric Allergy, Asthma and Immunology by Arnaldo Cantani" ispublic="1" isfriend="0" isfamily="0"/>
<photo id="26527339958" owner="14924442@N04" secret="a596f1d20b" server="4610" farm="5" title="Halo" ispublic="1" isfriend="0" isfamily="0"/>
<photo id="39488217555" owner="35486550@N00" secret="f806f5bfb6" server="4673" farm="5" title=""Allergies, Asthma & Urticaria" - Hosted by 'Hibiscus Health Caribbean Inc.'" ispublic="1" isfriend="0" isfamily="0"/>
<photo id="25505143007" owner="156074345@N03" secret="1d4c5b7122" server="4713" farm="5" title="The Impact of a Certified Air Cleaner on the Indoor Air Quality" ispublic="1" isfriend="0" isfamily="0"/>
<photo id="39664891004" owner="155471696@N03" secret="6d62b9521c" server="4603" farm="5" title="BreatheEZi_Asthma-attack" ispublic="1" isfriend="0" isfamily="0"/>
....
```

In the response, we receive a set of photos, identified by the photo _id_, and two other numbers, _farm-id_ and _secret_. These numbers enable us to access the image associated with each photo in the set. Flickr stores several versions, different sizes, of each photo, and all of them have a static URL. This URL is composed as follows:

```
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}_[mstzb].jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{o-secret}_o.(jpg|gif|png)
```

The values displayed inside brackets are available from the photo search response above. _mstzb_ are the options relative to the size of the desired photo. For example, for a medium sized photo, one would use the letter _m_. As an example, looking at the example response above, if we want to access the first image, we would use the URL:

```
https://farm2.staticflickr.com/1474/25529403237_81693a20ef_m.jpg
```

For more information about image URLs, please refer to https://www.flickr.com/services/api/misc.urls.html.



### Get the Photos

To get only the links to the photos use the _grep_ tool, and _sed_ to extract the values:

```
curl "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=YOUR_API_KEY&text=Asthma&per_page=10&privacy_filter=1" | grep "photo id" | sed 's/^.*id="\([^"]*\).*secret="\([^"]*\).*server="\([^"]*\).*farm="\([^"]*\).*$/https:\/\/farm\4.staticflickr.com\/\3\/\1_\2.jpg/'
```


Create a file named _getFlickrPhotos.sh_ with the previous command, but replace the api key by _$1_ and Asthma by _$2_ so you can use any key and disease as input, i.e :
```
curl "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=$1&text=$2&per_page=10&privacy_filter=1" | grep "photo id" | sed 's/^.*id="\([^"]*\).*secret="\([^"]*\).*server="\([^"]*\).*farm="\([^"]*\).*$/https:\/\/farm\4.staticflickr.com\/\3\/\1_\2.jpg/'
```
Now add permissions to execute the script, and execute it and saving the result to a file:

```
chmod 755 ./getFlickrPhotos.sh
./getFlickrPhotos.sh YOUR_API_KEY Asthma > AsthmaPhotos.txt
```

Type ```cat AsthmaPhotos.txt``` to check the links stored.


### Web Application with photos

Add the following PHP code to the file _mywebapp.php_ : 

```
$filename = $_GET['disease']."Photos.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$photos = explode("\n",$contents);
fclose($handle);

foreach ($photos as $p) {
  echo '<a href="'. $p .'"><img src="'. $p .'" /></a></br>';
}
```

Open the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_ (hit refresh) and check the results.

Now try for different diseases, but do not forget to run the shell scripts before.

## Additional References

- http://webpages.fc.ul.pt/~fjcouto/files/manual_soa_ajax_20120221.pdf 

- https://www.w3schools.com/xml/xml_services.asp

- https://developers.google.com/custom-search/json-api/v1/using_rest

- https://developer.twitter.com/en/docs