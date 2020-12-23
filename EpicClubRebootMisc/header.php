<?php
if(isset($_COOKIE['EPICNAME']) && isset($_COOKIE['EPICPASS'])){
// User nav bar
// Check if logged in or not, if not then show nav bar without extra icons
include($_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/connect.php');
// user creds are storred with $user (EVERY PAGE CHECKS THEM FIRST!)
// But while just editing HTML you must open user with < ? $user[VALUE] ? >
// lets get a copy here aswell for error reasons
// but we will not be editing in the html for error reasons also. you can do this yourself
$username = mysqli_real_escape_string($conn,$_COOKIE['EPICNAME']);
$password = mysqli_real_escape_string($conn,$_COOKIE['EPICPASS']);

$userQ = mysqli_query($conn,"SELECT * FROM `ec_users` WHERE `USERNAME`='$username' AND `PASSWORD`='$password'");
$user = mysqli_fetch_array($userQ);

// Update time online
$curtime = time();
mysqli_query($conn, "UPDATE `ec_users` SET `LAST_ONLINE`='$curtime' WHERE `ID`='$user[0]'");

$maintenance = 0; // 1 = on for all you non programmers
if($maintenance == 0){
	// if header has "maintain" then redirect
}else{	
	if($user['USERNAME']=='Kwame'){ // u can add mroe people
		// allowed to browse site
	}else{
		//not allowed in site
		if (strpos($_SERVER['REQUEST_URI'], "maintain") !== false){
			echo"
			<center style='font-family:Arial, Terminal;'>
				<text style='font-size:60px;font-weight:bold;position:fixed;left:20%;top:35%;'>EpicClub is in<br>Maintenance<br><br>
					<text style='color:grey;font-size:25px;'>Come back later while we maintain the website!</text>
				</text>
				<img style='position:fixed;left:60%;top:30%;' src='../EpicClubRebootMisc/IMGS/maintain.png'></img>
			</center>"; 
			exit;
		}else{
			echo"<script>window.location='../maintain'</script>"; exit;
		}
	} 
}

// daily currency
$lasttime = $user['DAILY_COINS'];
$nextcoins = $user['SILVER'] + 10; // + 10 or + whatever
$nextcoinsVIP = $user['GOLD'] + 10; // + 10 or + whatever
$nextcoinsVIPMEGA = $user['GOLD'] + 20; // + 10 or + whatever
if($curtime <= $lasttime){
	// They are not ready
	// Do nothing
		}else{
	// Add the money
	if($user['VIP']=='NONE'){
		mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$nextcoins' WHERE `ID` = '$user[0]'");
	}elseif($user['VIP']=='VIP'){
		mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$nextcoins' WHERE `ID` = '$user[0]'");
		mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$nextcoinsVIP' WHERE `ID` = '$user[0]'");
	}elseif($user['VIP']=='MEGA_VIP'){
		mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$nextcoins' WHERE `ID` = '$user[0]'");
		mysqli_query($conn, "UPDATE `ec_users` SET `GOLD`='$nextcoinsVIPMEGA' WHERE `ID` = '$user[0]'");
	}else{
		mysqli_query($conn, "UPDATE `ec_users` SET `SILVER`='$nextcoins' WHERE `ID` = '$user[0]'");
	}
	
	// Update next time
	$nexttime = $curtime + 86400;
	mysqli_query($conn, "UPDATE `ec_users` SET `DAILY_COINS`='$nexttime' WHERE `ID`='$user[0]'");
	//echo "$curtime curtime, $nexttime nextime, $nextnexttime updating time - $nextcoins next coints";
}

// Check banned status
$banLogsQ = mysqli_query($conn, "SELECT * FROM `ec_ban_logs` WHERE `USER_ID`='$user[0]'"); 


// Loop to see for active bans
$banned = 0; // 1 more intergar and off to banland kiddo
// $bannedRecords = mysqli_num_rows($banLogsQ);
while($banLogs = mysqli_fetch_array($banLogsQ)){
	$ST = $banLogs['START_TIME'];
	$L = $banLogs['LENGTH'];
	$STL = $ST + $L;
	if($STL > $curtime){
		$banned+=1; // banned kido
		mysqli_query($conn, "UPDATE `ec_users` SET `BANNED`='YES' WHERE `ID`='$user[0]'");
	}

	if($banLogs['LENGTH'] < 0){
		$banned+=1;
		mysqli_query($conn, "UPDATE `ec_users` SET `BANNED`='YES' WHERE `ID`='$user[0]'");
	}

	if($banLogs['LENGTH'] == -11122000){ #terminate number, my crushes bday <3
		$banned+=1;
		mysqli_query($conn, "UPDATE `ec_users` SET `BANNED`='YES' WHERE `ID`='$user[0]'");
		mysqli_query($conn, "UPDATE `ec_ban_logs` SET `START_TIME`='$curtime' WHERE `ID`='$user[0]'");
	}
}

if($user['BANNED']=='YES'){
	$banned+=1;
}

if($banned > 0){
	if (strpos($_SERVER['REQUEST_URI'], "banned") !== false){
		// on banned page
	}else{
		echo"<script>window.location='../banned.php'</script>"; exit; // i need to replace echo scripts with headers
	}
}

// update threads and posts for forums.
$turn = rand(0, 10); // higher the max number, the less 'lag' for site. but less updating time DEFAULT = 10 or 20
if($turn == 1){
	// this user is going to update forums
	$forums = mysqli_query($conn, "SELECT * FROM `ec_forums` WHERE 1");
	$forumsCount = mysqli_num_rows($forums);

	#$tableThreads = array();
	#$tablePosts = array();
	//update threads
	for($i = 0; $i < $forumsCount; $i++){
		$threads = mysqli_query($conn, "SELECT * FROM `ec_forum_threads` WHERE `TABLE_ID`='$i'");
		$threadsCount = mysqli_num_rows($threads);
		mysqli_query($conn,"UPDATE `ec_forums` SET `THREADS`='$threadsCount' WHERE `ID`='$i'");
	}

	// update posts
	for($i = 0; $i < $forumsCount; $i++){
		// Get all posts for current table, then update it
		$postsQ = mysqli_query($conn, "SELECT * FROM `ec_forum_posts` WHERE `TABLE_ID`='$i'");
		$posts = mysqli_num_rows($postsQ);
		mysqli_query($conn, "UPDATE `ec_forums` SET `POSTS`='$posts' WHERE `ID`='$i'");
	}
}


// get membership
$hasMembership = mysqli_query($conn, "SELECT * FROM `ec_membership` WHERE `USER_ID`='$user[0]' AND `ACTIVE`='YES'");
if(mysqli_num_rows($hasMembership) > 0){
	// check if it expires
	$membership = mysqli_fetch_array($hasMembership);
	if($membership['END_TIME'] > $curtime){
		echo"<script>console.log('Thank you for having an active Membership! #11122000')</script>";
	}else{
		// end it
		#mysqli_query($conn, "DELETE FROM `ec_membership` WHERE `ID`='$membership[0]'"); dont delete i still want the records
		mysqli_query($conn, "UPDATE `ec_membership` SET `ACTIVE`='NO' WHERE `ID`='$membership[0]'");
		mysqli_query($conn, "UPDATE `ec_users` SET `VIP`='NONE' WHERE `ID`='$user[0]'");
		echo"<script>alert('Your membership has expired!')</script>";
	}
}


echo"
<head>
	<link rel='stylesheet' type='text/css' href='/EpicClubRebootMisc/global.css' />
	<script type='text/javascript' src='/EpicClubRebootMisc/functions2.js'></script>
	<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' />
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
	<script>
		function Profile(){
			window.location='/User/?id=$user[0]'
			
		function Bell(){
			window.location='/Mod/assets'
		}
	}
	// my eyes only sshhhh
	</script>
</head>

<div style='width:100%;z-index:2;background-color:white;color:black;padding:10px;border:1px solid black;border-top:0px;border-radius-bottom-left:5px;border-radius-bottom-right:5px;position: fixed;' id='navbar'>
	<!--<a style='margin-right:5.5px;margin-left:5.5px;color:grey;' href='#'>LOGO</a>-->  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Dashboard/'>Dashboard</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Emporium/'>Emporium</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Play/'>Play</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Forums/'>Forums</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Upgrade/'>Upgrade</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/News/'>News</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Search/'>Search</a>  |  <a style='margin-right:5.5px;margin-left:5.5px;' href='/Friends/'>Friends</a>  
	";

	// Check for admin status
	if($user['POWER'] != "MEMBER"){
		echo"  |  <a style='margin-right:5.5px;margin-left:5.5px;font-weight:bold;' href='/Mod/'>Moderation Panel</a> ";
	}

	if(strlen($user['USERNAME']) >= 9){
		$size = "10.5px";
	}else{
		$size = "15px;";
	}

	echo"
	<span style='float:right;margin-right:30px;color:#abacad;'>
		<div>
			<div style='float:left;padding:5px;width:100px;font-size:$size;'>Hey $user[1]! </div>
			
			<div style='float:right;width:145px;font-size:20px;padding-left:20px;' >
				<i onclick='GoToM()' style='margin-right:7.5px;color:";
				// check if message pending, if pending color = red else #4f4f4f
				$hasMessageQ = mysqli_query($conn, "SELECT * FROM `ec_messages` WHERE `RECEIVE_ID`='$user[0]' AND `SEEN`='NO'");
				$hasMessage = mysqli_num_rows($hasMessageQ);

				if($hasMessage > 0){
					echo"red";
				}else{
					echo"#4f4f4f";
				}
				
				 echo";' class='fa fa-comment'></i>";
		
				if($user['POWER']!=='MEMBER' && $user['POWER']!=='MODERATOR'){
					$areAssetsQ = mysqli_query($conn, "SELECT * FROM `ec_user_assets` WHERE `STATUS`='PENDING'");
					$areAssets = mysqli_num_rows($areAssetsQ);
					if($areAssets > 0){
						echo"<a href='../Mod/assets' style='margin-right:7.5px;margin-left:7.5px;color:grey;' class='fa fa-bell'>
							<span onclick='Bell()' style='border-radius:50%;border:1px solid red;color:white;background-color:red;font-size:11.5px;position:relative;top:-10px;font-weight:bold;padding:1px;padding-left:3px;padding-right:3px;'>$areAssets </span>
						</a>";
					}else{
						echo"<a href='../Mod/assets' style='margin-right:7.5px;margin-left:7.5px;color:grey;' class='fa fa-bell'></a>";
					}
				}
	
				 echo"
				<i onclick='Gear()' style='margin-right:7.5px;color:#4f4f4f;' class='fa fa-cog'></i>
				<i onclick='Profile()' style='color:#4f4f4f;' class='fa fa-user'></i>";
				
				echo"
			</div>
			
			<div style='float:right;border:1px solid black;display: inline-block;background-color:#abacad;border-radius:5px;color:white;padding:17.5px;padding-top:5px;padding-bottom:5px;font-size:13px;'>
				<a style='color:white;' href='../User/Convert.php'>	
					$user[GOLD] <i style='color:#f4ff32;' class='fa fa-circle'></i>
					$user[SILVER] <i style='color:#e2e2e2;' class='fa fa-circle'></i>
				</a>
			</div>
		</div>
	</span>
</div>
<!-- Use for alert only, comment out with HTML comment RED for danger, ORANGE for warning, GREEN for notice -->
<div style='color:white;width:99.9%;background-color:red;padding:10px;padding-top:60px;position:fixed;z-index:-1;'>
	<center>
		<b>A new site is in construction, more details coming soon!</b>
	</center>
</div> 

";
}else{
	//dont show anything 4 now
}

?>