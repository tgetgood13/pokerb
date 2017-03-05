<?php
/*
	Author: Tom Getgood 
*/
	include("header.php");

	if(isset($_POST["site"]))
	{
		$t = post_checkInt("t");
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
		$ignore = post_checkString("ignore");
		if($ignore=="on") $ignore=1;
		else $ignore=0;
		$defunct = post_checkString("defunct");
		if($defunct=="on") $defunct=1;
		else $defunct=0;
		
		updateTournament($t, $site,$start_time,$buy_in,$ef_buy_in,$type,$ttype,$game,$description,$days,$rebuy,$rebuy_addon,$bounty, $ignore, $defunct, true);

		$hourly_rate = post_checkDouble("hourly_rate");
		$average_score = post_checkDouble("average_score");
		$best_profit = post_checkDouble("best_profit");
		
		updateTournamentStats($t, $hourly_rate, $average_score, $best_profit, true);
		
		echo "Tournament updated OK";
	}
	else if(isset($_POST["t"]) || isset($_GET["t"]))
	{
		$t = post_checkInt("t");
		if($t==-1) $t = $_GET["t"];
		
		$num_sites = 7;

		$tset = getTournamentById($t, true);
		$tsset = getTournamentStatsById($t, true);
		$sset = getAllSites(false);
		$tgset = getAllGames(true);
		$tyset = getAllTypes(false);
		$dset = getAllDays(false);
		$lset = getAllLevels(false);
		
		$hourly_rate = mysql_result($tsset,0,"hourly_rate");
		$average_score = mysql_result($tsset,0,"average_score");
		$best_profit = mysql_result($tsset,0,"best_profit");
?>
		<b>Edit tournament</b>:<br/>
		<form method="post" action="edit_tournament.php">
		<select name="site">
<?php
			for($i=0; $i<$num_sites; $i++)
			{
				$selected = "";
				if(mysql_result($tset,0,"site_id")==mysql_result($sset,$i,"site_id")) $selected=" selected=\"selected\"";
				
				echo "<option value=\"".mysql_result($sset,$i,"site_id")."\"".$selected.">".mysql_result($sset,$i,"short_name")."</option>";
			}
?>
		</select>
		Start Time: <input type="text" name="start_time" value="<?php echo mysql_result($tset,0,"start_time");?>"/> <i>hhmm</i><br/>
		Buy-In: $<input type="text" name="buy_in" value="<?php echo mysql_result($tset,0,"buy_in");?>"/><br/>
		Effective Buy-In: $<input type="text" name="ef_buy_in" value="<?php echo mysql_result($tset,0,"ef_buy_in");?>"/><br/>
		<select name="game">
<?php
			for($i=0; $i<mysql_numrows($tgset); $i++)
			{
				$selected = "";
				if(mysql_result($tset,0,"game_id")==mysql_result($tgset,$i,"game_id")) $selected=" selected=\"selected\"";
				
				echo "<option value=\"".mysql_result($tgset,$i,"game_id")."\"".$selected.">".mysql_result($tgset,$i,"short_name")."</option>";
			}
?>
		</select>
		<select name="type">
<?php
			for($i=0; $i<mysql_numrows($tyset); $i++)
			{
				$selected = "";
				if(mysql_result($tset,0,"type_id")==mysql_result($tyset,$i,"type_id")) $selected=" selected=\"selected\"";
				
				echo "<option value=\"".mysql_result($tyset,$i,"type_id")."\"".$selected.">".mysql_result($tyset,$i,"short_name")."</option>";
			}
?>
		</select>
		<select name="ttype">
			<option value="4" <?php if(mysql_result($tset,0,"ttype")==4) echo "selected=\"selected\"";?>>SHOT</option>
			<option value="3" <?php if(mysql_result($tset,0,"ttype")==3) echo "selected=\"selected\"";?>>BIG</option>
			<option value="1" <?php if(mysql_result($tset,0,"ttype")==1) echo "selected=\"selected\"";?>>CASH</option>
			<option value="0" <?php if(mysql_result($tset,0,"ttype")==0) echo "selected=\"selected\"";?>>BANK</option>
			<option value="2" <?php if(mysql_result($tset,0,"ttype")==2) echo "selected=\"selected\"";?>>QUAL</option>
		</select><br/>
		Description: <input type="text" name="description" value="<?php echo mysql_result($tset,0,"description");?>"/>
		<select name="days">
<?php
			for($i=0; $i<mysql_numrows($dset); $i++)
			{
				$selected = "";
				if(mysql_result($tset,0,"days_id")==mysql_result($dset,$i,"days_id")) $selected=" selected=\"selected\"";
				
				echo "<option value=\"".mysql_result($dset,$i,"days_id")."\"".$selected.">".mysql_result($dset,$i,"description")."</option>";
			}
?>
		</select>
		(Possible 3+) Rebuy? <input type="checkbox" name="rebuy" <?php if(mysql_result($tset,0,"rebuy")==1) echo "checked=\"checked\"";?>/><br/>
		Rebuy/Add-On Amount <input type="text" name="rebuy_addon" value="<?php echo mysql_result($tset,0,"rebuy_addon");?>" /> Bounty <input type="text" name="bounty" value="<?php echo mysql_result($tset,0,"bounty");?>"/><br/>
		Ignore? <input type="checkbox" name="ignore" <?php if(mysql_result($tset,0,"ignore_t")==1) echo "checked=\"checked\"";?>/><br/>
		Defunct? <input type="checkbox" name="defunct" <?php if(mysql_result($tset,0,"defunct_t")==1) echo "checked=\"checked\"";?>/><br/>
		<br/><br/>
		Hourly Rate: <input type="text" name="hourly_rate" value="<?php echo $hourly_rate;?>"/><br/>
		Average Score: <input type="text" name="average_score" value="<?php echo $average_score;?>" /><br/>
		Best Profit: <input type="text" name="best_profit" value="<?php echo $best_profit;?>" /><br/>
		
		<input type="submit" value="Submit"/>
		<input type="hidden" name="t" value="<?php echo $t;?>"/>
		</form>
<?php
	}
	include("footer.php");
?>