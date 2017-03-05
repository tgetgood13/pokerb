<?php
/*
	not sure I ever used this... saving site balances I think?
	Author: Tom Getgood 
*/
	include("header.php");

	if(isset($_POST["num_sites"]))
	{
		$num_sites = post_checkInt("num_sites");
		
		for($i=0; $i<$num_sites; $i++)
		{
			$balance = post_checkDouble("balance$i");
			$site_id = post_checkInt("site$i");
			
			addSiteState($site_id, $balance, true);
		}

		echo "Site States saved OK";
	}
	else
	{		
		echo "eh?";
	}
?>

<?php
	include("footer.php");
?>