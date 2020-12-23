<?php
	include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/connect.php' );
	$site = $_SERVER['DOCUMENT_ROOT'];
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
			// check if they have a render row
			
			$RenderRowQ = mysqli_query($conn, "SELECT * FROM `ec_avatar` WHERE `USER_ID`='$user[0]'");
			if(mysqli_num_rows($RenderRowQ) < 1){
				// insert
				mysqli_query($conn, "INSERT INTO `ec_avatar` VALUES('$user[0]','-1','-1','-1','-1','-1','-1','-1','-1','-1','-1')");
			}

			if(isset($_GET['reset'])){
				// set avatar default
				mysqli_query($conn, "UPDATE `ec_users` SET `AVATAR_IMG_URL`='../EpicClubRebootMisc/IMGS/template.png' WHERE `ID`='$user[0]'");#update image url
				mysqli_query($conn, "UPDATE `ec_avatar` SET `BODY_ITEM_ID`='-1', `MASK_ITEM_ID`='-1' ,`TOOL_ITEM_ID`='-1', `FACE_ITEM_ID`='-1', `HEAD_ITEM_ID`='-1', `HEAD_2_ITEM_ID`='-1', `EYES_ITEM_ID`='-1', `HAIR_ITEM_ID`='-1', `SHIRT_ITEM_ID`='-1', `TROU_ITEM_ID`='-1' WHERE `USER_ID`='$user[0]'");
				echo"<script>window.location='../Avatar/';</script>";
				exit;
			}

			if(isset($_GET['remove'])){ // why cant all my code be as clean as this :()
				$tools = array('body','tool','mask','face','head','eyes','hair','shirt','trou','head_2'); # add to array for more layers? not as easy as that, you need to add the layer name to other queries and statments :(
				$AllNames = array('BODY_ITEM_ID','TOOL_ITEM_ID','MASK_ITEM_ID','FACE_ITEM_ID','HEAD_ITEM_ID','EYES_ITEM_ID','HAIR_ITEM_ID','HEAD_2_ITEM_ID','SHIRT_ITEM_ID','TROU_ITEM_ID');				
				
				for($x = 0; $x < count($tools); $x++){
					if($_GET['remove']==$tools[$x]){
						// let me add more anti hacks and add render
						$text = strtoupper($tools[$x]."_ITEM_ID");
						mysqli_query($conn, "UPDATE `ec_avatar` SET `$text`='-1' WHERE `USER_ID`='$user[0]'");
						// see if they are wearing anything else
						$WearingAny = mysqli_query($conn, "SELECT * FROM `ec_avatar` WHERE `BODY_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `TOOL_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `MASK_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `FACE_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `HEAD_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `HEAD_2_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `EYES_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `HAIR_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `SHIRT_ITEM_ID`!='-1' AND `USER_ID`='$user[0]' OR `TROU_ITEM_ID`!='-1' AND `USER_ID`='$user[0]'");
						if(mysqli_num_rows($WearingAny) > 0){
							// render top layer <- easy hack
							for($d = 0; $d < count($AllNames); $d++){
								$TopLayerQ = mysqli_query($conn, "SELECT `$AllNames[$d]` FROM `ec_avatar` WHERE `$AllNames[$d]`<>'-1' AND `USER_ID`='$user[0]'");
								if(mysqli_num_rows($TopLayerQ) > 0){
									// we got other hat we need to render.
									$renderId = mysqli_fetch_array($TopLayerQ);
									echo"<script>window.location='render.php?id=$renderId[0]&type=hat'</script>";
									exit;
								}
							}
							
						}else{
							// they are removing their last item, give them template avatar
							$text2 = strtoupper($tools[$x]."_ITEM_ID"); // text2 to stop collision with text
							mysqli_query($conn, "UPDATE `ec_avatar` SET `$text2`='-1' WHERE `USER_ID`='$user[0]'");
							mysqli_query($conn, "UPDATE `ec_users` SET `AVATAR_IMG_URL`='../EpicClubRebootMisc/IMGS/template.png' WHERE `ID`='$user[0]'");
							echo"<script>window.location='../Avatar/?refresh'</script>";
							exit;
						}
						
					}
				}
			}
		
			echo"
			<body>";
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/global.php' );
				include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/global.php' );
				// see if id is set with type
				if(isset($_GET['id']) && isset($_GET['type'])){
					// check for right type
					if($_GET['type']=='hat' || $_GET['type']=='shirt' || $_GET['type']=='trousers'){ // i dont know why i did this...
						// see if id is numeric
						$type = mysqli_real_escape_string($conn, $_GET['type']);
						if(is_numeric($_GET['id'])){

							// this is for shills
							if(!isset($_GET['remove']) && isset($_GET['id']) && $_GET['id']==0){
								echo"<script>window.location='../GoAwayPlease'</script>";
								exit;
							}

							$id = mysqli_real_escape_string($conn, $_GET['id']);
							if($id < 0){
								#bye! 
								header("Location: ../"); exit;
							}

							if($type=='hat'){
								// see if hat exists
								$HatInfoQ = mysqli_query($conn, "SELECT * FROM `ec_items` WHERE `ID`='$id'");
								if(mysqli_num_rows($HatInfoQ) > 0){
									// get hat info
									$Hat = mysqli_fetch_array($HatInfoQ);
									
									// see if they have item
									$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$Hat[0]' AND `USER_ID`='$user[0]'");
									if(mysqli_num_rows($haveItem) < 1){
										// begone nooby hacker!
										header("Location: ../"); exit;
									}

									if($Hat['LAYER']=='BODY'){
										$ITEM_TYPE = "BODY_ITEM_ID";
									}elseif($Hat['LAYER']=='FACE') {
										$ITEM_TYPE = "FACE_ITEM_ID";
									}elseif($Hat['LAYER']=='HEAD'){
										$ITEM_TYPE = "HEAD_ITEM_ID";
									}elseif($Hat['LAYER']=='TOOL'){
										$ITEM_TYPE = "TOOL_ITEM_ID";
									}elseif($Hat['LAYER']=='MASK'){
										$ITEM_TYPE = "MASK_ITEM_ID";
									}elseif($Hat['LAYER']=='HEAD_2'){
										$ITEM_TYPE = "HEAD_2_ITEM_ID";
									}elseif($Hat['LAYER']=='EYES'){
										$ITEM_TYPE = "EYES_ITEM_ID";
									}elseif($Hat['LAYER']=='HAIR'){
										$ITEM_TYPE = "HAIR_ITEM_ID";
									}elseif($Hat['LAYER']=='SHIRT'){
										$ITEM_TYPE = "SHIRT_ITEM_ID";
									}elseif($Hat['LAYER']=='TROU'){
										$ITEM_TYPE = "TROU_ITEM_ID";
									}
									# update render tables ( ͡° ͜ʖ ͡°)
									mysqli_query($conn, "UPDATE `ec_avatar` SET `$ITEM_TYPE`='$Hat[0]' WHERE `USER_ID`='$user[0]'");
									//echo "<script>alert('$ITEM_TYPE')</script>";
									
									// render whole table, loop through it all. -1 = transparent layer / nothing
									ini_set("allow_url_fopen", "On");
									$RenderRow = mysqli_fetch_array($RenderRowQ);									
									$face_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[FACE_ITEM_ID]'");
									$face_item_img_url = mysqli_fetch_array($face_item_img_urlQ);

									$tool_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[TOOL_ITEM_ID]'");
									$tool_item_img_url = mysqli_fetch_array($tool_item_img_urlQ);

									$head_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[HEAD_ITEM_ID]'");
									$head_item_img_url = mysqli_fetch_array($head_item_img_urlQ);

									$body_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[BODY_ITEM_ID]'");
									$body_item_img_url = mysqli_fetch_array($body_item_img_urlQ);

									$mask_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[MASK_ITEM_ID]'");
									$mask_item_img_url = mysqli_fetch_array($mask_item_img_urlQ);
									
									$hair_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[HAIR_ITEM_ID]'");
									$hair_item_img_url = mysqli_fetch_array($hair_item_img_urlQ);
									
									$eyes_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[EYES_ITEM_ID]'");
									$eyes_item_img_url = mysqli_fetch_array($eyes_item_img_urlQ);

									$head_2_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[HEAD_2_ITEM_ID]'");
									$head_2_item_img_url = mysqli_fetch_array($head_2_item_img_urlQ);
									
									$shirt_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[SHIRT_ITEM_ID]'");
									$shirt_item_img_url = mysqli_fetch_array($shirt_item_img_urlQ);
									
									$trou_item_img_urlQ = mysqli_query($conn, "SELECT `AVATAR_IMG_URL` FROM `ec_items` WHERE `ID`='$RenderRow[TROU_ITEM_ID]'");
									$trou_item_img_url = mysqli_fetch_array($trou_item_img_urlQ);
									
									if($face_item_img_url['AVATAR_IMG_URL']==-1){
										$faceRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$faceRoot = $face_item_img_url['AVATAR_IMG_URL'];
									}

									if($head_item_img_url['AVATAR_IMG_URL']==-1){
										$headRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$headRoot = $head_item_img_url['AVATAR_IMG_URL'];
									}

									if($body_item_img_url['AVATAR_IMG_URL']==-1){
										$bodyRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$bodyRoot = $body_item_img_url['AVATAR_IMG_URL'];
									}

									if($tool_item_img_url['AVATAR_IMG_URL']==-1){
										$toolRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$toolRoot = $tool_item_img_url['AVATAR_IMG_URL'];
									}

									if($mask_item_img_url['AVATAR_IMG_URL']==-1){
										$maskRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$maskRoot = $mask_item_img_url['AVATAR_IMG_URL'];
									}
									
									if($head_2_item_img_url['AVATAR_IMG_URL']==-1){
										$head_2Root = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$head_2Root = $head_2_item_img_url['AVATAR_IMG_URL'];
									}
									
									if($eyes_item_img_url['AVATAR_IMG_URL']==-1){
										$eyesRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$eyesRoot = $eyes_item_img_url['AVATAR_IMG_URL'];
									}
									
									if($hair_item_img_url['AVATAR_IMG_URL']==-1){
										$hairRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$hairRoot = $hair_item_img_url['AVATAR_IMG_URL'];
									}
									
									if($shirt_item_img_url['AVATAR_IMG_URL']==-1){
										$shirtRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$shirtRoot = $shirt_item_img_url['AVATAR_IMG_URL'];
									}
									
									if($trou_item_img_url['AVATAR_IMG_URL']==-1){
										$trouRoot = "../EpicClubRebootMisc/IMGS/MAIN/blank.png";
									}else{
										$trouRoot = $trou_item_img_url['AVATAR_IMG_URL'];
									}

									$palette_img = imagecreatefrompng("../EpicClubRebootMisc/IMGS/template.png"); // get default body
									$body_img = imagecreatefrompng("$bodyRoot");
									$shirt_img = imagecreatefrompng("$shirtRoot");
									$trou_img = imagecreatefrompng("$trouRoot");
									$face_img = imagecreatefrompng("$faceRoot");
									$eyes_img = imagecreatefrompng("$eyesRoot");
									$hair_img = imagecreatefrompng("$hairRoot");
									$head_img = imagecreatefrompng("$headRoot");
									//echo "<script>alert('$eyesRoot')</script>";
									$mask_img = imagecreatefrompng("$maskRoot");
									$head_2_img = imagecreatefrompng("$head_2Root");
									$tool_img = imagecreatefrompng("$toolRoot");
									imageSaveAlpha($palette_img, true); // make it transparent
									imagecopy($palette_img,$face_img,0,0,0,0,121,181); // add face item
										imagecopy($palette_img,$eyes_img,0,0,0,0,121,181); // add eyes item
									imagecopy($palette_img,$body_img,0,0,0,0,121,181); // add body item
										imagecopy($palette_img,$shirt_img,0,0,0,0,121,181); // add shirt item
										imagecopy($palette_img,$trou_img,0,0,0,0,121,181); // add trousers item
									imagecopy($palette_img,$mask_img,0,0,0,0,121,181); // add mask item
										imagecopy($palette_img,$hair_img,0,0,0,0,121,181); // add hair item
									imagecopy($palette_img,$head_img,0,0,0,0,121,181); // add head item
										imagecopy($palette_img,$head_2_img,0,0,0,0,121,181); // add 2nd head item
									imagecopy($palette_img,$tool_img,0,0,0,0,121,181); // add tool item
									
									/// stackoverflow public code
									function generateRandomString($length = 20) { // higher the length, less collision rates
										    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
										    $charactersLength = strlen($characters);
										    $randomString = '';
										    for ($i = 0; $i < $length; $i++) {
										        $randomString .= $characters[rand(0, $charactersLength - 1)];
										    }
										    return $randomString;
										}
										$uni_string = generateRandomString();

									imagepng($palette_img,"../EpicClubRebootMisc/IMGS/AVATARS/$uni_string.png");

									mysqli_query($conn, "UPDATE `ec_users` SET `AVATAR_IMG_URL`='../EpicClubRebootMisc/IMGS/AVATARS/$uni_string.png' WHERE `ID`='$user[0]'");#update image url
									#echo"<script>alert('')</script>";
									if(isset($_GET['stage'])){
										// done
										echo"<script>window.location='../Avatar/';</script>";
									}else{
										echo"<script>window.location='../Avatar/?rendering=$id&type=$type';</script>";
									}
									
									#go to avatar page with succesful message?
									/* debuggin v1 toolkit xdxdxd
										#echo"<script>window.location='../EpicClubRebootMisc/IMGS/Avatars/$user[0].png';</script>";
										#echo "<img style='margin-top:500px;' src='$head_item_img_url[AVATAR_IMG_URL]' />";
										#echo"~~";
									*/
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
