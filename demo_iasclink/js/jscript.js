
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

var digits = "0123456789";
var phoneNumberDelimiters = "-";
var validWorldPhoneChars = phoneNumberDelimiters + " ";
var minDigitsInIPhoneNumber = 10;
var dosCase='';
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
function checkdate(objName,dosCase) 
{
	var datefield = objName;
	if (chkdate(objName,dosCase) == false) 
	{
		//datefield.value='';
		datefield.select();
		alert("That date is invalid.  Please try again.");
		datefield.focus();
		
		return false;
	}
	else {
		return true;
   }
}
function chkdate(objName,dosCase) {
var strDatestyle = "US"; //United States date style
//var strDatestyle = "EU";  //European date style
var strDate;
var strDateArray;
var strDay;
var strMonth;
var strYear;
var intday;
var intMonth;
var intYear;
var booFound = false;
var datefield = objName;
var strSeparatorArray = new Array("-"," ","/",".");
var intElementNr;
var err = 0;
var strMonthArray = new Array(12);
strMonthArray[0] = "01";
strMonthArray[1] = "02";
strMonthArray[2] = "03";
strMonthArray[3] = "04";
strMonthArray[4] = "05";
strMonthArray[5] = "06";
strMonthArray[6] = "07";
strMonthArray[7] = "08";
strMonthArray[8] = "09";
strMonthArray[9] = "10";
strMonthArray[10] = "11";
strMonthArray[11] = "12";
strDate = datefield.value;
if (strDate.length <= 5 && strDate.length >= 1) {
return false;
}
if (strDate.length < 1) {
return true;
}
for (intElementNr = 0; intElementNr < strSeparatorArray.length; intElementNr++) {
if (strDate.indexOf(strSeparatorArray[intElementNr]) != -1) {
strDateArray = strDate.split(strSeparatorArray[intElementNr]);
if (strDateArray.length != 3) {
err = 1;
return false;
}
else {
strDay = strDateArray[0];
strMonth = strDateArray[1];
strYear = strDateArray[2];
}
booFound = true;
   }
}
if (booFound == false) {
if (strDate.length>5) {
strDay = strDate.substr(0, 2);
strMonth = strDate.substr(2, 2);
strYear = strDate.substr(4);
   }
}
if (strYear.length == 2) {
	if(dosCase=='currentCase') {
		strYear = ("FY" +new Date().getFullYear()).substr(2, 2)+strYear;	
	}else {
		strYear = '19' + strYear;		
	}
}
// US style
if (strDatestyle == "US") {
strTemp = strDay;
strDay = strMonth;
strMonth = strTemp;
}
intday = parseInt(strDay, 10);
if (isNaN(intday)) {
err = 2;
return false;
}
intMonth = parseInt(strMonth, 10);
if (isNaN(intMonth)) {
for (i = 0;i<12;i++) {
if (strMonth.toUpperCase() == strMonthArray[i].toUpperCase()) {
intMonth = i+1;
strMonth = strMonthArray[i];
i = 12;
   }
}
if (isNaN(intMonth)) {
err = 3;
return false;
   }
}
intYear = parseInt(strYear, 10);
if (isNaN(intYear)) {
err = 4;
return false;
}
if (intMonth>12 || intMonth<1) {
err = 5;
return false;
}
if ((intMonth == 1 || intMonth == 3 || intMonth == 5 || intMonth == 7 || intMonth == 8 || intMonth == 10 || intMonth == 12) && (intday > 31 || intday < 1)) {
err = 6;
return false;
}
if ((intMonth == 4 || intMonth == 6 || intMonth == 9 || intMonth == 11) && (intday > 30 || intday < 1)) {
err = 7;
return false;
}
if (intMonth == 2) {
if (intday < 1) {
err = 8;
return false;
}
if (LeapYear(intYear) == true) {
if (intday > 29) {
err = 9;
return false;
}
}
else {
if (intday > 28) {
err = 10;
return false;
}
}
}
if (strDatestyle == "US") {
			if(intday<=9){
		intday="0"+intday
		}else{
		intday=intday
		}
datefield.value = strMonthArray[intMonth-1] +"-"+ intday+ "-"+ strYear;
}
else {
datefield.value = intday + " " + strMonthArray[intMonth-1] + " " + strYear;
}
return true;
}
function LeapYear(intYear) {
if (intYear % 100 == 0) {
if (intYear % 400 == 0) { return true; }
}
else {
if ((intYear % 4) == 0) { return true; }
}
return false;
}

function ValidatePhone(objPhone){
    if((trim(objPhone.value)==null)||(trim(objPhone.value)=="")){
		if(objPhone.id=='fax'){
			alert("Please Enter valid fax number.")
		}else{
			//alert("Please Enter valid phone number.")
		}
		objPhone.value="";		
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
	mywindow=open('mycal1.php?md='+q+'&rf=yes','','width=200,height=250,top=200,left=300');
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
	if(typeof(document.getElementById(q))=="object") {
		document.getElementById(q).value=fillDate;
	}
	if(typeof(document.getElementById("date"+q))!="object") {
		document.getElementById("date"+q).value=fillDate;
	}
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}
// CALENDER