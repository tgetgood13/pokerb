<?php
/*
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php
	$sites = "";
	if(isset($_POST["site"]))
	{
		foreach ($_POST["site"] as $s)
		{
			$sites.=$s.",";
		}		
	}
	if($sites=="") $sites = "ALL";
	
	$games = "";
	if(isset($_POST["game"]))
	{
		foreach ($_POST["game"] as $g)
		{
			$games.=$g.",";
		}		
	}
	if($games=="") $games = "ALL";

	$types = "";
	if(isset($_POST["type"]))
	{
		foreach ($_POST["type"] as $t)
		{
			$types.=$t.",";
		}		
	}
	if($types=="") $types = "ALL";
	
	$levels = "";
	if(isset($_POST["level"]))
	{
		foreach ($_POST["level"] as $l)
		{
			$levels.=$l.",";
		}		
	}
	if($levels=="") $levels = "ALL";

	$minfirst = 0;
	if(isset($_POST["minfirst"]))
	{
		$minfirst = $_POST["minfirst"];
	}
	
	$num_sites = 7;
	$sites_in_play = "";

	$tset = getAllTournaments($db, false);
	$sset = getAllSites($db, true);
	$tgset = getAllGames($db, true);
	$tyset = getAllTypes($db, false);
	$dset = getAllDays($db, false);
	$lset = getAllLevels($db, false);
?>

<br/>
<br/>

<form action="index.php" method="post">
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
Level:<br/>
<input type="checkbox" name="level[]" value="1" checked="checked"/> 1&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="2" checked="checked"/> 2&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="3" checked="checked"/> 3&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="4" checked="checked"/> 4&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="5" checked="checked"/> 5&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="6" checked="checked"/> 6&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="7"/> 7&nbsp;&nbsp;
<input type="checkbox" name="level[]" value="8"/> 8<br/>
<br/>
Min payout for first: $<input type="text" name="minfirst"/><br/>
<input type="submit"/>
</form>

<?php
	$tspan = 4;
	$tdiff = 5;  echo "<b style=\"color:red\">Time Difference set at +$tdiff hrs</b><br/>";
	$tnow = mktime(date("H")+$tdiff, date("i"), date("s"), date("n"), date("j"), date("y"));
	$twindow = mktime(date("H")+$tdiff+$tspan, date("i"), date("s"), date("n"), date("j"), date("y"));
	$hr = date("H", $twindow);
	$mn = date("i", $twindow);
	
	$day_now = date("D", $tnow);
	$day_later = date("D", $twindow);
	
	echo "<table>";
	echo "<tr>";
	echo "<th>Time</th>";
	echo "<th>Site</th>";
	echo "<th>Buy-In</th>";
	echo "<th>Desc</th>";
	echo "<th>Game</th>";
	echo "<th>&nbsp;</th>";
	echo "<th>&nbsp;</th>";
	echo "<th>Av. First</th>";
	echo "<th>Av. Field</th>";
	echo "<th>Av. Len</th>";
	echo "<th colspan=\"2\">ITM</th>";
	echo "<th colspan=\"2\">1st-3rd</th>";
	echo "<th colspan=\"2\">Best</th>";
	echo "<th>Profit</th>";
	echo "<th colspan=\"2\">$day_now ITM</th>";
	echo "<th colspan=\"2\">1st-3rd</th>";
	echo "<th colspan=\"2\">Best</th>";
	echo "<th>Profit</th>";
	echo "</tr>";
	
	if($hr<$tdiff)
	{
		$scset1 = getUpcomingSchedule($db, date("Hi",$tnow), "2359", $day_now, true);
		$scset2 = getUpcomingSchedule($db, "0000", $hr.$mn, $day_later, true);

		displaySchedule($db, $scset1, $sset, $levels, $games, $types, $minfirst, $sites);
		displaySchedule($db, $scset2, $sset, $levels, $games, $types, $minfirst, $sites);
	}
	else
	{
		$scset = getUpcomingSchedule($db, date("Hi",$tnow), $hr.$mn, $day_now, true);

		displaySchedule($db, $scset, $sset, $levels, $games, $types, $minfirst, $sites);
	}
	
	echo "</table>";
?>
<b>Add tournament</b>:<br/>
<form method="post" action="create_tournament.php">
<select name="site">
<?php
	for($i=0; $i<$num_sites; $i++)
	{
		echo "<option value=\"".mysql_result($sset,$i,"site_id")."\">".mysql_result($sset,$i,"short_name")."</option>";
	}
?>
</select>
Start Time: <input type="text" name="start_time"/> <i>hhmm</i><br/>
Buy-In: $<input type="text" name="buy_in"/><br/>
Effective Buy-In: $<input type="text" name="ef_buy_in"/><br/>
<select name="game">
<?php
	for($i=0; $i<mysqli_num_rows($tgset); $i++)
	{
		echo "<option value=\"".mysql_result($tgset,$i,"game_id")."\">".mysql_result($tgset,$i,"short_name")."</option>";
	}
?>
</select>
<select name="type">
<?php
	for($i=0; $i<mysqli_num_rows($tyset); $i++)
	{
		echo "<option value=\"".mysql_result($tyset,$i,"type_id")."\">".mysql_result($tyset,$i,"short_name")."</option>";
	}
?>
</select>
<select name="ttype">
	<option value="4">SHOT</option>
	<option value="3">BIG</option>
	<option value="1">CASH</option>
	<option value="0">BANK</option>
	<option value="2">QUAL</option>
</select><br/>
Description: <input type="text" name="description"/>
<select name="days">
<?php
	for($i=0; $i<mysqli_num_rows($dset); $i++)
	{
		echo "<option value=\"".mysql_result($dset,$i,"days_id")."\">".mysql_result($dset,$i,"description")."</option>";
	}
?>
</select>
(Possible 3+) Rebuy? <input type="checkbox" name="rebuy"/><br/>
Rebuy/Add-On Amount <input type="text" name="rebuy_addon" value="0" /> Bounty <input type="text" name="bounty" value="0"/><br/>
<input type="submit" value="Submit"/>
</form>

<b>Edit tournament</b>:<br/>
<form action="edit_tournament.php" method="post">
<select name="t">
<?php
	for($i=0; $i<mysqli_num_rows($tset); $i++)
	{
		echo "<option value=\"".mysql_result($tset,$i,"tournament_id")."\">".mysql_result($tset,$i,"site_name")." ".mysql_result($tset,$i,"start_time")." $".mysql_result($tset,$i,"buy_in")."</option>";
	}
?>
</select>
<input type="submit" value="Go"/>
</form>
<br/>defs:<br/> SHOT - more than $5k for first, but tourney can also be qualified for<br/>
BIG - more than $5k for first<br/>
CASH - more than $1k for first
<?php
	include("footer.php");

function displaySchedule($db, $scset, $sset, $level_string, $game_string, $type_string, $minfirst, $site_string)
{
	$levels = getAllLevels($db, false);

	for($i=0; $i<mysqli_num_rows($scset); $i++)
	{
		$buy_in = mysql_result($scset,$i,"buy_in");
		$rebuy = mysql_result($scset,$i,"rebuy");
		$ttype = mysql_result($scset,$i,"ttype");
						
		$maxEntry = -1;
		

			if($level_string!="ALL")
			{
				foreach (explode(",",$level_string) as $lv)
				{
					for($l=0; $l<mysqli_num_rows($levels); $l++)
					{
						if($lv==mysql_result($levels,$l,"level_id"))
						{
							// matched level
							if($ttype!=2 && $rebuy==0)
							{
								$maxEntry = mysql_result($levels,$l,"max_stake");
							}
							
							// rby
							if($ttype!=2 && $rebuy==1)
							{
								$maxEntry = mysql_result($levels,$l,"rebuy_max");
							}

							// qual
							if($ttype==2 && $rebuy==0)
							{
								$maxEntry = mysql_result($levels,$l,"qual_max");
							}
							
							// qual rby
							if($ttype==2 && $rebuy==1)
							{
								$maxEntry = mysql_result($levels,$l,"qual_rebuy");
							}
							break;
						}
					}
				}
			}
			
			$tfirst = mysql_result($scset,$i,"tfirst");
			$tfirstx = mysql_result($scset,$i,"tfirstx");
			if($tfirstx==0) $tfirstx=1;
			
			if( ($site_string=="ALL" || strstr($site_string, mysql_result($scset,$i,"site_name").",")) && ($level_string=="ALL" || $buy_in<=$maxEntry) && ($game_string=="ALL" || strstr($game_string, mysql_result($scset,$i,"game_desc").",")) && ($type_string=="ALL" || mysql_result($scset,$i,"type_desc")=="??" || strstr($type_string, mysql_result($scset,$i,"type_desc").",")) && (($tfirst/$tfirstx)>=$minfirst) )
			{
				echo "<tr>";
				echo "<td>".mysql_result($scset,$i,"start_time")."</td>";
				echo "<td style=\"background-color:".mysql_result($scset,$i,"display_color")."\">".mysql_result($scset,$i,"site_name")."</td>";
							
				echo "<td>$".$buy_in."</td>";
				//echo "<td>$".$buy_in." (".$siteMaxBuy.")</td>";
											
				echo "<td>".mysql_result($scset,$i,"description")."</td>";
				echo "<td>".mysql_result($scset,$i,"game_desc")."</td>";			
				echo "<td>".mysql_result($scset,$i,"type_desc")."</td>";
				
				switch($ttype)
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
						echo "<td style=\"background-color:#0f0\">BIG</td>";
						break;
					case 4:
						echo "<td style=\"background-color:#090\">SHOT</td>";
						break;
				}
				
				// stats
				if($ttype!=2)
				{
					echo "<td>$".number_format($tfirst/$tfirstx,2)."</td>";
				 //SUM(tr.score) AS tscore, COUNT(tr.score>0) AS tscorex, SUM(tr.first_place) AS tfirst, COUNT(tr.first_place>0) AS tfirstx, MAX(tr.cash) AS tmaxcash, COUNT(tr.id) AS tentries, MIN(tr.place) AS tminplace FROM tPkrTournament t";
	
					$tentries = mysql_result($scset,$i,"tentries");
					if($tentries==0) $tentries=1;
					
					echo "<td>".number_format( mysql_result($scset,$i,"total_ent")/$tentries ,2)."</td>";
					
					echo "<td>".number_format((mysql_result($scset,$i,"tlength")/$tentries)/60, 2)."</td>";
					
					$itm_w_zero = mysql_result($scset,$i,"itm");
					if($itm_w_zero<=0) $itm_w_zero=0;
					echo "<td>".$itm_w_zero."/".$tentries."</td>";
					$itm = ((mysql_result($scset,$i,"itm")/$tentries))*100;
					$itm_col = "#FFF";
					if($itm>=20) $itm_col="#0F0";
					echo "<td style=\"background-color:$itm_col\">".number_format($itm,2)."%</td>";
					
					$f123_w_zero = mysql_result($scset,$i,"f123");
					if($f123_w_zero<=0) $f123_w_zero=0;
					echo "<td>".$f123_w_zero."/".$itm_w_zero."</td>";
					$f123 = 0;
					if($itm_w_zero>0) $f123 = ((mysql_result($scset,$i,"f123")/$itm_w_zero))*100;
					$f123_col = "#FFF";
					if($f123>=10) $f123_col="#0F0";
					echo "<td style=\"background-color:$f123_col\">".number_format($f123,2)."%</td>";
					
					$minplace_col = "#FFF";
					if(mysql_result($scset,$i,"tminplace")==1) $minplace_col="#0F0";
					echo "<td style=\"background-color:$minplace_col\">".mysql_result($scset,$i,"tminplace")."</td>";
					
					$maxcash_col = "#FFF";
					if(mysql_result($scset,$i,"tmaxcash")>=1000) $maxcash_col="#0F0";
					echo "<td style=\"background-color:$maxcash_col\">$".number_format(mysql_result($scset,$i,"tmaxcash"),2)."</td>";
					
					$profit = mysql_result($scset,$i,"profit")/$tentries;				
					if($profit<0)
					{
						if($profit<=-10) echo "<td style=\"background-color:#f00;color:#fff;\">-$".number_format($profit*-1,2)."</td>";
						else echo "<td style=\"color:#f00;\">-$".number_format($profit*-1,2)."</td>";
					}
					else if($profit>=10) echo "<td style=\"background-color:#0f0;\">$".number_format($profit,2)."</td>";
					else echo "<td>$".number_format($profit,2)."</td>";
					
					// day o le week analysis
					$tentries = mysql_result($scset,$i,"tentries2");
					if($tentries==0) $tentries=1;
					
					$itm_w_zero = mysql_result($scset,$i,"itm2");
					if($itm_w_zero<=0) $itm_w_zero=0;
					echo "<td>".$itm_w_zero."/".$tentries."</td>";
					$itm = ((mysql_result($scset,$i,"itm2")/$tentries))*100;
					$itm_col = "#FFF";
					if($itm>=20) $itm_col="#0F0";
					echo "<td style=\"background-color:$itm_col\"><b>".number_format($itm,2)."%</b></td>";
					
					$f123_w_zero = mysql_result($scset,$i,"f1232");
					if($f123_w_zero<=0) $f123_w_zero=0;
					echo "<td><b>".$f123_w_zero."/".$itm_w_zero."</b></td>";
					$f123 = 0;
					if($itm_w_zero>0) $f123 = ((mysql_result($scset,$i,"f1232")/$itm_w_zero))*100;
					$f123_col = "#FFF";
					if($f123>=10) $f123_col="#0F0";
					echo "<td style=\"background-color:$f123_col\"><b>".number_format($f123,2)."%</b></td>";
					
					$minplace_col = "#FFF";
					if(mysql_result($scset,$i,"tminplace2")==1) $minplace_col="#0F0";
					echo "<td style=\"background-color:$minplace_col\"><b>".mysql_result($scset,$i,"tminplace2")."</b></td>";
					
					$maxcash_col = "#FFF";
					if(mysql_result($scset,$i,"tmaxcash2")>=1000) $maxcash_col="#0F0";
					echo "<td style=\"background-color:$maxcash_col\"><b>$".number_format(mysql_result($scset,$i,"tmaxcash2"),2)."</b></td>";
					
					$profit = mysql_result($scset,$i,"profit2")/$tentries;				
					if($profit<0)
					{
						if($profit<=-10) echo "<td style=\"background-color:#f00;color:#fff;\"><b>-$".number_format($profit*-1,2)."</b></td>";
						else echo "<td style=\"color:#f00;\"><b>-$".number_format($profit*-1,2)."</b></td>";
					}
					else if($profit>=10) echo "<td style=\"background-color:#0f0;\"><b>$".number_format($profit,2)."</b></td>";
					else echo "<td><b>$".number_format($profit,2)."</b></td>";
				}
				else
				{
					echo "<td colspan=\"9\" style=\"background-color:#ccc\"></td>";
				}
				echo "<td><a href=\"view_tournament_record.php?t=".mysql_result($scset,$i,"tournament_id")."\">record</a></td>";
				echo "<td><a href=\"edit_tournament.php?t=".mysql_result($scset,$i,"tournament_id")."\">edit</a></td>";
			
				echo "</tr>";
			}
			else
			{
				//
			}
	}
}
?>