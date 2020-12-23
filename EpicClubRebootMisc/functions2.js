// (c) Sharenet + EpicClub Rebooted
// some functions dont work for an odd reason.
console.log("Inserting Scripts is highly not recommended and insertions of scripts alert the site officals");

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
    var res = str.match(/[^\W][a-zA-Z\d]{3,20}/g);
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

function GoToM(){
	window.location='/Messages/'
}


function goBack(){
	window.history.back();
}


 

var gear = 0;
function Gear(){
	if(gear == 1){
		document.getElementById("backDrop").style.display = 'none';
		document.getElementById("gearBox").style.display = 'none';
		gear -= 1;
	}else{
		document.getElementById("backDrop").style.display = 'block';
		document.getElementById("gearBox").style.display = 'block';
		gear++;
	}
}