<?php
	$year = date("Y");
	echo"
	<div style='display:none;width:100%;height:100%;opacity:0.5;background-color:#000000;position:fixed;z-index:3;' id='backDrop'>
	</div>
	
	<center>
		<div id='gearBox' style='display:none;border-radius:5px;background-color:white;padding:30px;position:fixed;z-index:5;left:40%;top:25%;'>
			<a href='../Settings/' style='font-size:25px;'> Settings </a><br><br>
			<a href='#' style='font-size:25px;'> Help </a><br><br>
			<a href='../Avatar/' style='font-size:25px;'> Customise </a><br><br>
			<a href='../Trade/trades.php' style='font-size:25px;'> Trades </a><br><br>
			<a href='../logout.php' style='font-size:25px;'> Logout </a><br><br>
			<a onclick='Gear()' style='font-size:12px;'> Close </a><br><br>
			<text style='font-size:12px;color:grey'>&copy King Kwame $year</text><br>
			<text style='font-size:8px;color:grey'>EpicClub and Wisermen Interactive &reg are properties of Richard.F </text>
		</div>
	</center>
";
?>