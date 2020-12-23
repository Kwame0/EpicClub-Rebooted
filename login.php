<?php
include( $_SERVER['DOCUMENT_ROOT'] . '/EpicClubRebootMisc/connect.php' );

// Check if cookies are set
if(isset($_COOKIE['EPICPASS']) || isset($_COOKIE['EPICNAME'])){
	header("Location: ../"); exit; // Bye felica
	}else{
		$username = mysqli_real_escape_string($conn,$_POST['Username']);
		$passwordB4H = mysqli_real_escape_string($conn,$_POST['password1']); //password before hash
		$gosted = hash('gost',$passwordB4H);
		$password = hash('whirlpool',$gosted);

		$accountQ = mysqli_query($conn,"SELECT * FROM `ec_users` WHERE `USERNAME`='$username' AND `PASSWORD` = '$password'");
		$account = mysqli_num_rows($accountQ);

		if($account > 0){
			// Proceed with care, send them to dashboard
			// Make Cookies first
			setcookie('EPICNAME', $username, time() + 259200, '/'); 
			setcookie('EPICPASS', $password, time() + 259200, '/');
			echo "<script>window.location='/Dashboard/'</script>";
		}else{
			// Incorrect password or username, i cant be bothered to tell you which one 
			include_once 'EpicClubRebootMisc/HTMLS/IncorrectPass.html';
		}
}

?>