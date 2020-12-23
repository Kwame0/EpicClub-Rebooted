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
			echo"
				<center>
					<div style='height:115px;'></div> <!-- SPACE -->
						<div id='platform' style='width:1200px; border:1px solid black;display:flex;background-color:white;border-radius:10px;padding:20px;'>
							<div style='border:1px solid;display:inline-block;margin-right:20px;'>
								<img src='$user[AVATAR_IMG_URL]' title='Your Avatar' /><br><br><br>
								<a style='color:grey;font-weight:bold;' href='render.php?reset=yes'>Reset?</a>
							</div>

							<div style='border:1px solid;display:inline-block;width:80%;'>
							<h1 style='text-align:left;padding-left:15px;'>Customise <a href='#'><i class='fa fa-question-circle'></i></a>";

							if(isset($_GET['rendering']) && isset($_GET['type'])){
								echo"- Please wait while we render your avatar!";
								// go again, but stage = 2
								$id = $_GET['rendering'];
								$type = $_GET['type'];
								sleep(1.25);
								echo"<script>window.location='render.php?id=$id&type=$type&stage=finished'</script>";
								exit;
							}

							echo"</a></h1>
							<h3 style='text-align:left;padding-left:15px;'>Hats & Tools</h3>";
							// get hats
							$OurHatsQ = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `USER_ID`='$user[0]'");
							if(mysqli_num_rows($OurHatsQ) > 0){
								// echo erm
								while($CrateInfo = mysqli_fetch_array($OurHatsQ)){
									$HatQ = mysqli_query($conn, "SELECT * FROM `ec_items` WHERE `ID`='$CrateInfo[ITEM_ID]'");
									$Hat = mysqli_fetch_array($HatQ);
									if($Hat['RARE']=='YES'){
										$borderC = "red";
									}else{
										$borderC = "black";
									}

									if($Hat['ID']==-1){
										echo"";
									}else{
										if($Hat['LAYER']=='HEAD'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Hat</text>
											</a></div>
										";
										}

										if($Hat['LAYER']=='MASK'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Face Wear</text>
											</a></div>
										";
										}

										if($Hat['LAYER']=='BODY'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Body</text>
											</a></div>
										";
										}

										if($Hat['LAYER']=='TOOL'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Tool</text>
											</a></div>
										";
										}


										if($Hat['LAYER']=='FACE'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Face</text>
											</a></div>
										";
										}
										
										if($Hat['LAYER']=='HAIR'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Hair</text>
											</a></div>
										";
										}
										
										if($Hat['LAYER']=='EYES'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Eyes</text>
											</a></div>
										";
										}
										
										if($Hat['LAYER']=='HEAD_2'){
											echo"<div style='margin-top:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
												<text style='color:grey;font-size:12.5px'>Bigger Hat</text>
											</a></div>
										";
										}
									}

								}
							}else{
								echo"<h5 style='text-align:left;padding-left:15px;'><i>You have no hats</i></h5>";
							}
							
							echo"
							<div style='border-top:1px solid;width:80%;margin-top:10px;'></div>
							<text style='color:grey;font-size:15px;'>Currently Wearing</text><br>";

							// get currenty wearing
							$CurrentWearQ = mysqli_query($conn, "SELECT * FROM `ec_avatar` WHERE `USER_ID`='$user[0]'");
							$CurrentWear = mysqli_fetch_array($CurrentWearQ);
							if($CurrentWear['BODY_ITEM_ID'] !=-1 || $CurrentWear['TOOL_ITEM_ID'] !=-1 || $CurrentWear['MASK_ITEM_ID'] !=-1 || $CurrentWear['FACE_ITEM_ID']!=-1 || $CurrentWear['HEAD_ITEM_ID']!=-1 || $CurrentWear['EYES_ITEM_ID']!=-1 || $CurrentWear['HAIR_ITEM_ID']!=-1 || $CurrentWear['HEAD_2_ITEM_ID']!=-1 || $CurrentWear['SHIRT_ITEM_ID']!=-1 || $CurrentWear['TROU_ITEM_ID']!=-1){
								// they are wearing something.
								$WearingQ = mysqli_query($conn, "SELECT * FROM `ec_avatar` WHERE `USER_ID`='$user[0]'");
								$WearingPre = mysqli_fetch_array($WearingQ);
								$AllNames = array('BODY_ITEM_ID','TOOL_ITEM_ID','MASK_ITEM_ID','FACE_ITEM_ID','HEAD_ITEM_ID','EYES_ITEM_ID','HAIR_ITEM_ID','HEAD_2_ITEM_ID','SHIRT_ITEM_ID','TROU_ITEM_ID');
								for($i = 0; $i < count($AllNames); $i++){
									// get item
									$x = $AllNames[$i];
									$HatQ2 = mysqli_query($conn,"SELECT * FROM `ec_items` WHERE `ID`='$WearingPre[$x]'");
									$Hat2 = mysqli_fetch_array($HatQ2); // hat2 and hatq2 not to collide with hat or hatq so i added 2
									if($Hat2[0]==-1){
										// do nothing
									}else{
										if($Hat2['RARE']=='YES'){
											$borderC2 = "red";
										}else{
											$borderC2 = "black";
										}

										$maxCharSplit = array();

										$string = str_split($AllNames[$i]);
										
										for($c = 0; $c < count($string); $c++){
											if($string[$c]=="_"){
												// current c = first _, c-1 is max char
												$maxChar = $c - 1;
												array_push($maxCharSplit, $maxChar);
											}
										}
										$differencePre = count($string) - $maxCharSplit[0];
										$difference = $differencePre - 1;
										$remove = strtolower(substr($x, 0, -$difference));
										
										if($AllNames[$i]=='HEAD_2_ITEM_ID'){
											$remove = "head_2";
										}
										echo"<div style='display:inline-block;border:1px solid $borderC2;border-radius:5px;margin-right:10px;'><a href='render.php?remove=$remove&type=hat&id=0'>
											<img src='$Hat2[PREVIEW_IMG_URL]' title='$Hat2[NAME]' /><br>
											<text style='color:red;font-size:12.5px'>REMOVE</text>
										</a></div>
									";
									}
								}
							}else{
								echo"<i><text style='color:grey;font-size:12.5px;'><br><br>Not Wearing Anything!</text></i>";
							}
							echo"
							<h3 style='text-align:left;padding-left:15px;'>Custom Assets</h3>";
							
							$OurHatsQ = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `USER_ID`='$user[0]'");
							while($CrateInfo = mysqli_fetch_array($OurHatsQ)){
								$HatQ = mysqli_query($conn, "SELECT * FROM `ec_items` WHERE `ID`='$CrateInfo[ITEM_ID]'");
								$Hat = mysqli_fetch_array($HatQ);
								
								if($Hat['LAYER']=='SHIRT'){
									echo"<div style='margin-top:2.5px;margin-bottom:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
										<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
										<text style='color:grey;font-size:12.5px'>Shirt</text>
										</a></div>
									";
								}
								
								if($Hat['LAYER']=='TROU'){
									echo"<div style='margin-top:2.5px;margin-bottom:2.5px;display:inline-block;border:1px solid $borderC;border-radius:5px;margin-right:10px;'><a href='render.php?id=$Hat[0]&type=hat'>
										<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]' /><br>
										<text style='color:grey;font-size:12.5px'>Trousers</text>
										</a></div>
									";
								}
							}
							
							if(mysqli_num_rows($OurHatsQ)<1){
								echo"<h5 style='text-align:left;padding-left:15px;'><i>You have no Shirts or Trousers</i></h5>";
							}
							echo"
							</div>
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
