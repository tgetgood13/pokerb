# pokerb
Online record keeping for online poker tournaments

Set-up:
Set-up db, copy files into one folder... off you go!

For results entering the text format to input is (ignore the header line below):
Date    Start Site BI  End Time	Rbys/KO	Ent	Place	Score	Won	Comments
dd/mm/yy|hh:mm|SS|X   |hhmm|$x,xxx | X/ |X	|Y	|sX	|	|comments

e.g.:
03/01/16|16:00|88|4   |1929|$2,141 | 4/ |955	|130	|s6	|	|14.8k aft add-on. 55 loses to AJd (board pairs twice on river). 126 paid

Date
Start (Time)
Site (two letter abbreviation)
Buy-In (BI)
End Time (no :)
First place ($ rounded, with comma)
Rebuys followed by slash OR Bounties preceeded by slash
Entrants (with comma)
Place finished (with comma)
Score (s + score out of 10)
Won ($ with comma & decimal point if needed)
Comments


Notes / TO-DO:
Not sure they exist anymore but pokerb app doesn't parse 1/1 tourneys
 - e.g. a rebuy and a bounty combined
Also it can't handle a Progressive KO format... it will only count the number of bounties at the original rate
 - this is a severe limitation