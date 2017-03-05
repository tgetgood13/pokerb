<?php
/*
	Author: Tom Getgood
*/
	// no header as we are going to redirect
	session_start();

	if(isset($_POST["site"]))
	{
		include("dbase.php");
		openProdConnection();
		
		$site = post_checkInt("site");
		$start_time = post_checkString("start_time");
		$buy_in = post_checkDouble("buy_in");
		$ef_buy_in = post_checkDouble("ef_buy_in");
		$game = post_checkInt("game");
		$type = post_checkInt("type");
		$ttype = post_checkInt("ttype");
		$description = post_checkString("description");
		$days = post_checkInt("days");
		$rebuy = post_checkString("rebuy");
		if($rebuy=="on") $rebuy=1;
		else $rebuy = 0;
		$rebuy_addon = post_checkDouble("rebuy_addon");
		$bounty = post_checkDouble("bounty");
		
		addTournament($site,$start_time,$buy_in,$ef_buy_in,$type,$ttype,$game,$description,$days,$rebuy,$rebuy_addon,$bounty, false);
		
		close();
		
		$url = "index.php";
		$url = "Location: ".$url;
		header($url);	      
	}
	else
	{
		echo "problems!";
	}
?>