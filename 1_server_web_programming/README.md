# Server Web Programming 
Francisco Couto and Tiago Guerreiro

## Goal
This tutorial aims to help you create a simple web application composed of 3 modules:
- Data Collection
- Data Annotation
- Data Access

You can develop the application using:
- applicational web server (_appserver_), available at _appserver.alunos.di.fc.ul.pt_, features an Apache Web server and has support for PHP. 
- any linux machine with PHP (for example: http://howtoubuntu.org/how-to-install-lamp-on-ubuntu)


## Testing PHP 

PHP is a programming language that is specially targeted at web application development. PHP code can be directly integrated with HTML code. As one can create an HTML file and make it available online, by using a web server, one can also do that with a PHP file. The main difference is that the information available in the HTML is static while the PHP file will provide dynamic information. That is, the resulting content of accessing an HTML page is always the same, unless the HTML file is edited. Conversely, the resulting content of accessing a PHP file through the Web is the HTML **produced** by executing the PHP commands in that file. This result can vary depending on the input data, data available in the database, time of access, ...As a simple example, the file can include a command that changes the background color of the webpage depending on the time of the day that the file is accessed.

To make PHP files available you will have to resort to a Web server that supports PHP, meaning that all files with extension _.php_ will be executed by the PHP interpreter. A Web server is an application that is able to accept HTTP requests and return a response, normally an HTML file or other objects like images, PDFs, etc. One of the most popular Web servers that support PHP is Apache, that can be installed on several operating systems. 

The first step to write and deploy a web application is to identify where these files need to be stored so they can be executed by the Web server every time an HTTP request is received. In the case of _appserver_, and most Apache Web servers, the web root for each user is the **public_html** folder (or /var/www/html/ in local machines). 

To access _appserver_, on Linux or Mac, open a Terminal and input:
```
ssh awXXX@appserver.alunos.di.fc.ul.pt
```

On Windows, you can use applications like SSH Secure Shell or PuTTY to access the remote machine. They are installed in the lab. 
List of clients: https://en.wikipedia.org/wiki/Comparison_of_SSH_clients.

Upon attempt to connect, the system will ask you for a password (provided to you in class). Upon connection, and once you have your group number, change the password to one all the group members are aware of by using the following command:

```
passwd
```

Notice that you can only access _appserver_ inside FCUL network, so you need to be using one of the faculty labs or connected through the VPN. 

These terminals allow you to input commands in the remote machine. To transfer files to and from the remote machine, you can use file transfer specialized applications 
(on Windows: SSH Secure Shell, FileZilla, WinSCP) or map the remove location and manage it as if it was a local folder. 
To do so on Linux, in the file manager go to GO-> OPEN LOCATION and input:

```
ssh://awXXX@appserver.alunos.di.fc.ul.pt/home/awXXX
```

Upon making the connection, you will be able to navigate and edit your files as you would do with a local folder.


Therefore, the first step to create a webpage in the remote machine, is to create and allow access (change access restrictions) to this folder. In the terminal, in the root directory of your account:

```
mkdir public_html
chmod -R 755 public_html
```

The first command creates the directory in the user root directory. The second command sets the access permission rights to reading, writing and executing the files by different types of users.
Thsi is important to let the web server access your files. 

After creating the directory, you can then create the HTML file and place it into the directory **public_html**. To do so:

``` 
cd public_html
echo '<html>Hello World!</html>' > index html
```

or you can create the file index.html and edit it with your preferred text editor, making sure that the remote directory is updated with the new file.

Now you can open in your browser the link ```http://appserver.alunos.di.fc.ul.pt/~awXXX/``` and check the result. 
If using a local machine the link should start with localhost ```http://localhost/...```
This is result is static and will not change unless the file _index.html_ in the machine _appserver_ is updated.

To create a dynamic page, you can now create a PHP file inside the directory ```public_html```. You can perform the following commands:

```
cd ~
cd public_html
echo '<html><?php echo data(DATE_RFC822); ?> </html>' > index.php
```

By opening the URL ```http://appserver.alunos.di.fc.ul.pt/~awXXX/index.php``` you will see a blank page. Probably, an error occurred. To be able to test PHP files more efficiently, you can also execute the _php_ command, through the remote console:

```
php index.php
```

You found the line of the error. The function is called ```date```, not ```data```. Fix it and open the url again:

```
echo '<html><?php echo date(DATE_RFC822); ?> </html>' > index.php
```

By opening the URL ```http://appserver.alunos.di.fc.ul.pt/~awXXX/index.hp``` you will see that the content is not the content of the file; rather it is the content of the interpretation of that file at the time it was executed, that is, the date the access was made.

To create and edit the PHP files, you should use a text editor (e.g., Emacs, SublimeText , Notepad++, vi, nano,..), and not the _echo_ command, that should be used only to create very short files.


## Data Collection

First test the tool _curl_ to open the URL that provides you 10 PubMed identifiers about Asthma (type ```man curl``` to know more about curl). 

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=Asthma&retmax=10&retmode=xml"
```

You should get 10 PubMed identifiers on your screen embbed in a xml file.

Now let's parse the results using the _grep_ and _sed_ to keep only the Id numbers: 

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=Asthma&retmax=10&retmode=xml" | grep "<Id>" | sed -e "s/<Id>//" -e "s/<\/Id>//" 
```

You should get the 10 PubMed identifiers on your screen withou xml tags.

Create a file named _getPubMedIds.sh_ with the previous command, but replace Asthma by $1 so we can ask for different diseases, i.e :

```
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=$1&retmax=10&retmode=xml" | grep "<Id>" | sed -e "s/<Id>//" -e "s/<\/Id>//" 
```

Now add permissions to execute the script, and execute it saving the result to a file:

```
chmod 755 ./getPubMedIds.sh
./getPubMedIds.sh Asthma > Asthma.txt
```
Check the contents of file _Asthma.txt_, for example by using the _cat_ tool:

```
cat Asthma.txt  
```

## Data Annotation

To convert the Ids to links try the sed tool:

```
sed "s/^/https:\/\/www.ncbi.nlm.nih.gov\/pubmed\//" < Asthma.txt
```

Create a file named _convertPubMedIds.sh_ with the previous command, but replace Asthma by $1 so we can ask for different diseases, i.e :

```
sed "s/^/https:\/\/www.ncbi.nlm.nih.gov\/pubmed\//" < $1.txt
```

Now add permissions to execute the script, and execute it saving the result to a file:

```
chmod 755 ./convertPubMedIds.sh
./convertPubMedIds.sh Asthma > AsthmaLinks.txt
```

Check the contents of file _AsthmaLinks.txt_:

```
cat Asthma.txt  
```

## Data Access

PHP can receive data through [POST and GET](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol). As an example, create a file _mywebapp.php_ with the following code:

```
<?php
echo 'Disease: '.htmlspecialchars($_GET['disease']);
?>
```

Now, by opening the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php?disease=Asthma_ you will see _Asthma_, given as an argument, in the resulting page. 

Now add to the previous file the following HTML code to create a form:

```
<html>
    <form action='mywebapp.php' method='get'>
        <p> Disease: <input type='text' name='disease' /> </p>
        <p><input type='submit' /> </p>
    </form>

<p>Abstracts about the disease <?php echo htmlspecialchars($_GET['disease']); ?>:</p>
```

and the following PHP code to produce the links:

```
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
```

Open the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_ (hit refresh) and check the results.

Now try for different diseases.

## Additional References

- http://webpages.fc.ul.pt/~fjcouto/files/manual_php_mysql_java_oracle_201112.pdf (chapters 3 and 4) 
