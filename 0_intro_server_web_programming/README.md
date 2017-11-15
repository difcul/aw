# Intro to Server Web-Programming 
## The DI-FCUL Applicational Web Server
Francisco Couto and Tiago Guerreiro

This tutorial aims to familiarize students with the applicational web server placed at their disposal (_appserver-01_). Alongside, in this tutorial we will create basic PHP applications that store data into a relational database, using MySQL. At the end, students are expected to be comfortable with a setup that allows them to develop web applications using _appserver-01_. 

_Appserver_, available at _appserver-01.alunos.di.fc.ul.pt_, features an Apache Web server and has support for PHP, Java and MySQL. Each group has an account named awXXX where XXX equals the group name (e.g., 001). Passwords and access instructions are provided in the laboratorial class.

This tutorial introduces the basic concepts on using PHP and MySQL in a web application  by using examples that, although having been tested with _appserver_, can be easily adapted to other infra-structures with the same technology.

## Introduction

### PHP

PHP is a programming language that is specially targeted at web application development. PHP code can be directly integrated with HTML code. As one can create an HTML file and make it available online, by using a web server, one can also do that with a PHP file. The main difference is that the information available in the HTML is static while the PHP file will provide dynamic information. That is, the resulting content of accessing an HTML page is always the same, unless the HTML file is edited. Conversely, the resulting content of accessing a PHP file through the Web is the HTML **produced** by executing the PHP commands in that file. This result can vary depending on the input data, data available in the database, time of access, ...As a simple example, the file can include a command that changes the background color of the webpage depending on the time of the day that the file is accessed.

#### Web Server

To make PHP files available you will have to resort to a Web server that supports PHP, meaning that all files with extension _.php_ will be executed by the PHP interpreter. A Web server is an application that is able to accept HTTP requests and return a response, normally an HTML file or other objects like images, PDFs, etc. One of the most popular Web servers that support PHP is Apache, that can be installed on several operating systems. 

### MySQL

MySQL is a relational database management system that can be used by the PHP files to store, manage, update and collect data. The communication between PHP and MySQL is done through SQL commands that allow to create, delete, and manage data structures as well inserting, updating and deleting data.

## Accessing the remote machine with the Web server

To access _appserver_, on Linux or Mac, open a Terminal and input:
```
ssh awXXX@appserver-01.alunos.di.fc.ul.pt
```

On Windows, you can use applications like SSH Secure Shell or PuTTY to access the remote machine. They are installed in the lab.

Upon attempt to connect, the system will ask you for a password (provided to you in class). Upon connection, and once you have your group number, change the password to one all the group members are aware of by using the following command:

```
passwd
```

Notice that you can only access _appserver_ inside the DI network, so you need to be using the department's labs or connected through the VPN. 

These terminals allow you to input commands in the remote machine. To transfer files to and from the remote machine, you can use file transfer specialized applications (on Windows: SSH Secure Shell, FileZilla, WinSCP) or map the remove location and manage it as if it was a local folder. To do so on Linux, in the file manager go to GO-> OPEN LOCATION and input:

```
ssh://awXXX@appserver-01.alunos.di.fc.ul.pt/home/awXXX
```

Upon making the connection, you will be able to navigate and edit your files as you would do with a local folder.

## Accessing the Web server

The first step to write and deploy a web application is to identify where these files need to be stored so they can be executed by the Web server every time an HTTP request is received. In the case of _appserver_, and most Apache Web servers, the web root for each user is the **public_html** folder. Therefore, the first step to create a webpage in the remote machine, is to create and allow access (change access restrictions) to this folder. In the terminal, in the root directory of your account:

```
mkdir public_html
chmod -R 755 public_html
```

The first command creates the directory in the user root directory. The second command sets the access permission rights to reading, writing and executing the files by different types of users. 

After creating the directory, you can then create the HTML file and place it into the directory **public_html**. To do so:

``` 
cd public_html
echo '<html>Hello World!</html>' > index html
```

or you can create the file index.html and edit it with your preferred text editor, making sure that the remote directory is updated with the new file.

Now you can open in your browser the link ```http://appserver-01.alunos.di.fc.ul.pt/~awXXX/``` and check the result. This is result is static and will not change unless the file _index.html_ in the machine _appserver_ is updated.

To create a dynamic page, you can now create a PHP file inside the directory ```public_html```. You can perform the following commands:

```
cd ~
cd public_html
echo '<html><?php echo data(DATE_RFC822); ?> </html>' > index.php
```

By opening the URL ```http://appserver-01.alunos.di.fc.ul.pt/~awXXX/index.php``` you will see a blank page. Probably, an error occurred. To be able to test PHP files more efficiently, you can also execute the _php_ command, through the remote console:

```
php index.php
```

You found the line of the error. The function is called ```date```, not ```data```. Fix it and try it again.

By opening the URL ```http://appserver-01.alunos.di.fc.ul.pt/~awXXX/index.hp``` you will see that the content is not the content of the file; rather it is the content of the interpretation of that file at the time it was executed, that is, the date the access was made.

To create and edit the PHP files, you should use a text editor (e.g., Emacs, SublimeText , Notepad++, vi, nano,..), and not the _echo_ command, that should be used only to create very short files.

## Basic PHP concepts

### Dealing with input data

PHP can receive data through [POST and GET](https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol). As an example, create a file _get.php_ with the following:

```
<?php
echo 'Hello '.htmlspecialchars($_GET['name']).'!';
?>
```

Now, by opening the URL _http://appserver-01.alunos.di.fc.ul.pt/~awXXX/get.php?name=FCUL_ you will see _FCUL_, given as an argument, in the resulting page. 

You can also create the file form.html with the following HTML code to create a form:

```
<html>
    <form action='action.php' method='post'>
        <p> Your name: <input type='text' name='name' /> </p>
        <p> Your age: <input type='text' name='age' /> </p>
        <p><input type='submit' /> </p>
    </form>
</html>
```

Also create the file _action.php_ that will receive the results of the form through the following code:

```
<html>
Hi <?php echo htmlspecialchars($_POST['name']); ?>.
You are <?php echo (int)($_POST['age']); ?> years old.
</html>
```

Submit the form at _http://appserver-01.alunos.di.fc.ul.pt/~awXXX/form.html_ and check the results.

## MySQL and onwards

For those that are finished with the tutorial, go to http://webpages.fc.ul.pt/~fjcouto/files/manual_php_mysql_java_oracle_201112.pdf and follow chapters 3 and 4. 
