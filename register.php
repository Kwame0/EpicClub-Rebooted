<?php
include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/connect.php' );
// session_start();
// Check for Cookies (logged in) - session insec

if(!ISSET($_POST['email'])){
	// could be any post, email is just a random one,
	// bye felica!
	header("Location: ../"); exit;
}else{
	//nothing
}


if(ISSET($_COOKIE['EPICNAME']) || ISSET($_COOKIE['EPICPASS'])){
	// They are logged in and they are trying to make an account at the same time? lmao get the fuck out please.
	header("Location: ../"); exit;
}else{
	$username = mysqli_real_escape_string($conn,$_POST['Username']);
	$pre_password = mysqli_real_escape_string($conn,$_POST['password1']);
	$password2 = mysqli_real_escape_string($conn,$_POST['password2']);
	$email = mysqli_real_escape_string($conn,$_POST['email']);
	$gender = mysqli_real_escape_string($conn,$_POST['Gender']);

	// Check if passsword1 = password 2
	if ($pre_password == $password2){
		// check for Username Syntax!
		// below is cloest clone to roblox regex! curt-sy of King Kwame
		$re = '/\\A[a-z\\d]+(?:[.-][a-z\\d]+)*\\z/i';
		if(!preg_match_all($re, $username)){
			// Invalid Syntax
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/HTMLS/InvalidUserSyntax.html' );
			#include_once '/EpicClubRebootMisc/HTMLS/InvalidUserSyntax.html';
		}else{
			// Check Username Length
			$UsernameL = strlen($username);
			if ($UsernameL < 4 || $UsernameL > 20){
			// Invalid Length
			include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/HTMLS/InvalidLength.html' );
			#include_once '/EpicClubRebootMisc/HTMLS/InvalidLength.html';
			}else{

			// check if ip has more than 3 accounts.
			$ip = $_SERVER['REMOTE_ADDR'];
			$IpCheckQ = mysqli_query($conn, "SELECT * FROM `ec_users` WHERE `IP`='$ip'");
			$IpCheck = mysqli_num_rows($IpCheckQ);
			if($IpCheck > 2){ # for some reason i have to make it 2 to check 3 accounts?!?!
				#include_once '/EpicClubRebootMisc/HTMLS/3Accounts.html';
				include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/HTMLS/3Accounts.html' );
				exit;
				sleep(11122000); # birthday of my crush
			}

			// check if they have an account (username Taken)
			$AccountQuery = mysqli_query($conn,"SELECT * FROM `ec_users` WHERE `USERNAME` = '$username'");
			$Account = mysqli_num_rows($AccountQuery);
			if($Account < 1){
				// TyphoonShark'd
				$gosted = hash('gost',$pre_password);
				$password = hash('whirlpool',$gosted);
				$time = time();
				// stack overflow public code

				function generateRandomString($length = 10) {
					    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					    $charactersLength = strlen($characters);
					    $randomString = '';
					    for ($i = 0; $i < $length; $i++) {
					        $randomString .= $characters[rand(0, $charactersLength - 1)];
					    }
					    return $randomString;
					}
					$uni_string = generateRandomString();

				// Username Not Taken, INSERT ROW! (They can use same email, this can be used for poison bans XDDD l33t)
				//mysqli_query($conn,"INSERT INTO `ec_users` VALUES(NULL,'$username','$password','$email','$gender','0','20','MEMBER','NONE','NO', '$time', '$time','0', '', '$time', '/EpicClubRebootMisc/IMGS/template.png','$ip','NO','Just joined this site!', 'Hi, I am new here!','$uni_string','$time')");
				// Create cookies!
				setcookie('EPICNAME', $username, time() + 259200, '/'); 
				setcookie('EPICPASS', $password, time() + 259200, '/'); // Social Engeerning Cookie Stealin Attacks may occur
				// All done, account created, send to dashboard! (Dashboard will send verification email upon arrival)
				echo"<script>window.location='/Dashboard/'</script>";
				#echo"<script>alert('')</script>";
			}else{
				// Username Taken, Tell them to pick a NewerOne
				$number = rand(1,100);
				#include_once '/EpicClubRebootMisc/HTMLS/UserNameTaken.html';
				include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/HTMLS/UserNameTaken.html' );
			}
			}
		}

	}else{
		// Tell them to retype both fields
		include_once '/EpicClubRebootMisc/HTMLS/RetypeBothFields.html';
	}
}

//$options = ['cost' => 11,'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),];
//echo password_hash($pre_password, PASSWORD_BCRYPT, $options)."\n";
//echo hash('whirlpool',$pre_password);
error_reporting(E_ALL & ~E_NOTICE);
?>