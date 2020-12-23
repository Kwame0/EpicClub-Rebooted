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
			<body>";
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/global.php' );
			if(isset($_GET['id']) && is_numeric($_GET['id'])){
				$id = mysqli_real_escape_string($conn, $_GET['id']);
				// check if we item is real
				$RealItem = mysqli_query($conn, "SELECT * FROM `ec_items` WHERE `ID`='$id'");
				if(mysqli_num_rows($RealItem) > 0){
					// item is real
					$Item = mysqli_fetch_array($RealItem);
					// check if we have item
					$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `USER_ID`='$user[0]' AND `ITEM_ID`='$id'");
					if(mysqli_num_rows($haveItem) > 0){
						// check if they set price via post
						if(isset($_POST['price']) && is_numeric($_POST['price']) && isset($_POST['serial']) && is_numeric($_POST['serial'])){
							$crateId = mysqli_real_escape_string($conn, $_POST['serial']);
							$price = mysqli_real_escape_string($conn, $_POST['price']);
							if($price < 0){
								header("Location: ../"); exit;
							}
							// see if crateId.ITEM_ID = $Item[0] && crateId.USER_ID = $user[0]
							$crateInfoQ = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ID`='$crateId'");

							if(mysqli_num_rows($crateInfoQ) < 1){
								header("Location: ../"); exit;
								// stuff like this needs to be in place to stop sql errors appearing before hackers
							}

							$crateInfo = mysqli_fetch_array($crateInfoQ);
							$serial = $crateInfo['SERIAL'];
							if($crateInfo['ITEM_ID']==$Item[0] && $crateInfo['USER_ID']==$user[0]){
								//commence or update
								$Update = mysqli_query($conn, "SELECT * FROM `ec_reselling` WHERE `ITEM_ID`='$Item[0]' AND `USER_ID`='$user[0]' AND `SERIAL`='$serial'");
								if(mysqli_num_rows($Update) > 0){
									mysqli_query($conn, "UPDATE `ec_reselling` SET `PRICE`='$price' WHERE `ITEM_ID`='$Item[0]' AND `USER_ID`='$user[0]' AND `SERIAL`='$serial'");
									header("Location: ../Emporium/item.php?id=$Item[0]"); exit;
								}else{
									mysqli_query($conn, "INSERT INTO `ec_reselling` VALUES(NULL, '$user[0]', '$Item[0]', '$serial', '$price')");
									header("Location: ../Emporium/item.php?id=$Item[0]"); exit;
								}
							}else{
								// bye!
								header("Location: ../"); exit;
							}
						}else{
						echo"
							<center>
								<div style='height:115px;'></div> <!-- SPACE -->
									<div id='platform' style='width:1200px; border:1px solid black;background-color:white;border-radius:10px;padding:20px;'>
										<h1>Selling $Item[NAME]</h1>
										<img src='$Item[AVATAR_IMG_URL]' title='$Item[NAME]'>
										<form method='post'>
											<select name='serial' style='padding:5px;width:200px;'>";
											while($OurItem = mysqli_fetch_array($haveItem)){
												echo"<option value='$OurItem[0]'>$Item[NAME] - #$OurItem[SERIAL]</option>";
											}
										echo"</select><br>
										<input style='width:200px;padding:5px;margin-top:5px;' type='number' placeholder='Price' name='price' required></input>
										<br><button style='border:1px solid grey;border-radius:0px;padding:2.5px;'>Sell!</button>
										</form>
										<text style='color:grey;font-size:15px;'>Sell or update prices</text><br>
										<text style='color:grey;font-size:12.5px;'>30% Tax</text>
									</div>
								</div>
							</center>
						</body>"; exit;
						}
					}else{
						header("Location: ../"); exit;
					}
				}else{
					header("Location: ../"); exit;
				}
			}else{
				header("Location: ../"); exit;
			}
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
