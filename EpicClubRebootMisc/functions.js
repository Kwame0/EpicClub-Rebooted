// (c) Sharenet + EpicClub Rebooted
console.log("Hey There Partner! Want a hire?");

function LoginBoxAppr(){
	document.getElementById("Overlay").style.display = 'block';
	document.getElementById("LoginBox").style.display = 'block';
	document.getElementById("OVBackground").style.display = 'block';
}

function RegisterBoxAppr(){
	document.getElementById("Overlay").style.display = 'block';
	document.getElementById("RegisterBox").style.display = 'block';
	document.getElementById("OVBackground").style.display = 'block';

}


function ValidateEpicU(){
	var str = document.getElementById("username").value;
    var res = str.match(/[^\W][a-zA-Z\d.-]{3,20}/g);
    if (str == res){
    	document.getElementById("username").style.borderColor = "green";
    	console.log("Border Changed to Green - Will Check IF in EC DB Soon");
    }else{
    	document.getElementById("username").style.borderColor = "red";
    	console.log("Border Changed to Red - Wrong Syntax");
    }
}

function CloseRegi(){
	document.getElementById("Overlay").style.display = 'none';
	document.getElementById("RegisterBox").style.display = 'none';
	document.getElementById("OVBackground").style.display = 'none';
}

function CloseLogin(){
	document.getElementById("Overlay").style.display = 'none';
	document.getElementById("LoginBox").style.display = 'none';
	document.getElementById("OVBackground").style.display = 'none';
}