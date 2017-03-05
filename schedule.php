<?php
/*
	Pokerb main schedule page
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php

	$l = $_GET["level"];

	$tset = getFullSchedule(true);
?>

	<table>
	<tr>
		<th></th>
		<th>Time</th>
		<th>Day</th>
		<th>Site</th>
		<th>Game</th>
		<th>Type</th>
		<th>Buy-In ($)</th>
		<th></th>
		<th>Level</th>
		<th></th>
	</tr>
<?php
	for($i=0; $i<mysql_numrows($tset); $i++)
	{
		$level = mysql_result($tset,$i,"level_id");
		
		if($l==$level || $l==-1)
		{
			echo "<tr>";
			
			switch(mysql_result($tset,$i,"ttype"))
			{
				case 0:
					echo "<td style=\"background-color:#ccc\">BANK</td>";
					break;
				case 1:
					echo "<td><b>CASH</b></td>";
					break;
				case 2:
					echo "<td style=\"background-color:#ee0\"><i>QUAL</i></td>";
					break;
				case 3:
					echo "<td style=\"background-color:#0f0\"><b>BIG</b></td>";
					break;
				case 4:
					echo "<td style=\"background-color:#090\"><b>SHOT</b></td>";
					break;
			}
			echo "<td>".mysql_result($tset,$i,"start_time")."</td>";
			$days_col = "";
			if(mysql_result($tset,$i,"days")!="Any") $days_col = " style=\"background-color:#FFFF00\"";
			echo "<td".$days_col.">".mysql_result($tset,$i,"days")."</td>";
			echo "<td style=\"background-color:".mysql_result($tset,$i,"site_color")."\">".mysql_result($tset,$i,"site_name")."</td>";
			echo "<td>".mysql_result($tset,$i,"game_name")."</td>";
			echo "<td>".mysql_result($tset,$i,"type_name")."</td>";
			echo "<td>".mysql_result($tset,$i,"buy_in")."</td>";
			echo "<td>".mysql_result($tset,$i,"description")."</td>";
			echo "<td style=\"background-color:".mysql_result($tset,$i,"level_color")."\">".$level."</td>";
	
			// stats
			$hourly_rate = mysql_result($tset,$i,"hourly_rate");
			$hours_col = "#FFF";
			if($hourly_rate>=10) $hours_col="#0F0";
			else if($hourly_rate>0 && $hourly_rate<5) $hours_col = "#999";
			else if($hourly_rate<0) $hours_col="#F00";
			
			$best_profit = mysql_result($tset,$i,"best_profit");
			$profit_col = "#FFF";
			if($best_profit>=500) $profit_col="#0F0";
			
			$average_score = mysql_result($tset,$i,"average_score");
			
			echo "<td style=\"background-color:$hours_col\">$hourly_rate</td>";
			echo "<td style=\"background-color:$profit_col\">$best_profit</td>";
			echo "<td>$average_score</td>";
			echo "<td>";
			if(mysql_result($tset,$i,"ignore_t")) echo "IGN";
			else if(mysql_result($tset,$i,"defunct_t")) echo "DEF";
			else echo "&nbsp;";
			echo "</td>";
			echo "<td><a href=\"view_tournament_record.php?t=".mysql_result($tset,$i,"tournament_id")."\">record</a></td>";
			echo "<td><a href=\"edit_tournament.php?t=".mysql_result($tset,$i,"tournament_id")."\">edit</a></td>";
			echo "</tr>";
		}
	}
?>

<?php
	include("footer.php");
?>