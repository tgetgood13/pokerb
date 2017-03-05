<?php
/*
	Author: Tom Getgood 
*/
session_start();

include("dbase.php");

openProdConnection();

if(isset($_SESSION['username']) && userCheck($_SESSION['username'],$_SESSION['SessionId'])>0)
{

?>
<html>
<head>
<link type="text/css" rel="stylesheet" media="screen" href="styles.css" />
<script language="Javascript" type="text/javascript" src="/sortabletable.js"></script>

<title>The information contained within this page is STRICTLY PRIVATE AND CONFIDENTIAL AND NOT INTENDED TO BE VIEWED BY ANYONE BUT THE AUTHOR unless permission has been explicity granted.</title>

</head>
<body>

<b>The information contained within this page is STRICTLY PRIVATE AND CONFIDENTIAL AND NOT INTENDED TO BE VIEWED BY ANYONE BUT THE AUTHOR unless permission has been explicity granted.</b><br/>
<br/>
<a href="index.php">Home</a> | <a href="schedule.php?level=-1">Schedule</a> | <a href="enter_data.php">Enter Data</a> | <a href="report.php">Report</a> | <a href="cv.php">CV</a> | <a href="http://www.betfair.com" target="_blank">Betfair</a> | <a href="http://www.bbc.co.uk/news/business/market_data/currency/default.stm">Exchange Rate</a> | <a href="logout.php">Logout</a><br/>
<i>Note: pokerb app doesn't parse 1/1 tourneys (e.g. FT 1520)</i><br/>
<?php
}
else
{
        close();

        $url = "login.php";
        $url = "Location: ".$url;
        header($url);
}
?>