<?php
	// setcookie('loopMech','0',time() + 3600, '/');
	if(isset($_COOKIE['EPICNAME']) && isset($_COOKIE['EPICPASS'])){
		// they are already logged in check for creds at dashbaord
		echo"<script>window.location='/Dashboard/'</script>";
	}
	
	if(isset($_GET['protocal'])){
		// Go to dashboard with new creds :)
		// Check for cookies to stop redirecting loop
		if($_GET['protocal']=='redirect'){
			if(isset($_COOKIE['EPICNAME'])){
				echo"<script>window.location='/Dashboard/'</script>";
			}
		}else{
			// cookie not set, what do? :X
		}
	}else{
		// Show index
		 include_once 'EpicClubRebootMisc/HTMLS/index.html';
	}
?>
