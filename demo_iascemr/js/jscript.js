
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

var digits = "0123456789";
var phoneNumberDelimiters = "-";
var validWorldPhoneChars = phoneNumberDelimiters + " ";
var minDigitsInIPhoneNumber = 10;
function trim(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}
function isInteger(s){   
	var i;
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    return true;
}
function stripCharsInBag(s, bag){   
	var i;
    var returnString = "";
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}
function checkInternationalPhone(strPhone){
	s = stripCharsInBag(strPhone,validWorldPhoneChars);
	return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}

function ValidatePhone(objPhone){
    if((objPhone.value==null)||(objPhone.value=="")){
		if(objPhone.getAttribute("id")=='fax'){
			alert("Please Enter valid fax number.")
		}else{
			alert("Please Enter valid phone number.")
		}		
		return false;
	}
	if (checkInternationalPhone(objPhone.value)==false){
		if(objPhone.id=='fax'){
			alert("Please Enter valid fax number.")
		}else{
			alert("Please Enter a valid phone number");
		}		
		objPhone.value="";
		return false;
	}
	var phone = objPhone.value;
	var len = 0;
	var len = phone.length;
	if(len == 10) {
		if((phone.charAt(0) != "(" ) || (phone.charAt(4) != ")" ) || (phone.charAt(9) != "-")){
			objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(3,6)+"-"+phone.substring(6,10);
		}
	}else if(len >10 && len<= 12) {
		if((phone.charAt(3)!= "-" ) || (phone.charAt(7) != "-" )){
			objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(4,7)+"-"+phone.substring(8,12);
		}else if((phone.charAt(3)!= " " ) || (phone.charAt(7) != " ")){
			objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(4,7)+"-"+phone.substring(8,12);
		}
	}
}



// CALENDER
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
var mon=month+1;
if(mon<=9){
	mon='0'+mon;
}
var todaydate=mon+'-'+day+'-'+year;

function y2k(number){
	return (number < 1000)? number+1900 : number;
}
function newWindow(q){
	mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	if(q==8){
		if(fillDate > todaydate){
			alert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById("date"+q).value=fillDate;
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}
// CALENDER