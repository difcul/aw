# Building a Dynamic Web Application with AJAX
Tiago Guerreiro and Francisco Couto

In this tutorial, we focus our attention on the basic concepts of programming asynchronous web applications using AJAX. AJAX stands for Asynchronous Javascript and XML and, paraphrasing dfrom w3schools "is a developers dream, because you can:

* Update a web page without reloading the page
* Request data from a server - after the page has loaded 
* Receive data from a server - after the page has loaded
* Send data to a server - in the background"

In this tutorial, we show examples on how to use the Flickr API examples on the [Using Third-party Web Services](/../1_using_web_services/) tutorial to build a dynamic web page.

## AJAX Tutorial

Make your first AJAX web page by implementing in <your server> the following example: http://www.w3schools.com/ajax/tryit.asp?filename=tryajax_suggest_php

1.      copy the html code into an html file and create the gethint.php in <your server> (http://www.w3schools.com/php/php_ajax_php.asp) 
2.      test the gethint.php, example http://<your server>/gethint.php?q=a
3.      open your html file in your browser

## Use Flickr getRecent method

Now create a html file that shows every 3 seconds the most recent photos submitted to flickr

1.      Get the Flickr php code from the previous tutorial (https://github.com/difcul/aw1516/tree/master/1_using_web_services#the-flickr-example)
2.      and adapt the code to use the method flickr.photos.getRecent (https://www.flickr.com/services/api/flickr.photos.getRecent.htm)
by changing the following lines: 
```
'method'=> 'flickr.photos.getRecent,
'per_page'=> '5',
```
3.      test the php file to check if it retrieves the most five recent photos
4.      create an html file with the code of previous AJAX example, and perform the following modifications:
    1.      add the onload='showHint()' in the tag body (http://www.w3schools.com/jsref/event_onload.asp)
    2.      remove the form 
    3.      remove the str parameter of function showHint
    4.      remove the code for str.length == 0
    5.      add a settimeout of 3 seconds when a new response arrives (http://www.w3schools.com/jsref/met_win_settimeout.asp)
    6.      change "gethint.php?q="+str to the name of your Flickr php file

