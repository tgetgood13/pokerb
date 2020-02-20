<?php
/*
	Pokerb report pages
	Author: Tom Getgood 
*/
	include("header.php");
	
	function displayCashAmount($c, $n)
	{
		if($n<0) return "<span style=\"color:#f00;\">-".$c.number_format($n*-1,2)."</span>";
		else return $c.number_format($n,2);
	}
	
	if(isset($_GET["site"]) || isset($_GET["year"]) || isset($_GET["month"]))
	{
		$s = $_GET["site"];
		
		$y = $_GET["year"];
		$m = $_GET["month"];
		
		$view = $_GET["view"];
		
		$linkStr = "";
		if(isset($s)) $linkStr.= "&site=".$s;
		if(isset($y)) $linkStr.= "&year=".$y;
		if(isset($m)) $linkStr.= "&month=".$m;
		
		if($view=="s")
		{
			$rset = getSessionSpecificReport($db, $s, $y, $m, true);
?>
			<br/><a href="report.php?view=n<?php echo $linkStr;?>">normal</a> |  <a href="report.php?view=s<?php echo $linkStr;?>">session</a><br/>
			<table cellspacing="0" cellpadding="4">
				<tr>
				<th>Start</th>
				<th>End</th>
				<th>Day</th>
				<th>Length</th>
				<th>Entries</th>
				<th>Stake</th>
				<th>Ave. Score</th>
				<th>Won</th>
				<th>ITM</th>
				<th>1st-3rd</th>
				<th>Profit</th>
				</tr>
<?php
			$alt = true;
			for($i=0; $i<mysqli_num_rows($rset); $i++)
			{
				$alt = !$alt;
				echo "<tr".(($alt)?" class=\"alt\"":"").">";
				
				echo "<td>".mysql_result($rset,$i,"start_datetime")."</td>";
				echo "<td>".mysql_result($rset,$i,"end_datetime")."</td>";
				echo "<td>".mysql_result($rset,$i,"day")."</td>";
				echo "<td>".number_format(mysql_result($rset,$i,"tlen")/60, 2)."</td>";
				echo "<td>".mysql_result($rset,$i,"entry")."</td>";
				echo "<td>".displayCashAmount("$",mysql_result($rset,$i,"lost"))."</td>";
				echo "<td>".number_format(mysql_result($rset,$i,"tscore")/mysql_result($rset,$i,"true_entry"),2)."</td>";
				echo "<td>".displayCashAmount("$",mysql_result($rset,$i,"won"))."</td>";
				echo "<td>".mysql_result($rset,$i,"itm")."</td>";
				echo "<td>".mysql_result($rset,$i,"f23")."</td>";
				echo "<td>".displayCashAmount("$",mysql_result($rset,$i,"won")-mysql_result($rset,$i,"lost"))."</td>";
				
				echo "</tr>";
			}
			echo "</table>";
		}
		else
		{
			$rset = getSpecificReport($db, $s, $y, $m, true);
?>
			<br/><a href="report.php?view=n<?php echo $linkStr;?>">normal</a> |  <a href="report.php?view=s<?php echo $linkStr;?>">session</a><br/>
			<table cellspacing="0" cellpadding="4" style="font-size:smaller;">
				<tr>
				<th></th>
				<th>Date</th>
				<th>&nbsp;</th>
				<th>Buy-In</th>
				<th colspan="2">&nbsp;</th>
				<th>Total Buy-In</th>
				<th>Rebuys</th>
				<th>Bounty</th>
				<th>Entrants</th>
				<th>Place</th>
				<th>Cash</th>
				<th>Score</th>
				<th>Comments</th>
				</tr>
<?php
			$alt = true;
			$alt_date = "";
			for($i=0; $i<mysqli_num_rows($rset); $i++)
			{
				if($alt_date!=mysql_result($rset,$i,"session_id"))
				{
					$alt = !$alt;
					$alt_date = mysql_result($rset,$i,"session_id");
				}
			
				echo "<tr".(($alt)?" class=\"alt\"":"").">";
				echo "<td>".mysql_result($rset,$i,"session_id")."</td>";
				echo "<td width=\"100\"><a href=\"view_tournament_record.php?t=".mysql_result($rset,$i,"tournament_id")."\">".mysql_result($rset,$i,"tdate")." ".mysql_result($rset,$i,"start_time")."</a></td>";
				echo "<td>".mysql_result($rset,$i,"short_name")."</td>";
				echo "<td><a href=\"view_tournament_record.php?t=".mysql_result($rset,$i,"tournament_id")."\">$".mysql_result($rset,$i,"buy_in")."</a></td>";
				echo "<td>".mysql_result($rset,$i,"ptype")."</td>";
				echo "<td width=\"80\"".((mysql_result($rset,$i,"ttype")==2)?" style=\"background-color:#ee0\"":"")."><a href=\"view_tournament_record.php?t=".mysql_result($rset,$i,"tournament_id")."\">".mysql_result($rset,$i,"description")."</a></td>";
				echo "<td>$".number_format(mysql_result($rset,$i,"buy_in")+(mysql_result($rset,$i,"rebuys")*mysql_result($rset,$i,"rebuy_addon")),2)."</td>";
				echo "<td>".mysql_result($rset,$i,"rebuys")."</td>";
				echo "<td>".mysql_result($rset,$i,"bounties")."</td>";
				echo "<td>".mysql_result($rset,$i,"entrants")."</td>";
			
				if(mysql_result($rset,$i,"place")<=3 && mysql_result($rset,$i,"ttype")!=2) echo "<td class=\"act_green\">";
				else echo "<td>";
				echo mysql_result($rset,$i,"place")."</td>";		

				if(mysql_result($rset,$i,"first_place")>0 && mysql_result($rset,$i,"cash")==mysql_result($rset,$i,"first_place")) echo "<td class=\"act_green\">";
				else if(mysql_result($rset,$i,"cash")>0) echo "<td class=\"act_amber\">";
				else if(mysql_result($rset,$i,"ttype")==2) echo "<td style=\"background-color:#ee0\">";
				else echo "<td>";
				$bold = false;
				if(mysql_result($rset,$i,"cash")>0 && mysql_result($rset,$i,"ttype")!=2) $bold = true;
				if($bold) echo "<b>";
				echo "$".number_format(mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounties")*mysql_result($rset,$i,"bounty")),2);
				if($bold) echo "</b>";
				echo "</td>";
			
				if(mysql_result($rset,$i,"score")>7) echo "<td class=\"act_green\">";
				else if(mysql_result($rset,$i,"score")>0 && mysql_result($rset,$i,"score")<5) echo "<td class=\"act_black\">";
				else echo "<td>";
				echo mysql_result($rset,$i,"score")."</td>";
		
				echo "<td><em>".mysql_result($rset,$i,"comments")."</em></td>";
			
				echo "</tr>";
			}
			echo "</table>";
		}
	}
	else
	{
	$rset = getMonthlyReport($db, true);

	$sites = array("PP","FT","88","BF","PS","PD");
	$site_lost = array(0,0,0,0,0,0);
	$site_won = array(0,0,0,0,0,0);
	$site_entry = array(0,0,0,0,0,0);
	$site_profit = array(0,0,0,0,0,0);
	$site_itm = array(0,0,0,0,0,0);
	$site_123 = array(0,0,0,0,0,0);
	$site_clength = array(0,0,0,0,0,0);
	
	$year_profit = array(0,0,0,0,0,0);
	$year_earnings = array(0,0,0,0,0,0);
	
	$month_pass = -1;
	$site_ctr = 0;
	$year_ctr = 0;
	
	$month_lost = 0;
	$month_won = 0;
	$month_entry = 0;
	$month_itm = 0;
	$month_123 = 0;
	$month_clength = 0;
	
	$ttotal = 0;
	$tentry = 0;
	$tclength = 0;
?>
	<table style="font-size:smaller">
	<tr>
		<th>Year</td>
		<th>Month</td>
		<th colspan="7" style="background-color:#FF9900;color:#fff;">PP</th>
		<th colspan="7" style="background-color:#333333;color:#fff;">FT</th>
		<th colspan="7" style="background-color:#0000FF;color:#fff;">88</th>
		<th colspan="7" style="background-color:#00FFFF;color:#000;">BF</th>
		<th colspan="7" style="background-color:#FF0000;color:#000;">PS</th>
		<th colspan="7" style="background-color:#00FF00;color:#000;">PD</th>
		<th>Total Entry</th>
		<th>Total ITM</th>
		<th>Total CHours</th>
		<th>Total Won</th>
		<th>Total 1-3</th>
		<th>Monthly Payment</th>
		<th>Yearly Profit</th>
		<th>Year Total Payment</th>
	</tr>
	<tr>
		<th colspan="2">&nbsp;</th>
<?php
		for($j=0;$j<count($sites);$j++)
		{
?>
		<th>Entry</th>
		<th>CHours</th>
		<th>Stake</th>
		<th>Cash</th>
		<th>ITM</th>
		<th>Profit</th>
		<th>1st-3rd</th>
<?php
		}
?>
		<th colspan="2">&nbsp;</th>
	</tr>
<?php
	$tr_ctr = 0;

	while($tr_ctr<mysqli_num_rows($rset))
	{
		$month = mysql_result($rset,$tr_ctr,"month");
		
		// new month check
		if($month!=$month_pass)
		{
			//echo $site_ctr." < ".count($sites)."<br/>";
			if($site_ctr<count($sites))
			{
				for($k=0; $k<(count($sites)-$site_ctr); $k++)
				{
					echo "<td colspan=\"7\">&nbsp;</td>";
				}
			}
			
			if($month_pass!=-1)
			{
				echo "<td>".$month_entry."</td>";
				echo "<td>".$month_itm." (".number_format($month_itm/$month_entry,2)."%)</td>";
				echo "<td>".number_format($month_clength/60,2)."</td>";
				echo "<td align=\"right\">".displayCashAmount("$", $month_won-$month_lost)."</td>";
				echo "<td>".$month_123." (".number_format($month_123/$month_itm,2)."%)</td>";
				
				echo "<td style=\"color:#0c0;\">".displayCashAmount("�", ($month_lost*0.08)*0.6)."</td>";
				
				echo "<td>";
				if(mysql_result($rset,$tr_ctr,"month")==1) echo "<b>".displayCashAmount("$",$year_profit[$year_ctr])."</b>";
				echo "</td>";
				echo "<td style=\"color:#0c0;\">";
				if(mysql_result($rset,$tr_ctr,"month")==1) echo displayCashAmount("�", ($year_earnings[$year_ctr]*0.08)*0.6);
				echo "</td>";
	
				echo "</tr>";
			}
			
			echo "<tr>";
			echo "<td><a href=\"report.php?year=".mysql_result($rset,$tr_ctr,"year")."\">".mysql_result($rset,$tr_ctr,"year")."</a></td>";
			echo "<td><a href=\"report.php?year=".mysql_result($rset,$tr_ctr,"year")."&month=".mysql_result($rset,$tr_ctr,"month")."\">".mysql_result($rset,$tr_ctr,"month")."</a></td>";
			$month_pass = $month;
			
			$month_lost = 0;
			$month_won = 0;
			$month_entry = 0;
			$month_itm = 0;
			$month_123 = 0;
			$month_clength = 0;
			
			$site_ctr = 0;
			
			if(mysql_result($rset,$tr_ctr,"month")==1) $year_ctr+=1;
		}
		
		//echo $sites[$site_ctr]." ".$site_ctr." ".mysql_result($rset,$i,"short_name")."<br/>";
		if($sites[$site_ctr]==mysql_result($rset,$tr_ctr,"short_name"))
		{
			echo "<td><a href=\"report.php?site=".$sites[$site_ctr]."&year=".mysql_result($rset,$tr_ctr,"year")."&month=".mysql_result($rset,$tr_ctr,"month")."\">".mysql_result($rset,$tr_ctr,"entry")."</a></td>";
			echo "<td>".number_format(mysql_result($rset,$tr_ctr,"clength")/60,2)."</td>";
			echo "<td align=\"right\">$".number_format(mysql_result($rset,$tr_ctr,"lost"),2)."</td>";
			echo "<td align=\"right\">$".number_format(mysql_result($rset,$tr_ctr,"won"),2)."</td>";
			echo "<td>".mysql_result($rset,$tr_ctr,"itm")."</td>";
			echo "<td align=\"right\">".displayCashAmount("$", mysql_result($rset,$tr_ctr,"profit"))."</td>";
			echo "<td>".mysql_result($rset,$tr_ctr,"f23")."</td>";
		
			$month_lost += mysql_result($rset,$tr_ctr,"lost");
			$month_won += mysql_result($rset,$tr_ctr,"won");
			$month_entry += mysql_result($rset,$tr_ctr,"entry");
			$month_itm += mysql_result($rset,$tr_ctr,"itm");
			$month_123 += mysql_result($rset,$tr_ctr,"f23");
			$month_clength += mysql_result($rset,$tr_ctr,"clength");
		
			$ttotal += (mysql_result($rset,$tr_ctr,"won")-mysql_result($rset,$tr_ctr,"lost"));
			$tentry += mysql_result($rset,$tr_ctr,"entry");
			$tclength+= mysql_result($rset,$tr_ctr,"clength");
		
			$site_entry[$site_ctr] += mysql_result($rset,$tr_ctr,"entry");
			$site_lost[$site_ctr] += mysql_result($rset, $tr_ctr, "lost");
			$site_won[$site_ctr] += mysql_result($rset,$tr_ctr,"won");
			$site_profit[$site_ctr] += mysql_result($rset, $tr_ctr, "profit");
			$site_itm[$site_ctr] += mysql_result($rset,$tr_ctr,"itm");
			$site_123[$site_ctr] += mysql_result($rset,$tr_ctr,"f23");
			$site_clength[$site_ctr] += mysql_result($rset,$tr_ctr,"clength");
			
			$year_profit[$year_ctr] += mysql_result($rset, $tr_ctr, "profit");
			$year_earnings[$year_ctr] += mysql_result($rset,$tr_ctr,"lost");
			
			$tr_ctr ++;
		}
		else
		{
			echo "<td colspan=\"7\">&nbsp;</td>";

			if($site_ctr+1==count($sites)) $tr_ctr ++;
		}
		$site_ctr++;

	}
	
	if($site_ctr<count($sites))
	{
		for($k=0; $k<(count($sites)-$site_ctr); $k++)
		{
			echo "<td colspan=\"6\">&nbsp;</td>";
		}
	}
			
	echo "<td>".$month_entry."</td>";
	echo "<td>".$month_itm." (".number_format($month_itm/$month_entry,2)."%)</td>";
	echo "<td>".number_format($month_clength/60,2)."</td>";
	echo "<td align=\"right\">".displayCashAmount("$", $month_won-$month_lost)."</td>";
	echo "<td>".$month_123." (".number_format($month_123/$month_itm,2)."%)</td>";
	
	echo "<td style=\"color:#0c0;\">".displayCashAmount("�", ($month_lost*0.08)*0.6)."</td>";
	
	echo "<td><b>";
	echo displayCashAmount("$", $year_profit[$year_ctr]);
	echo "</b></td>";
	echo "<td style=\"color:#0c0;\">";
	echo displayCashAmount("�", ($year_earnings[$year_ctr]*0.08)*0.6);
	echo "</td>";
	
	echo "</tr>";
	
	// totals
	echo "<tr>";
	echo "<td colspan=\"2\">Total</td>";
	for($j=0;$j<count($sites);$j++)
	{
		echo "<td>".$site_entry[$j]."</td>";
		echo "<td>".number_format($site_clength[$j]/60,2)."</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>".displayCashAmount("$", $site_won[$j])."</td>";
		echo "<td>".$site_itm[$j]." (".number_format($site_itm[$j]/$site_entry[$j],2)."%)</td>";
		echo "<td>".displayCashAmount("$", $site_profit[$j])."</td>";
		echo "<td>".$site_123[$j]." (".number_format($site_123[$j]/$site_itm[$j],2)."%)</td>";
		//$site_lost = array();
		//$site_won = array();
		
	}
	echo "<td>$tentry</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>".number_format($tclength/60,2)."</td>";
	echo "<td align=\"right\"><b>$".number_format($ttotal,2)."</b></td>";
	
	echo "</tr>";
?>
	<tr>
		<th>&nbsp;</td>
		<th>&nbsp;</td>
		<th colspan="7" style="background-color:#FF9900;color:#fff;">PP</th>
		<th colspan="7" style="background-color:#333333;color:#fff;">FT</th>
		<th colspan="7" style="background-color:#0000FF;color:#fff;">88</th>
		<th colspan="7" style="background-color:#00FFFF;color:#000;">BF</th>
		<th colspan="7" style="background-color:#FF0000;color:#000;">PS</th>
		<th colspan="7" style="background-color:#00FF00;color:#000;">PD</th>
		<th colspan="4">&nbsp;</td>
		<th>&nbsp;</td>
	</tr>
<?php	
	echo "</table>";
	}
?>

<?php
	include("footer.php");
?>