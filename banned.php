<?php
	include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/connect.php' );
	if(ISSET($_COOKIE['EPICNAME']) && ISSET($_COOKIE['EPICPASS'])){
		// Confirm Credentials, if fail destroy cookies and redirect to homepage
		$username = mysqli_real_escape_string($conn,$_COOKIE['EPICNAME']);
		$password = mysqli_real_escape_string($conn,$_COOKIE['EPICPASS']);

		$accountQ = mysqli_query($conn,"SELECT * FROM `ec_users` WHERE `USERNAME`='$username' AND `PASSWORD`='$password'");
		$account = mysqli_num_rows($accountQ);
		if($account > 0){
			// Get user values
			$user = mysqli_fetch_array($accountQ);
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/header.php' );
			// include( $_SERVER['DOCUMENT_ROOT'] . 'EpicClubRebootMisc\HTMLS\Dashboard.html' );
			// include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/header.php' );
			echo"
			<body style='background-color:#1d1d1d'>";
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/global.php' );
			// Get banned details
			$banLogsQ = mysqli_query($conn, "SELECT * FROM `ec_ban_logs` WHERE `USER_ID`='$user[0]'");

			// Which one was the ban, if multiple bans show them. 
			$bans = array();
			while($banLogs = mysqli_fetch_array($banLogsQ)){
				$ST = $banLogs['START_TIME'];
				$L = $banLogs['LENGTH'];
				$STL = $ST + $L;
				if($STL > $curtime){
					array_push($bans, $banLogs['ID']);
					//echo"<script>alert('$banLogs[ID]')</script>";
				}
			}

			

			echo"
				<center>
					<div style='height:115px;'></div> <!-- SPACE -->
						<div id='platform' style='color:white;width:1200px; border:1px solid black;background-color:grey;border-radius:10px;padding:20px;'>
							<h1>B A N N E D</h1>";

							// all active bans are stored in array $bans with their respective IDs XDDDD
							$NumberOfBans = count($bans);

							if($NumberOfBans < 1 && $user['BANNED']=='NO'){
								header("Location: ../"); exit;
							}

							for($i = 0; $i < $NumberOfBans; $i++){
								$banInfoQ = mysqli_query($conn, "SELECT * FROM `ec_ban_logs` WHERE `ID`='$bans[$i]'");
								$banInfo = mysqli_fetch_array($banInfoQ);
								$modQ = mysqli_query($conn, "SELECT * FROM 	`ec_users` WHERE `ID`='$banInfo[MOD_ID]'");
								$mod = mysqli_fetch_array($modQ);
								
								echo"<text style='font-size:20px;'>You were banned by <b>$mod[USERNAME]</b> for $banInfo[REASON], This ban will expire in <b>";
								// Countdowns
								$secondsL = $STL - $curtime; //Seconds left
								// Find days
								$days = round($secondsL / 86400, 3);
								if($days < 1){
									// show hours
									$hours = round($secondsL / 3600, 3);
									if($hours < 1){
										// Show minutes
										$minutes = round($secondsL / 60, 3);
										if($minutes < 1){
											echo $secondsL." Seconds";
										}else{
											echo round(secondsL / 60)." Minutes";
										}
									}else{
										echo round($secondsL / 3600)." Hours";
									}
								}else{
									echo round($secondsL / 86400)." Days";
								}
								
								// Find Minutes
								echo"  </b></text><br>";
							}

							$banInfoQ2= mysqli_query($conn, "SELECT * FROM `ec_ban_logs` WHERE `LENGTH`='-11122000' AND `USER_ID`='$user[0]'");
							$banInfo2 = mysqli_fetch_array($banInfoQ2);

							if($NumberOfBans < 1 && $banInfo2['LENGTH']!=='-11122000'){
								// give them chance to activate
								echo"
								<a href='?Sorry'>Activate my account!, (You have been warned)</a>
								";
							}

							if($NumberOfBans < 1 && $banInfo2['LENGTH']=='-11122000'){
								echo"<text style='font-size:20px;'>Your account is <b>terminated</b><br>appeal@domain.com";

							}

							/*if(isset($_GET['Sorry'])){

							}*/

							if(isset($_GET['Sorry']) && $NumberOfBans < 1){
								mysqli_query($conn, "UPDATE `ec_users` SET `BANNED`='NO' WHERE `ID`='$user[0]'");
								mysqli_query($conn, "DELETE FROM `ec_ban_logs` WHERE `USER_ID`='$user[0]'");
								header("Location: ../"); exit;
							}
							echo"
						</div>
					</div>
				</center>
			</body>";

		}else{
			setcookie('EPICPASS','',time() - 666, '/');
			setcookie('EPICNAME','',time() - 666, '/');
			header("Location: ../"); exit;
		}

	}else{
		// No cookies set, tell them to go away please
		$siteURLQ = mysqli_query($conn,"SELECT * FROM `site_settings` WHERE `ID` = '1'");
		$siteURL = mysqli_fetch_array($siteURLQ);
		echo"<script>window.location='../?protocal=redirect';</script>";
}
?>
