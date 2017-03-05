<?php
/*
	Pokerb look at most profitable tourneys
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php

	//$l = $_GET["level"];

	$tset = getBaseMostProfitable(true);
?>	
	<form action="top_tourneys.php" method="post">
	Sites:<br/>
	<input type="checkbox" name="site[]" value="PP" checked="checked"/> PP&nbsp;&nbsp;<input type="checkbox" name="site[]" value="FT" checked="checked"/> FT&nbsp;&nbsp;<input type="checkbox" name="site[]" value="88" checked="checked"/> 88&nbsp;&nbsp;
	<input type="checkbox" name="site[]" value="BF" checked="checked"/> BF&nbsp;&nbsp;<input type="checkbox" name="site[]" value="PS" checked="checked"/> PS&nbsp;&nbsp;<input type="checkbox" name="site[]" value="PD" checked="checked"/> PD<br/>
	<br/>
	Game:<br/>
	<input type="checkbox" name="game[]" value="HENL" checked="checked"/> HENL&nbsp;&nbsp;
	<input type="checkbox" name="game[]" value="PLO"/> PLO&nbsp;&nbsp;
	<input type="checkbox" name="game[]" value="OLPL"/> OLPL&nbsp;&nbsp;
	<input type="checkbox" name="game[]" value="OLFL"/> OLFL&nbsp;&nbsp;
	<input type="checkbox" name="game[]" value="HEPL" checked="checked"/> HEPL&nbsp;&nbsp;
	<input type="checkbox" name="game[]" value="HA"/> HA&nbsp;&nbsp;<br/>
	<br/>
	Type:<br/>
	<input type="checkbox" name="type[]" value="HU"/> HU&nbsp;&nbsp;
	<input type="checkbox" name="type[]" value="6H"/> 6H&nbsp;&nbsp;
	<input type="checkbox" name="type[]" value="8h" checked="checked"/> 8H&nbsp;&nbsp;
	<input type="checkbox" name="type[]" value="9H" checked="checked"/> 9H&nbsp;&nbsp;
	<input type="checkbox" name="type[]" value="tH" checked="checked"/> tH&nbsp;&nbsp;<br/>
	<br/>
	Min $ Buy-In: <input type="text" name="buymin" /> Max : <input type="text" name="buymax"/><br/>
	Rebuys Only? <input type="checkbox"/><br/>
	<input type="submit" value="Go"/>
	<br/>
	</form>
	
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
		<th></th>
	</tr>
<?php
	

	for($i=0; $i<mysql_numrows($tset); $i++)
	{
		$tfirst = mysql_result($tset,$i,"tfirst");
		$tfirstx = mysql_result($tset,$i,"tfirstx");
		if($tfirstx==0) $tfirstx==1;
			
		$tentries = mysql_result($tset,$i,"tentries");
		if($tentries>0 && mysql_result($tset,$i,"profit")>0)
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
			echo "<td style=\"background-color:".mysql_result($tset,$i,"display_color")."\">".mysql_result($tset,$i,"site_name")."</td>";
			echo "<td>".mysql_result($tset,$i,"game_desc")."</td>";			
			echo "<td>".mysql_result($tset,$i,"type_desc")."</td>";
			echo "<td>".mysql_result($tset,$i,"buy_in")."</td>";
			echo "<td>".mysql_result($tset,$i,"description")."</td>";

			// stats
			echo "<td>$".number_format($tfirst/$tfirstx,2)."</td>";
				 //SUM(tr.score) AS tscore, COUNT(tr.score>0) AS tscorex, SUM(tr.first_place) AS tfirst, COUNT(tr.first_place>0) AS tfirstx, MAX(tr.cash) AS tmaxcash, COUNT(tr.id) AS tentries, MIN(tr.place) AS tminplace FROM tPkrTournament t";
	
					
					
			echo "<td>".number_format( mysql_result($tset,$i,"total_ent")/$tentries ,2)."</td>";
					
			echo "<td>".number_format((mysql_result($tset,$i,"tlength")/$tentries)/60, 2)."</td>";
					
			$itm_w_zero = mysql_result($tset,$i,"itm");
			if($itm_w_zero<=0) $itm_w_zero=0;
			echo "<td>".$itm_w_zero."/".$tentries."</td>";
			$itm = ((mysql_result($tset,$i,"itm")/$tentries))*100;
			$itm_col = "#FFF";
			if($itm>=20) $itm_col="#0F0";
			echo "<td style=\"background-color:$itm_col\">".number_format($itm,2)."%</td>";
					
			$f123_w_zero = mysql_result($tset,$i,"f123");
			if($f123_w_zero<=0) $f123_w_zero=0;
			echo "<td>".$f123_w_zero."/".$itm_w_zero."</td>";
			$f123 = ((mysql_result($tset,$i,"f123")/$itm_w_zero))*100;
			$f123_col = "#FFF";
			if($f123>=10) $f123_col="#0F0";
			echo "<td style=\"background-color:$f123_col\">".number_format($f123,2)."%</td>";
					
			$minplace_col = "#FFF";
			if(mysql_result($tset,$i,"tminplace")==1) $minplace_col="#0F0";
			echo "<td style=\"background-color:$minplace_col\">".mysql_result($tset,$i,"tminplace")."</td>";
					
			$maxcash_col = "#FFF";
			if(mysql_result($tset,$i,"tmaxcash")>=1000) $maxcash_col="#0F0";
			echo "<td style=\"background-color:$maxcash_col\">$".number_format(mysql_result($tset,$i,"tmaxcash"),2)."</td>";
					
			$profit = mysql_result($tset,$i,"profit")/$tentries;				
			if($profit<0) echo "<td style=\"color:#f00;\">-$".number_format($profit*-1,2)."</td>";
			else if($profit>=10) echo "<td style=\"background-color:#0f0;\">$".number_format($profit,2)."</td>";
			else echo "<td>$".number_format($profit,2)."</td>";
			
			echo "<td><a href=\"view_tournament_record.php?t=".mysql_result($tset,$i,"tournament_id")."\">record</a></td>";
			echo "<td><a href=\"edit_tournament.php?t=".mysql_result($tset,$i,"tournament_id")."\">edit</a></td>";
			echo "</tr>";
		}
	}
?>

<?php
	include("footer.php");
?>