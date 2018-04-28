# Building Web Services
Francisco Couto and Tiago Guerreiro

In RESTful Web Services URIs are used to access the resources, and the HTTP request method is used to define the action on those resources.
Examples:HTTP request
- Google Drive: https://developers.google.com/drive/v2/reference/
- Google Calendar: https://developers.google.com/google-apps/calendar/v3/reference/

## URI Mapping

The first step we will take is enabling our list of neat URIs. To do that, we will resort to the webserver rewrite capabilities,
to make available the following URLs:

```txt
http://appserver.alunos.di.fc.ul.pt/awXXX/rest/article/     => gets the list of all articles 
http://appserver.alunos.di.fc.ul.pt/awXXX/rest/article/29461607/    => gets data about a particular article given its Id
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
In a localhost configuration ensure that you have the option ```AllowOverride All``` in the _httpd.conf_ file.

About the RewriteRule Flags used (https://httpd.apache.org/docs/trunk/rewrite/flags.html):
- nc: case-insensitive
- qsa: combines query strings
- p: handled via a proxy request (the URL does not change in the browser)

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
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /article/list/
		$articleRestHandler = new ArticleRestHandler();
		$articleRestHandler->getAllArticles();
		break;
		
	case "single":
		// to handle REST Url /article/show/<id>/
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
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $status[500];
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
- http://appserver.alunos.di.fc.ul.pt/~awXXX/rest/article/29461607/

You can see that, given that we did not set an alternative response format, it is provided in HTML. 
To test the web service more extensively you can either build a client programatically and consume the service, or use a standalone general-purpose REST client. 

For example, you can use _curl_:

```shell
curl -X GET -H "Accept: application/json" -L "http://appserver-01.alunos.di.fc.ul.pt/~awXXX/rest/article/"
curl -X GET -H "Accept: application/json" -L "http://appserver-01.alunos.di.fc.ul.pt/~awXXX/rest/article/29461607/"
```

You can also use the Google Chrome extension "Advanced REST client" (https://advancedrestclient.com/) to test your requests and Accept headers.

## Additional References

- http://phppot.com/php/php-restful-web-service/

- https://spring.io/guides/gs/rest-service/





