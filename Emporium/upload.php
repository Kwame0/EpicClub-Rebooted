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
			$curtime = time();
			if(isset($_POST['name']) && isset($_POST['description']) && isset($_FILES["file"]) && isset($_POST['price'])){
				// sort file for moderation
				if ($_FILES["file"]["error"] > 0){
					echo "<script>alert('Error: " . $_FILES["file"]["error"] . "')</script>";
				}else{
					$info = getimagesize($_FILES['file']['tmp_name']);
					
					if ($info === FALSE) {
					  echo"<script>alert('Not A Valid Image file!')</script>";
					  echo"<script>window.location='../Emporium/upload'</script>"; exit;
					}
					
					if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
					   echo"<script>alert('Not A Valid Image file! (Avatar)')</script>";
					   echo"<script>window.location='../Emporium/upload'</script>"; exit;
					}
					
					$name = mysqli_real_escape_string($conn, $_POST['name']);
					$description = mysqli_real_escape_string($conn, $_POST['description']);
					$price = mysqli_real_escape_string($conn, $_POST['price']);
					$type = mysqli_real_escape_string($conn, $_POST['type']);
					
					if(is_numeric($price)){
						
						if($price < 0){
							$price = 0;
						}
						
						// not as random as randomly generated string 
						$number = mt_rand(1,999999999);
						
						$image_dir= '../EpicClubRebootMisc/IMGS/ASSETS/';
						move_uploaded_file($_FILES['file']['tmp_name'], $image_dir.$_FILES['file']['name'].$number);
						$imageAVATAR_URL = $image_dir.$_FILES['file']['name'].$number;
						//echo"<script>alert('$imageAVATAR_URL')</script>";
						
						// see if they have over normal records
						if($user['VIP']=='NONE'){
							// get number of uploads
							$numberOfU = mysqli_query($conn, "SELECT * FROM `ec_user_assets` WHERE `CREATOR_ID`='$user[0]' AND `STATUS`='ACCEPTED'");
							if(mysqli_num_rows($numberOfU) > 39){
								echo"<script>alert('You have reach the limit for uploads (40) For more uploads please upgrade')</script>";
								echo"<script>window.location='../Upgrade/'</script>"; exit;
							}
						}
						
						if($type==''){
							echo"<script>alert('Select a type!')</script>";
							echo"<script>window.history.back();'</script>"; exit;
						}elseif($type!='SHIRT' && $type!='TROU'){
							echo"<script>alert('Select a type!')</script>";
							echo"<script>window.history.back();'</script>"; exit;
						}elseif($type=='0'){
							echo"<script>alert('Select a type!')</script>";
							echo"<script>window.history.back();'</script>"; exit;
						}
						
						if($type=='SHIRT'){
							$upload_type = 'SHIRT';
						}elseif($type=='TROU'){
							$upload_type = 'TROU';
						}else{
							$upload_type = 'SHIRT';
						}
						
						// upload
						
						mysqli_query($conn, "INSERT INTO `ec_user_assets` VALUES(NULL,'$name','$description','$user[0]','$curtime','$price','$imageAVATAR_URL','PENDING','$type','0')");
						echo"<script>alert('Success! Wait until a moderator approves or disapproves of this asset!')</script>";
						//echo"<script>alert('$type')</script>";
						echo"<script>window.location='../Emporium/'</script>"; exit;
					}else{
						echo"<script>alert('Please Leave')</script>";
						// report hack attempt
						mysqli_query($conn, "INSERT INTO `ec_mod_logs` VALUES(NULL, '$user[0]', '$user[0]', 'Tried To Hack The Site', '$curtime')");
						echo"<script>window.location='http://google.com'</script>"; exit;
					}
				}
			}else{
				echo"
					<center>
						<div style='height:115px;'></div> <!-- SPACE -->
							<div id='platform' style='width:1200px; border:1px solid black;background-color:white;border-radius:10px;padding:20px;'>
							<h1>Upload</h1><br>
							<h4 style='color:grey;'>Upload a pair trousers or shirt</h4><br>
							<div style='display:flex;padding-left:20%;'>
								<div style='margin-right:3%;'><i class='fa fa-square' style='color:#19ff00;'></i> All pixels in this area are <b>Illegal</b></div>
								<div style='margin-right:3%;'><i class='fa fa-square' style='color:yellow;'></i> This is your <b>allowed</b> editing Area</div>
								<div style='margin-right:3%;'><i class='fa fa-square' style='color:red;'></i> <b>Do not edit</b> into this area </div>
							</div>
							<br><br>
								<img src='../EpicClubRebootMisc/IMGS/edit_trousers.png'></img>
								<img src='../EpicClubRebootMisc/IMGS/edit_shirt.png'></img>
								<form style='border:1px solid;width:900px;margin-top:25px;padding:2.5px;' method='post'  enctype='multipart/form-data' action='#'>
									<input style='padding:5px;border:1px solid;' type='file' name='file' id='file'> </input>
									<label style='width:200px;' for='file'>Asset</label><br>
									<input style='padding:5px;border:1px solid;margin-top:2.5px;' type='number' name='price' value='0'> <b>Silver</b></input><Br>
									<select name='type' style='padding:5px;padding-left:10px;padding-right:20px;margin-top:7.5px;'>
										<option name='SHIRT' selected>SHIRT</option>
										<option name='TROU'>TROU</option>
									</select><br>
									<input style='border:1px solid black;width:300px;padding:5px;margin-bottom:5px;' placeholder='Item name' name='name' maxlength='50'></input><br>
									<textarea style='border:1px solid black;width:300px;padding:5px;' placeholder='Description' name='description' maxlength='500'></textarea><br>
									<button style='width:400px;'>I am happy with this and will wait for approval by an offical</button>
								</form>
							</div>
						</div>
					</center>
				</body>";
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
