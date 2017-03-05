<?php
/*
	Pokerb experimental page looking at results when playing up (a 'shot')
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php
	$y = 2012;
	$b = 54;
	if(isset($_GET["year"])) $y = $_GET["year"];
	if(isset($_GET["buyin"])) $b = $_GET["buy_in"];
	
	$rset = getShotsByTypeAndThreshold(1, $b, $y, true);

	$entries = 0;
	$total_buyins = 0;
	$total_cash = 0;
	$cashes = 0;
?>
	<table cellspacing="0" cellpadding="0">
	<tr>
		<th>Date Time</th>
		<th>Site</th>
		<th>Length</th>
		<th>First</th>
		<th>Buy-In</th>
		<th>Rebuys</th>
		<th>Bounties</th>
		<th>Entrants</th>
		<th>Place</th>
		<th>Score</th>
		<th>Cash</th>
		<th>Comments</th>
<?php
	for($i=0; $i<mysql_numrows($rset); $i++)
	{
		echo "<tr>";
		
		$length = mysql_result($rset,$i,"tlength")/60;
		$buy_in = mysql_result($rset,$i,"buy_in")+(mysql_result($rset,$i,"rebuy_addon")*mysql_result($rset,$i,"rebuys"));
		
		echo "<td>".mysql_result($rset,$i,"tdate")." ".mysql_result($rset,$i,"start_time")."</td>";
		echo "<td>".mysql_result($rset,$i,"short_name")."</td>";
		echo "<td>".number_format($length,2)."</td>";
		if(mysql_result($rset,$i,"first_place")>0) echo "<td>$".number_format(mysql_result($rset,$i,"first_place"))."</td>";
		else echo "<td><i>unk</i></td>";
		echo "<td>$".number_format($buy_in,2)."</td>";
		if(mysql_result($rset,$i,"rebuys")>0) echo "<td>".mysql_result($rset,$i,"rebuys")."</td>";
		else echo "<td>-</td>";
		if(mysql_result($rset,$i,"bounties")>0) echo "<td>".mysql_result($rset,$i,"bounties")."</td>";
		else echo "<td>-</td>";
		echo "<td>".number_format(mysql_result($rset,$i,"entrants"))."</td>";
		echo "<td>".mysql_result($rset,$i,"place")."</td>";
		if(mysql_result($rset,$i,"score")>0) echo "<td>".mysql_result($rset,$i,"score")."</td>";
		else echo "<td>n/a</td>";
		if(mysql_result($rset,$i,"cash")>0)
		{
			echo "<td style=\"background-color:#0f0\">";
			$total_cash += mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounty")*mysql_result($rset,$i,"bounties"));
			$cashes ++;
		}
		else echo "<td>";
		echo "$".number_format(mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounty")*mysql_result($rset,$i,"bounties")),2)."</td>";
		echo "<td style=\"width:50%\">".mysql_result($rset,$i,"comments")."</td>";
		echo "</tr>";
		
		$total_buyins += $buy_in;
		$entries ++;
	}
?>
	</table>

<?php
	echo "<b>";
	echo "Entries $entries<br/>";
	echo "Cashes $cashes (".number_format(($cashes/$entries)*100,2)."%)<br/>";
	echo "Cash $".number_format($total_cash,2)." ($".number_format($total_cash/$entries,2)." per entry)<br/>";
	echo "Buy-Ins $".number_format($total_buyins,2)." ($".number_format($total_buyins/$entries,2)." average entry)<br/>";
	echo "</b>";
	
	include("footer.php");
?>