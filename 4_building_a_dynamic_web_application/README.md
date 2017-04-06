# Building a Dynamic Web Application with AJAX
Tiago Guerreiro and Francisco Couto

In this tutorial, we focus our attention on the basic concepts of programming asynchronous web applications using AJAX. AJAX stands for Asynchronous Javascript and XML and, paraphrasing dfrom w3schools "is a developers dream, because you can:

* Update a web page without reloading the page
* Request data from a server - after the page has loaded 
* Receive data from a server - after the page has loaded
* Send data to a server - in the background"

In this tutorial, we show examples on how to use the Flickr API examples on the [Using Third-party Web Services](https://github.com/difcul/aw1516/tree/master/1_using_web_services#the-flickr-example) tutorial to build a dynamic web page.

## AJAX Tutorial

Make your first AJAX web page by implementing in <your server> the following example: (https://www.w3schools.com/php/php_ajax_php.asp)

In appserver create a directory named ajax and create the following files:

**index.html**
```
<html>
<head>
<script>
function showHint(str) {
    if (str.length == 0) { 
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "gethint.php?q=" + str, true);
        xmlhttp.send();
    }
}
</script>
</head>
<body>

<p><b>Start typing a name in the input field below:</b></p>
<form> 
First name: <input type="text" onkeyup="showHint(this.value)">
</form>
<p>Suggestions: <span id="txtHint"></span></p>
</body>
</html>
```

**gethint.php**
```
<?php
// Array with names
$a[] = "Anna";
$a[] = "Brittany";
$a[] = "Cinderella";
$a[] = "Diana";
$a[] = "Eva";
$a[] = "Fiona";
$a[] = "Gunda";
$a[] = "Hege";
$a[] = "Inga";
$a[] = "Johanna";
$a[] = "Kitty";
$a[] = "Linda";
$a[] = "Nina";
$a[] = "Ophelia";
$a[] = "Petunia";
$a[] = "Amanda";
$a[] = "Raquel";
$a[] = "Cindy";
$a[] = "Doris";
$a[] = "Eve";
$a[] = "Evita";
$a[] = "Sunniva";
$a[] = "Tove";
$a[] = "Unni";
$a[] = "Violet";
$a[] = "Liza";
$a[] = "Elizabeth";
$a[] = "Ellen";
$a[] = "Wenche";
$a[] = "Vicky";

// get the q parameter from URL
$q = $_REQUEST["q"];

$hint = "";

// lookup all hints from array if $q is different from "" 
if ($q !== "") {
    $q = strtolower($q);
    $len=strlen($q);
    foreach($a as $name) {
        if (stristr($q, substr($name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
        }
    }
}

// Output "no suggestion" if no hint was found or output correct values 
echo $hint === "" ? "no suggestion" : $hint;
?>
```

An test your first ajax web page:
```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/ajax/
```
**IMPORTANT: replace aw030 by your group number**

## Use Flickr getRecent method

Now create a html file that shows every 3 seconds the most recent photos submitted to flickr

Use the same Flickr php code from the previous tutorial (https://github.com/difcul/aw1516/tree/master/1_using_web_services#the-flickr-example), just change the params. 

**flickr.php**
```
<?php
# build the API URL to call
$params = array(
        'api_key'=> 'YOUR_API_KEY',
        'method'=> 'flickr.photos.getRecent',
        'per_page'=> '5',
        'format'=> 'php_serial',
        );

$encoded_params = array();
foreach ($params as $k => $v){
  $encoded_params[] = urlencode($k).'='.urlencode($v);
}
$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);

# call the API and decode the response
$rsp = file_get_contents($url);
$rsp_obj = unserialize($rsp);
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

Test the php file to check if it retrieves the most five recent photos
```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/ajax/flickr.php
```

Now copy the html file with the code of previous AJAX example, and perform the following modifications:
    1.      add the _onload='showHint()'_ in the tag body (http://www.w3schools.com/jsref/event_onload.asp)
    2.      remove the form 
    3.      remove the str parameter of function showHint
    4.      remove the code for _str.length == 0_
    5.      add a settimeout of 3 seconds when a new response arrives (http://www.w3schools.com/jsref/met_win_settimeout.asp)
    6.      change _"gethint.php?q="+str_ to the name of your Flickr php file

**lastestphotos.html**
```
<html>
<head>
<script>
function showHint() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
                setTimeout(showHint,3000);
            }
        };
        xmlhttp.open("GET", "flickr.php", true);
        xmlhttp.send();
}
</script>
</head>
<body onload="showHint()">
<p>Latest photos:</p>
<p><span id="txtHint"></span></p>
</body>
</html>
```
Finally, test the file:
```
http://appserver-01.alunos.di.fc.ul.pt/~aw030/ajax/lastestphotos.html
```

