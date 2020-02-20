<?php
/*
	Pokerb db functions (some taken from SST Sitebuilder)
	Author: Tom Getgood
*/

ini_set('error_reporting', E_ALL);
ini_set('display_errors',1);	

include("../dblayer.php");

function openProdConnection()
{
	$username=getenv('DBUSER');
	$password=getenv('DBPASS');
    $database=getenv('DBNAME');
	
	mysql_connect(getenv('DBHOST'),$username,$password);
    @mysql_select_db($database) or die( "Unable to select database");
}

function close()
{
        mysql_close();
}

function getAllTournaments($debug)
{
	$query = "SELECT s.short_name AS site_name,t.tournament_id,t.site_id,t.start_time,t.buy_in,t.ef_buy_in,t.type_id,t.ttype,t.description,t.days_id,t.rebuy,t.ignore_t FROM tPkrTournament t";
	$query.=" JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.=" ORDER BY t.start_time, s.site_id";

	if($debug) echo $query;
	return db_select($query);
}

function getAllSites($debug)
{
	$query = "SELECT s.site_id,s.name,s.pounds,s.display_color,s.short_name,s.notes,st.balance FROM tPkrSite s";
	$query.=" JOIN tPkrSiteState st ON st.site_id=s.site_id";
	$query.=" ORDER BY date_created DESC";
	
	if($debug) echo $query;
	return db_select($query);
}

function getAllGames($debug)
{
	$query = "SELECT game_id,short_name,description FROM tPkrGame";
	
	if($debug) echo $query;
	return db_select($query);
}

function getAllTypes($debug)
{
	$query = "SELECT type_id,short_name,description FROM tPkrType";
	
	if($debug) echo $query;
	return db_select($query);
}

function getAllDays($debug)
{
	$query = "SELECT days_id,description FROM tPkrDays";
	
	if($debug) echo $query;
	return db_select($query);
}

function getTournamentById($tid, $debug)
{
	$tid = db_checkInt($tid);
	
	$query = "SELECT site_id,start_time,buy_in,ef_buy_in,type_id,game_id,ttype,description,days_id,rebuy,rebuy_addon,bounty,ignore_t,defunct_t FROM tPkrTournament WHERE tournament_id=$tid";
	
	if($debug) echo $query;
	return db_select($query);
}

function getTournamentStatsById($tid, $debug)
{
	$tid = db_checkInt($tid);
	
	$query = "SELECT hourly_rate, best_profit, average_score FROM tPkrTournamentStats WHERE tournament_id=$tid";
	
	if($debug) echo $query;
	return db_select($query);
}

function addTournament($site,$start_time,$buy_in,$ef_buy_in,$type,$ttype,$game,$description,$days,$rebuy,$rebuy_addon,$bounty, $debug)
{
	$site = db_checkInt($site);
	$start_time = db_checkString($start_time);
	$buy_in = db_checkDouble($buy_in);
	$ef_buy_in = db_checkDouble($ef_buy_in);
	$type = db_checkInt($type);
	$ttype = db_checkInt($ttype);
	$game = db_checkInt($game);
	$description = db_checkString($description);
	$days = db_checkInt($days);
	$rebuy = db_checkInt($rebuy);
	$rebuy_addon = db_checkDouble($rebuy_addon);
	$bounty = db_checkDouble($bounty);
	 
	$query = "INSERT INTO tPkrTournament VALUES (null,$site,$start_time,$buy_in,$ef_buy_in,$type,$ttype,$game,$description,$days,$rebuy,$rebuy_addon,$bounty,0,0)";
	if($debug) echo $query;
	return db_insert($query);
}

function updateTournament($tid, $site,$start_time,$buy_in,$ef_buy_in,$type,$ttype,$game,$description,$days,$rebuy,$rebuy_addon,$bounty, $ignore, $defunct, $debug)
{
	$tid = db_checkInt($tid);
	$site = db_checkInt($site);
	$start_time = db_checkString($start_time);
	$buy_in = db_checkDouble($buy_in);
	$ef_buy_in = db_checkDouble($ef_buy_in);
	$type = db_checkInt($type);
	$ttype = db_checkInt($ttype);
	$game = db_checkInt($game);
	$description = db_checkString($description);
	$days = db_checkInt($days);
	$rebuy = db_checkInt($rebuy);
	$ignore = db_checkInt($ignore);
	$defunct = db_checkInt($defunct);
	$rebuy_addon = db_checkDouble($rebuy_addon);
	$bounty = db_checkDouble($bounty);
	
	$query = "UPDATE tPkrTournament SET site_id=$site, start_time=$start_time, buy_in=$buy_in, ef_buy_in=$ef_buy_in, type_id=$type, ttype=$ttype, game_id=$game, description=$description, days_id=$days, rebuy=$rebuy, rebuy_addon=$rebuy_addon, bounty=$bounty, ignore_t=$ignore, defunct_t=$defunct WHERE tournament_id=$tid";
	if($debug) echo $query;
	return db_update($query);
}

function updateTournamentStats($tid, $hourly_rate, $average_score, $best_profit, $debug)
{
	$tid = db_checkInt($tid);
	$hourly_rate = db_checkDouble($hourly_rate);
	$average_score = db_checkDouble($average_score);
	$best_profit = db_checkDouble($best_profit);

	$query = "DELETE FROM tPkrTournamentStats WHERE tournament_id=$tid";
	db_delete($query);
	
	$query = "INSERT INTO tPkrTournamentStats VALUES ($tid, $hourly_rate, $average_score, $best_profit)";
	return db_insert($query);
}

function getTournamentIdBySiteTimeCost($time, $site, $cost, $debug)
{
	$time = db_checkString($time);
	$site = db_checkString($site);
	$cost = db_checkDouble($cost);
	
	$query = "SELECT tournament_id FROM tPkrTournament WHERE site_id=(SELECT site_id FROM tPkrSite WHERE short_name=$site) AND start_time=$time AND buy_in=$cost AND defunct_t=0";
	if($debug) echo $query;
	return db_select($query);
}

function getTournamentIdBySiteTimeTotalCost($time, $site, $cost, $debug)
{
	$time = db_checkString($time);
	$site = db_checkString($site);
	$cost = db_checkDouble($cost);
	
	$query = "SELECT tournament_id FROM tPkrTournament WHERE site_id=(SELECT site_id FROM tPkrSite WHERE short_name=$site) AND start_time=$time AND buy_in-(rebuy_addon*2)=$cost AND defunct_t=0";
	if($debug) echo $query;
	return db_select($query);
}

function getTournamentRecordByTournamentId($tid, $debug)
{
	$tid = db_checkInt($tid);
	
	$query = "SELECT tr.tdate,t.start_time,date_format(tr.tdate,'%W') AS day, s.short_name,t.buy_in,t.rebuy_addon, t.bounty, t.description,tr.tlength,tr.first_place,tr.rebuys,tr.bounties,tr.entrants,tr.place,tr.score,tr.cash,tr.comments FROM tPkrTournamentRecord tr JOIN tPkrTournament t ON t.tournament_id=tr.tournament_id JOIN tPkrSite s ON s.site_id=t.site_id WHERE tr.tournament_id=$tid ORDER BY tr.tdate ASC";
	if($debug) echo $query;
	return db_select($query);
}

function addTournamentRecord($tid,$sid,$date,$length,$first,$rebuy,$bounty,$entrants,$place,$score,$cash,$comments, $debug)
{
	$tid = db_checkInt($tid);
	$sid = db_checkInt($sid);
	$tdate = db_checkString($date);
	$tlen = db_checkInt($length);
	$first = db_checkInt($first);
	$rebuy = db_checkInt($rebuy);
	$bounty = db_checkDouble($bounty);
	$entrants = db_checkInt($entrants);
	$place = db_checkInt($place);
	$score = db_checkInt($score);
	$cash = db_checkDouble($cash);
	$comments = db_checkString($comments);
	
	$query = "INSERT INTO tPkrTournamentRecord VALUES (null,$tid,$sid,$tdate,$tlen,$first,$rebuy,$bounty,$entrants,$place,$score,$cash,$comments)";
	if($debug) echo $query;
	return db_insert($query);
}

function getLastSessionId($debug)
{
	$query = "SELECT MAX(session_id) AS id FROM tPkrSession";
	
	if($debug) echo $query;
	return mysql_result(db_select($query),0,"id");
}

function createNewSession($sid, $debug)
{
	$sid = db_checkInt($sid);
	
	$start = "SELECT CONCAT_WS(' ', r.tdate, CONCAT_WS(':', LEFT(t.start_time,2), MID(t.start_time,3,2),'00')) AS start FROM tPkrTournamentRecord r JOIN tPkrTournament t ON t.tournament_id=r.tournament_id WHERE session_id=$sid ORDER BY tdate, start_time LIMIT 1";
	if($debug) echo $start;
	$start = mysql_result(db_select($start),0,"start");
	
	$end = "SELECT DATE_ADD(CONCAT_WS(' ', r.tdate, CONCAT_WS(':', LEFT(t.start_time,2), MID(t.start_time,3,2),'00')), INTERVAL r.tlength MINUTE) AS end FROM `tPkrTournamentRecord` r JOIN tPkrTournament t ON t.tournament_id=r.tournament_id WHERE session_id=$sid ORDER BY DATE_ADD(CONCAT_WS(' ', r.tdate, CONCAT_WS(':', LEFT(t.start_time,2), MID(t.start_time,3,2),'00')), INTERVAL r.tlength MINUTE) DESC LIMIT 1";
	if($debug) echo $end;
	$end = mysql_result(db_select($end),0,"end");

	$query="INSERT INTO tPkrSession VALUES ($sid,'$start','$end')";
	if($debug) echo $query;
	return db_insert($query);
}

function updateSessionEnd($sid, $debug)
{
	$sid = db_checkInt($sid);
	
	$query = "UPDATE tPkrSession SET end_datetime=(SELECT DATE_ADD(CONCAT_WS(' ', r.tdate, CONCAT_WS(':', LEFT(t.start_time,2), MID(t.start_time,3,2),'00')), INTERVAL r.tlength MINUTE) AS end FROM `tPkrTournamentRecord` r";
	$query.= " JOIN tPkrTournament t ON t.tournament_id=r.tournament_id";
	$query.= " WHERE session_id=$sid";
	$query.= " ORDER BY DATE_ADD(CONCAT_WS(' ', r.tdate, CONCAT_WS(':', LEFT(t.start_time,2), MID(t.start_time,3,2),'00')), INTERVAL r.tlength MINUTE) DESC LIMIT 1) WHERE session_id=$sid";

	if($debug) echo $query;
	return db_update($query);
}

function addSiteState($site, $balance, $debug)
{
	$site = db_checkInt($site);
	$balance = db_checkDouble($balance);
	
	$query = "INSERT INTO tPkrSiteState VALUES (null, $site, $balance, now())";
	if($debug) echo $query;
	return db_insert($query);
}

function getFullSchedule($debug)
{
	$query = "SELECT t.tournament_id, t.start_time, d.description AS days, s.short_name AS site_name, s.display_color AS site_color, ty.short_name AS type_name, tg.short_name AS game_name, t.ef_buy_in AS buy_in, t.description, l.level_id, l.display_color AS level_color,t.ttype, ts.hourly_rate, ts.best_profit, ts.average_score, t.ignore_t, t.defunct_t FROM tPkrTournament t";
	$query.=" JOIN tPkrDays d ON d.days_id=t.days_id";
	$query.=" JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.=" JOIN tPkrGame tg ON tg.game_id=t.game_id";
	$query.=" JOIN tPkrType ty ON ty.type_id=t.type_id";
	$query.=" JOIN tPkrLevel l ON ((t.rebuy=0 && t.ef_buy_in>l.min_stake && t.ef_buy_in<=max_stake) || (t.rebuy=1 && t.buy_in>=l.rebuy_min && t.buy_in<=l.rebuy_max))";
	$query.=" LEFT JOIN tPkrTournamentStats ts ON ts.tournament_id=t.tournament_id";
	$query.=" ORDER BY start_time ASC, buy_in DESC";
	
	if($debug) echo $query;
	return db_select($query);
}

function getMonthlyReport($debug)
{
	//$tid = db_checkInt($tid);
	
	// daily by site
	//$query = "SELECT s.short_name, tr.tdate, SUM(tr.cash) AS won FROM tPkrTournamentRecord tr JOIN tPkrTournament t ON t.tournament_id=tr.tournament_id JOIN tPkrSite s ON s.site_id=t.site_id GROUP BY s.short_name, tr.tdate ORDER BY tr.tdate";
	
	// monthly by site
	$query = "SELECT s.short_name, YEAR(tr.tdate) AS year, MONTH(tr.tdate) AS month, SUM(t.ttype!=2) AS entry, SUM(t.buy_in)+SUM(CASE tr.rebuys WHEN 0 THEN 0 ELSE t.rebuy_addon*tr.rebuys END) AS lost, SUM(tr.cash)+SUM(tr.bounties*t.bounty) AS won, SUM(tr.cash>0 AND t.ttype!=2) AS itm, SUM(tr.place<=3 AND t.ttype!=2) AS f23, (SUM(tr.cash)+SUM(tr.bounties*t.bounty))-(SUM(t.buy_in)+SUM(CASE tr.rebuys WHEN 0 THEN 0 ELSE t.rebuy_addon*tr.rebuys END)) AS profit, SUM(tr.tlength) AS clength FROM tPkrTournamentRecord tr";
	$query.= " JOIN tPkrTournament t ON t.tournament_id=tr.tournament_id";
	$query.= " JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.= " GROUP BY s.short_name, YEAR(tr.tdate), MONTH(tr.tdate) ORDER BY YEAR(tr.tdate),MONTH(tr.tdate),s.site_id ASC";
	if($debug) echo $query;
	return db_select($query);
}

function getSpecificReport($site, $year, $month, $debug)
{
	if(isset($site)) $site = db_checkString($site);
	if(isset($year)) $year = db_checkInt($year);
	if(isset($month)) $month = db_checkInt($month);
	
	$where = "";
	if(isset($site)) $where.= "s.short_name=$site";
	if(isset($year))
	{
		if($where!="") $where.=" AND ";
		$where.= "YEAR(tr.tdate)=$year";
	}
	if(isset($month))
	{
		if($where!="") $where.=" AND ";
		$where.= "MONTH(tr.tdate)=$month";
	}
	
	$query = "SELECT t.tournament_id, tr.session_id, s.short_name, tr.tdate, t.start_time, t.buy_in, t.rebuy_addon, tr.rebuys, tr.cash, tr.bounties, t.bounty, tr.entrants, tr.first_place, tr.place, t.description, t.ttype, ty.short_name AS ptype, tr.score, tr.comments FROM tPkrTournamentRecord tr";
	$query.= " JOIN tPkrTournament t ON t.tournament_id=tr.tournament_id";
	$query.= " JOIN tPkrType ty ON t.type_id=ty.type_id";
	$query.= " JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.= " WHERE ".$where." ORDER BY tr.tdate, tr.session_id";
	
	if($debug) echo $query;
	return db_select($query);
}

function getSessionSpecificReport($site, $year, $month, $debug)
{
	if(isset($site)) $site = db_checkString($site);
	if(isset($year)) $year = db_checkInt($year);
	if(isset($month)) $month = db_checkInt($month);
	
	$where = "";
	if(isset($site)) $where.= "site.short_name=$site";
	if(isset($year))
	{
		if($where!="") $where.=" AND ";
		$where.= "YEAR(tr.tdate)=$year";
	}
	if(isset($month))
	{
		if($where!="") $where.=" AND ";
		$where.= "MONTH(tr.tdate)=$month";
	}
	
	$query = "SELECT SUM(true) AS true_entry,s.start_datetime, s.end_datetime, DAYNAME(s.start_datetime) AS day, TIMESTAMPDIFF(MINUTE,s.start_datetime, s.end_datetime) AS tlen, SUM(tr.score) AS tscore, SUM(t.ttype!=2) AS entry, SUM(t.buy_in)+SUM(CASE tr.rebuys WHEN 0 THEN 0 ELSE t.rebuy_addon*tr.rebuys END) AS lost, SUM(tr.cash)+SUM(tr.bounties*t.bounty) AS won, SUM(tr.cash>0 AND t.ttype!=2) AS itm, SUM(tr.place<=3 AND t.ttype!=2) AS f23 FROM tPkrSession s";
	$query.= " JOIN tPkrTournamentRecord tr ON tr.session_id=s.session_id";
	$query.= " JOIN tPkrTournament t ON t.tournament_id=tr.tournament_id";
	$query.= " JOIN tPkrSite site ON site.site_id=t.site_id";
	$query.= " WHERE ".$where." GROUP BY s.session_id ORDER BY s.start_datetime";
	
	if($debug) echo $query;
	return db_select($query);
}

function getAllLevels($debug)
{
	$query = "SELECT level_id,dollar_min,dollar_max,pound_min,pound_max,min_stake,max_stake,rebuy_min,rebuy_max,qual_max,qual_rebuy,other,display_color FROM tPkrLevel";

	if($debug) echo $query;
	return db_select($query);
}

function getUpcomingSchedule($start, $end, $day, $debug)
{
	$start = db_checkString($start);
	$end = db_checkString($end);
	$day = db_checkStringForLIKE($day);
	  
	/*$query = "SELECT t.tournament_id, t.site_id, start_time, buy_in, ty.short_name AS type_desc, ttype, t.description, rebuy, s.display_color, s.short_name AS site_name, ts.hourly_rate, ts.best_profit, ts.average_score FROM tPkrTournament t";
	$query.=" JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.=" JOIN tPkrDays d ON d.days_id=t.days_id";
	$query.=" JOIN tPkrType ty ON ty.type_id=t.type_id";
	$query.=" LEFT JOIN tPkrTournamentStats ts ON ts.tournament_id=t.tournament_id";
	$query.=" WHERE $start<=start_time AND start_time<=$end AND t.ignore_t=0 AND (d.description='Any' OR d.description LIKE $day)";
	$query.=" ORDER BY start_time, ttype, buy_in DESC";
	*/
	$query = "SELECT t.tournament_id, t.site_id, start_time, ef_buy_in AS buy_in, ty.short_name AS type_desc, tg.short_name AS game_desc, ttype, t.description, rebuy, s.display_color, s.short_name AS site_name,";// ts.hourly_rate, ts.best_profit, ts.average_score,";
	$query.= " SUM(tr.score) AS tscore, SUM(tr.score>0) AS tscorex, SUM(tr.entrants) AS total_ent, SUM(tr.cash+(tr.bounties*t.bounty)-(t.buy_in+(t.rebuy_addon*tr.rebuys))) AS profit, SUM(tr.first_place) AS tfirst, SUM(tr.first_place>0) AS tfirstx, MAX(tr.cash) AS tmaxcash, COUNT(tr.id) AS tentries, MIN(tr.place) AS tminplace, SUM(tr.tlength) AS tlength, SUM(tr.cash>0) AS itm, SUM(tr.place>=1 AND tr.place<=3) AS f123,";
	$query.= " SUM(CASE date_format(tr.tdate, '%W') LIKE $day WHEN 1 THEN tr.cash+(tr.bounties*t.bounty)-(t.buy_in+(t.rebuy_addon*tr.rebuys)) WHEN 0 THEN 0 END) AS profit2, SUM(CASE date_format(tr.tdate, '%W') LIKE $day WHEN 1 THEN tr.first_place WHEN 0 THEN 0 END) AS tfirst2, SUM((date_format(tr.tdate, '%W') LIKE $day AND tr.first_place)>0) AS tfirstx2, MAX(CASE date_format(tr.tdate, '%W') LIKE $day WHEN 1 THEN tr.cash WHEN 0 THEN 0 END) AS tmaxcash2, SUM(date_format(tr.tdate, '%W') LIKE $day) AS tentries2, MIN(CASE date_format(tr.tdate, '%W') LIKE $day WHEN 1 THEN tr.place WHEN 0 THEN 9999 END) AS tminplace2, SUM(date_format(tr.tdate, '%W') LIKE $day AND tr.cash>0) AS itm2, SUM(date_format(tr.tdate, '%W') LIKE $day AND tr.place>=1 AND tr.place<=3) AS f1232 FROM tPkrTournament t";
	$query.= " JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.= " JOIN tPkrDays d ON d.days_id=t.days_id";
	$query.= " JOIN tPkrType ty ON ty.type_id=t.type_id";
	$query.= " JOIN tPkrGame tg ON tg.game_id=t.game_id";
	//$query.= " LEFT JOIN tPkrTournamentStats ts ON ts.tournament_id=t.tournament_id";
	$query.= " LEFT JOIN tPkrTournamentRecord tr ON tr.tournament_id=t.tournament_id";
	$query.= " WHERE $start<=start_time AND start_time<=$end AND t.defunct_t=0 AND t.ignore_t=0 AND (d.description='Any' OR d.description LIKE $day)";
	$query.= " GROUP BY t.tournament_id ORDER BY start_time, ttype, buy_in DESC";
	
	//$buy_in = mysql_result($rset,$i,"buy_in")+(mysql_result($rset,$i,"rebuy_addon")*mysql_result($rset,$i,"rebuys"));
		
	//echo "<b>$".number_format(mysql_result($rset,$i,"cash")+(mysql_result($rset,$i,"bounty")*mysql_result($rset,$i,"bounties")),2)."</b></td>";
	
	
	if($debug) echo $query;
	return db_select($query);
}

// profit
function getBaseMostProfitable($debug)
{
	$query = "SELECT t.tournament_id, t.site_id, d.description AS days, start_time, ef_buy_in AS buy_in, ty.short_name AS type_desc, tg.short_name AS game_desc, ttype, t.description, rebuy, s.display_color, s.short_name AS site_name, ts.hourly_rate, ts.best_profit, ts.average_score,";
	$query.= " SUM(tr.score) AS tscore, SUM(tr.score>0) AS tscorex, SUM(tr.entrants) AS total_ent, SUM(tr.cash+(tr.bounties*t.bounty)-(t.buy_in+(t.rebuy_addon*tr.rebuys))) AS profit, SUM(tr.first_place) AS tfirst, SUM(tr.first_place>0) AS tfirstx, MAX(tr.cash) AS tmaxcash, COUNT(tr.id) AS tentries, MIN(tr.place) AS tminplace, SUM(tr.tlength) AS tlength, SUM(tr.cash>0) AS itm, SUM(tr.place>=1 AND tr.place<=3) AS f123 FROM tPkrTournament t";
	$query.= " JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.= " JOIN tPkrDays d ON d.days_id=t.days_id";
	$query.= " JOIN tPkrType ty ON ty.type_id=t.type_id";
	$query.= " JOIN tPkrGame tg ON tg.game_id=t.game_id";
	$query.= " LEFT JOIN tPkrTournamentStats ts ON ts.tournament_id=t.tournament_id";
	$query.= " LEFT JOIN tPkrTournamentRecord tr ON tr.tournament_id=t.tournament_id";
	$query.= " WHERE t.defunct_t=0 AND t.ignore_t=0 AND ttype!=2";
	$query.= " GROUP BY t.tournament_id ORDER BY buy_in, start_time, ttype DESC";
	
	if($debug) echo $query;
	return db_select($query);
}

// shots
function getShotsByTypeAndThreshold($type, $threshold, $year, $debug)
{
	$type = db_checkInt($type);
	$threshold = db_checkInt($threshold);
	$year = db_checkInt($year);

	$query = "SELECT session_id,tdate,YEAR(tdate) AS year,tlength,first_place,rebuys,bounties,entrants,place,score,cash,comments,t.tournament_id,start_time,buy_in,rebuy_addon,bounty,description,s.short_name FROM tPkrTournamentRecord r";
	$query.=" JOIN tPkrTournament t ON t.tournament_id=r.tournament_id";
	$query.=" JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.=" WHERE t.buy_in>=$threshold AND t.game_id=$type AND YEAR(r.tdate)=$year ORDER BY cash DESC";

	if($debug) echo $query;
	return db_select($query);
}

// cv
function getCVByTypeAndThreshold($type, $threshold, $debug)
{
	$type = db_checkInt($type);
	$threshold = db_checkInt($threshold);

	$query = "SELECT session_id,tdate,YEAR(tdate) AS year,tlength,first_place,rebuys,bounties,entrants,place,score,cash,comments,t.tournament_id,start_time,buy_in,rebuy_addon,bounty,description,s.short_name FROM tPkrTournamentRecord r";
	$query.=" JOIN tPkrTournament t ON t.tournament_id=r.tournament_id";
	$query.=" JOIN tPkrSite s ON s.site_id=t.site_id";
	$query.=" WHERE r.cash>=$threshold && t.game_id=$type ORDER BY cash DESC";

	if($debug) echo $query;
	return db_select($query);
}

// quick funcs
function getMoneyAmount($cash)
{
	return str_replace(",","",str_replace("$","",$cash));
}

function getScore($score)
{
	return str_replace("s","",$score);
}

function getNumber($num)
{
	return str_replace(",","",$num);
}


//security
function userCheck($username,$sessionId)
{
    $username = db_checkString($username);
    $sessionId = db_checkInt($sessionId);

    $query = "SELECT 1 FROM _log_adminsessions WHERE username=$username && sessionId=$sessionId";

    $result=db_select($query);
    return(mysql_numrows($result));
}

function securityCheck($username, $password)
{
    $username = db_checkString($username);
    $password = db_checkString($password);

	$query = "SELECT 1 FROM tUser WHERE username=$username && password=$password";
    $result=db_select($query);
    return(mysql_numrows($result));
}

function logAdminSession($sessionId,$username)
{
    $sessionId = db_checkInt($sessionId);
    $username = db_checkString($username);
        
    $query = "INSERT INTO _log_adminsessions (sessionId,username,dateCreated) VALUES ($sessionId,$username, now())";
    return db_insert($query);
}