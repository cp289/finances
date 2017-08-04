/**
To do:
	find out how to restrict input through javascript
	
**/

// called on document load
function setup() {
	document.getElementById('ntbutton').focus();
	
	// auto-fill date form
	var date=new Date();
	document.getElementById('m').value=date.getMonth()+1;
	document.getElementById('d').value=date.getDate();
	document.getElementById('y').value=date.getFullYear();
	
	// validate number
	document.getElementById('amount').addEventListener('keypress',validateNum);
}

// validates number input fields (doesn't work)
function validateNum(e){
	console.log(e.keyCode);
	console.log(e.key);
	if(!isNaN(this.value+e.key)||e.keyCode==8){
		console.log('allowed');
		return true;
	}
	//var txt = document.getElementById('amount').value;
	//document.getElementById('amount').value=txt.substr(0,txt.length-1);
	e.preventDefault();
}

// called when "new transaction" clicked
function newTrans() {
	document.getElementById('ntbutton').style="display:none;"; // append styles instead of edit?
	document.getElementById('ntform').style="display:block;";
	document.getElementById('ntform').getElementsByTagName('input')[0].focus();
}

// called when "cancel" is clicked
function cancelNewTrans() {
	document.getElementById('ntbutton').style="display:block;";
	document.getElementById('ntform').style="display:none;"; // reset field defaults
}

function autoFocus(stage) { //redesign: need different stuff... figure this out later...
	switch(stage){
		case 0:
			document.getElementsByTagName('input');
			break;
		case 1:
			break;
		case 2:
			break;
	}
}

// currently unused
function validateDate(m, d, y) {
	if(m==4||m==6||m==9||m==11){
		return d>=1&&d<=30
	}else if(m==2){ //leap year conditionals?? Use: (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
		return d>=1&&d<=29
	}else{
		return d>=1&&d<=31
	}
}