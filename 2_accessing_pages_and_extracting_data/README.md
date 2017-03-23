# Accessing webpages and extracting contents
Tiago Guerreiro and Francisco Couto

In the previous tutorial, we used available APIs to access images from Flickr and information from Google Places. There are several other APIs available that allow us to get a structured response to a prepared request. However, not all webpages and contents therein are accessible through web services. In those cases, we can still get access to the webpage content and process. 

In this tutorial, we first look at XPath (XML Path Language) as a tool to enable us to get information from XML and HTML documents. Then, we introduce cURL (client URL), a library that enables us to access URLs as we would do by using a web browser.  

# Introduction to XPath

XPath is a query language for selecting nodes from a XML document. However, it can be used to query any markup document. Let's start by looking at the simple XML below (adapted from [W3Schools](http://www.w3schools.com/xml/simple.xml):

```
<breakfast_menu>
    <food>
        <name>Belgian Waffles</name>
        <price>$5.95</price>
        <description>
        Two of our famous Belgian Waffles with plenty of real maple syrup
        </description>
        <calories>650</calories>
    </food>
    <food>
        <name>Strawberry Belgian Waffles</name>
        <price>$7.95</price>
        <description>
        Light Belgian waffles covered with strawberries and whipped cream
        </description>
        <calories>900</calories>
    </food>
    <food>
        <name>Berry-Berry Belgian Waffles</name>
        <price>$8.95</price>
        <description>
        Light Belgian waffles covered with an assortment of fresh berries and whipped cream
        </description>
        <calories>900</calories>
    </food>
    <food>
        <name type="different">French Toast</name>
        <price>$4.50</price>
        <description>
        Thick slices made from our homemade sourdough bread
        </description>
        <calories>600</calories>
    </food>
    <food>
        <name>Homestyle Breakfast</name>
        <price>$6.95</price>
        <description>
        Two eggs, bacon or sausage, toast, and our ever-popular hash browns
        </description>
        <calories>950</calories>
    </food>
</breakfast_menu>
```

XPAth enables us to query that document by using the structure of the document. To be able to get all food names we can use the query:

```
/breakfast_menu/food/name
```

The XPath syntax enables more complex queries:

- ```/breakfast_menu/food/name[1]``` - first element of type _name_, that is a child of _food_, that is a child of _breakfastmenu_;
- ```name``` - root elements of type _name_;
- ```//name``` - elements of type _name_ that are descendants of something;
- ```/breakfast_menu//name``` - elements of type _name_ that are descendants of _breakfastmenu_; 
- ```/breakfast_menu/food/*``` - any elements that is a child of _food_, that is then a child of _breaskfastmenu_;
- - ```/breakfast_menu/food/name/@type``` - all _type_ attributes of tags _name_ that are a child of food,...
- ```/breakfast_menu/food/name[@type='different']``` - element of type _name_, that has an attribute _type_ with the value 'different', that is a child of food...
- Check W3C for more [XPath Syntax](http://www.w3schools.com/xsl/xpath_syntax.asp)

By applying the same concepts to HTML documents rather than XML, one can use, for example:

```
//a/@href
```

to retrieve all href links from one webpage.

## Parsing XML RSS Feeds with XPath

Let's take a look at the RSS feed of the [Soccer News](http://feeds.feedburner.com/soccernewsfeed?format=xml) webpage:

```
<channel>
    <title>SoccerNews.com - The Latest Soccer &amp; Transfer News </title>
    
    <link>http://www.soccernews.com</link>
    <description>The Latest Soccer News from around the globe.</description>
    <lastBuildDate>Mon, 14 Mar 2016 15:07:06 +0000</lastBuildDate>
    <language>en-US</language>
    <sy:updatePeriod>hourly</sy:updatePeriod>
    <sy:updateFrequency>1</sy:updateFrequency>
    
    <atom10:link xmlns:atom10="http://www.w3.org/2005/Atom" rel="self" type="application/rss+xml" href="http://feeds.feedburner.com/soccernewsfeed" /><feedburner:info xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0" uri="soccernewsfeed" /><atom10:link xmlns:atom10="http://www.w3.org/2005/Atom" rel="hub" href="http://pubsubhubbub.appspot.com/" /><xhtml:meta xmlns:xhtml="http://www.w3.org/1999/xhtml" name="robots" content="noindex" /><feedburner:emailServiceId xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">soccernewsfeed</feedburner:emailServiceId><feedburner:feedburnerHostname xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">https://feedburner.google.com</feedburner:feedburnerHostname>
    <item>
        <title>Lukaku linked with Chelsea return</title>
        <link>http://www.soccernews.com/lukaku-linked-with-chelsea-return/195872/</link>
        <comments>http://www.soccernews.com/lukaku-linked-with-chelsea-return/195872/#comments</comments>
        <pubDate>Mon, 14 Mar 2016 15:07:06 +0000</pubDate>
        <dc:creator><![CDATA[Deke Hardman]]></dc:creator>
                <category><![CDATA[Editorial]]></category>
        <category><![CDATA[FA Cup]]></category>
        <category><![CDATA[Chelsea]]></category>
        <category><![CDATA[Diego Costa]]></category>
        <category><![CDATA[Everton]]></category>
        <category><![CDATA[Romelu Lukaku]]></category>
        <category><![CDATA[West Bromwich Albion]]></category>

        <guid isPermaLink="false">http://www.soccernews.com/?p=195872</guid>
        <description><![CDATA[The Metro understands the Chelsea could attempt to resign Romelu Lukaku from Everton. The Belgium international scored twice as the [&#8230;]]]></description>
                <content:encoded><![CDATA[The Metro understands the Chelsea could attempt to resign Romelu Lukaku from Everton. The Belgium international scored twice as the [&#8230;]]]></content:encoded>
            <wfw:commentRss>http://www.soccernews.com/lukaku-linked-with-chelsea-return/195872/feed/</wfw:commentRss>
        <slash:comments>0</slash:comments>
        </item>
        <item>
        <title>Neuer shuns Premier League switch</title>
        <link>http://www.soccernews.com/neuer-shuns-premier-league-switch/195869/</link>
        <comments>http://www.soccernews.com/neuer-shuns-premier-league-switch/195869/#comments</comments>
        <pubDate>Mon, 14 Mar 2016 14:41:50 +0000</pubDate>
        <dc:creator><![CDATA[Deke Hardman]]></dc:creator>
                <category><![CDATA[Bundesliga]]></category>
        <category><![CDATA[English Premier League]]></category>
        <category><![CDATA[Football Transfer News & Transfer Rumours]]></category>
        <category><![CDATA[Bayern Munich]]></category>
        <category><![CDATA[Joe Hart]]></category>
        <category><![CDATA[Manchester City]]></category>
        <category><![CDATA[Manuel Neuer]]></category>
        ...
```

Imagine we want to retrieve all the news titles from that feed. One possible query path would be to simply use _//title_. The following example shows how to get and apply a XPath query to a RSS feed from Soccer News using PHP:

```
<?php

$url = 'http://feeds.feedburner.com/soccernewsfeed?format=xml';
$xml = simplexml_load_file($url);

$news = $xml->xpath('//title');

echo '<ul>';

foreach ($news as $title){
    echo '<li>'.$title.'</li>'; 
}

echo '</ul>';
?>
```

Try it with other feeds and queries. 

## Tools

Current browsers and their developer tools make it easier for developers to identify paths to use in their queries. For example, when using Google Chrome, you can right-click any webpage element, click "Inspect element" and in the source code box, once again, right click the line with the content you wish and select "Copy->XPath". While you can retrieve a path to use in your queries, this is normally too specific and you can, manually, find a simpler path.

There are tools available to help you finding your paths and checking them. Examples are XPath Checker, a Firefox plugin, and Xpath Helper, a Google Chrome one.

# Introduction to cURL 

cURL is a library that allows transfer of data across a variety of protocols. cURL is commonly used to perform HTTP requests. In a previous tutorial, you have used file_get_contents() to retrieve data from the Flickr and Google Places API. In that sense, you performed GET requests. With cURL, besides those simple requests, you can perform complex FTP uploads/downloads, interaction with an authentication enclosed HTTPS site, and obviously, perform POST requests.  

## Performing requests with cURL

One simple cURL request, a simple GET, can be done as follows:

```
<?php
//
// A very simple example that gets a HTTP page.
//

$ch = curl_init();

curl_setopt ($ch, CURLOPT_URL, "https://ciencias.ulisboa.pt/");
curl_setopt ($ch, CURLOPT_HEADER, 0);

curl_exec ($ch);

curl_close ($ch);
?>
```

The above code initializes a cURL resource (_curl\_init_) and then assigns some settings to it. Some of the core settings we can work with are:
- CURLOPT_RETURNTRANSFER - return the response as a string instead of outputting it to the screen. In the above example we did not set this to _true_ and therefore the contents are being presented onscreen;
- CURLOPT_CONNECTTIMEOUT - number of seconds to spend attempting to connect;
- CURLOPT_TIMEOUT - Number of seconds to allow cURL to execute;
- CURLOPT_USERAGENT - URL to send request to;
- CURLOPT_POST - Send request as a POST;
- CURLOPT_POSTFIELDS - Array of data to POST in the request.

All settings can be set by using the _curl\_setopt()_ method, which takes three parameters: the cURL resource, the setting and the value. It is possible to set multiple settings at once by using _curl\_setopt\_array_:

```
$ch = curl_init();
curl_setopt_array ($ch, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://ciencias.ulisboa.pt/''
    ));
```

Performing a POST request to a webpage (https://httpbin.org/), for example to automatically fill-up a form can be done:

```
<?php

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://httpbin.org/post");
curl_setopt($ch, CURLOPT_POST, 3);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "foo='blabla'&bar='boeboe'&description='something'");

curl_exec ($ch);
curl_close ($ch); 
?>
```

## Scraping a webpage with cURL and XPath

Now let's look at the [Google Scholar author page](http://scholar.google.ca/citations?user=mo6UPuIAAAAJ) of a renowned :-) author, that only by coincidence, is an author of this tutorial. Imagine you would like to collect all article titles using cURL and XPath. The code to do so is below and the comments highlight what is being done in each line/block:

```
<?php
//The page URL
$url = 'http://scholar.google.ca/citations?user=mo6UPuIAAAAJ';

//cURL init, settings and execution
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($curl);
curl_close($curl);

//Creating a DOMDocument
$doc = new DOMDocument();

//Avoid the presentation of warnings due to ill-created HTML results
libxml_use_internal_errors(true);
$doc->loadHTML($html);
libxml_clear_errors();

//An alternative way to apply XPath to a result, in this case filtering a DOM tree
$xpath = new DOMXpath($doc);

//To get the path, we inspected the source code of the page
$news = $xpath->query('//a[@class="gsc_a_at"]'); 

echo '<ul>';

// The value of the DOMElement is echoed.
foreach ($news as $title){
    echo '<li>'.$title->nodeValue.'</li>'; 
}

echo '</ul>'; 

?>
```

# Challenge

Explore cURL and XPath with different webpages (ex: using different user agents and methods).

