<?php
/*
	Author: Tom Getgood 
*/
	include("header.php");
?>

<?php
	if(isset($_POST["datae"]))
	{
		$datae = $_POST["datae"];
		
		$new_session = false;
		$session_id = getLastSessionId($db, false);
		
		// session id
		if(isset($_POST["oldsession"]) && $_POST["oldsession"]=="on")
		{
				echo "using current session...";
		}
		else
		{
			$new_session=true;
			$session_id+=1;
		}
		
		//first pass to check all tournaments
		$ts = explode("\r\n",$datae);
		
		$actr=0;
		while($actr<count($ts))
		{
			$fs = explode("|", $ts[$actr]);
			
			$date = "20".substr($fs[0],6,2)."-".substr($fs[0],3,2)."-".substr($fs[0],0,2);
			$time = str_replace(":","",$fs[1]);
			$site = $fs[2];
			$cost = $fs[3];
			
			// len
			$tlen = 0;
			$shrs = substr($time,0,2);
			$smins = substr($time,2,2);
			$ehrs = substr($fs[4],0,2);
			$emins = substr($fs[4],2,2);
			if($emins>$smins) $tlen += $emins-$smins;
			else $tlen += (60-$smins)+$emins-60;
			if($ehrs>=$shrs) $tlen+= 60*($ehrs-$shrs);
			else $tlen+= (60*(24-$shrs))+(60*$ehrs);
			
			$firstpl = getMoneyAmount($fs[5]);
			
			$rebuy_or_bounty = trim($fs[6]);
			$rby = 0;
			$bounty = 0;
			if(strlen($rebuy_or_bounty)>0)
			{			
				if(stripos($rebuy_or_bounty,"/")==0)
				{
					$bounty = str_replace("/","",$rebuy_or_bounty);
				}
				else
				{
					$rby = str_replace("/","",$rebuy_or_bounty);
				}
			}
			
			$enter = getNumber($fs[7]);
			$place = getNumber($fs[8]);
			$score = getScore($fs[9]);
			$cash = getMoneyAmount($fs[10]);
			$comments = stripslashes($fs[11]);

			$tid = getTournamentIdBySiteTimeCost($db, $time,$site,$cost, false);
			
			if(mysqli_num_rows($tid)>0)
			{
				addTournamentRecord($db, mysql_result($tid,0,"tournament_id"), $session_id, $date, $tlen, $firstpl, $rby, $bounty, $enter, $place, $score, $cash, $comments, false);
			}
			else
			{
				// bit of a hack...
				
				echo $time." ".$site." ".$cost." not found, trying 1r+1a...";
				
				$tid = getTournamentIdBySiteTimeTotalCost($db, $time,$site,$cost, false);
				
				if(mysqli_num_rows($tid)>0)
				{
					addTournamentRecord($db, mysql_result($tid,0,"tournament_id"), $session_id, $date, $tlen, $firstpl, $rby, $bounty, $enter, $place, $score, $cash, $comments, false);
				}
				else
				{
					echo $time." ".$site." ".$cost." not found.";
				}
			}
			
			$actr++;
		}

		if($new_session) createNewSession($db, $session_id, true);
		else updateSessionEnd($db, $session_id, true);
					
		echo $actr." results added.";

		//addTournament($site,$start_time,$buy_in,$type,$ttype,$description,$days,$rebuy, false);
	      
	}
	else
	{
?>
		<form action="enter_data.php" method="post">
		Enter data in the correct format:<br/>
		<textarea name="datae" rows="10" cols="80"></textarea><br/>
		Use last session ID? <input type="checkbox" name="oldsession"/><br/>
		<input type="submit"/>
		</form>
<?php
	}

	include("footer.php");
?>