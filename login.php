<?php
/*
    standard SST login page
    Author: Tom Getgood 
*/
session_start();

include("dbase.php");

$state = "none";

if (isset($_SESSION['username']))
{
        openProdConnection();
        $uCheck = userCheck($_SESSION['username'],$_SESSION['SessionId']);
        close();

        if($uCheck>0)
        {
                $url = "index.php";
                $url = "Location: ".$url;
                header($url);
        }
        else
        {
                $state = "login";
        }
}
else
{
        if (isset($_POST['username']))
        {
                $username = $_POST['username'];
                $password = $_POST['password'];
        
                openProdConnection();
                $sCheck = securityCheck($username,$password);
                if($sCheck>0)
                {
		                $sessionId = mt_rand();
                        logAdminSession($sessionId,$username);
                        close();

						$_SESSION["SessionId"] = $sessionId;
                        $_SESSION['username'] = $username;

                        $url = "index.php";
                        $url = "Location: ".$url;
                        header($url);
                }
                else
                {
                        close();
                        $state="error";

                }
        }
        else
        {
                $state="login";
        }
}
?>

<html>
<head>
<link type="text/css" rel="stylesheet" media="screen" href="styles.css" />
<script language="Javascript" type="text/javascript" src="sortabletable.js"></script>

<title>BB</title>

</head>
<body>

<h1>BB Login</h1>

<?php

if($state=="error")
{
?>
		<p>
			<br/>
	        Incorrect username/password combo.<br/>
	        <br/>
	    </p>
<?php
}
else if($state=="login")
{
?>
        <p>
        	<br/>
        	In order to continue, you need to enter your user name and password.<br/>
        	<br/>
        </p>

        <form method="POST" action="login.php">
        <fieldset>
        	<label for="username">Username:</label>
        	<input type="text" name="username"/><br/>
        	<label for="password">Password:</label>
        	<input type="password" name="password"/><br/>
        	<label for="submit">&nbsp;</label>
        	<input type="submit" name="submit" value="Submit"/><br/>
 		</fieldset>
        </form>
        <br/>
<?php
}
else
{
        echo "You are not authorised to see anything!";
}
?>

</body>
</html>
