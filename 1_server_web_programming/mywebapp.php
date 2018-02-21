<html>
    <form action='mywebapp.php' method='get'>
        <p> Disease: <input type='text' name='disease' /> </p>
        <p><input type='submit' /> </p>
    </form>
</html>
<html>
<p>Abstracts about the disease <?php echo htmlspecialchars($_GET['disease']); ?>:</p>

<?php
$filename = $_GET['disease']."Links.txt";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$links = explode("\n",$contents);

foreach($links as $v) {
  echo '<a href="' . $v . '">' . $v . '</a></br>'; 
}

fclose($handle);
?>
</html>
