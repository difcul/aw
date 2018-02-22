<html>
    <form action='mywebapp.php' method='get'>
        <p> Disease: <input type='text' name='disease' /> </p>
        <p><input type='submit' /> </p>
    </form>

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

foreach ($c as $key => $value) {
  echo '<a href="' . $key . '">' . $value . '</a></br>'; 
}

$filename = $_GET['disease']."Photos.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$photos = explode("\n",$contents);
fclose($handle);

foreach ($photos as $p) {
  echo '<a href="'. $p .'"><img src="'. $p .'" /></a></br>';
}


?>
</html>
