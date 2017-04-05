# Building a RESTful Web Service
Tiago Guerreiro and Francisco Couto
(tutorial adapted from http://phppot.com/php/php-restful-web-service/)

In this tutorial, we will learn how to build web services that follow the RESTful principles. In such services, URIS are used to access the resources, either they are data or functions. Examples of RESTful URIs are:

```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/1/
```

**IMPORTANT: replace aw030 by your group number**

In these examples, a simple, self-explainable URI is available to be used and is later internally translated to a request to a real file, function, with its also translated arguments. In this tutorial, we will learn how to do that without the need for any framework (although they can be used to make the development more agile, particularly in bigger projects).

Create a new folder for your test project called "teste" in your appserver-01 account (inside the _public_html_ folder).

## URI Mapping

The first step we will take is enabling our list of neat URIs. To do that, we will resort to the webserver rewrite capabilities. Let's consider we want to have the two aformentioned URIs:

```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/     => gets the list of all mobile devices 
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/1/    => gets a particular mobile given its ID
```

In the _teste_ folder create and edit the _.htaccess_ file. We will now map the requested URL to a PHP file where we can parse and follow up with the request:

**.htaccess**
```
# Turn rewrite engine on
Options +FollowSymlinks
RewriteEngine on

# map neat URL to internal URL
RewriteRule ^mobile/list/$   http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/RestController.php?view=all [nc,qsa]
RewriteRule ^mobile/list/([0-9]+)/$   http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/RestController.php?view=single&id=$1 [nc,qsa]
```

This will work in appserver, in a localhost configuration remove or change the prefix of each url. 

If you try the URLs now, you will get an error as the file you are redirecting to does not exist.

## Controller

Let's create the file RestController.php that will unpack and dispatch the requests internally. In the folder _.htaccess_, we are forwarding all the requests to the file _RestController.php_ with a key named "view" to identify the request. Let's identify the request and dispatch the request to methods that will handle it:

**RestController.php**
```
<?php
require_once("MobileRestHandler.php");
		
$view = "";
if(isset($_GET["view"]))
	$view = $_GET["view"];
/*
controls the RESTful services
URL mapping
*/
switch($view){

	case "all":
		// to handle REST Url /mobile/list/
		$mobileRestHandler = new MobileRestHandler();
		$mobileRestHandler->getAllMobiles();
		break;
		
	case "single":
		// to handle REST Url /mobile/show/<id>/
		$mobileRestHandler = new MobileRestHandler();
		$mobileRestHandler->getMobile($_GET["id"]);
		break;

	case "" :
		//404 - not found;
		break;
}
?>
```

## RESTful Web Service Handler

Let's start by building a base class that can be used in all RESTful service handlers. It has two methods: one that is used to construct the response, and a second one that is built to hold the different HTTP status code and its messages.

**SimpleRest.php**
```
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

Now let's build a handler that extends SimpleRest. The first addition relates to how it handles the response format. This is decided based on the Request Header parameter "Accept". The values that can be used in the request are like "application/json" or "application/xml" or "text/html". The second relevant aspect to consider is the usage of status codes. For success, status code 200 should be set in response and sent. Similarly, other status codes can and should be used according to the situation (ex: resource not available).

Analyze and create the following file:

**MobileRestHandler.php**
```
<?php
require_once("SimpleRest.php");
require_once("Mobile.php");
		
class MobileRestHandler extends SimpleRest {

	function getAllMobiles() {	

		$mobile = new Mobile();
		$rawData = $mobile->getAllMobile();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No mobiles found!');		
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
    			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;		
	}
	
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;		
	}
	
	public function encodeXml($responseData) {
		// creating object of SimpleXMLElement
		$xml = new SimpleXMLElement('<?xml version="1.0"?><mobile></mobile>');
		foreach($responseData as $key=>$value) {
			$xml->addChild($key, $value);
		}
		return $xml->asXML();
	}
	
	public function getMobile($id) {

		$mobile = new Mobile();
		$rawData = $mobile->getMobile($id);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No mobiles found!');		
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

You should notice that there is still a file missing. That is your domain class: "Mobile.php". Here would be where you would access your data, be it a variable, a file, or a SQL database. Let's use a simple variable-based example:

**Mobile.php** 
```
<?php
/* 
A domain Class to demonstrate RESTful web services
*/
Class Mobile {
	
	private $mobiles = array(
		1 => 'Apple iPhone 6S',  
		2 => 'Samsung Galaxy S6',  
		3 => 'Apple iPhone 6S Plus',  			
		4 => 'LG G4',  			
		5 => 'Samsung Galaxy S6 edge',  
		6 => 'OnePlus 2');
		
	/*
		you should hookup the DAO here
	*/
	public function getAllMobile(){
		return $this->mobiles;
	}
	
	public function getMobile($id){
		
		$mobile = array($id => ($this->mobiles[$id]) ? $this->mobiles[$id] : $this->mobiles[1]);
		return $mobile;
	}	
}
?>
```

## Testing the services

You can now use the browser to access your URIs and check the results:

```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/
http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/1/
```

You can see that, given that we did not set an alternative response format, it is provided in HTML. 
To test the web service more extensively you can either build a client programatically and consume the service, or use a standalone general-purpose REST client. For example, you can use curl from the shell of appserver:

```
[aw030@appserver-01 ~]$  curl -X GET -H "Content-type: application/json" -H "Accept: application/json" -L "http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/1/"
{"1":"Apple iPhone 6S"}

[aw030@appserver-01 ~]$ curl -X GET -H "Content-type: application/json" -H "Accept: application/xml" -L "http://appserver-01.alunos.di.fc.ul.pt/~aw030/teste/mobile/list/1/"
<?xml version="1.0"?>
<mobile><1>Apple iPhone 6S</1></mobile>
```

To test the web service more extensively you can either build a client programatically and consume the service, or use a standalone general-purpose REST client. There are several examples available, one is the Google Chrome extension "Advanced REST client" (https://advancedrestclient.com/). Install it and test your requests and Accept headers.



