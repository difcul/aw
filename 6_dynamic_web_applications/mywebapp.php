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
</head>
<body onload='updatePhotos()'>

    <form action='mywebapp.php' method='get' autocomplete='off'>
        <p>Disease: <input type='text' id="searchDisease" name='disease' onkeyup="showHint(this.value)" value="<?php echo htmlspecialchars($_GET['disease']); ?>"/> 
	Suggestions: <span id="txtHint"></span></p>
        <p><input type='submit' /> </p>
    </form>



</html>

<p>Abstracts about the disease <?php echo htmlspecialchars($_GET['disease']); ?>:</p>

<?php
$filename = $_GET['disease']."Links.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$links = explode("\n",$contents);
fclose($handle);

$filename = $_GET['disease']."Titles.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$titles = explode("\n",$contents);
fclose($handle);

$c=array_combine($links,$titles);

echo '<div vocab="http://schema.org/" typeof="ScholarlyArticle" resource="#article">';

foreach ($c as $key => $value) {
  echo '<span property="name">';
  echo '<a href="' . $key . '">' . $value . '</a></br>'; 
  echo '</span>';
}

echo '</div>';

echo '<p><span id="latestPhotos"></span></p>';

?>



</html>
