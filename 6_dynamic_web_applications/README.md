# Dynamic Web Applications
Francisco Couto and Tiago Guerreiro

AJAX stands for Asynchronous Javascript and XML and, paraphrasing from w3schools "is a developers" dream, because you can:

* Update a web page without reloading the page
* Request data from a server - after the page has loaded 
* Receive data from a server - after the page has loaded
* Send data to a server - in the background"

## Suggestions

Start by copying the _txt_ files and the _mywebapp.php_ from a previous module.

Replace the beginning of the _mywebapp.php_ to add the following _javascript_ code:

```html
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

    <form action='mywebapp.php' method='get' autocomplete='off'>
        <p>Disease: <input type='text' id="searchDisease" name='disease' onkeyup="showHint(this.value)"/> 
	Suggestions: <span id="txtHint"></span></p>
        <p><input type='submit' /> </p>
    </form>



</html>
...
```

Now create a file _gethint.php_ that generates possible suggestions of diseases matching a given prefix:

```php
<?php
// Array with names
$a = array("Asthma", "Anemia", "Angioma", "Arthritis");

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

Now test your web application in the browser by typing in the form and checking the suggestions.

## Dynamic photos 


Now update your web application so it gets the photos about the disease in a dynamic way.
 
Add to the _mywebapp.php_ with the following javascript function, that calls itself every 3 seconds (https://www.w3schools.com/jsref/met_win_settimeout.asp).
```javascript
...
function updatePhotos() {
    str = document.getElementById("searchDisease").value;
    if (str.length == 0) { 
        document.getElementById("latestPhotos").innerHTML = "";
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("latestPhotos").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "getphotos.php?disease=" + str, true);
        xmlhttp.send();
    }
    setTimeout(updatePhotos,3000);
}
</script>
...
```

Add the _onload='showHint()'_ in the tag body to call the function for the first time (http://www.w3schools.com/jsref/event_onload.asp)

```php
<body onload='updatePhotos()'>
```

Replace the PHP code to show the photos by defining the place where the photos will be shown:

```php
echo "<p><span id='latestPhotos'></span></p>"
```

Create the _getphotos.php_ file with the PHP code removed in the previous step:

```php
<?php
$filename = $_GET['disease']."Photos.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$photos = explode("\n",$contents);
fclose($handle);

foreach ($photos as $p) {
  echo '<a href="'. $p .'"><img src="'. $p .'" /></a></br>';
}
?>
```

Now test your web application in the browser and check that the images are shown and removed dynamically, without the need to click the submit button. 

Now try to change the photos _txt_ file, for example using the _tac_ tool (type ```man tac``` to know more about _tac_):
```shell
cp AsthmaPhotos.txt AsthmaPhotosOriginal.txt 
tac AsthmaPhotosOriginal.txt  > AsthmaPhotos.txt 
```

Check now in the browser the order of the photos, and return to their original order:

```shell
cat AsthmaPhotosOriginal.txt  > AsthmaPhotos.txt
```

To keep the disease name in the text input after clicking the submit, add the following code inside the _input_ of type text.
```php
value="<?php echo htmlspecialchars($_GET['disease']); ?>"
```

## Using Web Services

The _mywebapp.php_ should not retrieve data directly from the data files or a database,
instead it should invoke the RESTful Web Services URIs to access the data resources.

Thus, the _gethint.php_ and _getphotos.php_ should be converted into 
RESTful Web Services that return a JSON response, as it was done in the previous module. 

The _mywebapp.php_ code that reads the articles about a disease from the files
should also be replaced to invoke a RESTful Web Service using the ```xmlhttp.open``` and parse the JSON result.

To request a JSON response do not forget to set the request header: 
```javascript
...
xmlhttp.open("GET", "disease/" + str + "/photos/", true);
xmlhttp.setRequestHeader('Accept', 'application/json');
xmlhttp.send();
...
```

Consider that the web service returns the following response:
```json
{"photos":[
  {"link":"https://farm8.staticflickr.com/7834/47246897321_32ddb1b7e8.jpg", "title":"Ventolin Inhaler 100 mcg"},
  {"link":"https://farm8.staticflickr.com/7926/32306113307_ecab9c7bd9.jpg", "title":"Asthma attack girl"},
...
]}
```

To parse the response you can use the JSON.parse function:
```javascript
...
var myArr = JSON.parse(this.responseText);
for (var i = 0; i < myArr.length; i++) {
    myImg = '<a href="'. myArr[i].link .'"><img src="'. myArr[i].link .'" alt="'. myArr[i].title .'" /></a></br>';
    document.getElementById("latestPhotos").innerHTML = myImg;
}
...
```

For displaying multiple images, for example you can generate a slideshow or a grid with them:
- https://www.w3schools.com/howto/howto_js_slideshow.asp
- https://www.w3schools.com/howto/howto_js_image_grid.asp


More information: https://www.w3schools.com/js/js_json_php.asp

## Additional references

- https://www.w3schools.com/xml/ajax_intro.asp

- https://www.w3schools.com/js/js_json_intro.asp

- https://www.tutorialspoint.com/ajax/

- https://www.tutorialspoint.com/json/

- https://www.taniarascia.com/how-to-use-json-data-with-php-or-javascript/