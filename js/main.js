/**
To do:
	find out how to restrict input through javascript
	
**/

// called on document load
function setup() {
	document.getElementById('trans_date').focus();
	
	/* Use this autofill feature later?
	var date=new Date();
	document.getElementById('m').value=date.getMonth()+1;
	document.getElementById('d').value=date.getDate();
	document.getElementById('y').value=date.getFullYear();
	*/
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
