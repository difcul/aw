# Server Web Programming 
Francisco Couto and Tiago Guerreiro

## Goal
This tutorial aims to help you create a simple web application composed of 3 steps:
- Retrieval
- Annotation
- Access

You can develop the application using:
- applicational web server (_appserver_), available at _appserver.alunos.di.fc.ul.pt_, features an Apache Web server and has support for PHP. 
- any linux machine with PHP (for example: http://howtoubuntu.org/how-to-install-lamp-on-ubuntu)


## Testing PHP 

PHP is a programming language that is specially targeted at web application development. PHP code can be directly integrated with HTML code. As one can create an HTML file and make it available online, by using a web server, one can also do that with a PHP file. The main difference is that the information available in the HTML is static while the PHP file will provide dynamic information. That is, the resulting content of accessing an HTML page is always the same, unless the HTML file is edited. Conversely, the resulting content of accessing a PHP file through the Web is the HTML **produced** by executing the PHP commands in that file. This result can vary depending on the input data, data available in the database, time of access, ...As a simple example, the file can include a command that changes the background color of the webpage depending on the time of the day that the file is accessed.

To make PHP files available you will have to resort to a Web server that supports PHP, meaning that all files with extension _.php_ will be executed by the PHP interpreter. A Web server is an application that is able to accept HTTP requests and return a response, normally an HTML file or other objects like images, PDFs, etc. One of the most popular Web servers that support PHP is Apache, that can be installed on several operating systems. 

The first step to write and deploy a web application is to identify where these files need to be stored so they can be executed by the Web server every time an HTTP request is received. 
In the case of _appserver_, and most Apache Web servers, the web root for each user is the **public_html** folder (or _/var/www/html/_ in local machines). 

In Windows 10, you can also install PHP and a Apache Web server on Ubuntu (https://kwiksteps.com/php-ubuntu-on-windows/).

### Appserver

To access _appserver_, on Linux or Mac, open a Terminal and input:
```shell
ssh awXXX@appserver.alunos.di.fc.ul.pt
```

On Windows, you can use applications like SSH Secure Shell or PuTTY to access the remote machine. They are installed in the lab. 
List of clients: https://en.wikipedia.org/wiki/Comparison_of_SSH_clients.


Upon attempt to connect, the system will ask you for a password (provided to you in class). Upon connection, and once you have your group number, change the password to one all the group members are aware of by using the following command:

```shell
passwd
```

Notice that you can only access _appserver_ inside FCUL network, so you need to be using one of the faculty labs or connected through the VPN. 

These terminals allow you to input commands in the remote machine. To transfer files to and from the remote machine, you can use file transfer specialized applications 
(on Windows: SSH Secure Shell, FileZilla, WinSCP) or map the remove location and manage it as if it was a local folder. 
To do so on Linux, in the file manager go to GO-> OPEN LOCATION and input:

```shell
ssh://awXXX@appserver.alunos.di.fc.ul.pt/home/awXXX
```

Upon making the connection, you will be able to navigate and edit your files as you would do with a local folder.

### Static Web Page

The first step to create a webpage is to create and allow access (change access restrictions) to this folder. 
In the terminal, type in the home directory:

```shell
mkdir public_html
chmod go+rx ~/
chmod go+rx public_html
```

The first command creates a subdirectory in the home directory. The second and third commands give access permission rights to read and execute files to groups and other users.
This is important to let the web server access your files. 

After creating the directory, you can then create the HTML file and place it into the directory **public_html**. To do so:

```shell 
cd public_html
echo '<html>Hello World!</html>' > index.html
```

The ```>``` is a redirection operator ( https://en.wikipedia.org/wiki/Redirection_(computing) ) that move the output of
the command ```echo``` to the file _index.html_ .
Or you can create the file index.html and edit it with your preferred text editor, making sure that the remote directory is updated with the new file.

Now you can open in your browser the link ```http://appserver.alunos.di.fc.ul.pt/~awXXX/``` and check the result. 

If using a local machine the link should start with localhost ```http://localhost/...```.

In a DI labs should be ```http://localhost/~fcXXXXX/```. 

This is result is static and will not change unless the file _index.html_ in the machine _appserver_ or localhost is updated.

Note that in some machines you may have to change the permissions of the file:
```shell
chmod go+rx index.html
```

### Dynamic Web Page

To create a dynamic page, you can now create a PHP file inside the directory ```public_html```. You can perform the following commands:

```shell
cd ~
cd public_html
echo '<html><?php echo data(DATE_RFC822); ?> </html>' > index.php
```

By opening the URL ```http://appserver.alunos.di.fc.ul.pt/~awXXX/index.php``` you will see a blank page. Probably, an error occurred. To be able to test PHP files more efficiently, you can also execute the _php_ command, through the remote console:

```shell
php index.php
```

You found the line of the error. The function is called ```date```, not ```data```. Fix it and open the url again:

```shell
echo '<html><?php echo date(DATE_RFC822); ?> </html>' > index.php
```

By opening the URL ```http://appserver.alunos.di.fc.ul.pt/~awXXX/index.php``` you will see that the content is not the content of the file; rather it is the content of the interpretation of that file at the time it was executed, that is, the date the access was made.

To create and edit the PHP files, you should use a text editor (e.g., gedit, emacs, SublimeText, Notepad++, vi, nano,..), and not the _echo_ command, that should be used only to create very short files.


## Retrieval

Use the tool _curl_ to open an URL that provides you with 10 PubMed identifiers about Asthma (type ```man curl``` to know more about ```curl```). 

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=Asthma&retmax=10&retmode=xml"
```

You should get 10 PubMed identifiers on your screen embbed in a xml file. This URL corresponds to a web service that we will explore further in the following modules.

Now parse the results using the tools _grep_ and _sed_ to keep only the Id numbers (again type ```man``` and the name of tool to know more about it): 

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=Asthma&retmax=10&retmode=xml" | grep "<Id>" | sed -e "s/<Id>//" -e "s/<\/Id>//" 
```

Now you should get the 10 PubMed identifiers on your screen without xml tags.

Using a text editor create a file named _getPubMedIds.sh_  and copy and paste the following command into it:

```shell
curl "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=$1&retmax=10&retmode=xml" | grep "<Id>" | sed -e "s/<Id>//" -e "s/<\/Id>//" 
```

Note that we replaced Asthma by _$1_ so we can use the name of disease as input,
Now add permissions to execute the script, and execute it saving the result to a file using the redirection operator:

```shell
chmod u+x ./getPubMedIds.sh
./getPubMedIds.sh Asthma > Asthma.txt
```

Check the contents of file _Asthma.txt_, for example by using the _cat_ tool:

```shell
cat Asthma.txt  
```

## Annotation

To convert the Ids to links try the _sed_ tool:

```shell
sed "s/^/https:\/\/www.ncbi.nlm.nih.gov\/pubmed\//" < Asthma.txt
```

Using a text editor create a file named _convertPubMedIds.sh_ and copy and paste the following command into it:

```shell
sed "s/^/https:\/\/www.ncbi.nlm.nih.gov\/pubmed\//" < $1.txt
```

Note that we replaced Asthma by _$1_ so we can use the name of disease as input.
Now add permissions to execute the script, and execute it saving the result to a file:

```shell
chmod u+x ./convertPubMedIds.sh
./convertPubMedIds.sh Asthma > AsthmaLinks.txt
```

Check the contents of file _AsthmaLinks.txt_:

```shell
cat AsthmaLinks.txt  
```

## Access

PHP can receive data through [POST and GET](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol). As an example, create a file _mywebapp.php_ with the following code:

```php
<?php
echo 'Disease: '.htmlspecialchars($_GET['disease']);
?>
```

Now, by opening the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php?disease=Asthma_ you will see _Asthma_, given as an argument, in the resulting page. 

Now clear the previous file, and add the following HTML code to create a form:

```html
<html>
    <form action='mywebapp.php' method='get'>
        <p> Disease: <input type='text' name='disease' /> </p>
        <p><input type='submit' /> </p>
    </form>
```

and the following PHP code to produce the links according to the input:

```php
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
```

Open the URL _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_ (hit refresh) and check the results.


## Multiple diseases

You can execute the scripts multiple times using the ```xargs``` command.
First, create a file named _diseases.txt_ with one disease per line, for example:
```txt
Asthma
Alzheimer
Tuberculosis
Cirrhosis
Diabetes
```

Then use ```xargs``` to get the identifiers of all the previous diseases: 

```shell
cat diseases.txt | xargs -I {} ./getPubMedIds.sh {}
```

To get and convert and save to a file, create a script named _getConvertPubMedIds.sh_ with the following contents:
```shell
./getPubMedIds.sh $1 > $1.txt
./convertPubMedIds.sh $1 > $1Links.txt
```


Then use ```xargs``` with the new script:
```shell
cat diseases.txt | xargs -I {} ./getConvertPubMedIds.sh {}
```

Now you should be able to find the links for all previous diseases in _http://appserver.alunos.di.fc.ul.pt/~awXXX/mywebapp.php_ 

## Invoking Shell Scripts 

Another option to invoke shell scripts is using the ```for``` command:

```shell
for disease in $( cat diseases.txt ); do
    echo $disease
    ./getConvertPubMedIds.sh disease
done
```

Or invoke them from another application:

- Python: 
```python
import os 
os.system("./getConvertPubMedIds.sh" + disease)
```

- PHP:
```php
passthru("./getConvertPubMedIds.sh" + disease)
```

- Java:
```java
Runtime.getRuntime().exec("./getConvertPubMedIds.sh" + disease)
```

Note: curl libraries are also available for Python, Java or PHP.


## Additional References

- http://labs.rd.ciencias.ulisboa.pt/book/ (Chapter 3 - Data Retrieval)

- http://webpages.fc.ul.pt/~fjcouto/files/manual_php_mysql_java_oracle_201112.pdf

- https://www.w3schools.com/html/

- https://www.w3schools.com/php/
