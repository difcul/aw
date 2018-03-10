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

    <form action='mywebapp.php' method='get'>
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
echo "<p><span id="latestPhotos"></span></p>"
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

## Additional references

- https://www.w3schools.com/xml/ajax_intro.asp

- https://www.tutorialspoint.com/ajax/index.htm