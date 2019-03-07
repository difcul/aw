# Using Web Services
Francisco Couto and Tiago Guerreiro

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

We will use now the EFetch method (https://www.ncbi.nlm.nih.gov/books/NBK25499/#chapter4.EFetch) to get the titles of the articles. 

For example, check the XML output of the following call to get the data about the PubMed Id 29462659:

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29462659&retmode=text&rettype=xml" 
```

To get only the title and use the _grep_ tool, and _sed_ to remove the XML tags and trim the whitespaces:

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=29462659&retmode=text&rettype=xml" | grep "<ArticleTitle>" | sed -e "s/<[^>]*>//g" -e "s/^ *//" -e "s/ *$//"
```

Note that these commands are even available in a live Linux: _https://youtu.be/QvW2GOi2Nrg_

Using a text editor create a file named _getPubMedTitles.sh_ and copy and paste the following command into it:

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=$1&retmode=text&rettype=xml" | grep "<ArticleTitle>" | sed -e "s/<[^>]*>//g" -e "s/^ *//" -e "s/ *$//"
```
Note that we replaced 29462659 by _$1_ so we can use any PubMed Id as input.

Now add permissions to execute the script, and execute it:

```shell
chmod u+x ./getPubMedTitles.sh
./getPubMedTitles.sh 29462659
```

The EFetch method allows to get data from multiple Ids by separating them by a comma, for example try:

```shell
./getPubMedTitles.sh "29462659,29461895" 
```

Create this input list by applying the _tr_ tool the PubMed Ids used in_Asthma.txt_ file created in a previous module (type ```man tr``` to know more about _tr_):  
```shell
tr '\n' ',' < Asthma.txt
```

Now execute the script using this list as input and saving the result to a file:
```shell
./getPubMedTitles.sh $(tr '\n' ',' < Asthma.txt)  > AsthmaTitles.txt
```

### Web Application with titles

Get the file _mywebapp.php_ created in a previous module, remove the _foreach_ block and add the following PHP code after the _explode_ command:
```php
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

Or the URL _http://localhost/.../mywebapp.php_ in case you are using a local machine.

Now try for different diseases, but do not forget to run the shell scripts before, for example by using ```xargs```.


## Flickr API

The [Flickr API](http://www.flickr.com/services/api/) is a powerful way to interact with Flickr accounts. 
With the API, you can read almost all the data associated with pictures and sets. You can also upload pictures through the API and change/add picture information.

### Getting an API key

Some APIs require developers to request a key to use the API.
Applying for a non-commercial API Key from Flickr is straightforward. 
Check how to do it at _https://www.flickr.com/services/api/misc.api_keys.html_
or watch this video: _https://youtu.be/WMoLk0P5_bk_

If you don't have one, you will need to create a Yahoo account.
If you didn't create an account before, you may request a temporary Flickr API key in class. However, it should not be used outside of the class; you should create your own at the cost of making too many requests with the provided account and reaching the limit of requests for the _difcul_ account.

### Photos Search

Check the method _flickr.photos.search_ (https://www.flickr.com/services/api/flickr.photos.search.html) and inspect the number of parameters available to customize a call to this method. 
Also, look at a possible response, in XML. 

To search for 10 public photos about Asthma test the following call using the search string (_text_) as being "Asthma". 
Do not forget to replace the ```YOUR_API_KEY```.

```shell
curl "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=YOUR_API_KEY&text=Asthma&per_page=10&privacy_filter=1"
```

The response provided should similar to:
```xml
<?xml version="1.0" encoding="utf-8" ?>
<rsp stat="ok">
<photos page="1" pages="1322" perpage="10" total="13218">
	<photo id="32283410077" owner="148095701@N04" secret="0f334b1c0a" server="7862" farm="8" title="North Park Urgent Care | Lakeview Walk in Clinic" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="46310890595" owner="143417296@N04" secret="69c7ca0405" server="7845" farm="8" title="Cupping Therapy - Using Cupping Therapy" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="40260232383" owner="145524237@N05" secret="fae1f35c19" server="7829" farm="8" title="NATUROPATHY Center" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="33349433718" owner="166825568@N03" secret="0bf6344ce0" server="7816" farm="8" title="5 Effective Yoga Poses Treatments For Oily Skin" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="40259487683" owner="148303290@N02" secret="cf98f2d207" server="7881" farm="8" title="Best Doctors in Mansarovar Jaipur  https://www.docconsult.in/jaipur/mansarovar/dental-surgeon-speciality https://www.docconsult.in/jaipur/mansarovar/asthma-allergy-specialist-speciality https://www.docconsult.in/jaipur/mansarovar/cardiologist-speciality h" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="47165352182" owner="159521341@N07" secret="8ffc96aa51" server="7825" farm="8" title="Seretide Inhaler Price USA" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="40251948203" owner="168604488@N03" secret="a415c2a9f5" server="7886" farm="8" title="First class Bond back cleaning" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="47215151641" owner="161918792@N05" secret="5af87f9885" server="7909" farm="8" title="Influenza" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="47161658612" owner="126031154@N08" secret="141f5c57e4" server="7872" farm="8" title="Rowan tree on Malvern hills" ispublic="1" isfriend="0" isfamily="0" />
	<photo id="32274580577" owner="166825568@N03" secret="0eb84273ec" server="7859" farm="8" title="yoga-for-diabetes" ispublic="1" isfriend="0" isfamily="0" />
</photos>
</rsp>
```

In the response, we receive a set of photos, identified by the photo _id_, and two other numbers, _farm-id_ and _secret_. These numbers enable us to access the image associated with each photo in the set. Flickr stores several versions, different sizes, of each photo, and all of them have a static URL. This URL is composed as follows:

```txt
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}.jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{secret}_[mstzb].jpg
    or
https://farm{farm-id}.staticflickr.com/{server-id}/{id}_{o-secret}_o.(jpg|gif|png)
```

The values displayed inside brackets are available from the photo search response above. _mstzb_ are the options relative to the size of the desired photo. For example, for a medium sized photo, one would use the letter _m_. As an example, looking at the example response above, if we want to access the first image, we would use the URL https://farm8.staticflickr.com/7862/32283410077_0f334b1c0a_m.jpg

For more information about image URLs, please refer to https://www.flickr.com/services/api/misc.urls.html.


### Get the Photos

To get only the links to the photos use the _grep_ tool, and _sed_ to extract the values:

```shell
curl "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=YOUR_API_KEY&text=Asthma&per_page=10&privacy_filter=1" | grep "photo id" | sed 's/^.*id="\([^"]*\).*secret="\([^"]*\).*server="\([^"]*\).*farm="\([^"]*\).*$/https:\/\/farm\4.staticflickr.com\/\3\/\1_\2.jpg/'
```

Using a text editor create a file named _getFlickrPhotos.sh_ and copy and paste the following command into it:

```shell
curl "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=$1&text=$2&per_page=10&privacy_filter=1" | grep "photo id" | sed 's/^.*id="\([^"]*\).*secret="\([^"]*\).*server="\([^"]*\).*farm="\([^"]*\).*$/https:\/\/farm\4.staticflickr.com\/\3\/\1_\2.jpg/'
```

Note that we replaced the api key by _$1_ and Asthma by _$2_ so you can use any key and disease as input.

Now add permissions to execute the script, and execute it and saving the result to a file:

```shell
chmod u+x ./getFlickrPhotos.sh
./getFlickrPhotos.sh YOUR_API_KEY Asthma > AsthmaPhotos.txt
```

Type ```cat AsthmaPhotos.txt``` to check the links stored.


### Web Application with photos

Add the following PHP code to the file _mywebapp.php_ : 

```php
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

Now try for different diseases, but do not forget to run the shell scripts before, for example by using ```xargs```.

## Additional References

- http://labs.rd.ciencias.ulisboa.pt/book/ (Chapter 3 - Data Retrieval)

- http://webpages.fc.ul.pt/~fjcouto/files/manual_soa_ajax_20120221.pdf 

- https://www.w3schools.com/xml/xml_services.asp

- https://developers.google.com/custom-search/json-api/v1/using_rest

- https://developer.twitter.com/en/docs