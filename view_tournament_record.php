<?php
/*
	Pokerb individual tourney page
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php

	$tid = $_GET["t"];

	$rset = getTournamentRecordByTournamentId($tid, true);

	$tentries = mysql_numrows($rset);
	$tlength = 0;
	$tbuyin = 0;
	$trebuys = 0;
	$tbounties = 0;
	$tcash = 0;
	$tscoreentries = 0;
	$tscore = 0;
	$tfirstentries = 0;
	$tfirst = 0;
?>

	<table style="font-size:smaller;">
	<tr>
		<th>Date</th>
		<th>Length</th>
		<th>First Place</th>
		<th>Total Buy-In</th>
		<th>Rebuys</th>
		<th>Bounties</th>
		<th>Entrants</th>
		<th>Place</th>
		<th>Score</th>
		<th>Cash</th>
		<th>Comments</th>
	</tr>
<?php
	for($i=0; $i<mysql_numrows($rset); $i++)
	{
		echo "<tr>";
		
		$length = mysql_result($rset,$i,"tlength")/60;
		$buy_in = mysql_result($rset,$i,"buy_in")+(mysql_result($rset,$i,"rebuy_addon")*mysql_result($rset,$i,"rebuys"));
		
		echo "<td>".mysql_result($rset,$i,"tdate")." ".mysql_result($rset,$i,"start_time")." ".mysql_result($rset,$i,"day")."</td>";
		echo "<td>".number_format($length,2)."</td>";
		echo "<td>".mysql_result($rset,$i,"first_place")."</td>";
		echo "<td><b>$".number_format($buy_in,2)."</b></td>";
		echo "<td>".mysql_result($rset,$i,"rebuys")."</td>";
		echo "<td>".mysql_result($rset,$i,"bounties")."</td>";
		echo "<td>".mysql_result($rset,$i,"entrants")."</td>";
		
		if(mysql_result($rset,$i,"cash")>0) echo "<td class=\"act_amber\">";
		else echo "<td>";
		echo mysql_result($rset,$i,"place")."</td>";
		
		if(mysql_result($rset,$i,"score")>7) echo "<td class=\"act_green\">";
		else if(mysql_result($rset,$i,"score")>0 && mysql_result($rset,$i,"score")<5) echo "<td class=\"act_black\">";
		else echo "<td>";
		echo mysql_result($rset,$i,"score")."</td>";
		
		if(mysql_result($rset,$i,"first_place")>0 && mysql_result($rset,$i,"cash")==mysql_result($rset,$i,"first_place")) echo "<td class=\"act_green\">";
		else if(mysql_result($rset,$i,"cash")>0) echo "<td class=\"act_amber\">";
		else echo "<td>";
		echo "<b>$".number_format(mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounty")*mysql_result($rset,$i,"bounties")),2)."</b></td>";
		
		echo "<td>".mysql_result($rset,$i,"comments")."</td>";
		
		$tlength += $length;
		$tbuyin += $buy_in;
		$trebuys += mysql_result($rset,$i,"rebuys");
		$tbounties += mysql_result($rset,$i,"bounties");
		$tcash += mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounty")*mysql_result($rset,$i,"bounties"));
		if(mysql_result($rset,$i,"score")>0)
		{
			$tscoreentries += 1;
			$tscore += mysql_result($rset,$i,"score");
		}
		if(mysql_result($rset,$i,"first_place")>0)
		{
			$tfirstentries += 1;
			$tfirst += mysql_result($rset,$i,"first_place");
		}
		
		echo "</tr>";
	}
	
	if($tentries>0)
	{
		if($tscoreentries==0) $tscoreentries = 1;
		if($tfirstentries==0) $tfirstentries = 1;
?>
		<tr>
		<td>&nbsp;</td>
		<td><?php echo number_format($tlength/$tentries,2);?></td>
		<td><?php echo number_format($tfirst/$tfirstentries,2);?></td>
		<td><b>$<?php echo number_format($tbuyin/$tentries,2);?></b></td>
		<td><?php echo number_format($trebuys/$tentries,2);?></td>
		<td><?php echo number_format($tbounties/$tentries,2);?></td>
		<td colspan="2">&nbsp;</td>
		<td><?php echo number_format($tscore/$tscoreentries,2);?></td>
		<td><b>$<?php echo number_format($tcash/$tentries,2);?></b></td>
		<td>&nbsp;</td>
		</tr>
<?php
	}
?>
	</table>

<?php
	include("footer.php");
?>