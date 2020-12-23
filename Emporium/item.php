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
			// get item id
			if(isset($_GET['id'])){
				$id = mysqli_real_escape_string($conn, $_GET['id']);
				# see if it exists
				$hatExists = mysqli_query($conn, "SELECT * FROM `ec_items` WHERE `ID`='$id'");
				if(mysqli_num_rows($hatExists) > 0){
					
					// global vars
					if($id < 1){
						header("Location: ../Emporium/"); exit;
					}
					
					$Hat = mysqli_fetch_array($hatExists);

					//update sales
					$anySold = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$id'");
					$Sold = mysqli_num_rows($anySold);
					mysqli_query($conn,"UPDATE `ec_items` SET `SALES`='$Sold' WHERE `ID`='$id' ");

					//note: must be verified to sell, trade and possible more
					if(isset($_POST['saleId']) && is_numeric($_POST['saleId'])){
						// check if verified
						if($user['VERIFIED']=='YES'){
							// see if the sale is still there
							$id = mysqli_real_escape_string($conn, $_POST['saleId']);
							$saleQ = mysqli_query($conn, "SELECT * FROM `ec_reselling` WHERE `ID`='$id'"); // you could add AND `ITEM_ID`
							if(mysqli_num_rows($saleQ)){
								// sale exists check price and see if we have enough whatever ID they submitted they will buy that hat
								$sale = mysqli_fetch_array($saleQ);
								// see if we buying from ourself
								if($user[0]==$sale['USER_ID']){
									header("Location: ?id=$Hat[0]"); exit;
								}
								
								if($user['GOLD'] >= $sale['PRICE']){
									// buy it!
									// remove money
									$newMoney = $user['GOLD'] - $sale['PRICE'];
									mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$newMoney' WHERE `ID`='$user[0]'");
									// give money
									$otherUserQ = mysqli_query($conn, "SELECT * FROM `ec_users` WHERE `ID`='$sale[USER_ID]'");
									$otherUser = mysqli_fetch_array($otherUserQ);
									$tax = round($sale['PRICE'] / 100) * 30;
									$newGain = $sale['PRICE'] - $tax;
									$moneyGain = $otherUser['GOLD'] + $newGain;
									mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$moneyGain' WHERE `ID`='$otherUser[0]'");
									mysqli_query($conn, "UPDATE `ec_crate` SET `USER_ID`='$user[0]' WHERE `ITEM_ID`='$Hat[0]' AND `USER_ID`='$sale[USER_ID]' AND `SERIAL`='$sale[SERIAL]'");
									mysqli_query($conn, "DELETE FROM `ec_reselling` WHERE `ID`='$sale[0]'");
									// send other user he sold his item
									$curtime = time();
									mysqli_query($conn, "INSERT INTO `ec_messages` VALUES(NULL, '1','$otherUser[0]','You have sold an item!','You have sold $Hat[NAME] for $newGain Gold (After TAX) to $otherUser[USERNAME]','','$curtime','NO')");
									header("Location: ?id=$Hat[0]&SELLSUCC");
								}else{
									header("Location: ?id=$Hat[0]&NESELL"); exit;
								}
							}else{
								exit; // we dont want to give the try hards an error message? they may think there on to something, waste their time
							}
						}else{
							// must be verified to buy
							echo"<script>alert('Your account must be verified to buy items.')</script>";
							echo"<script>window.location='../Verify/'</script>";exit;
						}
					}

					if(isset($_GET['remove']) && is_numeric($_GET['remove'])){
						// see if its there sale
						$delid = mysqli_real_escape_string($conn, $_GET['remove']);
						$ourSale = mysqli_query($conn, "SELECT * FROM `ec_reselling` WHERE `ID`='$delid' AND `USER_ID`='$user[0]'");
						if(mysqli_num_rows($ourSale) > 0){
							// remove
							mysqli_query($conn,"DELETE FROM `ec_reselling` WHERE `ID`='$delid'");
							header("Location: ../Emporium/item.php?id=$Hat[0]"); exit;
						}else{
							header("Location: ../"); exit;
						}
					}

					// yes it does, get hat information;
					if($Hat['RARE']=='YES'){
						$RareColor = 'red';
						$BorderBuyC = 'red';
						$backgroundIMG = "background-image: url(../EpicClubRebootMisc/IMGS/rare.png);";
					}else{
						$RareColor = 'black';
						$BorderBuyC = 'grey';
						$backgroundIMG = "";
					}

					$made = gmdate("l jS M Y",$Hat['TIME']);
					echo"
						<center>
							<div style='height:115px;'></div> <!-- SPACE -->
								<div id='platform' style='width:1200px; margin-bottom:10px; border:1px solid $RareColor;background-color:white;border-radius:10px;padding:20px;display:flex;flex-wrap:wrap;$backgroundIMG'>
									
									<div style='display:inline-block;width:20%;border-right:1px solid $BorderBuyC;'>
										<h2>$Hat[NAME]</h2><br>
										<img src='$Hat[PREVIEW_IMG_URL]' title='$Hat[NAME]'><br><br><br>
										<text style='font-size:20px;'><i style='padding-right:5px;' class='fa fa fa-archive'></i> $Hat[SALES] <br><br>
										<text style='font-size:20px;'><i style='padding-right:5px;' class='fa fa fa-calendar'></i> $made";
										echo" <br><br>";
										if($Hat['RARE']=='YES'){
											echo"<div style='border-top:1px dotted red;'></div>";
											
											if(isset($_GET['NESELL'])){
												echo "<text style='color:red'>Not enough Money!</text><br><br>";
											}elseif(isset($_GET['SELLSUCC'])) {
												echo "<text style='color:green'>Brought!</text><br><br>";
											}
											
											$IsSelling = mysqli_query($conn, "SELECT * FROM `ec_reselling` WHERE `ITEM_ID`='$Hat[0]' ORDER BY `PRICE`");
											if(mysqli_num_rows($IsSelling) > 0){
												while($Sale = mysqli_fetch_array($IsSelling)){
													$SellerQ = mysqli_query($conn, "SELECT * FROM `ec_users` WHERE `ID`='$Sale[USER_ID]'");
													$Seller = mysqli_fetch_array($SellerQ);
													echo"
													<div style='display:flex;'>
														<img width='50' height='75' src='$Seller[AVATAR_IMG_URL]' title='$Seller[1]' />
														<div style='width:70%;text-align:left;'>
															<text style='font-size:20px'>#$Sale[SERIAL] - </text>
															<a href='../User/?id=$Seller[0]'><text style='font-size:15px;'>$Seller[1]</text></a><br>
															<form method='post'>
																<button style='padding:2.5px;border:1px solid grey;margin-left:30px;padding-top:7.5px;padding-bottom:7.5px;width:110px;' name='saleId' value='$Sale[0]'>
																	<i style='color:#f4ff32;' class='fa fa-circle'></i> <text style='font-weight:bold;'>$Sale[PRICE]</text>
																</button>";
																$ourItem = mysqli_query($conn, "SELECT * FROM `ec_reselling` WHERE `ITEM_ID`='$Hat[0]' AND `USER_ID`='$user[0]' AND `SERIAL`='$Sale[SERIAL]'");
																if(mysqli_num_rows($ourItem) > 0){
																	echo "<a href='?id=$Hat[0]&remove=$Sale[0]' style='color:red;font-size:10px;'>Remove</a>";
																}
															echo"</form>
														</div>
													</div>
													";
												}

												// check if we have item
												$haveRare = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$Hat[0]' AND `USER_ID`='$user[0]'");
												if(mysqli_num_rows($haveRare) > 0){
													echo "<br><br><a style='color:grey;font-size:20px;' href='sitem.php?id=$Hat[0]'>Sell?</a>";
												}
											}else{
												echo "<br><i style='font-size:15px;'>No one is selling</i>";
												// check if we have item
												$haveRare = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$Hat[0]' AND `USER_ID`='$user[0]'");
												if(mysqli_num_rows($haveRare) > 0){
													echo "<br><br><a style='color:grey;font-size:20px;' href='sitem.php?id=$Hat[0]'>Sell?</a>";
												}
											}
										}
										echo"
									</div>
									
									<div style='display:inline-block;width:78%;display:flex;flex-direction:column;padding-left:15px;'>";
									// see if they have a request for buying the hat!
									if(isset($_GET['buy'])){
										// good to go
										// check what mode
										if($_GET['buy']=='g'){
											// using gold
											// see if item is rare
											if($Hat['RARE']=='YES'){
												// see if in stock
												if($Hat['STOCK'] > 0){
													// check if we have enough money
													$EnoughMoney = $user['GOLD'] - $Hat['GOLD_PRICE'];
													if($EnoughMoney >= 0){
														// check if we have item
														$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
														if(mysqli_num_rows($haveItem) < 1){
															// check if item offsale.
															if($Hat['OFFSALE']=='YES'){
																header("Location: ../"); exit;
															}
															
															// check if we have brought rare already 
															$alreadyGotQ = mysqli_query($conn, "SELECT * FROM `ec_anti_horde` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
															if(mysqli_num_rows($alreadyGotQ) > 0){
																echo"<script>alert('You already brought one!')</script>";
																echo"<script>window.location='../Emporium/item?id=$id'</script>"; exit;
															}

															// buy
															$NewStock = $Hat['STOCK'] - 1;
															$SerialPre = $Hat['ORIGINAL_STOCK'] - $NewStock;
															if($SerialPre == 0){
																$Serial = 1; # win
															}else{
																$Serial = $Hat['ORIGINAL_STOCK'] - $NewStock;
															}
															mysqli_query($conn, "UPDATE `ec_items` SET `STOCK`='$NewStock' WHERE `ID`='$id'"); #reserve stock first
															mysqli_query($conn, "INSERT INTO `ec_crate` VALUES(NULL,'$id','$user[0]','$Serial')");#insert into our inventory
															mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$EnoughMoney' WHERE `ID`='$user[0]'");#take money
															mysqli_query($conn, "INSERT INTO `ec_anti_horde` VALUES(NULL,'$id','$user[0]','1')");
															#Done!
															echo"<text style='border:1px solid green;background-color:#1dff00;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Brought item!<br></text>";
														}else{
															echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>You already own this item!<br></text>";
														}
														
													}else{
														// not enough
														echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Not enough money!<br></text>";
													}
												}else{
													echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Out of Stock!<br></text>";
												}
											}else{
												// if hat offsale
												if($Hat['OFFSALE']=='YES'){
													echo"<script>window.location='../'</script>"; exit;
												}
												// Check if we have enough money
												$EnoughMoney = $user['GOLD'] - $Hat['GOLD_PRICE'];
												if($EnoughMoney >= 0){
													// check if we have item
													$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
													if(mysqli_num_rows($haveItem) < 1){
														// buy!
														mysqli_query($conn, "INSERT INTO `ec_crate` VALUES(NULL,'$id','$user[0]','0')");#insert into our inventory
														mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$EnoughMoney' WHERE `ID`='$user[0]'");#take money
														#Done!
														echo"<text style='border:1px solid green;background-color:#1dff00;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Brought item!<br></text>";
													}else{
														echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>You already own this item!<br></text>";
													}
												}else{
													// not enough
													echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Not enough money!<br></text>";
												}
											}
										}elseif($_GET['buy']=='s'){
											if($Hat['RARE']=='YES'){
												// see if in stock
												if($Hat['STOCK'] > 0){
													// check if we have enough money
													$EnoughMoney = $user['SILVER'] - $Hat['SILVER_PRICE'];
													if($EnoughMoney >= 0){
														// check if we have item
														$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
														if(mysqli_num_rows($haveItem) < 1){
															
															if($Hat['OFFSALE']=='YES'){
																header("Location: ../"); exit;
															}
															
															// buy
															$NewStock = $Hat['STOCK'] - 1;
															$SerialPre = $Hat['ORIGINAL_STOCK'] - $NewStock;
															if($SerialPre == 0){
																$Serial = 1; # win
															}else{
																$Serial = $Hat['ORIGINAL_STOCK'] - $NewStock;
															}
															
															// check if we have brought rare already 
															$alreadyGotQ = mysqli_query($conn, "SELECT * FROM `ec_anti_horde` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
															if(mysqli_num_rows($alreadyGotQ) > 0){
																echo"<script>alert('You already brought one!')</script>";
																echo"<script>window.location='../Emporium/item?id=$id'</script>"; exit;
															}
															
															mysqli_query($conn, "INSERT INTO `ec_anti_horde` VALUES(NULL,'$id','$user[0]','1')");
															mysqli_query($conn, "UPDATE `ec_items` SET `STOCK`='$NewStock' WHERE `ID`='$id'"); #reserve stock first
															mysqli_query($conn, "INSERT INTO `ec_crate` VALUES(NULL,'$id','$user[0]','$Serial')");#insert into our inventory
															mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$EnoughMoney' WHERE `ID`='$user[0]'");#take money
															#Done!
															echo"<text style='border:1px solid green;background-color:#1dff00;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Brought item!<br></text>";
														}else{
															echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>You already own this item!<br></text>";
														}
														
													}else{
														// not enough
														echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Not enough money!<br></text>";
													}
												}else{
													echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Out of Stock!<br></text>";
												}
											}else{
												if($Hat['OFFSALE']=='YES'){
													echo"<script>window.location='../'</script>"; exit;
												}

												// Check if we have enough money
												$EnoughMoney = $user['SILVER'] - $Hat['SILVER_PRICE'];
												if($EnoughMoney >= 0){
													// check if we have item
													$haveItem = mysqli_query($conn, "SELECT * FROM `ec_crate` WHERE `ITEM_ID`='$id' AND `USER_ID`='$user[0]'");
													if(mysqli_num_rows($haveItem) < 1){
														// buy!
														mysqli_query($conn, "INSERT INTO `ec_crate` VALUES(NULL,'$id','$user[0]','0')");#insert into our inventory
														mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$EnoughMoney' WHERE `ID`='$user[0]'");#take money
														#Done!
														echo"<text style='border:1px solid green;background-color:#1dff00;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Brought item!<br></text>";
													}else{
														echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>You already own this item!<br></text>";
													}
												}else{
													// not enough
													echo"<text style='border:1px solid red;background-color:#ed3648;color:white;font-weight:bold;font-size:20px;padding:5px;width:400px;'>Not enough money!<br></text>";
												}
											}
										}
									}
									echo"<br>
										<i><i style='font-size:25px;' class='fa fa-quote-left'></i>  
										$Hat[DESCRIPTION]  
										<i style='font-size:25px;' class='fa fa-quote-right'></i></i>
										<br><br><br>";
										if($Hat['OFFSALE']=='YES'){
										 	echo"<text style='color:grey;font-size:25px;'>Item Offsale</text>";
										}else{
											if($Hat['GOLD_PRICE'] > 0){
												echo"<a style='border:1px solid $BorderBuyC;padding:5px;border-radius:5px;font-size:25px;width:250px;' href='?id=$Hat[0]&buy=g'><i style='padding-right:2.5px;color:#f4ff32;' class='fa fa-circle'></i>$Hat[GOLD_PRICE]</a>";
											}

											if($Hat['SILVER_PRICE']){
												echo"<a style='margin-top:5px;border:1px solid $BorderBuyC;padding:5px;border-radius:5px;font-size:25px;width:250px;' href='?id=$Hat[0]&buy=s'><i style='padding-right:2.5px;color:grey;' class='fa fa-circle'></i>$Hat[SILVER_PRICE]</a>";
											}
										}

										if($Hat['RARE']=='YES'){
											if($Hat['STOCK'] > 0){
												echo"<text style='margin-top:5px;color:red;padding:5px;border-radius:5px;font-size:15px;width:250px;'><i style='padding-right:7.5px;' class='fa fa-industry'></i>$Hat[STOCK] Left</text>";
											}
										}
										echo"<br><br><br>";
										if($Hat['RARE']=='YES'){
											echo"
											<h2 style='text-align:left;'>Recent Average Price</h2>
											<img src='http://via.placeholder.com/800x150' />
											<br>";
										}

										// comment count
										$NumCommentsQ = mysqli_query($conn, "SELECT * FROM `ec_item_comments` WHERE `ITEM_ID`='$id'");
										$NumComments = mysqli_num_rows($NumCommentsQ);
										echo"
										<h2 style='text-align:left;'>Comments ($NumComments)</h2>
										";
										if(isset($_POST['comment'])){
											// check if they posted in last 30 seconds
											$curtime = time();
											$last30seconds = $curtime - 30;
											// last comment
											$lastCommentQ = mysqli_query($conn, "SELECT * FROM `ec_item_comments` WHERE `USER_ID`='$user[0]' ORDER BY `TIME`DESC LIMIT 1");
											$lastComment = mysqli_fetch_array($lastCommentQ);
											if($last30seconds > $lastComment['TIME']){
												//good, POST
												$comment = strip_tags(mysqli_real_escape_string($conn, $_POST['comment']));
												mysqli_query($conn, "INSERT INTO `ec_item_comments` VALUES(NULL,'$id', '$user[0]', '$comment', '$curtime', '0', '0')");
												echo"
													<form method='post' action=''>
														<textarea style='width:500px;height:100px;border:1px solid;' maxlength='255' disabled>Comment Posted!</textarea><br>
														<button style='width:500px;border-radius:0px;' disabled>Comment!</button>
													</form>
												";
											}else{
											echo"
												<form method='post' action=''>
													<textarea style='width:500px;height:100px;border:1px solid;' maxlength='255' disabled>You have commented in the last 30 seconds!</textarea><br>
													<button style='width:500px;border-radius:0px;' disabled>Comment!</button>
												</form>
											";
											}
										}else{
											echo"
											<form method='post' action=''>
												<textarea style='width:500px;height:100px;border:1px solid;' name='comment' maxlength='255' required></textarea><br>
												<button style='width:500px;border-radius:0px;'>Comment!</button>
											</form>
											";
										}

										// get comments + page
										// near pefect page setting, almost perfected
										if(isset($_GET['page'])){
											$page = mysqli_real_escape_string($conn, $_GET['page']);
											if(is_numeric($page)){
												$offsetPre = $page * 20;
												$offset = $offsetPre - 20;
												$nPage = mysqli_real_escape_string($conn, $_GET['page']) + 1;
												$bPage = mysqli_real_escape_string($conn, $_GET['page']) - 1;
											}else{
												$page = 1;
												$nPage = 2;
												$offset = 0;
												$bPage = 1;
											}
										}else{
											$bPage = 1;
											$page = 1;
											$nPage = 2;
											$offset = 0;
										}

										// check if have comments
										$hasComments = mysqli_query($conn, "SELECT * FROM `ec_item_comments` WHERE `ITEM_ID`='$id'");
										if(mysqli_num_rows($hasComments) > 0){
											$getComments = mysqli_query($conn, "SELECT * FROM `ec_item_comments` WHERE `ITEM_ID`='$id' ORDER BY `TIME` DESC LIMIT 20 OFFSET $offset");

											// check if page isset and has no comments
											if(isset($_GET['page']) && mysqli_num_rows($getComments) < 1){
												echo"<script>window.location='../Emporium/item.php?id=$id';</script>";
												exit;
											}

											while($CommentData = mysqli_fetch_array($getComments)){
												$UserInfoQ = mysqli_query($conn, "SELECT * FROM `ec_users` WHERE `ID`='$CommentData[USER_ID]'");
												$UserInfo = mysqli_fetch_array($UserInfoQ);
												$date = gmdate("jS l F Y",$CommentData['TIME']);
												echo"
												<div style='border:1px solid;width:900px;margin-bottom:10px;display:flex;'>
														<div style='width:23%;border-right:1px solid;'>
															<a href='../User/?id=$UserInfo[0]'>
																<img src='$UserInfo[AVATAR_IMG_URL]' width='77' height='115' /><br>";
																if(strlen($UserInfo['USERNAME']) > 10){
																	// cut it
																	$NewName = substr($UserInfo['USERNAME'], 0, 10)."...";
																	echo $NewName;
																}else{
																	echo $UserInfo['USERNAME'];
																}
																echo"
															</a>
														</div>
													

													<div style='width:75%;'>
													<div style='margin:0px;text-align:left;'>
														<text style='text-align:left;font-size:12.5px;color:grey;width:100px;padding-left:5px;'>$date</text>
													</div>
													<div style='margin:0px;text-align:right;'>
														<a href='#/Report/ItemCommentId=$CommentData[0]' style='text-align:left;font-size:12.5px;color:grey;width:100px;padding-left:5px;'>Report</a>
													</div>
														<br>
														$CommentData[COMMENT]<br>";
														if($user['POWER']!=="MEMBER"){
															echo"
																<a href='#/Mod/itemTools.php?CensorComment=$CommentData[0]' style='text-align:left;font-size:12.5px;color:grey;'>Censor</a>
															";
														}
														echo"
													</div>
												</div>
												";
											}
										}else{
											echo"<br><br><i>This hat has no comments!</i>";
										}
										echo"<br>
										<div><a class='fa fa-chevron-circle-left' href='?id=$id&page=$bPage'></a>  |  <a class='fa fa-chevron-circle-right' href='?id=$id&page=$nPage'></a></div>
									<div>
								</div>
							</div>
						</center>
					</body>";
				}else{
					// bye!
					echo"<script>window.location='../Emporium';</script>"; exit;
				}
			}else{
				# bye!
				echo"<script>window.location='../Emporium';</script>"; exit;
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
