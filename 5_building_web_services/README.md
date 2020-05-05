# Building Web Services
Francisco Couto and Tiago Guerreiro

In RESTful Web Services URIs are used to access the resources, and the HTTP request method is used to define the action on those resources.
Examples:
- Google Drive: https://developers.google.com/drive/v2/reference/
- Google Calendar: https://developers.google.com/google-apps/calendar/v3/reference/

## URI Mapping

The first step we will take is enabling our list of neat URIs. To do that, we will resort to the webserver rewrite capabilities,
to make available the following URLs:

```txt
http://appserver.alunos.di.fc.ul.pt/awXXX/rest/article/     => gets the list of all articles 
http://appserver.alunos.di.fc.ul.pt/awXXX/rest/article/30826551/    => gets data about a particular article given its Id
```

We will now map the requested URL to a PHP file where we can parse and follow up with the request.

Create a _rest_ folder (inside _public_html_), and create and edit the _.htaccess_ file (https://httpd.apache.org/docs/2.4/howto/htaccess.html):

***.htaccess***
```txt
# Turn rewrite engine on
Options +FollowSymlinks
RewriteEngine on

# map neat URL to internal URL
RewriteRule ^article/$   http://appserver.alunos.di.fc.ul.pt/~awXXX/rest/RestController.php?view=all [nc,qsa,p]
RewriteRule ^article/([0-9]+)/$  http://appserver.alunos.di.fc.ul.pt/~awXXX/rest/RestController.php?view=single&id=$1 [nc,qsa,p]
```
Do not forget to replace the XXX, or change the URL to the correct one.  
About the RewriteRule Flags used (https://httpd.apache.org/docs/trunk/rewrite/flags.html):
- nc: case-insensitive
- qsa: combines query strings
- p: handled via a proxy request (the URL does not change in the browser)


In a localhost configuration ensure that you have the option ```AllowOverride All``` in the _httpd.conf_ file,
for example by typing:

```shellscript
find /etc/ -name httpd.conf | grep -A 20 -B 20 'AllowOverride All'
```

Note that the _httpd.conf_ can only be changed with superuser privileges (https://httpd.apache.org/docs/2.4/configuring.html).

If you try the URLs now, you will get an error as the file you are redirecting to does not exist.

## Controller

Let's create the file RestController.php that will unpack and dispatch the requests internally. 
In the folder _.htaccess_, we are forwarding all the requests to the file _RestController.php_ with a key named "view" 
to identify the request. 
Create the file _RestController.php_ to identify the request and dispatch the request to methods that will handle it:

***RestController.php***
```php
<?php
require_once("ArticleRestHandler.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];

switch($view){

	case "all":
		// to handle REST Url /article/
		$articleRestHandler = new ArticleRestHandler();
		$articleRestHandler->getAllArticles();
		break;
		
	case "single":
		// to handle REST Url /article/<id>/
		$articleRestHandler = new ArticleRestHandler();
		$articleRestHandler->getArticle($_GET["id"]);
		break;

	case "" :
		//404 - not found;
		break;
}
?>
```

## RESTful Web Service Handler

Create the file _SimpleRest.php_  with a base class that can be used in all RESTful service handlers. 
It has two methods: one that is used to construct the response, and a second one that is built to hold the different HTTP status code and its messages.

***SimpleRest.php***
```php
<?php 
/*
A simple RESTful webservices base class
Use this as a template and build upon it
*/
class SimpleRest {
	
	private $httpVersion = "HTTP/1.1";

	public function setHttpHeaders($contentType, $statusCode){
		
		$statusMessage = $this -> getHttpStatusMessage($statusCode);
		
		header($this->httpVersion. " ". $statusCode ." ". $statusMessage);		
		header("Content-Type:". $contentType);
	}
	
	public function getHttpStatusMessage($statusCode){
		$httpStatus = array(
			100 => 'Continue',  
			101 => 'Switching Protocols',  
			200 => 'OK',
			201 => 'Created',  
			202 => 'Accepted',  
			203 => 'Non-Authoritative Information',  
			204 => 'No Content',  
			205 => 'Reset Content',  
			206 => 'Partial Content',  
			300 => 'Multiple Choices',  
			301 => 'Moved Permanently',  
			302 => 'Found',  
			303 => 'See Other',  
			304 => 'Not Modified',  
			305 => 'Use Proxy',  
			306 => '(Unused)',  
			307 => 'Temporary Redirect',  
			400 => 'Bad Request',  
			401 => 'Unauthorized',  
			402 => 'Payment Required',  
			403 => 'Forbidden',  
			404 => 'Not Found',  
			405 => 'Method Not Allowed',  
			406 => 'Not Acceptable',  
			407 => 'Proxy Authentication Required',  
			408 => 'Request Timeout',  
			409 => 'Conflict',  
			410 => 'Gone',  
			411 => 'Length Required',  
			412 => 'Precondition Failed',  
			413 => 'Request Entity Too Large',  
			414 => 'Request-URI Too Long',  
			415 => 'Unsupported Media Type',  
			416 => 'Requested Range Not Satisfiable',  
			417 => 'Expectation Failed',  
			500 => 'Internal Server Error',  
			501 => 'Not Implemented',  
			502 => 'Bad Gateway',  
			503 => 'Service Unavailable',  
			504 => 'Gateway Timeout',  
			505 => 'HTTP Version Not Supported');
		return ($httpStatus[$statusCode] ? $httpStatus[$statusCode] : $httpStatus[500]);
	}
}
?>
```

Now you need to create a handler that extends SimpleRest in a file ArticleRestHandler.php_.
The first addition relates to how it handles the response format. 
This is decided based on the Request Header parameter "Accept". 
The values that can be used in the request are like "application/json" or "application/xml" or "text/html". 
The second relevant aspect to consider is the usage of status codes. 
For success, status code 200 should be set in response and sent. 
Similarly, other status codes can and should be used according to the situation (ex: resource not available).

***ArticleRestHandler.php***
```php
<?php
require_once("SimpleRest.php");
require_once("Article.php");
		
class ArticleRestHandler extends SimpleRest {

	function getAllArticles() {	

		$article = new Article();
		$rawData = $article->getAllArticle();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No articles found!');		
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}
	
	public function encodeHtml($responseData) {
	
		$htmlResponse = "<table border='1'>";
		foreach($responseData as $key=>$value) {
		        if (!empty($value)) {
    			   $htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
			}
		}
		$htmlResponse .= "</table>";
		return "<html>".$htmlResponse."</html>";		
	}
	
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;		
	}
	
	public function encodeXml($responseData) {
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><article></article>');
		foreach($responseData as $key=>$value) {
			$xml->addChild($key, $value);
		}
		return $xml->asXML();
	}
	
	public function getArticle($id) {

		$article = new Article();
		$rawData = $article->getArticle($id);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No articles found!');		
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($rawData);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($rawData);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($rawData);
			echo $response;
		}
	}
}
?>
```

## Domain: where the data is

You should notice that there is still a file missing. 
That is your domain class: _Article.php_. 
Here would be where you would access your data, be it a variable, a file, or a SQL database. 
Copy the Asthma.txt and AsthmaTitles.txt from previous modules and create the class with a simple read file:

***Article.php*** 
```php
<?php
/* 
A domain Class to demonstrate RESTful web services
*/
Class Article {

      	private $ids;
	private $titles;

	private $disease="Asthma";
	
      	private function readFiles(){

	        $filename = $this->disease.".txt";
      		$handle = fopen($filename, "r");
      		$contents = fread($handle, filesize($filename));
      		$this->ids = explode("\n",$contents);
      		fclose($handle);

		$filename = $this->disease."Titles.txt";
      		$handle = fopen($filename, "r");
      		$contents = fread($handle, filesize($filename));
      		$t = explode("\n",$contents);
      		fclose($handle);
	
		$this->titles=array_combine($this->ids,$t);
	}
      
		
	public function getAllArticle(){
		$this->readFiles();
		return $this->ids;
	}
	
	public function getArticle($id){
		$this->readFiles();
		return array($id => $this->titles[$id]);
	}	
}
?>
```

## Testing the services

You can now use the browser to access your URIs and check the results:
- http://appserver.alunos.di.fc.ul.pt/~awXXX/rest/article/
- http://appserver.alunos.di.fc.ul.pt/~awXXX/rest/article/30826551/

You can see that, given that we did not set an alternative response format, it is provided in HTML. 
To test the web service more extensively you can either build a client programatically and consume the service, or use a standalone general-purpose REST client. 

For example, you can use _curl_:

```shell
curl -X GET -H "Accept: text/html" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/"
curl -X GET -H "Accept: application/json" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/"
curl -X GET -H "Accept: application/xml" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/"

curl -X GET -H "Accept: text/html" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/30826551/"
curl -X GET -H "Accept: application/json" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/30826551/"
curl -X GET -H "Accept: application/xml" -L "http://appserver.alunos.di.fc.ul.pt/~aw000/aw/5_building_web_services/rest/article/30826551/"
```

To test with your own web service just remove ```aw/5_building_web_services``` and replace ```aw000``` by your group number.

You can also use the Google Chrome extension "Advanced REST client" (https://advancedrestclient.com/) to test your requests and Accept headers.

## Modify data  

You need to add to the file RestController.php the code to manage others HTTP request methods.
For example, to deal with POST requests you can add:

***RestController.php***
```php
<?php
require_once("ArticleRestHandler.php");

//identify the request method.
$requestType = $_SERVER['REQUEST_METHOD'];
 
switch ($requestType) {

      case 'POST':
      	 $articleRestHandler = new ArticleRestHandler();
	 $articleRestHandler->insertArticle($_POST["id"],$_POST["title"]);
     	 break;

      case 'GET':
    	 $view = "";
	 if(isset($_GET["view"]))
		$view = $_GET["view"];
	 ...
      	 break;

      case "" :
      	 //404 - not found;
	 break;
}
?>
```

And then implement the insertArticle function in the Article class that modifies your files or database. (Part of the additional exercise)

More about the HTTP methods definitions: https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html

## Additional Exercise for Evaluation - Submit by May 18th

- Add the following capabilities to your RESTful web service (accounts for 3 points out of 4):

**Feedback**: GET the rating of a particular article; and update (POST) the rating (+1 or -1) of a particular article.
Notice that all article ratings should start with 0.

**Search**: When searching for articles, return the list of articles ordered by ranking.

- Improve your RESTful web service to support the following:

**Support multiple diseases** (0.8 points): Instead of supporting only Asthma, edit your RESTful web service so that it can support the other diseases of the first script (Alzheimer, Asthma, Cirrhosis, Diabetes, and Tuberculosis).

Notice that the URIs in the script do not specify the disease, but to support multiple diseases it should (instead, it is hardcoded in Article.php).
Also, you should copy (from prior scripts) the files related to links and titles of those diseases (e.g., DiabetesLinks.txt, DiabetesTitles.txt).

**Inserting an Article** (0.2 points): Complete the script by supporting the ability to add an article, including implementing the insertArticle function in the Article class. 

- **HINTS**:

You can have one file with the ratings (e.g., AsthmaRatings.txt) and edit the ratings using sed (for instance by specifying the line number). Alternatively, you may have one file for each article, by combining the disease and articleID.

Consider checking tutorials from w3schools if you are struggling with PHP (https://www.w3schools.in/category/php/) and REST (https://www.w3schools.in/restful-web-services/intro/).

- All students should submit their files by Monday, **May 18th**. Submit your relevant files on Moodle - a ZIP file AW-5-XXXXX.ZIP, where 5 means the fifth script, and XXXXX is to be replaced by your student number (for example, AW-5-12345.ZIP).

In your zip file, include a Text File with:
The curl commands that can be used to test your GET and POST requests. 
All files created when following the script should also be included in the submission (e.g., .php, .txt or .sh files).


## Additional References

- https://books.google.pt/books?id=jklKNnLO104C&printsec=frontcover

- http://phppot.com/php/php-restful-web-service/

- https://spring.io/guides/gs/rest-service/





