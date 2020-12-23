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
						<h1 style='text-align:left;padding-left:50px;'>Store</h1>
						<div id='platform' style='width:1200px;border:1px solid black;background-color:white;border-top-right-radius:10px;border-top-left-radius:10px;padding:20px;padding-top:0px;padding-bottom:0px;display:flex;flex-wrap:wrap;'>
							<div style='display:inline-block;width:20%;border-right:1px solid;text-align:left;'>
								<br><a style='color:grey;font-size:22.5px;' href='?type=HAT'>Hats</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=HAT_2'>Second Hat</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=FACE'>Mouths</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=MASK'>Face Wear</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=BODY'>Body</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=TOOL'>Tools</a><br><br><br>
								
								
								<br><a style='color:grey;font-size:22.5px;' href='?type=SHIRT'>Shirts</a><br>
								<br><a style='color:grey;font-size:22.5px;' href='?type=TROU'>Pants</a><br>
								<br><a style='color:grey;font-size:22.5px;font-weight:bold;' href='upload'>Upload</a><br>
							</div>
							<div style='display:inline-block;width:79%;display:flex;flex-wrap:wrap;padding-left:2.5px;'>";
							// Get hats!
							if(isset($_GET['Search'])){

							}else{
								// get hats by default
								if(isset($_GET['page'])){
									$page = mysqli_real_escape_string($conn, $_GET['page']);
									if(is_numeric($page)){
										if($page >= 1){
											$offsetPRE = $page * 28;
											$offset = $offsetPRE - 28;
											$nPage = mysqli_real_escape_string($conn, $_GET['page']) + 1;
											$bPage = mysqli_real_escape_string($conn, $_GET['page']) - 1;
										}else{
											exit("<script>window.location='../Emporium';</script>");
										}
									}else{
										exit("<script>window.location='../Emporium';</script>");
									}
								}else{
									// first page + default offset
									$page = 1;
									$nPage = 2;
									$bPage = 1;
									$offset = 0; 
								}
								
								if(isset($_GET['type'])){
									$type = mysqli_real_escape_string($conn, $_GET['type']);
									$que = "SELECT * FROM `ec_items` WHERE `LAYER`='$type' ORDER BY `ID` DESC LIMIT 30 OFFSET $offset";
								}else{
									$que = "SELECT * FROM `ec_items` WHERE `LAYER`!='SHIRT' AND `LAYER`!='TROU' ORDER BY `ID` DESC LIMIT 30 OFFSET $offset";
								}

								$areHats = mysqli_query($conn, $que);
								if(mysqli_num_rows($areHats) > 0){
									// there are hats!
									while($Hat = mysqli_fetch_array($areHats)){
										if($Hat[0]==-1){
											// show nothing 
										}else{

										if($Hat['RARE']=='YES'){
											$borderColor = "red";
										}else{
											$borderColor = "black";
										}
										
										if(isset($_GET['type'])){
											$height = "300px";
										}else{
											$height = "200px";
										}
										
										echo"
											<a href='item?id=$Hat[0]'>
											<div style='height:$height;width:225px;margin:10px;padding:10px;display:inline-block;border:1.25px solid $borderColor;border-radius:5px;'>
												<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]'></img><br><br>
												<text style='color:grey;font-size:15px;'>$Hat[NAME]</text><br>
												<br>";

												if($Hat['GOLD_PRICE'] > 0){
													echo "<i style='font-size:17.5px;color:#f4ff32;' class='fa fa-circle'></i>  $Hat[GOLD_PRICE]<br>";
												}
												
												if($Hat['SILVER_PRICE'] > 0){
													echo"<i style='font-size:17.5px;color:grey;' class='fa fa-circle'></i>  $Hat[SILVER_PRICE]";
												}

												if($Hat['SILVER_PRICE'] < 1 && $Hat['GOLD_PRICE'] < 1){
													echo"<text style='margin:5px;color:grey;font-size:15px;'>Offsale</text>";
												}

												if($Hat['RARE']=='YES' && $Hat['STOCK'] > 0){
													echo"<br><br><text style='font-weight:bold;font-size:12.5px;color:red;'>$Hat[STOCK] Left!</text>";
												}
												echo"
											</div></a>
										";
										}
									} //echo"<text style='padding-top:150px;font-size:80px;'>...</text>";
								}else{
									echo"<br>  No hats available :(";
								}
							}

							echo"</div>
						</div>
						<div style='border:1px solid;background-color:white;width:1200px;padding:20px;border-top:0px;padding-top:0px;padding-bottom:0px;'>
							<a class='fa fa-chevron-circle-left' href='?page=$bPage'></a>  <text style='font-size:20px;'>$page</text>  <a class='fa fa-chevron-circle-right' href='?page=$nPage'></a><br>
							<text style='font-size:10px;'>Pagination update will arive shortly</text>
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
