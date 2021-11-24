
var alrtWidth = "450";
function foo(){} // only to pass in href of Anchors which fires event for onClick.
function ele(id,task){if(dgi(id)) {dgi(id).style.display=task;}} // to show or hide any block with id.

//function below is to toggle the display of any block.
function disp_toggle(id){if(dgi(id).style.display == 'block'){ele(id,'none');} else {ele(id,'block');}}

//two functions below are just the short code for getting elements by name or ID.
function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}
function include(file) //to include other js files in this file.
{
  var script  = document.createElement('script');
  script.src  = file;
  script.type = 'text/javascript';
  script.defer = true;
  document.getElementsByTagName('head').item(0).appendChild(script);
}

function js_GET(key){
	var querystring = document.location.href;
	if(querystring.indexOf("?")==-1){
		return false;
	}
	q = window.location.search.substring(1);
	arr_q_vars = q.split("&");
	for (i=0;i<arr_q_vars.length;i++){
		key_name = arr_q_vars[i].split("=");
		if (key_name[0] == key){
			return key_name[1];
		}
	}
}
/* END OF CODE   */

//Code to check Phone Number
/* Code For Phone Validation*/
// Declaring required variables
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "-";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + " ";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 10;
//For Zip code validation on saving records.
var boolVal = false;

function view_only_acc_call(mode){
	fAlert("You do not have permission to perform this action.",'',340);
	if(mode == 1){
		return false;
	}
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}


//Function
function trim(val) 
{ 
	if(val!="" && val!="undefined" && val!=null){
		return val.replace(/^\s+|\s+$/, ''); 
	}
	else 
	return ''; 
}
function LTrim( value ) 
{
	var re = /\s*((\S+\s*)*)/;
	return value.replace(re, "$1");
}
// Removes ending whitespaces
function RTrim( value ) 
{
	var re = /((\s*\S+)*)\s*/;
	return value.replace(re, "$1");
}
// Removes leading and ending whitespaces
function trimBothSide( value ) 
{
	return LTrim(RTrim(value));
}

function isInteger(s)
{   var i;
    for (i = 0; i < s.length; i++)
    {
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
    {
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function checkInternationalPhone(strPhone){
	s=stripCharsInBag(strPhone,validWorldPhoneChars);
	return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}

function invalid_input_msg(obj,msg){
	obj.className = 'mandatory';
	top.fAlert(msg);
	obj.select();
	obj.focus();
	obj.value='';
}

function set_ss_no(objSS){
	var default_format = "###-##-####";
	if(objSS.value == "" || objSS.value.length < 9){
		invalid_input_msg(objSS, "Please Enter a valid Social Security #");
	}else{
		var refinedPh = objSS.value.replace(/[^0-9+]/g,"");					
		if(refinedPh.length < 9){
			invalid_input_msg(objSS, "Please Enter a valid Social Security #");
		}else{
			switch(default_format){
				case "###-##-####":
					objSS.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,5)+"-"+refinedPh.substring(5,9);
				break;
				default:
					objSS.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,5)+"-"+refinedPh.substring(5,9);
				break;
			}
		}
	}
	//changeClass(objPhone);
}
			
function ValidatePhone(objPhone,global_val)
{
	var global_val;
	if ((objPhone.value==null)||(objPhone.value==""))
	{
		objPhone.className = 'mandatory';
		alert("Please Enter valid phone number")
		objPhone.value='';
		objPhone.focus();
		return false;
	}
	if (checkInternationalPhone(objPhone.value)==false)
	{
		objPhone.className = 'mandatory';
		top.fAlert('Please enter a valid Phone Number','',objPhone);
		objPhone.value='';
		return false;
	}

	 var phone = objPhone.value;
	 var len = 0;
	 var len = phone.length;
	if (len  > 12){
		objPhone.className = 'mandatory';
		top.fAlert('Please enter a valid Phone Number','',objPhone);
		objPhone.value='';
		return false;
	}
	 if(len == 10)
	  {
		 if((phone.charAt(0) != "(" ) || (phone.charAt(4) != ")" ) || (phone.charAt(9) != "-"))
		 {
			 if(global_val=='###-###-####')
			 {
			 objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(3,6)+"-"+phone.substring(6,10);
			 }
			 else  if(global_val=='(###) ###-####')
			 {
				objPhone.value =  phone.substring(0,0)+"("+phone.substring(0,3)+") "+phone.substring(3,6)+"-"+phone.substring(6,10); 
			 }
			 else
			 {
			 objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(3,6)+"-"+phone.substring(6,10);
			 }
		 }
	  }
	  if(len >10 && len<= 12)
	  {
		 if((phone.charAt(3)!= "-" ) || (phone.charAt(7) != "-" ))
		 {
			 objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(4,7)+"-"+phone.substring(8,12);
		 }else if((phone.charAt(3)!= " " ) || (phone.charAt(7) != " "))
		 {
			 objPhone.value =  phone.substring(0,0)+""+phone.substring(0,3)+"-"+phone.substring(4,7)+"-"+phone.substring(8,12);
		 }
	  }
	  changeClass(objPhone);
}
 /* Code For Phone Validation*/
function PhoneTenDigit(obj){
	obj.value = obj.value.replace(/-/gi,"");
	if(obj.value.length >= 10){
		obj.value = obj.value.replace(/-/gi,"");
		document.getElementById(obj.id).value = document.getElementById(obj.id).value.substring(0, 10);	
		ValidatePhone(obj);
	}
}
/////////////End by ram
function GetXmlHttpObject()
{ 
	var objXMLHttp=null
	if (window.XMLHttpRequest)
	{
		objXMLHttp=new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
	}
	return objXMLHttp
}


var allFieldsName = new Array();
var loadingImgFunction = '';

function new_zip_vs_state(zip_code,fieldsName,LoadingImgFunctionPath){
	allFieldsName = fieldsName;
	loadingImgFunction = eval(LoadingImgFunctionPath);
	
	if(zip_code == ''){
		return false;
	}
	else if(isNaN(zip_code)){
		alert('Please enter correct '+top.zipLabel);
		document.getElementById(allFieldsName[0]).select();
		return false;
	}
	
	xmlHttp = GetXmlHttpObject();
	if(xmlHttp == null){
		alert ("Browser does not support HTTP Request")
		return
	} 
	
	var url = "../../common/add_city_state.php"
	url = url+"?zipcode="+zip_code;
	
	xmlHttp.onreadystatechange = function()
	{
		 if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 { 
	 		var changecolor='#F6C67A';
			var result = xmlHttp.responseText;
			if(result){
				var val=result.split("-");
				document.getElementById(allFieldsName[1]).value = trim(val[0]);
				document.getElementById(allFieldsName[2]).value = trim(val[1]);
			}else	{
				top.fAlert('Please enter correct Zip code');
				document.getElementById(allFieldsName[0]).style.backgroundColor = changecolor;
				document.getElementById(allFieldsName[0]).select();
				document.getElementById(allFieldsName[1]).value = '';
				document.getElementById(allFieldsName[2]).value = '';
			}
			if(loadingImgFunction){
				loadingImgFunction('none');
			}
		 }
		 else{
			 if(loadingImgFunction){
				loadingImgFunction('block');
			 }
		 }
	}
	
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}

var newzip_code;
//By ashwani sharma to get city and state on the basis of zipcode in add new patient
function zip_vs_state_R6(objZip,objCity,objState,objCountry,objCounty){
	zip_code = $(objZip).val();
	if(zip_code == ''){
		//alert('Please Enter Zip Code.');
		return false;
	}
	$.ajax({
			   	url:top.WRP+"/interface/common/add_city_state.php?zipcode="+zip_code,
				success:function(data)
				{
					var changecolor='#F6C67A';	
					var val=data.split("-");
					var city = $.trim(val[0]);
					var state = $.trim(val[1]);
					var country = $.trim(val[3]);
					var county = $.trim(val[4]); 
					if(city!="")
					{
						$(objCity).val(city);
						$(objState).val(state);
						if(country){$(objCountry).val(country);}
						if(county){$(objCounty).val(county);}
						return;
					}
					if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
						if(typeof(document.getElementById("zipCodeStatus"))!="undefined" && document.getElementById("zipCodeStatus") != null && document.getElementById("zipCodeStatus") != "undefined")
						document.getElementById("zipCodeStatus").value="OK";
						return;
					}
					if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
						//DO NOT VALIDATE ZIPCODE
						return;
					}
					
					$(objZip).val("");
					changeClass(document.getElementById($(objZip).attr("id")),1)
					if(typeof(top.fAlert) != "undefined")
						top.fAlert('Please enter correct '+top.zipLabel);
					else{
						alert('Please enter correct '+top.zipLabel);
					}
					$(objZip).select();
					$(objCity).val(" ");
					$(objState).val(" ");
					$(objCounty).val(" ");
				}
		   });
}
function zip_vs_state(zip_code,page,obj){

	obj = obj || null;
	if(zip_code == ''){
		return false;
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	  {
	  	alert ("Browser does not support HTTP Request")
	 	 return
	  } 
		if((page=="add_patient") || (page=="edit_patient"))	{
			if(top.stop_zipcode_validation=="YES") {
				//DO NOT VALIDATE ZIPCODE
			}else {
				if(!validate_zip(obj) && typeof(obj)=="object"){
					top.fAlert('Please enter correct '+top.zipLabel);	
					return false;
				}
				else if(isNaN(zip_code) && typeof(obj)!="object" && top.int_country != "UK") {
					top.fAlert('Please enter correct '+top.zipLabel);
					document.getElementById('code').value = ''; 
					return false;
				}
			}
			var url= top.WRP +"/interface/common/add_city_state.php";
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstateChanged 
		}else if(page=="occupation")	{
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstateoccupation 
		}else if(page=="resp_party")	{
			if(document.demographics_edit_form.elem_patientStatus.value != "Active") return;
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstateresp_party
		}else if(page=="add_facility")	{
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code+"&zipext=y"
			xmlHttp.onreadystatechange=zipstateChanged 
		}else if(page=="primary")	{
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_primary
		}else if(page=="secondary")	{			
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_secondary
		}else if(page=="tertiery")	{			
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_tertiary		
		
		}else if(page=="new_patient_popup")	{
			var url= top.WRP +"/interface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstateChanged 
		}
		else if(page=="RefferringPhysician")	{
			var url= top.WRP +"/interface/common/add_city_state.php"		
			url=url+"?zipcode="+zip_code+"&zipext=y"
			xmlHttp.onreadystatechange=zipstateChangedRef 
		}
		else if(page=="add_insurance")	{
			var url= top.WRP+"/interface/common/add_city_state.php"			
			url=url+"?zipcode="+zip_code+"&zipext=y"
			newzip_code = zip_code;
			parent.parent.show_loading_image('block');
			xmlHttp.onreadystatechange=zipstateChangedIns 
		}
		else if(page=="PosFacility")	{
			var url= top.WRP +"/interface/common/add_city_state.php"			
			url=url+"?zipcode="+zip_code+"&zipext=y"
			xmlHttp.onreadystatechange=zipstateChangedPosFacility
		}
		else if(page=="add_groups")	{
			var url= top.WRP +"/interface/common/add_city_state.php"			
			url=url+"?zipcode="+zip_code+"&zipext=y"
			xmlHttp.onreadystatechange=zipstateChangedAdd_groups
		}
		else if(page=="policy")	{
			var url= top.WRP +"/interface/common/add_city_state.php"			
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=setPolicyState
		}
		else if(page=="add_edit_Appointment_provider")	{
			var url= top.WRP +"/inteface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_addEditAppointment 
		}else if(page == "interface_reports_clinical"){
			var url = "../../common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_load 
		}
		else if(page == "addmin_lab"){
			var url= top.WRP +"/inteface/common/add_city_state.php"
			url=url+"?zipcode="+zip_code
			xmlHttp.onreadystatechange=zipstate_load
		}
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
}

//Karandeep Singh Dhaliwal
function zip_vs_state_family_state(zip_code, num_id)
{
	if(zip_code == '')
	{
		return false;
	}
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request")
		return;
	}
	//alert("A");
	var url=top.WRP+ "/interface/common/add_city_state.php";
	url=url+"?zipcode="+zip_code;
	xmlHttp.onreadystatechange=function()
	{
		 if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
		 { 
	 		var changecolor='#F6C67A';
			var result=xmlHttp.responseText;
			if(result)	{
				var val=result.split("-");
				document.getElementById("city_table_family_information" + num_id).value=trim(val[0]);
				document.getElementById("state_table_family_information" + num_id).value=trim(val[1]);
                                
                                                                document.getElementById("code_table_family_information" + num_id).style.backgroundColor='#FFFFFF';
			}else	{
				if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
					return;
				}
				if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
					//DO NOT VALIDATE ZIPCODE
					return;
				}
				
				top.fAlert('Please enter correct '+top.zipLabel);
				document.getElementById("code_table_family_information" + num_id).style.backgroundColor=changecolor;
				document.getElementById("city_table_family_information" + num_id).value='';
				document.getElementById("state_table_family_information" + num_id).value='';
			}
		 }
	}
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}
//Karandeep Singh Dhaliwal
function setPolicyState()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
	 		var changecolor='#F6C67A';
			var result=xmlHttp.responseText;
			if(result)	{
				var val=result.split("-");
				document.getElementById("City").value=trim(val[0]);
				document.getElementById("State").value=trim(val[1]);
			}else	{
				if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
					return;
				}
				if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
					//DO NOT VALIDATE ZIPCODE
					return;
				}
				
				top.fAlert('Please enter correct '+top.zipLabel);
				document.getElementById("Zip").style.backgroundColor=changecolor;
				document.getElementById("City").value='';
				document.getElementById("State").value='';
			}
	}	
}
function zipstateChangedAdd_groups()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("code").style.backgroundColor = '#FFFFFF';
			document.getElementById("city").style.backgroundColor = '#FFFFFF';
			document.getElementById("state").style.backgroundColor = '#FFFFFF';
			document.getElementById("city").value=trim(val[0]);
			document.getElementById("state").value=trim(val[1]);
			document.getElementById("zip_ext").value=trim(val[2]);
			
		}else if(typeof(top.int_country) =="undefined" || (top.int_country != "UK")) {
			
			document.getElementById("code").value='';
			document.getElementById("city").value='';
			document.getElementById("state").value='';
			document.getElementById("zip_ext").value='';
			alert("Please enter a valid  Zip code1.");
			document.getElementById("code").style.backgroundColor=changecolor;
			return false;
		}
	}	
}

function zipstateChangedRef()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("rcity").value=trim(val[0]);
			document.getElementById("rstate").value=trim(val[1]);
			document.getElementById("zip_ext").value=trim(val[2]);
		}
	}	
}
function zipstateChangedPosFacility()
{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("pos_facility_city").value=trim(val[0]);
			document.getElementById("pos_facility_state").value=trim(val[1]);
			if(document.getElementById("zip_ext")){
				document.getElementById("zip_ext").value=trim(val[2]);
			}
			
		}else	{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}
			
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("pos_facility_zip").style.backgroundColor=changecolor;
			document.getElementById("pos_facility_zip").value='';
			document.getElementById("pos_facility_city").value='';
			document.getElementById("pos_facility_state").value='';
			if(document.getElementById("zip_ext")){
				document.getElementById("zip_ext").value='';
			}
		}
	}	
}
function zipstateChangedIns()
{
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete")
	{ 
		parent.parent.show_loading_image('none');
		var changecolor = '#F6C67A';
		var result = xmlHttp.responseText;
		if(result){
			var val = result.split("-");
			document.getElementById("City").value=trim(val[0]);
			document.getElementById("State").value=trim(val[1]);
			if(document.getElementById("zip_ext")){
				document.getElementById("zip_ext").value=trim(val[2]);
			}
		}else{
			document.getElementById("City").value = '';
			document.getElementById("State").value = '';
			window.open('../../../common/addZipCode.php?code='+newzip_code,'mywindow','width=800,height=100');				
		}
	}	
}

function zipstateresp_party()	{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("rcode").className="input_text_10";
			document.getElementById("rcity").value=trim(val[0]);
			document.getElementById("rcity").className="input_text_10";
			document.getElementById("rstate").value=trim(val[1]);
			document.getElementById("rstate").className="input_text_10";
		}else	{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}
			
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("rcode").className="mandatory";
			document.getElementById("rcode").value='';
			document.getElementById("rcity").value='';
			document.getElementById("rstate").value='';
		}
	} 
}

function zipstateoccupation()	{
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 { 
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("ecity").value=trim(val[0]);
			document.getElementById("estate").value=trim(val[1]);
                        
			document.getElementById("ecode").style.backgroundColor='#FFFFFF';
		}else	{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("ecode").style.backgroundColor=changecolor;
			document.getElementById("ecode").value='';
			document.getElementById("ecity").value='';
			document.getElementById("estate").value='';
		}
	} 
}
function zipstate_primary()	{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("code1").className="input_text_10";
			document.getElementById("city1").value=trim(val[0]);
			document.getElementById("city1").className="input_text_10";
			document.getElementById("state1").value=trim(val[1]);
			document.getElementById("state1").className="input_text_10";
		}else{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("code1").className="mandatory";
			document.getElementById("code1").value='';
			document.getElementById("city1").value='';
			document.getElementById("state1").value='';
		}
	}
	
}
function zipstate_secondary()	{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("code2").className="input_text_10";
			document.getElementById("city2").value=trim(val[0]);
			document.getElementById("city2").className="input_text_10";
			document.getElementById("state2").value=trim(val[1]);
			document.getElementById("state2").className="input_text_10";
		}else	{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}			
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("code2").className="mandatory";
			document.getElementById("code2").value='';
			document.getElementById("city2").value='';
			document.getElementById("state2").value='';
		}
	}
}
function zipstate_tertiary()	{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("code3").className="input_text_10";
			document.getElementById("city3").value=trim(val[0]);
			document.getElementById("city3").className="input_text_10";
			document.getElementById("state3").value=trim(val[1]);
			document.getElementById("state3").className="input_text_10";
		}else	{
			if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
				return;
			}
			if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
				//DO NOT VALIDATE ZIPCODE
				return;
			}			
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("code3").className="mandatory";
			document.getElementById("code3").value='';
			document.getElementById("city3").value='';
			document.getElementById("state3").value='';
		}
	}
}

function zipstateChanged() 
{ 
	if(document.getElementById('zipAjaxLoad')){
		document.getElementById('zipAjaxLoad').style.display = 'block';
	}
	if(top.fmain){
		if(top.fmain.front){	
			if(top.fmain.document.getElementById('butId2')){
				//top.fmain.front.document.getElementById('butId2').setAttribute('disabled','disabled');
			}
		}
	}

	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{ 
		var changecolor='#F6C67A';
		var changecolor1='#FFFFFF';
		var result=xmlHttp.responseText;
		
		if(xmlHttp.status == 200){
			if(result)	{
				var val=result.split("-");
				if(document.getElementById("code")) {
					document.getElementById("code").className="input_text_10";
				}
				if(document.getElementById("postal_code")) {
					document.getElementById("postal_code").className="input_text_10";
				}
				document.getElementById("city").value=trim(val[0]);
				document.getElementById("city").className="input_text_10";
				document.getElementById("state").value=trim(val[1]);
				document.getElementById("state").className="input_text_10";
				if(document.getElementById("zip_ext")){
					document.getElementById("zip_ext").value=trim(val[2]);
					document.getElementById("zip_ext").className="input_text_10";
				}		
				if(document.getElementById("zipCodeStatus")){
					document.getElementById("zipCodeStatus").value="OK";
				}
                if(document.getElementById("code")){document.getElementById("code").style.backgroundColor = '#FFFFFF';}
				if(document.getElementById('country_code')){
					if(val[3]){document.getElementById('country_code').value=val[3];}
				}
				if(document.getElementById("county")){
					document.getElementById("county").value=trim(val[4]);
					document.getElementById("county").className="input_text_10";
				}
			}else{ 
				if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
					if(typeof(document.getElementById("zipCodeStatus"))!="undefined" && document.getElementById("zipCodeStatus") != null && document.getElementById("zipCodeStatus") != "undefined")
					document.getElementById("zipCodeStatus").value="OK";
					if(typeof(document.getElementById('zipAjaxLoad')) != "undefined"){
						document.getElementById('zipAjaxLoad').style.display = 'none';
					}
					return;
				}
				
				if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
					//DO NOT VALIDATE ZIPCODE
					if(document.getElementById('zipAjaxLoad')){
						document.getElementById('zipAjaxLoad').style.display = 'none';
					}
					return
				}
				if(typeof(top.fAlert) != "undefined")
				top.fAlert('Please enter correct '+top.zipLabel);
				else{
					alert('Please enter correct '+top.zipLabel);
				}
				document.getElementById("code").style.backgroundColor=changecolor;
				document.getElementById("code").value='';
				document.getElementById("city").value='';
				document.getElementById("state").value='';
				if(document.getElementById("zip_ext")){document.getElementById("zip_ext").value="";}
				if(document.getElementById("zipCodeStatus") && top.int_country != "UK"){
					document.getElementById("zipCodeStatus").value="NA";
				}
				if(document.getElementById("county")){document.getElementById("county").value="";}
			}
			if(document.getElementById('zipAjaxLoad')){
				document.getElementById('zipAjaxLoad').style.display = 'none';
			}
		}
	} 
}

function zipstate_load(){
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");						
			document.getElementById("city").value=trim(val[0]);						
			document.getElementById("state").value=trim(val[1]);						
		}else{
			top.fAlert('Please enter correct '+top.zipLabel);					
			document.getElementById("zip").value='';
			document.getElementById("city").value='';
			document.getElementById("state").value='';						
		}
	}
}

function zipstate_addEditAppointment()	{
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	{
		var changecolor='#F6C67A';
		var result=xmlHttp.responseText;
		if(result)	{
			var val=result.split("-");
			document.getElementById("zip").className="input_text_10";
			document.getElementById("city").value=trim(val[0]);
			document.getElementById("city").className="input_text_10";
			document.getElementById("state").value=trim(val[1]);
			document.getElementById("state").className="input_text_10";
		}else	{
			top.fAlert('Please enter correct '+top.zipLabel);
			document.getElementById("zip").className="mandatory";
			document.getElementById("zip").value='';
			document.getElementById("city").value='';
			document.getElementById("state").value='';
		}
	}
}

//end here
function check_patient_byashwani(title,fname,lname)	{
	xmlHttpA = GetXmlHttpObject()
	myvar = eval(myvar);
	frm = myvar.document.demographics_edit_form;
	mname = frm.mname.value;
	suffix = frm.suffix.value;
	if(xmlHttpA==null)
	{
		alert ("Browser does not support HTTP Request")
		return
	}
	
	if(fname=="" || lname=="")	{	
		alert("Please enter firstname and lastname");
		return false;
	}else if(fname!="" && lname!="")	{
		url='add_patient.php?pname='+fname+'&lastname='+lname+'&title='+title+'&mname='+mname+'&suffix='+suffix;
		window.open("../common/check_patientinfo_popup.php?pname="+fname+"&lastname="+lname+"&title="+title+"&mname="+mname+"&suffix="+suffix,"Surinder","width=410, height=380, top=125, left= 250");	
		xmlHttpA.open("GET",url,true)
		xmlHttpA.send(null);	
	}	
}


function patientinfo_byashwani(){
	myvar = eval(myvar);
	frm = myvar.document.demographics_edit_form;
	if(xmlHttpA.readyState==4 || xmlHttpA.readyState=="complete")
	{		
		myvar.document.getElementById("defaultTab").innerHTML=xmlHttpA.responseText;
		if(myvar.document.getElementById("exist").value=="alreadyexist")	{
			myvar.document.getElementById("exist2").value=myvar.document.getElementById("exist").value;
		}else{			
			myvar.document.getElementById("exist2").value='';
		}
	}
}

function addappointment(url){
	window.open(url,'appoint','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=410,height=650,left=300,top=10');					
}

function validate(obj)
{	
	obj = top.fmain.document.getElementById(obj);
	var changecolor='#FFCC66';	
	var myvar = top.fmain.all_data;
	msg = '';
	msgs='';
	frm = myvar.document.demographics_edit_form;
	if(myvar.document.getElementById("fname").value=="" || myvar.document.getElementById("lname").value==""){
		top.fmain.document.getElementById("load_image21").style.display = "none";
		alert('Please enter First name and Last name to precede save.');		
		return false;
	}
	var flagActivePatient = true;
	if(myvar.document.getElementById("exist2").value=="alreadyexist"){
			var conf=confirm("Patient Already Exist !\nAre you sure to save already existing patient as new patient?");
	}else{
		var conf=confirm("Do you want to create a new patient")
	}
	if(conf==true){
		var zipCodeStatus = myvar.document.getElementById("zipCodeStatus");
		var zipCode = myvar.document.getElementById("postal_code");
		if(zipCodeStatus.value == 'NA'){
			if(confirm("Zip code Does not exist.Do you want to add Zip Code")){
					zipCodeStatus.value = "NA";
			}else{
				zipCodeStatus.value = "NotOk";
			}
		}
		//////////////ravi
		var erx_entry = myvar.document.getElementById("erx_entry").value;
		var Allow_erx_medicare = myvar.document.getElementById("Allow_erx_medicare").value;
		if(Allow_erx_medicare == 'Yes'){
			if(erx_entry == 0){
				var erxmsg = "Patient is not registered for e/RX\n";
				erxmsg += "Would you like to register patient for e/Rx";
				if(confirm(erxmsg)){
					myvar.document.demographics_edit_form.erx_entry.value = 1;
					myvar.document.demographics_edit_form.chkErxAsk.value = 1;
				}
				else{
					myvar.document.demographics_edit_form.erx_entry.value = 0;
					myvar.document.demographics_edit_form.chkErxAsk.value = 0;
				}
			}
			else{
				myvar.document.demographics_edit_form.erx_entry.value = 1;
				myvar.document.demographics_edit_form.chkErxAsk.value = 1;					
			}				
		}
	}
	else{
		top.fmain.document.getElementById("load_image21").style.display = "none";
	}
	
	if(!conf){
		frm.title.value="";
		frm.fname.value="";
		frm.mname.value="";
		frm.lname.value ="";
		frm.suffix.value="";
		frm.dob.value="";
		frm.street.value="";
		frm.street2.value="";
		frm.ss.value="";
		frm.postal_code.value="";
		frm.city.value="";
		frm.state.value="";
		frm.phone_home.value=""
		frm.phone_biz.value=""
		frm.phone_cell.value=""
		myvar.document.getElementById("exist2").value="";						
	}
	else if(obj.id!="butId2"){
		if(myvar.document.getElementById("table1").style.display=="block"){
					
				date1=myvar.demographics_edit_form.reg_date.value;
				date2=date1.replace("-","/");
				date3=date2.replace("-","/");
				DateToShow=date3;
				idt1=myvar.demographics_edit_form.curr_date.value;
				idt2=idt1.replace("-","/");
				idt3=idt2.replace("-","/");
				DateToCheck=idt3;
				
				d=new Date();
				d=new Date(d.getYear(), d.getMonth(), d.getDate());
				//* Code Singh*//
				cDate=Date.parse(myvar.demographics_edit_form.from_date_byram.value); 
				dDate=Date.parse(myvar.demographics_edit_form.dob.value); 
				if(myvar.demographics_edit_form.fname.value=="" || myvar.demographics_edit_form.lname.value==""  ||
					myvar.demographics_edit_form.street.value=="" || myvar.demographics_edit_form.city.value=="" ||
					myvar.demographics_edit_form.state.value=="" || myvar.demographics_edit_form.postal_code.value=="" 
					)	{
					
						msg=msg + ' Patient Information\n\n';
				}
				if(new Date(DateToCheck) < new Date(DateToShow))
				{
					  msg=msg + '   Please Fill Till Current Date in Registration Date \n';
					  myvar.demographics_edit_form.reg_date.style.backgroundColor=changecolor;
				}	
				if(myvar.demographics_edit_form.reg_date.value == '')
				{
						
					  msg=msg + '   Please Fill Registration Date \n';
					  myvar.demographics_edit_form.reg_date.style.backgroundColor=changecolor;
				}	
				
				if (myvar.demographics_edit_form.fname.value == '')
				{
				   msg  = msg + '   First name is required\n'; 
				   myvar.demographics_edit_form.fname.style.backgroundColor=changecolor;
				}
				
				 if(myvar.demographics_edit_form.lname.value == '')
				 {  msg = msg + '   Last name is required\n';  
					myvar.demographics_edit_form.lname.style.backgroundColor=changecolor;
				 
				 }
				 if(myvar.demographics_edit_form.dob.value == '' || myvar.demographics_edit_form.dob.value == '00-00-0000')
				 { 
					msg = msg + '   Date of Birth is required\n'; 
					myvar.demographics_edit_form.dob.style.backgroundColor=changecolor;
				 }
				 else if(dDate >= cDate) 
					{
					msg = msg + '  - Date of Birth Should Be Past Date.\n';
					myvar.demographics_edit_form.dob.style.backgroundColor=changecolor; 
					
					} 
				 if(myvar.demographics_edit_form.sex.value == '')
				 {  msg = msg + '   Sexual information is required\n';  
					myvar.demographics_edit_form.sex.style.backgroundColor=changecolor;
				 
				 }
				 ////////Skip SSSN IF MIor Patient///////////
					 var datstr=myvar.demographics_edit_form.dob.value;
					 var ageDiff=getAgeDifference(datstr);
					 var skipSSN=false;
					// alert(ageDiff);
					 if(ageDiff<18){
					  skipSSN=true;
					 }
					  if(ageDiff==18){
					  skipSSN=false;
					 }
					 myvar.demographics_edit_form.patient_age.value=ageDiff;
				 //////////End Code to skip SSN//////////////
				 
				 if (myvar.demographics_edit_form.ss.value != "") {
				
					ssNum=myvar.demographics_edit_form.ss.value;	
				
				if(ssNum.length<9 && skipSSN==false)
				{	
						msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
						myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
				
				}else{								
						
					if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
					{
							
						msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
						myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;											
					}else{				
					
						for (i=0;i<ssNum.length;i++) {
							if (ssNum.charAt(i) == "-") {
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						}
						if((ssNum.length<9) || (ssNum.length>9))
						{
							msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
						}else{
							for (i=0;i<ssNum.length;i++) {
								if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) 
								{
								   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
								   myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
								   break;
								}
							}		
						}
					}
				}			
				}
				
				if(myvar.demographics_edit_form.street.value == '')
				{  
					msg = msg + '   Address is required\n';
					myvar.demographics_edit_form.street.style.backgroundColor=changecolor;
				}
				if(myvar.demographics_edit_form.postal_code.value=='')	{
					msg = msg + top.zipLabel + ' is required\n';
					myvar.demographics_edit_form.postal_code.style.backgroundColor=changecolor;
				}
				else{
					validateZIP(myvar.demographics_edit_form.postal_code); //zip validate
				}
				if(myvar.demographics_edit_form.city.value == '')
				{ 
					msg = msg + '   City is required\n'; 
					myvar.demographics_edit_form.city.style.backgroundColor=changecolor;
				}
				
				if(myvar.demographics_edit_form.state.value == '')
				{  
					msg = msg + '   State is required\n'; 
					myvar.demographics_edit_form.state.style.backgroundColor=changecolor;
				}			
				
				/*if(myvar.demographics_edit_form.country_code.value == '')
				{  
					msg = msg + '   Country is required\n'; 
					myvar.demographics_edit_form.country_code.style.backgroundColor=changecolor;
				}*/
				
				var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/;
				if(myvar.demographics_edit_form.phone_home.value=="" && myvar.demographics_edit_form.phone_biz.value=="" && myvar.demographics_edit_form.phone_cell.value==""&& myvar.demographics_edit_form.phone_contact1.value=="") //check US phone
				{
					msg = msg + '  One Phone Number is requierd\n';
				}
				
				if(myvar.demographics_edit_form.pass1)
				{ 
					if(myvar.demographics_edit_form.pass1.value != myvar.demographics_edit_form.pass2.value)
					{	
						msg = msg + '   Password does not match \n';  
						myvar.demographics_edit_form.pass1.style.backgroundColor=changecolor;
						myvar.demographics_edit_form.pass2.style.backgroundColor=changecolor;	
					} 
				} 
				//Check Status
				if(myvar.document.demographics_edit_form.elem_patientStatus.value != "Active")
				{
					flagActivePatient = false;
				}
				if(myvar.document.getElementById("table4").style.display=="block"){
					if(myvar.demographics_edit_form.fname1.value!="" || myvar.demographics_edit_form.mname1.value!="" ||	myvar.demographics_edit_form.lname1.value!=""  ||
					myvar.demographics_edit_form.suffix1.value!=""  || myvar.demographics_edit_form.street1.value!="" || 	myvar.demographics_edit_form.street_emp.value!="" ||
					myvar.demographics_edit_form.dlicence1.value!="" || myvar.demographics_edit_form.city1.value!="" || myvar.demographics_edit_form.relation1.value!="" ||
					myvar.demographics_edit_form.state2.value!="" || myvar.demographics_edit_form.postal_code1.value!="" ||	myvar.demographics_edit_form.phone_home1.value!="" ||
					myvar.demographics_edit_form.phone_biz1.value!="" || myvar.demographics_edit_form.phone_cell1.value!=""){
						
						if(myvar.demographics_edit_form.fname1.value=="" || myvar.demographics_edit_form.lname1.value==""  ||
							myvar.demographics_edit_form.street1.value=="" || myvar.demographics_edit_form.city1.value=="" ||
							myvar.demographics_edit_form.state2.value=="" || myvar.demographics_edit_form.postal_code1.value=="" 
						)
						{
						
							msg= msg+ '\n  Responsible Party\n\n';
						}
						myvar.document.getElementById("table4").style.display="block";
						cDate=Date.parse(myvar.demographics_edit_form.from_date_byram1.value); 
						dDate=Date.parse(myvar.demographics_edit_form.dob1.value); 
							
						if (myvar.demographics_edit_form.fname1.value == '')
						{
							msg  = msg + '   First name is required\n'; 
							myvar.demographics_edit_form.fname1.style.backgroundColor=changecolor;
						}
						
						if(myvar.demographics_edit_form.lname1.value == '')
						{  
							msg = msg + '   Last name is required\n';  
							myvar.demographics_edit_form.lname1.style.backgroundColor=changecolor;
						}
						//relation1
						if(myvar.demographics_edit_form.relation1.value == '')
						{ 
							msg = msg + '   Relation with patient is required\n';  
							myvar.demographics_edit_form.relation1.style.backgroundColor=changecolor;
						}
						if(dDate >= cDate) 
						{
							msg = msg + '  - Date of Birth Should Be Past Date.\n';
							myvar.demographics_edit_form.dob1.style.backgroundColor=changecolor; 
						} 
							
						if(myvar.demographics_edit_form.ss1.value != ""){
							ssNum=myvar.demographics_edit_form.ss1.value;	
							if(ssNum.length<9)
							{	
								msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
								myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
							}else{								
								if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
								{
									msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
									myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
								}else{				
									for(i=0;i<ssNum.length;i++) {
										if (ssNum.charAt(i) == "-") {
											before = ssNum.substring(0,i);
											after = ssNum.substring(i+1);
											ssNum = before + after;
										}
									}										
									for (i=0;i<ssNum.length;i++) {
										// Make sure the 9 characters are indeed numbers
										if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
										   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
										   myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor; 
										   break;
										}
									}						
								}
							}			
						}
						 
						if(myvar.demographics_edit_form.street1.value == '')
						{ 
							msg = msg + '   Address is required\n';
							myvar.demographics_edit_form.street1.style.backgroundColor=changecolor;
						}
						 
						if(myvar.demographics_edit_form.city1.value == '')
						{ 
							msg = msg + '   City is required\n'; 
							myvar.demographics_edit_form.city1.style.backgroundColor=changecolor;
						}
						 
						if(myvar.demographics_edit_form.state.value == '')
						{  
							msg = msg + '   State is required\n'; 
							myvar.demographics_edit_form.state.style.backgroundColor=changecolor;
						}
						if(myvar.demographics_edit_form.postal_code1.value=='')	{
							msg = msg + top.zipLabel + ' is required\n';
							myvar.demographics_edit_form.postal_code1.style.backgroundColor=changecolor;
						}else	{
							validateZIP(myvar.demographics_edit_form.postal_code1); //zip validate
						}
						 
						if(myvar.demographics_edit_form.country_code1.value == '')
						{ 	 	
							msg = msg + '   Country is required\n'; 
							myvar.demographics_edit_form.country_code1.style.backgroundColor=changecolor;
						}
						var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/;
						if(myvar.demographics_edit_form.phone_home1.value=="" && myvar.demographics_edit_form.phone_biz1.value=="" && myvar.demographics_edit_form.phone_cell1.value==""&& myvar.demographics_edit_form.phone_contact1.value=="") //check US phone
						{
							msg = msg + '  One Phone Number is requierd\n';
						}
					}
				}				
			}	 
		if(myvar.demographics_edit_form.relation1.value=="self"){
				myvar.demographics_edit_form.title1.value=myvar.demographics_edit_form.title.value;
				myvar.demographics_edit_form.fname1.value=myvar.demographics_edit_form.fname.value;
				myvar.demographics_edit_form.mname1.value=myvar.demographics_edit_form.mname.value;
				myvar.demographics_edit_form.lname1.value=myvar.demographics_edit_form.lname.value;
				myvar.demographics_edit_form.suffix1.value=myvar.demographics_edit_form.suffix.value;
				myvar.demographics_edit_form.dob1.value=myvar.demographics_edit_form.dob.value;
				myvar.demographics_edit_form.sex1.value=myvar.demographics_edit_form.sex.value;
				myvar.demographics_edit_form.ss1.value=myvar.demographics_edit_form.ss.value;
				myvar.demographics_edit_form.street1.value=myvar.demographics_edit_form.street.value;
				myvar.demographics_edit_form.street_emp.value=myvar.demographics_edit_form.street2.value;
				myvar.demographics_edit_form.status1.value=myvar.demographics_edit_form.status.value;
				myvar.demographics_edit_form.dlicence1.value=myvar.demographics_edit_form.dlicence.value;
				myvar.demographics_edit_form.city1.value=myvar.demographics_edit_form.city.value;
				myvar.demographics_edit_form.state2.value=myvar.demographics_edit_form.state.value;
				myvar.demographics_edit_form.postal_code1.value=myvar.demographics_edit_form.postal_code.value;
				//myvar.demographics_edit_form.country_code1.value=myvar.demographics_edit_form.country_code.value;
				myvar.demographics_edit_form.phone_home1.value=myvar.demographics_edit_form.phone_home.value;
				myvar.demographics_edit_form.phone_biz1.value=myvar.demographics_edit_form.phone_biz.value;
				myvar.demographics_edit_form.phone_cell1.value=myvar.demographics_edit_form.phone_cell.value;
		}
	
		if(flagActivePatient == false)
		{
			myvar.demographics_edit_form.submit();
		}
		else if(msg == '')
		{
			/////////////////////////////////////////
			// AJAX VALIDATION FOR UNIQUE SSN # /////
			/////////////////////////////////////////
			var ssn_number;
			ssn_number=myvar.demographics_edit_form.ss.value;
			var url_dt="../common/getssnnumber.php";								
			url_dt=url_dt+"?ssn_number="+ssn_number;
			xmlHttp_dt=GetXmlHttpObject();
			if(xmlHttp_dt==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			}						
			xmlHttp_dt.onreadystatechange = function() 
			{
				if (xmlHttp_dt.readyState==4 || xmlHttp_dt.readyState=="complete")
				{ 	
					if(xmlHttp_dt.responseText=='ssn_unique'){
						 myvar.demographics_edit_form.submit();
						 
					}else{									
						
						alert(" - SSN # is not unique. ");	
					}												
				}
			}
			xmlHttp_dt.open("GET",url_dt,true);				
			xmlHttp_dt.send(null); 
			confmsg="The following information of Patient has been updating\n\n";
			confmsg+="Title 	:"+frm.title.value+"\n";
			confmsg+="Name	: "+frm.fname.value+" "+frm.mname.value+" "+frm.lname.value+" "+frm.suffix.value+"\n";
			confmsg+="DOB 	: "+frm.dob.value+"\n";	
			confmsg+="SEX	: "+frm.sex.value+"\n";
			confmsg+="Address1 :"+frm.street.value+"\n";
			confmsg+="Address2 :"+frm.street2.value+"\n";
			confmsg+="Postal Code :"+frm.postal_code.value+"\n";
			confmsg+="City	:"+frm.city.value+"\n";
			confmsg+="State	:"+frm.state.value+"\n";
			confmsg+="Phone	:"+frm.phone_home.value+"\n";
			confmsg+="	:"+frm.phone_biz.value+"\n";
			confmsg+="	:"+frm.phone_cell.value+"\n";
			if(myvar.document.getElementById("exist2").value=="alreadyexist"){
				if(confmsg)	{
					alert(confmsg);
				}
			}
		}
		else 
		{
			alert(msg);
		}
	}	
	else if(obj.id=="butId2"){
		if(myvar.demographics_edit_form.pass1){ 
			if(myvar.demographics_edit_form.pass1.value != myvar.demographics_edit_form.pass2.value){	
				msg = msg + '   Password does not match \n';  
				myvar.demographics_edit_form.pass1.style.backgroundColor=changecolor;
				myvar.demographics_edit_form.pass2.style.backgroundColor=changecolor;	
			} 
		}
		if(myvar.demographics_edit_form.ss.value != "") {	
			ssNum=myvar.demographics_edit_form.ss.value;		
				if(ssNum.length<9){
					msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
					myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;		
				}else{												
					if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
					{						
							msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
							//return false;					
					}else{				
						for (i=0;i<ssNum.length;i++) {
							if (ssNum.charAt(i) == "-") {
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						}									
						for (i=0;i<ssNum.length;i++) {
							// Make sure the 9 characters are indeed numbers
							if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
								// If not a number, stop the form post
							   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							   myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
							   break;
							}
						}						
					}
				}				
			}	
			if(myvar.demographics_edit_form.ss1.value != "") {	
				ssNum=myvar.demographics_edit_form.ss1.value;		
				if(ssNum.length<9)
				{	
						msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
						myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;		
				}else{												
					if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
					{						
							msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
							//return false;					
					}else{				
						for (i=0;i<ssNum.length;i++) {
							if (ssNum.charAt(i) == "-") {
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						 }									
						for (i=0;i<ssNum.length;i++) {
							// Make sure the 9 characters are indeed numbers
							if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
								// If not a number, stop the form post
							   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							   myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor; 
							   break;
							}
						}						
					}
				}				
			}
			if(msg){
				alert(msg);		
			}
			else{
				if(msg == ''){
					
					/////////////////////////////////////////
					// AJAX VALIDATION FOR UNIQUE SSN # /////
					/////////////////////////////////////////
					
					var ssn_number;
					ssn_number=myvar.demographics_edit_form.ss.value;
					
					var url_dt="../common/getssnnumber.php";								
					url_dt=url_dt+"?ssn_number="+ssn_number;
									
					xmlHttp_dt=GetXmlHttpObject();
				
					if(xmlHttp_dt==null)
					{
						alert ("Browser does not support HTTP Request");
						return;
					}						
									
					xmlHttp_dt.onreadystatechange = function() 
					{
						if (xmlHttp_dt.readyState==4 || xmlHttp_dt.readyState=="complete")
						{ 	
							//alert(xmlHttp_dt.responseText);
							if(xmlHttp_dt.responseText=='ssn_unique'){
								 myvar.demographics_edit_form.submit();
							}else{									
								alert(" - SSN # is not unique. ");	
								parent.document.getElementById('load_image21').style.display='none';
							}												
						}
					}
					xmlHttp_dt.open("GET",url_dt,true);				
					xmlHttp_dt.send(null);   
				}
				//myvar.demographics_edit_form.submit();//after prac. mand
			}
		//myvar.demographics_edit_form.submit();
		}
	}

function getCollapseStatus(){

	myvar = eval(top.fmain.all_data);
	if(myvar.document.getElementById("table1").style.display=="block"){
		myvar.document.demographics_edit_form.ptInfoCollapseStatus.value=1;
	}
	else if(myvar.document.getElementById("table1").style.display=="none"){
		myvar.document.demographics_edit_form.ptInfoCollapseStatus.value=0;
		
	}
	//getting status of Responsible Party table
	if(myvar.document.getElementById("table4").style.display=="block"){
		myvar.document.demographics_edit_form.resPartyCollapseStatus.value=1;
	}
	else if(myvar.document.getElementById("table4").style.display=="none"){
		myvar.document.demographics_edit_form.resPartyCollapseStatus.value=0;
		
	}
	//getting status of Patient Occupation table
	if(myvar.document.getElementById("table2").style.display=="block"){
		myvar.document.demographics_edit_form.ptOccCollapseStatus.value=1;
	}
	else if(myvar.document.getElementById("table2").style.display=="none"){
		myvar.document.demographics_edit_form.ptOccCollapseStatus.value=0;
	}
	//getting status of Miscellaneous table
	if(myvar.document.getElementById("table222").style.display=="block"){
		myvar.document.demographics_edit_form.miscCollapseStatus.value=1;
	}
	else if(myvar.document.getElementById("table222").style.display=="none"){
		myvar.document.demographics_edit_form.miscCollapseStatus.value=0;
	}
	//getting status of patient info table
}

function validate2(tab_save,newDemographics)
{
	if(!newDemographics)
		getCollapseStatus();
	myvar = top.fmain.all_data;
	msg = '';
	var flagActivePatient = true;
	var erx_entry = myvar.demographics_edit_form.erx_entry.value;
	var changecolor='#FFCC66';
	var Allow_erx_medicare = myvar.demographics_edit_form.Allow_erx_medicare.value;
	if(Allow_erx_medicare == 'Yes')
	{
		if(erx_entry == 0)
		{
			var erxmsg = "Patient is not registered for e/RX\n";
			
			erxmsg += "Would you like to register patient for e/Rx";
			
			if(confirm(erxmsg)){
				myvar.demographics_edit_form.erx_entry.value = 1;
				myvar.demographics_edit_form.chkErxAsk.value = 1;
			}
			else{
				myvar.demographics_edit_form.erx_entry.value = 0;
				myvar.demographics_edit_form.chkErxAsk.value = 0;
			}
		}
	}
	
	if(tab_save!="Demographics")
	{		
		 if(myvar.document.getElementById("table1").style.display=="block")
		 {
			date1=myvar.demographics_edit_form.reg_date.value;
			date2=date1.replace("-","/");
			date3=date2.replace("-","/");
			DateToShow=date3;
			idt1=myvar.demographics_edit_form.curr_date.value;
			idt2=idt1.replace("-","/");
			idt3=idt2.replace("-","/");
			DateToCheck=idt3;
		
			d=new Date();
			d=new Date(d.getYear(), d.getMonth(), d.getDate());
			//* Code Singh*//
			cDate=Date.parse(myvar.demographics_edit_form.from_date_byram.value); 
			dDate=Date.parse(myvar.demographics_edit_form.dob.value); 
		
		 	//End By Ram Singh Code//

			if(new Date(DateToCheck) < new Date(DateToShow))
			{
				  msg=msg + '   Please Fill Till Current Date in Registration Date \n';
				  myvar.demographics_edit_form.reg_date.style.backgroundColor=changecolor;
			}	
			if(myvar.demographics_edit_form.reg_date.value == '')
			{
				  msg=msg + '   Please Fill Registration Date \n';
				  myvar.demographics_edit_form.reg_date.style.backgroundColor=changecolor;
			}	
		
			if (myvar.demographics_edit_form.fname.value == '')
			{
			   msg  = msg + '   First name is required\n'; 
			   myvar.demographics_edit_form.fname.style.backgroundColor=changecolor;
			}
		
			 if(myvar.demographics_edit_form.lname.value == '')
			 {
				msg = msg + '   Last name is required\n';  
				myvar.demographics_edit_form.lname.style.backgroundColor=changecolor;
			 }
			 if(myvar.demographics_edit_form.sex.value == '')
			 {  
			 	msg = msg + '   Sexual Information is required\n'; 
				myvar.demographics_edit_form.sex.style.backgroundColor=changecolor;
			 }
		
			 /*if(myvar.demographics_edit_form.providerID.selectedIndex <= 0)
			 { 
				msg = msg + '   Please select your Primary Provider\n'; 
				myvar.demographics_edit_form.providerID.style.backgroundColor=changecolor;
			 } 
			 */
			 if(myvar.demographics_edit_form.dob.value == '' || myvar.demographics_edit_form.dob.value == '00-00-0000')
			 { 
				msg = msg + '   Date of Birth is required\n'; 
				myvar.demographics_edit_form.dob.style.backgroundColor=changecolor;
			 }
			 else if(dDate >= cDate) 
			 {
				msg = msg + '  - Date of Birth Should Be Past Date.\n';
				myvar.demographics_edit_form.dob.style.backgroundColor=changecolor; 
			 } 
			 /*else{ 
							isField=myvar.demographics_edit_form.dob;
							myvar=eval(myvar);
							splitDate = isField.value.split("-");
							if (splitDate[2] && splitDate[2].length == 2){splitDate[2] = "20"+splitDate[2]}
							refDate = new Date(splitDate[0]+"-"+splitDate[1]+"-"+splitDate[2]);
							
							var currentTime = new Date();
							var month = currentTime.getMonth()+1;
							var day = currentTime.getDate();
							var year = currentTime.getFullYear();
							
							var sDate = new Date(splitDate[2],splitDate[0],splitDate[1]);
							var iDate = new Date(year,month,day);
												
							if(month == splitDate[0] && year==splitDate[2] && splitDate[1] >=day )
							{
								msg = msg + '   Date of Birth Should Be Past Date \n';		
								isField.style.backgroundColor=changecolor;
							}
							else if (splitDate[0] < 1 || splitDate[0] > 12 || refDate.getDate() != splitDate[1] || splitDate[2].length != 4 || splitDate[2] <= 1900)
							{			
								msg = msg + '   Invalid Date of Birth \n';		
								isField.style.backgroundColor=changecolor;
							}
			
			 }*/
			/*
			 if(myvar.demographics_edit_form.sex.selectedIndex <= 0)
			 { 
				msg = msg + '   Please select you sex\n'; 
				myvar.demographics_edit_form.sex.style.backgroundColor=changecolor; 
			 }
			 */
		 ////////Skip SSSN IF MIor Patient///////////
		 	 var datstr=myvar.demographics_edit_form.dob.value;
			 var ageDiff=getAgeDifference(datstr);
			 var skipSSN=false;
			// alert(ageDiff);
			 if(ageDiff<18)
			 {
			  skipSSN=true;
			 }
			  if(ageDiff==18)
			  {
				  skipSSN=false;
			  }
			 myvar.demographics_edit_form.patient_age.value=ageDiff;
		 //////////End Code to skip SSN//////////////
		 
		 /*
		 if(myvar.demographics_edit_form.ss.value == '' && skipSSN==false)
		 { 	
			msg = msg + '   Social Security Number (SSN) is required\n'; 
			myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
		 }   
		 */	
		 if (myvar.demographics_edit_form.ss.value != "")
		 {
	     	ssNum=myvar.demographics_edit_form.ss.value;	
		  	if(ssNum.length<9 && skipSSN==false)
			{	
				msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
				myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
			}else//3
			{								
				if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
				{
					msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
					myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;											
				}else//2
				{
						for (i=0;i<ssNum.length;i++)
						{
							if (ssNum.charAt(i) == "-")
							{
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						 }
						if((ssNum.length<9) || (ssNum.length>9))
						{
							msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
						}else//1
						{
							for (i=0;i<ssNum.length;i++)
							{
									if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) 
									{
									   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
									   myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
									   break;
									}
							}//for loop		
						}//else 1
				}//else 2
			}//else 3
		}//else 4
 
		 if(myvar.demographics_edit_form.street.value == '')
 		 {
			msg = msg + '   Address is required\n';
 			myvar.demographics_edit_form.street.style.backgroundColor=changecolor;
		 }
		 if(myvar.demographics_edit_form.postal_code.value=='')
		 {
			msg = msg + top.zipLabel + ' is required\n';
		 	myvar.demographics_edit_form.postal_code.style.backgroundColor=changecolor;
		 }else
		 {
		 	 validateZIP(myvar.demographics_edit_form.postal_code); //zip validate
		 }
		 if(myvar.demographics_edit_form.city.value == '')
		 {
			msg = msg + '   City is required\n'; 
		 	myvar.demographics_edit_form.city.style.backgroundColor=changecolor;
		 }
		 if(myvar.demographics_edit_form.state.value == '')
		 {
			msg = msg + '   State is required\n'; 
		 	myvar.demographics_edit_form.state.style.backgroundColor=changecolor;
		 }
		 /*if(myvar.demographics_edit_form.country_code.value == '')
		 {  msg = msg + '   Country is required\n'; 
			myvar.demographics_edit_form.country_code.style.backgroundColor=changecolor;
		 
		 }*/
 
		 var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/;

		 if(myvar.demographics_edit_form.phone_home.value=="" && myvar.demographics_edit_form.phone_biz.value=="" && myvar.demographics_edit_form.phone_cell.value=="" && myvar.demographics_edit_form.phone_contact.value=="") //check US phone && 
		 {
			msg = msg + '  One Phone Number is requierd\n';
			//myvar.demographics_edit_form.phone_home.style.backgroundColor=changecolor;
		 }/*else{
			 var objPhone=eval(myvar.demographics_edit_form.phone_home);
			 ValidatePhone(objPhone);
		 }*/

		 if(myvar.demographics_edit_form.pass1)
		 { 
			 if(myvar.demographics_edit_form.pass1.value != myvar.demographics_edit_form.pass2.value)
		 	{
				msg = msg + '   Password does not match \n';  
				myvar.demographics_edit_form.pass1.style.backgroundColor=changecolor;
				myvar.demographics_edit_form.pass2.style.backgroundColor=changecolor;	
		 	}
		 }
		 //Check Status
		if(myvar.document.demographics_edit_form.elem_patientStatus.value != "Active")
		{
			flagActivePatient = false;
		}
 
		  if(myvar.document.getElementById("table4").style.display=="block"){
		if(myvar.demographics_edit_form.fname1.value!="" || myvar.demographics_edit_form.mname1.value!="" || myvar.demographics_edit_form.lname1.value!=""  ||
		myvar.demographics_edit_form.suffix1.value!=""  || myvar.demographics_edit_form.street1.value!="" || myvar.demographics_edit_form.street_emp.value!="" ||
		myvar.demographics_edit_form.dlicence1.value!="" || myvar.demographics_edit_form.city1.value!="" || myvar.demographics_edit_form.relation1.value!="" ||
		myvar.demographics_edit_form.postal_code1.value!="" ||	myvar.demographics_edit_form.phone_home1.value!="" ||
		myvar.demographics_edit_form.phone_biz1.value!="" || myvar.demographics_edit_form.phone_cell1.value!="")
		{
			if(myvar.demographics_edit_form.fname1.value=="" || myvar.demographics_edit_form.lname1.value==""  ||
				myvar.demographics_edit_form.street1.value=="" || myvar.demographics_edit_form.city1.value=="" ||
				myvar.demographics_edit_form.postal_code1.value=="" 
			)
			{
				msg= msg+ '\n  Responsible Party\n\n';
			}	
			myvar.document.getElementById("table4").style.display="block";
			cDate=Date.parse(myvar.demographics_edit_form.from_date_byram1.value); 
			dDate=Date.parse(myvar.demographics_edit_form.dob1.value); 
			
			 if (myvar.demographics_edit_form.fname1.value == '')
			 {
			   msg  = msg + '   First name is required\n'; 
			   myvar.demographics_edit_form.fname1.style.backgroundColor=changecolor;
			 }
		
			 if(myvar.demographics_edit_form.lname1.value == '')
			 {
				msg = msg + '   Last name is required\n';  
				myvar.demographics_edit_form.lname1.style.backgroundColor=changecolor;
			 }
			 //relation1
			  if(myvar.demographics_edit_form.relation1.value == '')
			 {
				msg = msg + '   Relation with patient is required\n';  
				myvar.demographics_edit_form.relation1.style.backgroundColor=changecolor;
			 }
			 /*if(myvar.frm_resp1.dob1.value == '' || myvar.frm_resp1.dob1.value == '00-00-0000')
			 { 
				msg = msg + '   Date of Birth is required\n'; 
				myvar.frm_resp1.dob1.style.backgroundColor=changecolor;
			 } 
			 else */
			if(dDate >= cDate) 
			{
				msg = msg + '  - Date of Birth Should Be Past Date.\n';
				myvar.demographics_edit_form.dob1.style.backgroundColor=changecolor; 
			} 
			 /*else{ 
				validate_dt_dob(myvar.frm_resp1.dob1);	
			 }*/
			 /* 
			 if(myvar.demographics_edit_form.sex1.selectedIndex <= 0)
			 { 
				msg = msg + '   Please select you sex\n'; 
				myvar.demographics_edit_form.sex1.style.backgroundColor=changecolor; 
			 }
			  */
			/* if(myvar.frm_resp1.ss1.value == '')
			 { 	
				msg = msg + '   Social Security Number (SSN) is required\n'; 
				myvar.frm_resp1.ss1.style.backgroundColor=changecolor;
			 }*/   	
			 if (myvar.demographics_edit_form.ss1.value != "")
			 {
				ssNum = myvar.demographics_edit_form.ss1.value;	
				if(ssNum.length<9)
				{	
					msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
					myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
				}else
				{
					if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
					{
						msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
						myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
						//return false;
					}else
					{
						for (i=0;i<ssNum.length;i++)
						{
							if (ssNum.charAt(i) == "-")
							{
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						}
						for (i=0;i<ssNum.length;i++)
						{
							// Make sure the 9 characters are indeed numbers
							if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + ""))
							{
								// If not a number, stop the form post
							   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							   myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor; 
							   break;
							}
						}//for loop
					}
				}//else
						
		}// if
 
		 if(myvar.demographics_edit_form.street1.value == '')
		 {
			msg = msg + '   Address is required\n';
			myvar.demographics_edit_form.street1.style.backgroundColor=changecolor;
		 }
 
		 if(myvar.demographics_edit_form.city1.value == '')
		 {
			msg = msg + '   City is required\n'; 
			myvar.demographics_edit_form.city1.style.backgroundColor=changecolor;
		 }
 
		if(myvar.demographics_edit_form.state.value == '')
		{
			msg = msg + '   State is required\n'; 
			myvar.demographics_edit_form.state.style.backgroundColor=changecolor;
		}
		if(myvar.demographics_edit_form.postal_code1.value=='')
		{
			msg = msg + top.zipLabel + ' is required\n';
			myvar.demographics_edit_form.postal_code1.style.backgroundColor=changecolor;
		}else
		{
			validateZIP(myvar.demographics_edit_form.postal_code1); //zip validate
		}

		 if(myvar.demographics_edit_form.country_code1.value == '')
		 {
			msg = msg + '   Country is required\n'; 
			myvar.demographics_edit_form.country_code1.style.backgroundColor=changecolor;
		 }
 
		 var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/;
		 
		
		 /*if(myvar.frm_resp1.phone_home1.value=="") //check US phone
		 {
			msg = msg + '  Home Phone is requierd\n';
			myvar.frm_resp1.phone_home1.style.backgroundColor=changecolor;
		 }*/
		 if(myvar.demographics_edit_form.phone_home1.value=="" && myvar.demographics_edit_form.phone_biz1.value=="" && myvar.demographics_edit_form.phone_cell1.value=="") //check US phone
		 {
			msg = msg + '  One Phone Number is requierd\n';
			//myvar.demographics_edit_form.phone_home.style.backgroundColor=changecolor;
		 }
	
	}//if
 }//if
 			}//if
			if(myvar.demographics_edit_form.relation1.value=="self")
			{
				myvar.demographics_edit_form.title1.value=myvar.demographics_edit_form.title.value;
				myvar.demographics_edit_form.fname1.value=myvar.demographics_edit_form.fname.value;
				myvar.demographics_edit_form.mname1.value=myvar.demographics_edit_form.mname.value;
				myvar.demographics_edit_form.lname1.value=myvar.demographics_edit_form.lname.value;
				myvar.demographics_edit_form.suffix1.value=myvar.demographics_edit_form.suffix.value;
			
				myvar.demographics_edit_form.dob1.value=myvar.demographics_edit_form.dob.value;
				myvar.demographics_edit_form.sex1.value=myvar.demographics_edit_form.sex.value;
				myvar.demographics_edit_form.ss1.value=myvar.demographics_edit_form.ss.value;
				myvar.demographics_edit_form.street1.value=myvar.demographics_edit_form.street.value;
				myvar.demographics_edit_form.street_emp.value=myvar.demographics_edit_form.street2.value;
				myvar.demographics_edit_form.status1.value=myvar.demographics_edit_form.status.value;
				myvar.demographics_edit_form.dlicence1.value=myvar.demographics_edit_form.dlicence.value;
				myvar.demographics_edit_form.city1.value=myvar.demographics_edit_form.city.value;
				myvar.demographics_edit_form.state2.value=myvar.demographics_edit_form.state.value;
				myvar.demographics_edit_form.postal_code1.value=myvar.demographics_edit_form.postal_code.value;
				//myvar.demographics_edit_form.country_code1.value=myvar.demographics_edit_form.country_code.value;
				myvar.demographics_edit_form.phone_home1.value=myvar.demographics_edit_form.phone_home.value;
				myvar.demographics_edit_form.phone_biz1.value=myvar.demographics_edit_form.phone_biz.value;
				myvar.demographics_edit_form.phone_cell1.value=myvar.demographics_edit_form.phone_cell.value;
			}
			 if(flagActivePatient == false)
			 {
				 myvar.demographics_edit_form.submit();
			 }
			 else if((msg == '')){						
				/////////////////////////////////////////
				// AJAX VALIDATION /////
				/////////////////////////////////////////							
				var ssn_number;
				ssn_number=myvar.demographics_edit_form.ss.value;
				
				var url_dt="../common/getssnnumber.php";								
				url_dt=url_dt+"?ssn_number="+ssn_number;
								
				xmlHttp_dt=GetXmlHttpObject();
			
				if(xmlHttp_dt==null)
				{
					alert ("Browser does not support HTTP Request");
					return;
				}						
											
				xmlHttp_dt.onreadystatechange = function(){
					if (xmlHttp_dt.readyState==4 || xmlHttp_dt.readyState=="complete"){ 																					
						if(xmlHttp_dt.responseText=='ssn_unique'){
							 myvar.demographics_edit_form.submit();								 
						}else{
							alert(" - SSN # is not unique. ");	
						}												
					}
				}
				xmlHttp_dt.open("GET",url_dt,true);				
				xmlHttp_dt.send(null);   
				
				
				var userName;
				userName=myvar.demographics_edit_form.usernm.value;
				
				var urlUserName="searchDemoUserName.php";								
				urlUserName = urlUserName+"?userName="+userName;
								
				xmlHttpUserName=GetXmlHttpObject();
			
				if(xmlHttpUserName==null)
				{
					alert ("Browser does not support HTTP Request");
					return;
				}						
											
				xmlHttpUserName.onreadystatechange = function(){
					if (xmlHttpUserName.readyState==4 || xmlHttpUserName.readyState=="complete"){ 																					
						if(xmlHttpUserName.responseText=="0"){
							 myvar.demographics_edit_form.submit();								 
						}else{
							alert(" - User name already exists please try another. ");	
						}												
					}
				}
				xmlHttpUserName.open("GET",urlUserName,true);				
				xmlHttpUserName.send(null);   
				
			  }
				 else{
					alert(msg);
				 }
			}
			else{
				var msg = 'Please confirm the following \n';
				if(myvar.demographics_edit_form.pass1){ 
					if(myvar.demographics_edit_form.pass1.value != myvar.demographics_edit_form.pass2.value){	
						msg += '  �   Password and Confirm Password does not match \n';  
						myvar.demographics_edit_form.pass1.style.backgroundColor=changecolor;
						myvar.demographics_edit_form.pass2.style.backgroundColor=changecolor;	
					}
			 	}
				if(myvar.demographics_edit_form.ss.value != ""){
					ssNum=myvar.demographics_edit_form.ss.value;		
					if(ssNum.length<9)
					{	
						msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
						myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;		
					}
					else{												
						if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" )){
							msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
							//return false;					
						}
						else{				
							for (i=0;i<ssNum.length;i++) {
								if (ssNum.charAt(i) == "-") {
									before = ssNum.substring(0,i);
									after = ssNum.substring(i+1);
									ssNum = before + after;
								}
						 	}
							for (i=0;i<ssNum.length;i++) {
								// Make sure the 9 characters are indeed numbers
								if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
									// If not a number, stop the form post
							   		msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							   		myvar.demographics_edit_form.ss.style.backgroundColor=changecolor; 
							   		break;
								}
							}//for loop		
						}
					}
				}

				if(myvar.demographics_edit_form.ss1.value != ""){	
					ssNum=myvar.demographics_edit_form.ss1.value;		
				if(ssNum.length<9){
					msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
					myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;		
				}
				else{												
					if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" )){						
						msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
						myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
						//return false;					
					}
					else{				
						for (i=0;i<ssNum.length;i++) {
							if (ssNum.charAt(i) == "-") {
								before = ssNum.substring(0,i);
								after = ssNum.substring(i+1);
								ssNum = before + after;
							}
						}									
						for (i=0;i<ssNum.length;i++) {
							// Make sure the 9 characters are indeed numbers
							if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
								// If not a number, stop the form post
							   msg += '  �   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
							   myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor; 
							   break;
							}
						}						
					}
				}//else
			}
			
			/////////////////////////////////////////
			// AJAX VALIDATION /////
			/////////////////////////////////////////							
			/*var ssn_number;
			ssn_number=myvar.demographics_edit_form.ss.value;
			var url_dt="../common/getssnnumber.php";								
			url_dt=url_dt+"?ssn_number="+ssn_number;
			xmlHttp_dt=GetXmlHttpObject();
			if(xmlHttp_dt==null){
				alert ("Browser does not support HTTP Request");
				return;
			}
			xmlHttp_dt.onreadystatechange = function(){
				if (xmlHttp_dt.readyState==4 || xmlHttp_dt.readyState=="complete"){ 																					
					if(xmlHttp_dt.responseText=='ssn_unique'){
						myvar.demographics_edit_form.submit();								 
					}
					else{
						msg += ' �  SSN # is not unique\n';							
					}												
				}
			}
			xmlHttp_dt.open("GET",url_dt,true);				
			xmlHttp_dt.send(null);   
			*/	
			var userName,ssnNumber;
			userName=myvar.demographics_edit_form.usernm.value;
			ssnNumber=myvar.demographics_edit_form.ss.value;
			var urlUserName="AJAXValidationDemo.php";								
			urlUserName = urlUserName+"?userName="+userName+"&ssnNumber="+ssnNumber;			
			xmlHttpUserName=GetXmlHttpObject();
			if(xmlHttpUserName==null){
				alert ("Browser does not support HTTP Request");
				return;
			}						
			xmlHttpUserName.onreadystatechange = function(){				
				if (xmlHttpUserName.readyState==4 || xmlHttpUserName.readyState=="complete"){ 	
					if(xmlHttpUserName.responseText != ""){
						var strAJAXResp = xmlHttpUserName.responseText;
						var arrAJAXResp = strAJAXResp.split("~~");					
						if(arrAJAXResp[0] == "1"){
							msg += '  �   SSN # is not unique\n';	
							myvar.demographics_edit_form.ss.style.backgroundColor=changecolor;
						}
						if(arrAJAXResp[1] == "1"){
							msg += '  �   Login-Id already exists please try another\n';
							myvar.demographics_edit_form.usernm.style.backgroundColor=changecolor;
						}					
						
						if(msg != 'Please confirm the following \n'){			
							top.fmain.document.getElementById('load_image21').style.display='none';
							alert(msg);
						}
						else{
							var zipCodeStatus = myvar.document.getElementById("zipCodeStatus");
							var zipCode = myvar.document.getElementById("postal_code");				
							if(zipCodeStatus.value == 'NA'){
								if(confirm("Zip code Does not exist.Do you want to add Zip Code")){
									zipCodeStatus.value = "NA";
								}else{
									zipCodeStatus.value = "NotOk";
								}
							}							
							myvar.demographics_edit_form.submit();//after prac. mand
							//alert("submited");
						}//else
					}									
				}
			}
			xmlHttpUserName.open("GET",urlUserName,true);				
			xmlHttpUserName.send(null); 
		}
}


	function validate_occu()
	{
			  msg = '';
			  myvar=eval(myvar);
			  var valid = "0123456789-";
			  field2='';
			  
			  if(myvar.demographics_edit_form.epostal_code.value!='')
			  {
					validateZIP(myvar.demographics_edit_form.epostal_code); //zip validate
					
			  }		  		
			  if(myvar.demographics_edit_form.family_size.value!='')
			  {
				   field2=myvar.demographics_edit_form.family_size.value;
				   
				   for (var i=0; i < field2.length; i++) {
							temp = "" + field2.substring(i, i+1);
							if (temp == "-") hyphencount++;
							if (valid.indexOf(temp) == "-1") {
								msg = msg + '   Invalid family size \n';
							//alert("Invalid characters in your zip code. Please try again.");
								myvar.demographics_edit_form.family_size.style.backgroundColor=changecolor;
							//return false;
								break;
							}				
					 }		
				}	
				//if(myvar.demographics_edit_form.financial_review.value!='')
				// {
				//alert(myvar.demographics_edit_form.financial_review.value);
				//validate_dt(myvar.demographics_edit_form.financial_review);
				//  }
				
			  if(msg == '')
			  {
				myvar.demographics_edit_form.submit();
			  }
			  else
			  {
				 alert (msg);
			  }		  
	}

function validate_misc()
{
	myvar = eval(myvar);
	myvar.demographics_edit_form.submit();
}
///Code to Submit Case Tab//
function validate_inscase(){
	myvar = eval(myvar);
	msg = '';
		
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/; 
	cDate=Date.parse(myvar.insurance_new.from_date_forcase.value); 
	dDate=Date.parse(myvar.insurance_new.in1subscriber_DOB.value); 
	if (myvar.insurance_new.in1provider.value == ''){
		msg  = msg + '   Primary Provider name is required\n';
		myvar.insurance_new.in1provider.style.backgroundColor=changecolor;
	}
	
	if (myvar.insurance_new.in1subscriber_fname.value == ''){
		msg  = msg + '   Primary subscriber name is required\n'; 
		myvar.insurance_new.in1subscriber_fname.style.backgroundColor=changecolor;
	}
	
	/*if (myvar.insurance_new.in1subscriber_relationship.value == ''){
		msg  = msg + '   Primary relationship name is required\n'; 
		myvar.insurance_new.in1subscriber_relationship.style.backgroundColor=changecolor;
	}*/
	
	
	/*if (myvar.insurance_new.in1plan_name.value == ''){
		msg  = msg + '   Primary subscriber plan name is required\n'; 
		myvar.insurance_new.in1plan_name.style.backgroundColor=changecolor;
	}*/
	
	if (myvar.insurance_new.in1policy_number.value == ''){
		msg  = msg + '   Primary subscriber policy no is required\n'; 
		myvar.insurance_new.in1policy_number.style.backgroundColor=changecolor;
	}
	
	/*if (myvar.insurance_new.in1group_number.value == ''){
		msg  = msg + '   Primary subscriber group no is required\n';
		myvar.insurance_new.in1group_number.style.backgroundColor=changecolor; 
	}*/
	
	if(myvar.insurance_new.in1subscriber_DOB.value != '00-00-0000')
	{ 
 		if(dDate >= cDate) 
		{
			msg = msg + '  - Date of Birth Should Be Past Date.\n';
			myvar.insurance_new.in1subscriber_DOB.style.backgroundColor=changecolor; 
		} 
	} 
	
	if(msg == '')
	{
		myvar.insurance_new.submit();
	}else{
		alert(msg);
	}
}

//end Code to submit Case Tab
function validate_resp1()
{				
	msg = '';
	
	myvar = eval(myvar);
	myvar.document.getElementById("table4").style.display="block";
	cDate = Date.parse(myvar.demographics_edit_form.from_date_byram1.value); 
	dDate = Date.parse(myvar.demographics_edit_form.dob1.value);	
	
	if (myvar.demographics_edit_form.fname1.value == '')
	{
		msg  = msg + '   First name is required\n'; 
		myvar.demographics_edit_form.fname1.style.backgroundColor=changecolor;
	}
	
	if(myvar.demographics_edit_form.lname1.value == '')
	{  
		msg = msg + '   Last name is required\n';  
		myvar.demographics_edit_form.lname1.style.backgroundColor=changecolor;	
	}
	//relation1
	if(myvar.demographics_edit_form.relation1.value == '')
	{  
		msg = msg + '   Relation with patient is required\n';  
		myvar.demographics_edit_form.relation1.style.backgroundColor=changecolor;
	}	
	/*if(myvar.frm_resp1.dob1.value == '' || myvar.frm_resp1.dob1.value == '00-00-0000')
	{ 
		msg = msg + '   Date of Birth is required\n'; 
		myvar.frm_resp1.dob1.style.backgroundColor=changecolor;
	} 
	else */
	if(dDate >= cDate) 
	{
		msg = msg + '  - Date of Birth Should Be Past Date.\n';
		myvar.demographics_edit_form.dob1.style.backgroundColor=changecolor; 
	} 
	/*else{ 
	validate_dt_dob(myvar.frm_resp1.dob1);	
	}*/
	
	if(myvar.demographics_edit_form.sex1.selectedIndex <= 0)
	{ 
		msg = msg + '   Please select you sex\n'; 
		myvar.demographics_edit_form.sex1.style.backgroundColor=changecolor; 
	}	
	/* if(myvar.frm_resp1.ss1.value == '')
	{ 	
	msg = msg + '   Social Security Number (SSN) is required\n'; 
	myvar.frm_resp1.ss1.style.backgroundColor=changecolor;
	}*/   	
	if(myvar.demographics_edit_form.ss1.value != "") {	
		ssNum = myvar.demographics_edit_form.ss1.value;		
		if(ssNum.length<9)
		{	
			msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
			myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
		
		}else{
			if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
			{
				msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
				myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor;
			}else{		
				for(i=0;i<ssNum.length;i++) {
					if (ssNum.charAt(i) == "-") {
						before = ssNum.substring(0,i);
						after = ssNum.substring(i+1);
						ssNum = before + after;
					}
				}							
				for(i=0;i<ssNum.length;i++) {
					// Make sure the 9 characters are indeed numbers
					if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
						// If not a number, stop the form post
					   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
					   myvar.demographics_edit_form.ss1.style.backgroundColor=changecolor; 
					   break;
					}
				}						
			}
		}				
	}
	
	if(myvar.demographics_edit_form.street1.value == '')
	{  
		msg = msg + '   Address is required\n';
		myvar.demographics_edit_form.street1.style.backgroundColor=changecolor;
	}
	
	if(myvar.demographics_edit_form.city1.value == '')
	{  
		msg = msg + '   City is required\n'; 
		myvar.demographics_edit_form.city1.style.backgroundColor=changecolor;
	}
	
	if(myvar.demographics_edit_form.state.value == '')
	{  
		msg = msg + '   State is required\n'; 
		myvar.demographics_edit_form.state.style.backgroundColor=changecolor;
	}
	if(myvar.demographics_edit_form.postal_code1.value=='')
	{		
		msg = msg + top.zipLabel + ' is required\n';
		myvar.demographics_edit_form.postal_code1.style.backgroundColor=changecolor;
	}
	else{
		validateZIP(myvar.demographics_edit_form.postal_code1); //zip validate
	}
	
	if(myvar.demographics_edit_form.country_code1.value == '')
	{
		msg = msg + '   Country is required\n'; 
		myvar.demographics_edit_form.country_code1.style.backgroundColor=changecolor;
	}
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/;
	/*if(myvar.frm_resp1.phone_home1.value=="") //check US phone
	{
	msg = msg + '  Home Phone is requierd\n';
	myvar.frm_resp1.phone_home1.style.backgroundColor=changecolor;
	}*/
	if(myvar.demographics_edit_form.phone_home1.value=="" && myvar.demographics_edit_form.phone_biz1.value=="" && myvar.demographics_edit_form.phone_cell1.value==""&& myvar.demographics_edit_form.phone_contact1.value=="") //check US phone
	{
		msg = msg + '  One Phone Number is requierd\n';
		//myvar.demographics_edit_form.phone_home.style.backgroundColor=changecolor;
	}	
}


function validate_resp2()
{
	 msg = '';
	 myvar=eval(myvar);
	 cDate=Date.parse(myvar.demographics_edit_form.from_date_byram2.value); 
	 dDate=Date.parse(myvar.demographics_edit_form.dob2.value); 
	 if (myvar.demographics_edit_form.fname2.value == '')
	 {
	   msg  = msg + '   First name is required\n'; 
	   myvar.demographics_edit_form.fname2.style.backgroundColor=changecolor;
	 }		
	 if(myvar.demographics_edit_form.lname2.value == '')
	 { 
		msg = msg + '   Last name is required\n';  
		myvar.demographics_edit_form.lname2.style.backgroundColor=changecolor;
	 
	 }
	 if(myvar.demographics_edit_form.relation2.value == '')
	 {  msg = msg + '   Relation with patient is required\n';  
		myvar.demographics_edit_form.relation2.style.backgroundColor=changecolor;
	 
	 }
	 if(myvar.demographics_edit_form.dob2.value == '' || myvar.demographics_edit_form.dob2.value == '00-00-0000')
	 { 
		msg = msg + '   Date of Birth is required\n'; 
		myvar.demographics_edit_form.dob2.style.backgroundColor=changecolor;
	 } 
	 else if(dDate >= cDate) 
		{
		msg = msg + '  - Date of Birth Should Be Past Date.\n';
		myvar.demographics_edit_form.dob2.style.backgroundColor=changecolor; 
		
		} 
	 /*else{ 
		validate_dt_dob(myvar.frm_resp1.dob2);
		
	 }*/
	 if(myvar.demographics_edit_form.sex2.selectedIndex <= 0)
	 { 
		msg = msg + '   Please select you sex\n'; 
		myvar.demographics_edit_form.sex2.style.backgroundColor=changecolor; 
	 }
	 if(myvar.demographics_edit_form.ss2.value == '')
	 { 	
		msg = msg + '   Social Security Number (SSN) is required\n'; 
		myvar.demographics_edit_form.ss2.style.backgroundColor=changecolor;
	 }
	 
	if(myvar.demographics_edit_form.ss2.value != ""){
		ssNum = myvar.demographics_edit_form.ss2.value;		
		if(ssNum.length<9)
		{	
			msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
			myvar.demographics_edit_form.ss2.style.backgroundColor=changecolor;
		}
		else{			
			if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
			{
				msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
				myvar.demographics_edit_form.ss2.style.backgroundColor=changecolor;
			}else{				
				for(i=0;i<ssNum.length;i++) {
					if (ssNum.charAt(i) == "-") {
						before = ssNum.substring(0,i);
						after = ssNum.substring(i+1);
						ssNum = before + after;
					}
				}							
				for(i=0;i<ssNum.length;i++) {
					// Make sure the 9 characters are indeed numbers
					if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
						// If not a number, stop the form post
					   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
					   myvar.demographics_edit_form.ss2.style.backgroundColor=changecolor; 
					   break;
					}
				}						
			}
		}		
	}

	if(myvar.demographics_edit_form.street2.value == '')
	{ 
		msg = msg + '   Address is required\n';
		myvar.demographics_edit_form.street2.style.backgroundColor=changecolor;
	}
	if(myvar.demographics_edit_form.city2.value == '')
	{  
		msg = msg + '   City is required\n'; 
		myvar.demographics_edit_form.city2.style.backgroundColor=changecolor;
	}
	if(myvar.demographics_edit_form.state2.value == '')
	{ 
		msg = msg + '   State is required\n'; 
		myvar.demographics_edit_form.state2.style.backgroundColor=changecolor;
	}
	
	validateZIP(myvar.demographics_edit_form.postal_code2); //zip validate
	
	if(myvar.demographics_edit_form.country_code2.value == '')
	{  
		msg = msg + '   Country is required\n'; 
		myvar.demographics_edit_form.country_code2.style.backgroundColor=changecolor;
	}

	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ;
	/*if(myvar.frm_resp1.phone_home2.value=="") //check US phone
	{
		msg = msg + '  Home Phone is requierd\n';
		myvar.frm_resp1.phone_home2.style.backgroundColor=changecolor;
	}*/
	if(myvar.demographics_edit_form.phone_home2.value=="" && myvar.demographics_edit_form.phone_biz2.value=="" && myvar.demographics_edit_form.phone_cell2.value==""&& myvar.demographics_edit_form.phone_contact2.value=="") //check US phone
	{
		msg = msg + '  One Phone Number is requierd\n';
		//myvar.demographics_edit_form.phone_home.style.backgroundColor=changecolor;
	}
	
	if(msg == '')
	{
		myvar.demographics_edit_form.submit();
	}
	else
	{
		alert (msg);
	}
}

function validate_resp3()
{
	myvar=eval(myvar);
	cDate=Date.parse(myvar.frm_resp1.from_date_byram3.value); 
	dDate=Date.parse(myvar.frm_resp1.dob3.value); 
	msg = '';
	if (myvar.frm_resp1.fname3.value == '')
	{
		msg  = msg + '   First name is required\n'; 
		myvar.frm_resp1.fname3.style.backgroundColor=changecolor;
	}
	
	if(myvar.frm_resp1.lname3.value == '')
	{ 
		msg = msg + '   Last name is required\n';  
		myvar.frm_resp1.lname3.style.backgroundColor=changecolor;
	}
	//relation1
	if(myvar.frm_resp1.relation3.value == '')
	{  
		msg = msg + '   Relation with patient is required\n';  
		myvar.frm_resp1.relation3.style.backgroundColor=changecolor;
	}
	
	if(myvar.frm_resp1.dob3.value == '' || myvar.frm_resp1.dob3.value == '00-00-0000')
	{ 
		msg = msg + '   Date of Birth is required\n'; 
		myvar.frm_resp1.dob3.style.backgroundColor=changecolor;
	} 
	else if(dDate >= cDate) 
	{
		msg = msg + '  - Date of Birth Should Be Past Date.\n';
		myvar.frm_resp1.dob3.style.backgroundColor=changecolor; 
	} 
  
	if(myvar.frm_resp1.sex3.selectedIndex <= 0)
	{ 
		msg = msg + '   Please select you sex\n'; 
		myvar.frm_resp1.sex3.style.backgroundColor=changecolor; 
	}
	
	if(myvar.frm_resp1.ss3.value == '')
	{ 	
		msg = msg + '   Social Security Number (SSN) is required\n'; 
		myvar.frm_resp1.ss3.style.backgroundColor=changecolor;
	} 
	   	
	if(myvar.frm_resp1.ss3.value != ""){
		ssNum=myvar.frm_resp1.ss3.value;	
		if(ssNum.length<9)
		{	
			msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX \n';
			myvar.frm_resp1.ss3.style.backgroundColor=changecolor;
		}else{								
			if((ssNum.charAt(3) != "-" ) || (ssNum.charAt(6) != "-" ))
			{
				msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
				myvar.frm_resp1.ss3.style.backgroundColor=changecolor;
			}else{				
				for (i=0;i<ssNum.length;i++) {
					if (ssNum.charAt(i) == "-") {
						before = ssNum.substring(0,i);
						after = ssNum.substring(i+1);
						ssNum = before + after;
					}
				}
								
				for (i=0;i<ssNum.length;i++) {
					// Make sure the 9 characters are indeed numbers
					if ((parseInt(ssNum.charAt(i)) + "") == (Number.NaN + "")) {
						// If not a number, stop the form post
					   msg = msg + '   Invalid Social Security Number (SSN). eg. XXX-XX-XXXX\n';
					   myvar.frm_resp1.ss3.style.backgroundColor=changecolor; 
					   break;
					}
				}						
			}
		}		
	}
 
	if(myvar.frm_resp1.street3.value == '')
	{  
		msg = msg + '   Address is required\n';
		myvar.frm_resp1.street3.style.backgroundColor=changecolor;
	}
	
	if(myvar.frm_resp1.city3.value == '')
	{  
		msg = msg + '   City is required\n'; 
		myvar.frm_resp1.city3.style.backgroundColor=changecolor;
	}
	
	if(myvar.frm_resp1.state3.value == '')
	{  
		msg = msg + '   State is required\n'; 
		myvar.frm_resp1.state3.style.backgroundColor=changecolor;
	}
	
	validateZIP(myvar.frm_resp1.postal_code3); //zip validate
	
	if(myvar.frm_resp1.country_code3.value == '')
	{  
		msg = msg + '   Country is required\n'; 
		myvar.frm_resp1.country_code3.style.backgroundColor=changecolor;
	} 
	
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ;
	
	/*if(myvar.frm_resp1.phone_home3.value=="") //check US phone
	{
		msg = msg + '  Home Phone is requierd\n';
		myvar.frm_resp1.phone_home3.style.backgroundColor=changecolor;
	}*/
	if(myvar.frm_resp1.phone_home3.value=="" && myvar.frm_resp1.phone_biz3.value=="" && myvar.frm_resp1.phone_cell3.value==""&& myvar.frm_resp1.phone_contact3.value=="") //check US phone
	{
		msg = msg + '  One Phone Number is requierd\n';
		//myvar.demographics_edit_form.phone_home.style.backgroundColor=changecolor;
	}
	
	if(msg == '')
	{
		myvar.frm_resp1.submit();
	}
	else
	{	
		alert (msg);
	}
}

var blPreviousInsChk = false;
function validateInsurance(tab_save){
	
//----------------------------------------------------------------------------------------
	var myvar = top.fmain.all_data;
	myvar = eval(myvar);
	msg = '';
	msgs = '';
	var changecolor='#F6C67A';
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ; 
	//alert(myvar.insuranceCaseFrm.from_date_subscriber1.value);
	cDate=Date.parse(myvar.insuranceCaseFrm.from_date_subscriber1.value); 
	dDate=Date.parse(myvar.insuranceCaseFrm.i1subscriber_DOB.value); 
	
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(myvar.insuranceCaseFrm.i1effective_date.value,top.date_format);
		actDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(myvar.insuranceCaseFrm.i1expiration_date.value,top.date_format);
		expDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate=Date.parse(myvar.insuranceCaseFrm.i1effective_date.value); 
		expDate=Date.parse(myvar.insuranceCaseFrm.i1expiration_date.value); 
	}
	expPriDate=Date.parse(myvar.insuranceCaseFrm.priExpirationDate.value);
	actPriDate=Date.parse(myvar.insuranceCaseFrm.actPreviousPri.value);
	var primaryInsNames = myvar.insuranceCaseFrm.primaryInsName.value;
	var primaryInsNames2 = myvar.insuranceCaseFrm.insprovider.value;
	//actRefTerDate = Date.parse(myvar.insuranceCaseFrm.eff1_date.value);
	//endRefTerDate = Date.parse(myvar.insuranceCaseFrm.end1_date.value);
	
	var patientName = myvar.insuranceCaseFrm.patientName.value;
	var expPreviousPriDate = myvar.insuranceCaseFrm.i1effective_date.value;
	
	var newmsg="";
	if(tab_save!="Insurance"){//after prac. mand	
		if (myvar.insuranceCaseFrm.i1provider.value == ''){
			msg  = msg + '   Primary Provider Name is required \n';
			myvar.insuranceCaseFrm.insprovider.select();
			myvar.insuranceCaseFrm.insprovider.style.backgroundColor=changecolor;
		}
		if(myvar.insuranceCaseFrm.i1policy_number.value == ''){
			msg  = msg + '   Primary subscriber policy no is required\n'; 
			myvar.insuranceCaseFrm.i1policy_number.style.backgroundColor=changecolor;
		}	
		
		if(myvar.insuranceCaseFrm.i1effective_date.value == ''){
			msg  = msg + '   Primary Activation Date is required\n'; 
			myvar.insuranceCaseFrm.i1effective_date.style.backgroundColor=changecolor;
		}
		
		if(myvar.insuranceCaseFrm.primaryId.value != myvar.insuranceCaseFrm.primaryMainId.value){
			if(myvar.insuranceCaseFrm.actPreviousPri.value != ''){
				if(actDate <= actPriDate){
					msg  = msg + '   Primary Activation Date Must Be Greater Than Previous Activation Date.\n';
				}
			}
			
			if(myvar.insuranceCaseFrm.i1expiration_date.value != ''){
				if(actDate >= expDate){
					msg  = msg + '   Primary Expiration Date Must Be Greater Than Activation Date.\n'; 
				}
				else if(primaryInsNames2 != ''){
					msgs += 'Primary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames2+' for '+patientName+' ?<br>';
				}
			}	
		
			if(myvar.insuranceCaseFrm.primaryId.value != ''){
				if(myvar.insuranceCaseFrm.priExpirationDate.value != ''){			
					if(actDate <= expPriDate){
						msg  = msg + '   Primary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
					}
				}
				else{
					myvar.getElementById("expPreviousPri").value = expPreviousPriDate;
					msgs += 'Primary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames+' for '+patientName+' ?<br>';
				}
			}
		}
		else{
			if(myvar.insuranceCaseFrm.i1expiration_date.value != ''){			
				if(actDate >= expDate){
					msg  = msg + '   Primary Expiration Date Must Be Greater Than Previous Activate Date.\n';		
				}
				else{
					myvar.getElementById("expPreviousPri").value = expPreviousPriDate;
					msgs += 'Primary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+primaryInsNames+' for '+patientName+' ?<br>';
				}
			}			
		}
		
		if(myvar.insuranceCaseFrm.i1subscriber_relationship.value == ''){
			msg  = msg + '   Primary subscriber Relations is required\n'; 
			myvar.insuranceCaseFrm.i1subscriber_relationship.style.backgroundColor=changecolor;
		}
		if(myvar.insuranceCaseFrm.i1subscriber_fname.value == ''){
			msg  = msg + '   Primary subscriber First Name is required\n'; 
			myvar.insuranceCaseFrm.i1subscriber_fname.style.backgroundColor=changecolor;
		}
		if (myvar.insuranceCaseFrm.lastName.value == ''){
			msg  = msg + '   Primary subscriber Last Name is required\n'; 
			myvar.insuranceCaseFrm.lastName.style.backgroundColor=changecolor;
		}
		
		
		if(myvar.insuranceCaseFrm.i1subscriber_DOB.value != '00-00-0000')
		{ 
			 if(dDate >= cDate){
				msg = msg + '  - Date of Birth Should Be Past Date.\n';
				myvar.insuranceCaseFrm.i1subscriber_DOB.style.backgroundColor=changecolor;			
			} 
		}
		/*
		if(myvar.insuranceCaseFrm.i1referalreq){
			if (myvar.insuranceCaseFrm.i1referalreq.value == 'Yes' && document.insuranceCaseFrm.ref1_phy.value==""){
				msg  = msg + '   Primary Referral Physician Name is required\n'; 
				myvar.insuranceCaseFrm.ref1_phy.style.backgroundColor=changecolor;
			}
			if (myvar.insuranceCaseFrm.i1referalreq.value == 'Yes' && document.insuranceCaseFrm.reffral_no1.value==""){
				msg  = msg + '   Primary Referral#  no is required\n'; 
				myvar.insuranceCaseFrm.reffral_no1.style.backgroundColor=changecolor;
			}
			if (myvar.insuranceCaseFrm.i1referalreq.value == 'Yes' && document.insuranceCaseFrm.eff1_date.value!=""){
				if(myvar.insuranceCaseFrm.end1_date.value == ''){
					msg  = msg + '   Primary Referral  End Effected is required\n'; 
					myvar.insuranceCaseFrm.end1_date.style.backgroundColor=changecolor;
				}
				else if(actRefTerDate >= endRefTerDate){
					msg  = msg + '   Primary Referral Start Date Must Be grater Than End Effected.\n'; 				
				}
			}
		}
		*/
		if (myvar.insuranceCaseFrm.secInsCompVal.value != 'Unassigned'){
			msg = validateInsurance1();
		}
		
		if (myvar.insuranceCaseFrm.terInsCompVal.value != 'Unassigned'){
			msg = validateInsurance2();		
		}
	}
	else{//after prac. mand.	
		msg = validateInsuranceNew();		
	}
	
	if(msg == true){
		myvar.insuranceCaseFrm.chooseNewform.value = 'Save';				
		if(msgs){
			/*
			var title = "imwemr";
			var btn1 = "Yes";
			var btn2 = "No";			
			var func1="hideConfirmYesNo";		
			displayMessg123(title,msgs,btn1,btn2,func1,1,1);	
			*/
			top.fancyConfirm(msgs,"","window.top.fmain.all_data.hideConfirmYesNo(1)", "window.top.fmain.all_data.hideConfirmYesNo(0)");
		}
		else{
			top.fmain.document.getElementById('load_image21').style.display='block';
			
			myvar.insuranceCaseFrm.submit();
		}
	}
	else{	
		if (blPreviousInsChk == true){
			myvar.insuranceCaseFrm.reset();				
		}
		/*if (msg.search("Please expire previous insurance before inserting new one")==-1){				
		}
		else{			
			myvar.insuranceCaseFrm.reset();			
		}*/
	}
}
/////////////////function validateInsuranceNew() code start after Prac mand.
function validateInsuranceNew(){
	var myWindow = window.top.fmain.all_data;
	//myWindow = eval(myWindow);
	
	msgs = '';
	var changecolor='#F6C67A';
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/  ;
	//alert(myvar.insuranceCaseFrm.i1provider.value);
	cDate=Date.parse(myWindow.insuranceCaseFrm.from_date_subscriber1.value); 
	dDate=Date.parse(myWindow.insuranceCaseFrm.i1subscriber_DOB.value); 
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(myWindow.insuranceCaseFrm.i1effective_date.value,top.date_format);
		actDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(myWindow.insuranceCaseFrm.i1expiration_date.value,top.date_format);
		expDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate=Date.parse(myWindow.insuranceCaseFrm.i1effective_date.value); 
		expDate=Date.parse(myWindow.insuranceCaseFrm.i1expiration_date.value); 
	}
	//actDate=Date.parse(myWindow.insuranceCaseFrm.i1effective_date.value); 
	//expDate=Date.parse(myWindow.insuranceCaseFrm.i1expiration_date.value); 
	expPriDate=Date.parse(myWindow.insuranceCaseFrm.priExpirationDate.value);
	actPriDate=Date.parse(myWindow.insuranceCaseFrm.actPreviousPri.value);
	var primaryInsNames = myWindow.insuranceCaseFrm.primaryInsName.value;
	var primaryInsNames2 = myWindow.insuranceCaseFrm.insprovider.value;
//	actRefTerDate = Date.parse(myWindow.insuranceCaseFrm.eff1_date.value);
//	endRefTerDate = Date.parse(myWindow.insuranceCaseFrm.end1_date.value);
//	var endDateRefPri = Date.parse(myWindow.insuranceCaseFrm.end1_date.value);

	
	var patientName = myWindow.insuranceCaseFrm.patientName.value;
	var expPreviousPriDate = myWindow.insuranceCaseFrm.i1effective_date.value;
	///////
	if(myWindow.insuranceCaseFrm.primaryInsName.value)
		primaryInsNames = myWindow.insuranceCaseFrm.primaryInsName.value;
	else
		primaryInsNames = myWindow.insuranceCaseFrm.insprovider.value;
	//////
	var newmsg="";
	var msg = "";
	var blChkforChagePriIns = false;
	if(myWindow.insuranceCaseFrm.primaryId.value != "" &&  myWindow.insuranceCaseFrm.primaryMainId.value != ""){
		if(myWindow.insuranceCaseFrm.primaryId.value != myWindow.insuranceCaseFrm.primaryMainId.value){
				blChkforChagePriIns = true;
		}
	}
	if(blChkforChagePriIns == true){		
		if(myWindow.insuranceCaseFrm.actPreviousPri.value != ''){
			if(actDate <= actPriDate){
				msg  += '   Primary Activation Date Must Be Greater Than Previous Activation Date.\n';
			}
		}
			
		if(myWindow.insuranceCaseFrm.i1expiration_date.value != ''){
			if(actDate > expDate){
				msg  +=  '   Primary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else if(primaryInsNames2 != ''){
				exitingExpirationDate=myWindow.document.getElementById("exitingExpirationDate").value;
				//alert(exitingExpirationDate);
				if(myWindow.insuranceCaseFrm.i1expiration_date.value!=exitingExpirationDate){
					msgs += 'Primary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames2+' for '+patientName+' ?<br>';
				}
			}
		}	
		//alert('c');
		if(myWindow.insuranceCaseFrm.primaryId.value != ''){
			//alert('d');
			if(myWindow.insuranceCaseFrm.priExpirationDate.value != ''){		
			//alert('e');
				
				if(actDate <= expPriDate){
					msg  +=  '   Primary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
				}
				//alert(document.getElementById("insurenceProviderExit").value);
				//alert(document.getElementById("insprovider").value);
				exitingExpirationDate=myWindow.document.getElementById("exitingExpirationDate").value; 
				//alert(exitingExpirationDate);
				insurenceProviderExit=myWindow.document.getElementById("insurenceProviderExit").value;
				newinsprovider=myWindow.document.getElementById("insprovider").value;
				if(insurenceProviderExit!=newinsprovider){
					if(myWindow.document.getElementById("exitingExpirationDate").value=="00-00-0000"){
						msg  += 'Please expire previous primary insurance.\n';
						blPreviousInsChk = true;
					}
				}
				else if(insurenceProviderExit==newinsprovider && document.insuranceCaseFrm.i1expiration_date.value==""){
					if(myWindow.document.getElementById("exitingExpirationDate").value=="00-00-0000"){
						//msg  = msg + 'Please expire previous insurance before inserting new one.2p\n';
					}
				}
			}
			else{
				
				myWindow.document.getElementById("expPreviousPri").value = expPreviousPriDate;
				//alert(document.getElementById("exitingExpirationDate").value);
				if(myWindow.document.getElementById("exitingExpirationDate").value=="00-00-0000"){
					msg  += 'Please expire previous primary insurance.\n';
					blPreviousInsChk = true;
				}
				exitingExpirationDate=myWindow.document.getElementById("exitingExpirationDate").value;
				if(myWindow.insuranceCaseFrm.i1expiration_date.value!=exitingExpirationDate){
					msgs += 'Primary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames+' for '+patientName+' ?<br>';
					//msgs += 'Please expire previous insurance '+primaryInsNames+' for '+patientName+' before inserting new one.<br>';
				}
			}
		}
	}
	else{
		
		if(myWindow.insuranceCaseFrm.i1expiration_date.value != ''){			
			if(actDate > expDate){
				msg  +=  '   Primary Expiration Date Must Be Greater Than Previous Activate Date.\n';		
			}
			else{				
			//alert(myWindow.document.getElementById("expPreviousPri").value);
				
				myWindow.document.getElementById("expPreviousPri").value = expPreviousPriDate;//ravi
				
				exitingExpirationDate=myWindow.document.getElementById("exitingExpirationDate").value; 
				
				if(myWindow.insuranceCaseFrm.i1expiration_date.value!=exitingExpirationDate){
					msgs += 'Primary Insurance Company : <br><br>';//ravi
					msgs += 'Are you sure wanted to set expiry of '+primaryInsNames+' for '+patientName+' ?<br>';//ravi
				}
			}
		}			
	}

	if(myWindow.insuranceCaseFrm.i1subscriber_DOB.value != '00-00-0000')
	{
		if(dDate >= cDate){
			msg +=  '  - Date of Birth Should Be Past Date.\n';
			myWindow.insuranceCaseFrm.i1subscriber_DOB.style.backgroundColor=changecolor;			
		} 
	}
	
	if($('#i1referalreq').val() == "Yes")
	{
		var pri_ref_ch_flag = 0;
		$('#primaryReffCont > table').each(function()
		{	
			if(pri_ref_ch_flag == 0)
			{
				reff_start_date = $('.reff_start_date_cl',$(this)).val();
				reff_end_date = $('.reff_end_date_cl',$(this)).val();
				/*
				if($.trim(reff_start_date) != "" && $.trim(reff_end_date) == "")
				{
					msg += '   Primary Referral End Effected is required\n';	
					pri_ref_ch_flag = 1;						
				}
				*/				
				//actRefTerDate = Date.parse(reff_start_date);
				//endRefTerDate = Date.parse(reff_end_date);
				if(top.date_format != "undefined"){
					arrActDate = fnArrDate(reff_start_date,top.date_format);
					actRefTerDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
					
					arrExpDate = fnArrDate(reff_end_date,top.date_format);
					endRefTerDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
					
				}else{
					actRefTerDate=Date.parse(reff_start_date); 
					endRefTerDate=Date.parse(reff_end_date); 
				}
				
				
				if(actRefTerDate >= endRefTerDate)
				{
					msg  += '   Primary Referral End Date Must Be greater Than Start Effected.\n'; 	
					pri_ref_ch_flag = 1;
				}
			}
		});
	}	
	
	
	/*
	if (myWindow.insuranceCaseFrm.i1referalreq && myWindow.insuranceCaseFrm.eff1_date){
		if (myWindow.insuranceCaseFrm.i1referalreq.value == 'Yes' && myWindow.insuranceCaseFrm.eff1_date.value!=""){
			if(actRefTerDate >= endRefTerDate){
				msg  += '   Primary Referral Start Date Must Be grater Than End Effected.\n'; 				
			}
		}
	}
	*/

	if (myWindow.insuranceCaseFrm.secInsCompVal.value != 'Unassigned'){
		msg += validateInsuranceNew1(msg);
	}
	if (myWindow.insuranceCaseFrm.terInsCompVal.value != 'Unassigned'){
		msg += validateInsuranceNew2(msg);		
	}
	if(msg){
		top.fmain.document.getElementById('load_image21').style.display='none';
		top.fAlert(msg);
		return false;
	}
	else{
		return true;
	}
	
}
///////new function

function validateInsuranceNew1(msg1){
	var myWindow = top.fmain.all_data;
	myWindow = eval(myWindow);
	var myvar = top.fmain.all_data;
	myvar = eval(myvar);	
	var changecolor='#F6C67A';
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/  ;	
	cDate=Date.parse(myWindow.insuranceCaseFrm.from_date_subscriber2.value); 
	dDate=Date.parse(myWindow.insuranceCaseFrm.i2subscriber_DOB.value); 
	
	//actDate2=Date.parse(myWindow.insuranceCaseFrm.i2effective_date.value); 
	//expDate2=Date.parse(myWindow.insuranceCaseFrm.i2expiration_date.value); 
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(myWindow.insuranceCaseFrm.i2effective_date.value,top.date_format);
		actDate2 = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(myWindow.insuranceCaseFrm.i2expiration_date.value,top.date_format);
		expDate2 = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate2=Date.parse(myWindow.insuranceCaseFrm.i2effective_date.value); 
		expDate2=Date.parse(myWindow.insuranceCaseFrm.i2expiration_date.value); 
	}
	
	secExpDate=Date.parse(myWindow.insuranceCaseFrm.secExpirationDate.value); 	
	actSecDate = Date.parse(myWindow.insuranceCaseFrm.actPreviousSec.value);
//	actRefTerDate = Date.parse(myWindow.insuranceCaseFrm.eff2_date.value);
//	endRefTerDate = Date.parse(myWindow.insuranceCaseFrm.end2_date.value);	
	var patientName = myWindow.insuranceCaseFrm.patientName.value;
	var secondaryInsNames = myWindow.insuranceCaseFrm.secondaryInsName.value;
	var secondaryInsNames2 = myWindow.insuranceCaseFrm.secInsCompVal.value;
	var expPreviousSecDate = myWindow.insuranceCaseFrm.i2effective_date.value;
// 	var endDateRefSec = Date.parse(myWindow.insuranceCaseFrm.end2_date.value);
	var blChkforChageSecIns = false;
	var msg = "";
	if(myWindow.insuranceCaseFrm.secondaryId.value != "" &&  myWindow.insuranceCaseFrm.secondaryMainId.value != ""){
		if(myWindow.insuranceCaseFrm.secondaryId.value != myWindow.insuranceCaseFrm.secondaryMainId.value){
				blChkforChageSecIns = true;
		}
	}
	if(blChkforChageSecIns == true){	
		//alert('1');
		if(myWindow.insuranceCaseFrm.actPreviousSec.value != ''){
			if(actDate2 <= actSecDate){
			msg  += '   Secondary Activation Date Must Be Greater Than Previous Activation Date.\n';
			}
		}
		if(myWindow.insuranceCaseFrm.i2expiration_date.value != ''){
			//alert('3');
			if(actDate2 > expDate2){
				msg  += '   Secondary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else{
				secExitingExpirationDate=myWindow.document.getElementById("secExitingExpirationDate").value;
				if(myWindow.insuranceCaseFrm.i2expiration_date.value!=secExitingExpirationDate){	
					myWindow.document.getElementById("expPreviousSec").value = expPreviousSecDate;
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+secondaryInsNames2+' for '+patientName+' ?<br>';
				}
			}
		}	
		
		if(myWindow.insuranceCaseFrm.secondaryId.value != ''){
			//alert('4');
			if(myWindow.insuranceCaseFrm.secExpirationDate.value != ''){			
			//alert('5');
				if(actDate2 <= secExpDate){
				msg  += '   Secondary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
				}
				
				insurenceProviderExit=myWindow.document.getElementById("insurenceProviderExitSec").value;
				newinsprovider=myWindow.document.getElementById("secInsCompVal").value;
				if(insurenceProviderExit!=newinsprovider){
					if(myWindow.document.getElementById("secExitingExpirationDate").value=="00-00-0000"){
						msg  += 'Please expire previous secondary insurance.\n';
						blPreviousInsChk = true;
					}
				}
				else if(insurenceProviderExit==newinsprovider && myWindow.insuranceCaseFrm.i2expiration_date.value==""){
					if(myWindow.document.getElementById("exitingExpirationDate").value=="00-00-0000"){
						//msg  = msg + 'Please expire previous insurance before inserting new one.\n';
					}
				}
			}
			else{
				//alert('6');
				myWindow.document.getElementById("expPreviousSec").value = expPreviousSecDate;
				
				if(myWindow.document.getElementById("secExitingExpirationDate").value=="00-00-0000"){
					msg  +=  'Please expire previous secondary insurance .\n';
					blPreviousInsChk = true;
				}
				secExitingExpirationDate=myWindow.document.getElementById("secExitingExpirationDate").value;
				if(myWindow.insuranceCaseFrm.i2expiration_date.value!=secExitingExpirationDate){	
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+secondaryInsNames+' for '+patientName+' ?<br>';
					//msgs += 'Please expire previous insurance '+primaryInsNames+' for '+patientName+' before inserting new one.<br>';
				}
				
			}
			
		}
	}
	else{
		//alert('2b');
		if(myWindow.insuranceCaseFrm.i2expiration_date.value != ''){
			
			if(actDate2 > expDate2){
				
				msg  += '   Secondary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else{
				
				secExitingExpirationDate=myWindow.document.getElementById("secExitingExpirationDate").value; 
				if(myWindow.document.getElementById("i2expiration_date").value!=secExitingExpirationDate){					
				
					myWindow.document.getElementById("expPreviousSec").value = expPreviousSecDate;
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+secondaryInsNames2+' for '+patientName+' ?<br>';
					
				}
			}
		}			
	}
	
	if($('#i2referalreq').val() == "Yes")
	{
		var pri_ref_ch_flag = 0;
		$('#SecondaryReffCont > table').each(function()
		{	
			if(pri_ref_ch_flag == 0)
			{
				reff_start_date = $('.reff_start_date_cl',$(this)).val();
				reff_end_date = $('.reff_end_date_cl',$(this)).val();
				/*
				if($.trim(reff_start_date) != "" && $.trim(reff_end_date) == "")
				{
					msg += '   Secondary Referral End Effected is required\n';	
					pri_ref_ch_flag = 1;						
				}
				*/				
				//actRefTerDate = Date.parse(reff_start_date);
				//endRefTerDate = Date.parse(reff_end_date);
				
				if(top.date_format != "undefined"){
					arrActDate = fnArrDate(reff_start_date,top.date_format);
					actRefTerDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
					
					arrExpDate = fnArrDate(reff_end_date,top.date_format);
					endRefTerDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
					
				}else{
					actRefTerDate=Date.parse(reff_start_date); 
					endRefTerDate=Date.parse(reff_end_date); 
				}
				
				if(actRefTerDate >= endRefTerDate)
				{
					msg  += '   Secondary Referral End Date Must Be greater Than Start Effected.\n'; 	
					pri_ref_ch_flag = 1;
				}
			}
		});
	}	

/*
	if (myWindow.insuranceCaseFrm.i2referalreq && myWindow.insuranceCaseFrm.eff2_date){
		if (myWindow.insuranceCaseFrm.i2referalreq.value == 'Yes' && myWindow.insuranceCaseFrm.eff2_date.value!=""){
			if(actRefTerDate >= endRefTerDate){
				msg  += '   Secondary Referral Start Date Must Be grater Than End Effected.\n'; 				
			}
		}
	}
	*/
	/*if (document.insuranceCaseFrm.i2referalreq){
		if (document.insuranceCaseFrm.i2referalreq.value == 'Yes' && document.insuranceCaseFrm.end2_date.value!=""){
			if(endDateRefSec <= cDate){
				msg  = msg + '   Secondary Referral End date should be in the future.\n'; 				
			}
		}
	}*/
	if(myWindow.insuranceCaseFrm.i2subscriber_DOB.value != '00-00-0000')
	{ 
		if(dDate >= cDate) 
		{	
			if(msg1.indexOf('Date of Birth Should Be Past Date')<0)
			msg += '  - Date of Birth Should Be Past Date.\n';
			myWindow.insuranceCaseFrm.i2subscriber_DOB.style.backgroundColor=changecolor; 
		} 
	}
	
	return msg;
	
	
}

////////new function

function validateInsuranceNew2(msg1){
	var myWindow = top.fmain.all_data;
	myWindow = eval(myWindow);
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ;

	cDate=Date.parse(myWindow.insuranceCaseFrm.from_date_subscriber3.value); 
	dDate=Date.parse(myWindow.insuranceCaseFrm.i3subscriber_DOB.value); 
	//actDate3=Date.parse(myWindow.insuranceCaseFrm.i3effective_date.value); 
	//expDate3=Date.parse(myWindow.insuranceCaseFrm.i3expiration_date.value); 
	
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(myWindow.insuranceCaseFrm.i3effective_date.value,top.date_format);
		actDate3 = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(myWindow.insuranceCaseFrm.i3expiration_date.value,top.date_format);
		expDate3 = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate3=Date.parse(myWindow.insuranceCaseFrm.i3effective_date.value); 
		expDate3=Date.parse(myWindow.insuranceCaseFrm.i3expiration_date.value); 
	}
	terExpDate=Date.parse(myWindow.insuranceCaseFrm.terExpirationDate.value);	
	actTerDate = Date.parse(myWindow.insuranceCaseFrm.actPreviousTer.value);
//	actRefTerDate = Date.parse(myWindow.insuranceCaseFrm.eff3_date.value);
//	endRefTerDate = Date.parse(myWindow.insuranceCaseFrm.end3_date.value);
	var patientName = myWindow.insuranceCaseFrm.patientName.value;
	var TertiaryInsNames = myWindow.insuranceCaseFrm.TertiaryInsName.value;
	var TertiaryInsNames2 = myWindow.insuranceCaseFrm.terInsCompVal.value;
	var expPreviousTerDate = myWindow.insuranceCaseFrm.i3effective_date.value;
//	var endDateRefTer = Date.parse(myWindow.insuranceCaseFrm.end3_date.value);
	//alert(myWindow.insuranceCaseFrm.TertiaryId.value)
	//alert(myWindow.insuranceCaseFrm.TertiaryMainId.value)
	var blChkforChageTerIns = false;
	var msg = "";
	if(myWindow.insuranceCaseFrm.TertiaryId.value != "" &&  myWindow.insuranceCaseFrm.TertiaryMainId.value != ""){
		if(myWindow.insuranceCaseFrm.TertiaryId.value != myWindow.insuranceCaseFrm.TertiaryMainId.value){
				blChkforChageTerIns = true;
		}
	}
	if(blChkforChageTerIns == true){
		//alert('1');
		if(myWindow.insuranceCaseFrm.actPreviousSec.value != ''){
			if(actDate3 <= actTerDate){
				msg  += '   Tertiary Activation Date Must Be Greater Than Previous Activation Date.\n';
			}
		}
		if(myWindow.insuranceCaseFrm.i3expiration_date.value != ''){
			if(actDate3 > expDate3){
				msg  +=  '   Tertiary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else{
				TerExitingExpirationDate=myWindow.document.getElementById("TerExitingExpirationDate").value;
				if(myWindow.document.getElementById("i3expiration_date").value!=TerExitingExpirationDate){	
					myWindow.document.getElementById("expPreviousTer").value = expPreviousTerDate;
					msgs += 'Tertiary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+TertiaryInsNames2+' for '+patientName+' ?<br>';
				}
			}
		}
	//alert('2');

		if(myWindow.insuranceCaseFrm.TertiaryId.value != ''){
			//alert('3');
			if(myWindow.insuranceCaseFrm.terExpirationDate.value != ''){			
			//alert('4');
				if(actDate3 <= terExpDate){
					msg  += '   Tertiary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
				}
				insurenceProviderExit=myWindow.document.getElementById("insurenceProviderExitTer").value;
				newinsprovider=myWindow.document.getElementById("terInsCompVal").value;
				if(insurenceProviderExit!=newinsprovider){
					if(myWindow.document.getElementById("TerExitingExpirationDate").value=="00-00-0000"){
						msg  += 'Please expire previous tertiary insurance.\n';
						blPreviousInsChk = true;
					}
				}
			}
			else{
				//alert('6');
				myWindow.document.getElementById("expPreviousTer").value = expPreviousTerDate;
				
				if(myWindow.document.getElementById("TerExitingExpirationDate").value=="00-00-0000"){
					msg +=  'Please expire previous insurance.\n';
					blPreviousInsChk = true;
				}
				TerExitingExpirationDate=myWindow.document.getElementById("TerExitingExpirationDate").value; 
				if(myWindow.document.getElementById("i3expiration_date").value!=TerExitingExpirationDate){
					msgs += 'Tertiary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+TertiaryInsNames+' for '+patientName+' ?<br>';
				}
			}
			
		}
		
	}
	else{
		if(myWindow.insuranceCaseFrm.i3expiration_date.value != ''){
			if(actDate3 > expDate3){
				msg  += '   Tertiary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else{
				TerExitingExpirationDate=myWindow.document.getElementById("TerExitingExpirationDate").value; 
				if(myWindow.document.getElementById("i3expiration_date").value!=TerExitingExpirationDate){			
					myWindow.document.getElementById("expPreviousTer").value = expPreviousTerDate;
					msgs += 'Tertiary Insurance Company : <br><br>';
					msgs += 'Are you sure wanted to set expiry of '+TertiaryInsNames2+' for '+patientName+' ?<br>';
				}
			}
		}			
	}
	
	if($('#i3referalreq').val() == "Yes")
	{
		var pri_ref_ch_flag = 0;
		$('#TertiaryReffCont > table').each(function()
		{	
			if(pri_ref_ch_flag == 0)
			{
				reff_start_date = $('.reff_start_date_cl',$(this)).val();
				reff_end_date = $('.reff_end_date_cl',$(this)).val();
				/*
				if($.trim(reff_start_date) != "" && $.trim(reff_end_date) == "")
				{
					msg += '   Tertiary Referral End Effected is required\n';	
					pri_ref_ch_flag = 1;						
				}
				*/				
				//actRefTerDate = Date.parse(reff_start_date);
				//endRefTerDate = Date.parse(reff_end_date);
				if(top.date_format != "undefined"){
					arrActDate = fnArrDate(reff_start_date,top.date_format);
					actRefTerDate = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
					
					arrExpDate = fnArrDate(reff_end_date,top.date_format);
					endRefTerDate = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
					
				}else{
					actRefTerDate=Date.parse(reff_start_date); 
					endRefTerDate=Date.parse(reff_end_date); 
				}
				
				if(actRefTerDate >= endRefTerDate)
				{
					msg  += '   Tertiary Referral End Date Must Be greater Than Start Effected.\n'; 	
					pri_ref_ch_flag = 1;
				}
			}
		});
	}	
	/*
	if (myWindow.insuranceCaseFrm.i3referalreq && myWindow.insuranceCaseFrm.eff3_date){	
		if (myWindow.insuranceCaseFrm.i3referalreq.value == 'Yes' && myWindow.insuranceCaseFrm.eff3_date.value!=""){
			if(actRefTerDate >= endRefTerDate){
				msg  += '   Tertiary Referral Start Date Must Be grater Than End Effected.\n'; 				
			}
		}
	}
	*/
	/*if (myWindow.insuranceCaseFrm.i3referalreq){
		if (myWindow.insuranceCaseFrm.i3referalreq.value == 'Yes' && myWindow.insuranceCaseFrm.end3_date.value!=""){
			if(endDateRefTer <= cDate){
				msg  = msg + '   Tertiary Referral End date should be in the future.\n'; 				
			}
		}
	}*/
	
	if(myWindow.insuranceCaseFrm.i3subscriber_DOB.value != '00-00-0000')
	{ 
		if(dDate >= cDate) 
		{	
			if(msg1.indexOf('Date of Birth Should Be Past Date')<0)
			msg += '  - Date of Birth Should Be Past Date.\n';
			var changecolor='#F6C67A';
			myWindow.insuranceCaseFrm.i3subscriber_DOB.style.backgroundColor=changecolor; 
		
		} 
	}
	
	return msg;
	
	
}

/////////////function validateInsuranceNew() code end Prac mand.
function hideConfirmYesNo(obj)
{	
	var myWindow = top.fmain.all_data;
	if(obj == 1){		
		myWindow.insuranceCaseFrm.submit();
		
	}
	else{
		top.fmain.document.getElementById('load_image21').style.display='none';
	}
	if(typeof(myWindow.document.getElementById('div_slotys2'))!="undefined") {
		myWindow.document.getElementById('div_slotys2').style.visibility = 'hidden';
		myWindow.document.getElementById('div_slotys2').innerHTML='';
	}
}
function displayMessg123(title,msg,btn1,btn2,func1,func2,showCancel,showImage,misc)
{
  var myWindow = top.fmain.all_data;
  text = '<div id="divCon22" style="position:relative; z-index:1000;">';
  //text += '<iframe name="ieHeack" src="../main/ss.html" frameborder="0" width="100%" height="100%" style="display:block;position:absolute;top:0px;left:0px;z-index:1000;"></iframe>';		  
		 
  text += '<table align="center" width="300px" border=0 cellpadding=2 cellspacing=0 class="confirmTable3" style="position:absolute;top:0px;left:0px;z-index:1000;">';
  text += '<tr><td height="25" class="text_10b" background="../../../images/border_top_center.jpg" colspan="2" >';		  		  
  text += title;
  text += '</td></tr>';
  text += '<tr><Td class="confirmBackground" align="left">';
  if((typeof showImage == "undefined") || (showImage != 0))
  {
	text += '<img src="../../../images/stop.gif" alt="stop">';
  }
  text += '</td>';
  text += '<Td class="text_msg">';
  text += '</Td>';
  text += '</tr>';
  
  text += '<tr><td class="text_msg" nowrap="nowrap" colspan="2">'+msg+'</td>';
  text += '</tr>';
  text += '<tr  height=25><td colspan="2"  class="confirmBackground">';
  text += '<table align="center">';
  text += '<tr><td class="confirmBackground" height=25>';
  
  text += '<input type="button"  value="'+btn1+'" onClick="window.'+func1+'(1)" class="confirmButton3">';
  text += '</td>';
  text += '<td class="confirmBackground"  height=25 align="left">';
  text += '<input type="button" value="'+btn2+'" onClick="window.'+func1+'(0)" class="confirmButton3">';		  		  
  text += '</td></tr></table>';	
  text += '</td></tr>';
  text+='</table>';			 
  text += '</div>';	  
  if (document.getElementById) 
  {
	// myvar=eval(myvar);	
	// mDiv = document.getElementById('div_slotys2');	
	//myWindow.document.write(text);	
	myWindow.document.getElementById('div_slotys2').innerHTML = text;	
	myWindow.document.getElementById('div_slotys2').style.visibility = 'visible';
  }	
}
function validateInsurance1(){
	//-----------------------------------------------
	myvar = top.fmain.fron.all_data;
	//msg = '';
	var changecolor='#F6C67A';
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ;
	//alert(myvar.insuranceCaseFrm.i1provider.value);
		
	cDate=Date.parse(document.insuranceCaseFrm.from_date_subscriber2.value); 
	dDate=Date.parse(document.insuranceCaseFrm.i2subscriber_DOB.value); 
	
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(document.insuranceCaseFrm.i2effective_date.value,top.date_format);
		actDate2 = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(document.insuranceCaseFrm.i2expiration_date.value,top.date_format);
		expDate2 = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate2=Date.parse(document.insuranceCaseFrm.i2effective_date.value); 
		expDate2=Date.parse(document.insuranceCaseFrm.i2expiration_date.value); 
	}
	//actDate2=Date.parse(document.insuranceCaseFrm.i2effective_date.value); 
	//expDate2=Date.parse(document.insuranceCaseFrm.i2expiration_date.value); 
	secExpDate=Date.parse(document.insuranceCaseFrm.secExpirationDate.value); 	
	actSecDate = Date.parse(document.insuranceCaseFrm.actPreviousSec.value);
//	actRefTerDate = Date.parse(document.insuranceCaseFrm.eff2_date.value);
//	endRefTerDate = Date.parse(document.insuranceCaseFrm.end2_date.value);	
	var patientName = document.insuranceCaseFrm.patientName.value;
	var secondaryInsNames = document.insuranceCaseFrm.secondaryInsName.value;
	var secondaryInsNames2 = document.insuranceCaseFrm.secInsCompVal.value;
	var expPreviousSecDate = document.insuranceCaseFrm.i2effective_date.value;
	if (document.insuranceCaseFrm.i2provider.value == ''){
			msg  = msg + '   Secondary Provider name is required\n'; 
			document.insuranceCaseFrm.secInsCompVal.focus();
			document.insuranceCaseFrm.secInsCompVal.style.backgroundColor=changecolor;
		}
		if (document.insuranceCaseFrm.i2policy_number.value == ''){
			msg  = msg + '   Secondary subscriber policy no is required\n'; 
			document.insuranceCaseFrm.i2policy_number.style.backgroundColor=changecolor;
		}		
		
		if (document.insuranceCaseFrm.i2effective_date.value == ''){
			msg  = msg + '   Secondary Activation Date is required\n'; 
			document.insuranceCaseFrm.i2effective_date.style.backgroundColor=changecolor;
		}
		
		if(document.insuranceCaseFrm.secondaryId.value != document.insuranceCaseFrm.secondaryMainId.value){
			if(document.insuranceCaseFrm.actPreviousSec.value != ''){
				if(actDate2 <= actSecDate){
					msg  = msg + '   Secondary Activation Date Must Be Greater Than Previous Activation Date.\n';
				}
			}
			if(document.insuranceCaseFrm.i2expiration_date.value != ''){
				if(actDate2 >= expDate2){
					msg  = msg + '   Secondary Expiration Date Must Be Greater Than Activation Date.\n'; 
				}
				else if(secondaryInsNames2 != ''){
					document.getElementById("expPreviousSec").value = expPreviousSecDate;
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+secondaryInsNames2+' for '+patientName+' ?<br>';
				}
			}	
		
			if(document.insuranceCaseFrm.secondaryId.value != ''){
				if(document.insuranceCaseFrm.secExpirationDate.value != ''){			
					if(actDate2 <= secExpDate){
						msg  = msg + '   Secondary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
					}
				}
				else{
					document.getElementById("expPreviousSec").value = expPreviousSecDate;
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+secondaryInsNames+' for '+patientName+' ?<br>';
				}
			}
		}
		else{
			if(document.insuranceCaseFrm.i2expiration_date.value != ''){
				if(actDate2 >= expDate2){
					msg  = msg + '   Secondary Expiration Date Must Be Greater Than Activation Date.\n'; 
				}
				else if(secondaryInsNames2 != ''){
					document.getElementById("expPreviousSec").value = expPreviousSecDate;
					msgs += 'Secondary Insurance Company : <br><br>';
					msgs += 'Are you sure you would like to expire '+secondaryInsNames2+' for '+patientName+' ?<br>';
				}
			}			
		}
		if(document.insuranceCaseFrm.i2subscriber_relationship.value == ''){
			msg  = msg + '   Secondary subscriber Relations is required\n'; 
			document.insuranceCaseFrm.i2subscriber_relationship.style.backgroundColor=changecolor;
		}
		if (document.insuranceCaseFrm.i2subscriber_fname.value == ''){
				msg  = msg + '   Secondary subscriber First Name is required\n'; 
				document.insuranceCaseFrm.i2subscriber_fname.style.backgroundColor=changecolor;
		}
		if (document.insuranceCaseFrm.lastName2.value == ''){
				msg  = msg + '   Secondary subscriber Last Name is required\n'; 
				document.insuranceCaseFrm.lastName2.style.backgroundColor=changecolor;
		}
		/*
		if(document.insuranceCaseFrm.i2referalreq){
			if (document.insuranceCaseFrm.i2referalreq.value == 'Yes' && document.insuranceCaseFrm.ref2_phy.value==""){
				msg  = msg + '   Secondary Referral Physician Name is required\n'; 
				document.insuranceCaseFrm.ref2_phy.style.backgroundColor=changecolor;
			}
			if (document.insuranceCaseFrm.i2referalreq.value == 'Yes' && document.insuranceCaseFrm.reffral_no2.value==""){
				msg  = msg + '   Secondary Referral#  no is required\n'; 
				document.insuranceCaseFrm.reffral_no2.style.backgroundColor=changecolor;
			}
			if (document.insuranceCaseFrm.i2referalreq.value == 'Yes' && document.insuranceCaseFrm.eff2_date.value!=""){
				if(document.insuranceCaseFrm.end2_date.value == ''){
					msg  = msg + '   Secondary Referral  End Effected is required\n'; 
					document.insuranceCaseFrm.end2_date.style.backgroundColor=changecolor;
				}
				else if(actRefTerDate >= endRefTerDate){
					msg  = msg + '   Secondary Referral Start Date Must Be grater Than End Effected.\n'; 				
				}
			}
		}
		
		*/
		if(document.insuranceCaseFrm.i2subscriber_DOB.value != '00-00-0000')
		{ 
			//validate_dt_dob(document.insuranceCaseFrm.i2subscriber_DOB);
			if(dDate >= cDate) 
			{
			msg = msg + '  - Date of Birth Should Be Past Date.\n';
			document.insuranceCaseFrm.i2subscriber_DOB.style.backgroundColor=changecolor; 
			
			} 
		}
		return msg;
}
function validateInsurance2(){
//----------------------------------------------------------------------------------------
	//document=eval(document);
	//msg = '';
var changecolor='#F6C67A';
	var re = /^\(?[2-9]\d{2}[\)\.-]?\s?\d{3}[\s\.-]?\d{4}$/ ;

	cDate=Date.parse(document.insuranceCaseFrm.from_date_subscriber3.value); 
	dDate=Date.parse(document.insuranceCaseFrm.i3subscriber_DOB.value); 
	
	if(top.date_format != "undefined"){
		arrActDate = fnArrDate(document.insuranceCaseFrm.i3effective_date.value,top.date_format);
		actDate3 = new Date(arrActDate[0],arrActDate[1]-1,arrActDate[2]);
		
		arrExpDate = fnArrDate(document.insuranceCaseFrm.i3expiration_date.value,top.date_format);
		expDate3 = new Date(arrExpDate[0],arrExpDate[1]-1,arrExpDate[2]);
		
	}else{
		actDate3=Date.parse(document.insuranceCaseFrm.i3effective_date.value); 
		expDate3=Date.parse(document.insuranceCaseFrm.i3expiration_date.value); 
	}
	
	//actDate3=Date.parse(document.insuranceCaseFrm.i3effective_date.value); 
	//expDate3=Date.parse(document.insuranceCaseFrm.i3expiration_date.value); 
	terExpDate=Date.parse(document.insuranceCaseFrm.terExpirationDate.value);	
	actTerDate = Date.parse(document.insuranceCaseFrm.actPreviousTer.value);
//	actRefTerDate = Date.parse(document.insuranceCaseFrm.eff3_date.value);
//	endRefTerDate = Date.parse(document.insuranceCaseFrm.end3_date.value);
	var patientName = document.insuranceCaseFrm.patientName.value;
	var TertiaryInsNames = document.insuranceCaseFrm.TertiaryInsName.value;
	var TertiaryInsNames2 = document.insuranceCaseFrm.terInsCompVal.value;
	var expPreviousTerDate = document.insuranceCaseFrm.i3effective_date.value;
	
	if (document.insuranceCaseFrm.i3provider.value == ''){
		msg  = msg + '   Tertiary Provider name is required<br>'; 
		document.insuranceCaseFrm.terInsCompVal.style.backgroundColor=changecolor;
	}
	if (document.insuranceCaseFrm.i3policy_number.value == '')
	{
		msg  = msg + '   Tertiary subscriber policy no is required\n'; 
		document.insuranceCaseFrm.i3policy_number.style.backgroundColor=changecolor;
	}
	if(document.insuranceCaseFrm.i3subscriber_relationship.value == ''){
			msg  = msg + '   Tertiary subscriber Relations is required\n'; 
			document.insuranceCaseFrm.i3subscriber_relationship.style.backgroundColor=changecolor;
		}
	if (document.insuranceCaseFrm.i3subscriber_fname.value == ''){
		msg  = msg + '   Tertiary subscriber First Name is required\n'; 
		document.insuranceCaseFrm.i3subscriber_fname.style.backgroundColor=changecolor;
	}
	if (document.insuranceCaseFrm.lastName3.value == ''){
		msg  = msg + '   Tertiary subscriber Last Name is required\n'; 
		document.insuranceCaseFrm.lastName3.style.backgroundColor=changecolor;
	}
	
	if (document.insuranceCaseFrm.i3effective_date.value == ''){
		msg  = msg + '   Tertiary Activation Date is required\n'; 
		document.insuranceCaseFrm.i3effective_date.style.backgroundColor=changecolor;
	}		
	if(document.insuranceCaseFrm.TertiaryId.value != document.insuranceCaseFrm.TertiaryMainId.value){		
		
		if(document.insuranceCaseFrm.actPreviousSec.value != ''){
			if(actDate3 <= actTerDate){
				msg  = msg + '   Tertiary Activation Date Must Be Greater Than Previous Activation Date.\n';
			}
		}
		if(document.insuranceCaseFrm.i3expiration_date.value != ''){
			if(actDate3 >= expDate3){
				msg  = msg + '   Tertiary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else if(TertiaryInsNames2 != ''){
				document.getElementById("expPreviousTer").value = expPreviousTerDate;
				msgs += 'Tertiary Insurance Company : <br><br>';
				msgs += 'Are you sure you would like to expire '+TertiaryInsNames2+' for '+patientName+' ?<br>';
			}
		}
	
		if(document.insuranceCaseFrm.TertiaryId.value != ''){
			if(document.insuranceCaseFrm.terExpirationDate.value != ''){			
				if(actDate3 <= terExpDate){
					msg  = msg + '   Tertiary Activate Date Must Be Greater Than Previous Expiration Date.\n';		
				}
			}
			else{
				document.getElementById("expPreviousTer").value = expPreviousTerDate;
				msgs += 'Tertiary Insurance Company : <br><br>';
				msgs += 'Are you sure you would like to expire '+TertiaryInsNames+' for '+patientName+' ?<br>';
			}			
		}
	}
	else{
		if(document.insuranceCaseFrm.i3expiration_date.value != ''){
			if(actDate3 >= expDate3){
				msg  = msg + '   Tertiary Expiration Date Must Be Greater Than Activation Date.\n'; 
			}
			else if(TertiaryInsNames2 != ''){
				document.getElementById("expPreviousTer").value = expPreviousTerDate;
				msgs += 'Tertiary Insurance Company : <br><br>';
				msgs += 'Are you sure you would like to expire '+TertiaryInsNames2+' for '+patientName+' ?<br>';
			}
		}			
	}
	
	if(document.insuranceCaseFrm.i3subscriber_DOB.value != '00-00-0000')
	{ 
		//validate_dt_dob(document.insuranceCaseFrm.i3subscriber_DOB);
		 if(dDate >= cDate) 
			{
			msg = msg + '  - Date of Birth Should Be Past Date.\n';
			document.insuranceCaseFrm.i3subscriber_DOB.style.backgroundColor=changecolor; 
			
			} 
	} 
	/*
	if(document.insuranceCaseFrm.i3referalreq){
		if (document.insuranceCaseFrm.i3referalreq.value == 'Yes' && document.insuranceCaseFrm.ref3_phy.value==""){
			msg  = msg + '   Tertiary Referral Physician Name is required\n'; 
			document.insuranceCaseFrm.ref3_phy.style.backgroundColor=changecolor;
		}
		if (document.insuranceCaseFrm.i3referalreq.value == 'Yes' && document.insuranceCaseFrm.reffral_no3.value==""){
			msg  = msg + '   Tertiary Referral#  no is required\n'; 
			document.insuranceCaseFrm.reffral_no3.style.backgroundColor=changecolor;
		}
		if (document.insuranceCaseFrm.i3referalreq.value == 'Yes' && document.insuranceCaseFrm.eff3_date.value!=""){
			if(document.insuranceCaseFrm.end3_date.value == ''){
				msg  = msg + '   Tertiary Referral  End Effected is required\n'; 
				document.insuranceCaseFrm.end3_date.style.backgroundColor=changecolor;
			}
			else if(actRefTerDate >= endRefTerDate){
				msg  = msg + '   Tertiary Referral Start Date Must Be grater Than End Effected.\n'; 				
			}
		}
	}
	*/
	return msg;
}

function detele_it()
{
	myvar=eval(myvar);
	var default_tab = document.document.getElementById("defaultTab").className;
	var sec_1 = document.document.getElementById("section1").className;
	var sec_2 = document.document.getElementById("section2").className;
	var obj_del = document.insuranceCaseFrm;
	
	if(default_tab == "ContentShown")
	{
		obj_del.i1provider.value = '';
		obj_del.i1plan_name.value = '';
		obj_del.i1policy_number.value = '';
		obj_del.i1group_number.value = '';
		obj_del.i1subscriber_fname.value = '';
		obj_del.i1subscriber_mname.value = '';
		obj_del.i1subscriber_lname.value = '';
		obj_del.i1subscriber_street.value = '';
		obj_del.i1subscriber_city.value = '';
		obj_del.i1subscriber_state.value = '';
		obj_del.i1subscriber_postal_code.value = '';
		obj_del.i1subscriber_country.value = '';
		obj_del.i1subscriber_phone.value = '';
		obj_del.i1copay.value = '';
		obj_del.i1policy_number.value = '';
		obj_del.i1group_number.value = '';
		obj_del.i1subscriber_DOB.value = '';
		
		obj_del.i1subscriber_employer.value = '';
		obj_del.i1subscriber_employer_street.value = '';
		obj_del.i1subscriber_employer_city.value = '';
		obj_del.i1subscriber_employer_state.value = '';
		
		obj_del.i1subscriber_employer_postal_code.value = '';
		obj_del.i1subscriber_employer_country.value = '';
		obj_del.i1subscriber_ss.value = '';
		obj_del.i1subscriber_relationship.value = '';
		
		document.insuranceCaseFrm.submit();
		
	}else if(sec_1 == "ContentShown"){
	
		obj_del.i2provider.value = '';
		obj_del.i2plan_name.value = '';
		obj_del.i2policy_number.value = '';
		obj_del.i2group_number.value = '';
		
		obj_del.i2subscriber_fname.value = '';
		obj_del.i2subscriber_mname.value = '';
		obj_del.i2subscriber_lname.value = '';
		obj_del.i2subscriber_street.value = '';
		obj_del.i2subscriber_city.value = '';
		obj_del.i2subscriber_state.value = '';
		obj_del.i2subscriber_postal_code.value = '';
		obj_del.i2subscriber_country.value = '';
		obj_del.i2subscriber_phone.value = '';
		obj_del.i2copay.value = '';
		obj_del.i2policy_number.value = '';
		obj_del.i2group_number.value = '';
		obj_del.i2subscriber_DOB.value = '';		
		obj_del.i2subscriber_employer.value = '';
		obj_del.i2subscriber_employer_street.value = '';
		obj_del.i2subscriber_employer_city.value = '';
		obj_del.i2subscriber_employer_state.value = '';		
		obj_del.i2subscriber_employer_postal_code.value = '';
		obj_del.i2subscriber_employer_country.value = '';
		obj_del.i2subscriber_ss.value = '';
		obj_del.i2subscriber_relationship.value = '';
		
		document.insuranceCaseFrm.submit();		
		
	}else if(sec_2 == "ContentShown"){
	
		obj_del.i3provider.value = '';
		obj_del.i3plan_name.value = '';
		obj_del.i3policy_number.value = '';
		obj_del.i3group_number.value = '';
		
		obj_del.i3subscriber_fname.value = '';
		obj_del.i3subscriber_mname.value = '';
		obj_del.i3subscriber_lname.value = '';
		obj_del.i3subscriber_street.value = '';
		obj_del.i3subscriber_city.value = '';
		obj_del.i3subscriber_state.value = '';
		obj_del.i3subscriber_postal_code.value = '';
		obj_del.i3subscriber_country.value = '';
		obj_del.i3subscriber_phone.value = '';
		obj_del.i3copay.value = '';
		obj_del.i3policy_number.value = '';
		obj_del.i3group_number.value = '';
		obj_del.i3subscriber_DOB.value = '';		
		obj_del.i3subscriber_employer.value = '';
		obj_del.i3subscriber_employer_street.value = '';
		obj_del.i3subscriber_employer_city.value = '';
		obj_del.i3subscriber_employer_state.value = '';		
		obj_del.i3subscriber_employer_postal_code.value = '';
		obj_del.i3subscriber_employer_country.value = '';
		obj_del.i3subscriber_ss.value = '';
		obj_del.i3subscriber_relationship.value = '';
		
		document.insuranceCaseFrm.submit();
	}
}
	function validate_dt_dob(isField)
	{
		myvar=eval(myvar);
			//alert(isField.value);
			splitDate = isField.value.split("-");
			if (splitDate[2] && splitDate[2].length == 2){splitDate[2] = "20"+splitDate[2]}
			refDate = new Date(splitDate[0]+"-"+splitDate[1]+"-"+splitDate[2]);
			
			if (splitDate[0] < 1 || splitDate[0] > 12 || refDate.getDate() != splitDate[1] || splitDate[2].length != 4)
			{			
			  	msg = msg + '   Invalid Date of Birth \n';		
				isField.style.backgroundColor=changecolor;
			}
	}
	function validate_financial1()
	{
		myvar=eval(myvar);
	myvar.sign.clear();
	}
	function validate_financial2()
	{
		myvar=eval(myvar);
	myvar.sign1.clear();
	}
		function validate_financial3()
	{
		myvar=eval(myvar);
	myvar.sign3.clear();
	}
	function validate_financial22()
	{
		myvar=eval(myvar);
	myvar.sign22.clear();
	}
function validate_hippas()
	{
		myvar=eval(myvar);
	myvar.sign_hippa.clear();
	}

	
	function validate_financial()
	{
			 msg='';
			myvar=eval(myvar);
			 
			 //return false;
			 isField=myvar.financial_edit.fin_sign_datee;
			 if(isField!=""){			
			 	validate_dt_sign(isField); //validate date
			 }



			if (myvar.sign22) 
			{
			myvar.financial_edit.signature.value=myvar.sign22.signature();
			}
			
			 if(msg == ''){
			myvar.financial_edit.submit();
			 }else{
			 	alert (msg);
			 }
			 var cleartCl = document.getElementById('clearsi');
			eval(cleartCl.id +".className='ButHidden';");
			 
	}
	function validatehippa()
	{
		myvar=eval(myvar);
			 msg='';
			 //return false;
			 isField=myvar.form_hippa_edit.p_date;
			 
			 if(isField!=""){			
			 	validate_dt_sign(isField); //validate date
			 }

			if (myvar.sign_hippa) 
			{
			myvar.form_hippa_edit.signature.value=myvar.sign_hippa.signature();
			}
			
			 if(msg == ''){
			myvar.form_hippa_edit.submit();
			 }else{
			 	alert (msg);
			 }
			 var cleartCl = document.getElementById('clearsi');
			eval(cleartCl.id +".className='ButHidden';");	
			}

function validaterefra()
	{
		myvar=eval(myvar);
			//alert("misc");
			 msg='';
			  isField=myvar.financial_edit_form.fin_sign_date;
			 	 if(isField!=""){			
			 	validate_dt_sign(isField); //validate date
			 }
			if (myvar.sign1) 
			{
			myvar.financial_edit_form.signature1.value=myvar.sign1.signature();
			}
			if (myvar.sign) 
			{
				myvar.financial_edit_form.signature.value=myvar.sign.signature();
			}
			
			 if(msg == '')
			  {
				myvar.financial_edit_form.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			  var cleartCl1 = document.getElementById('clearpasi');
			var cleartCl2 = document.getElementById('clearwisi');
			eval(cleartCl1.id +".className='ButHidden';");	
			eval(cleartCl2.id +".className='ButHidden';");	
			// var cleartCl = document.getElementById('clearsi');
			//eval(cleartCl.id +".className='ButHidden';");	
	
			
	}
function validate_financial23()
	{
		myvar=eval(myvar);
//	alert("y");
	myvar.sign_dis.clear();
	}
	function validate_dis()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			  isField=myvar.financial_edit1.dis_sign_date;
			// alert(isField);
 if(isField!=""){			
			 	validate_dt_sign(isField); //validate date
			 }
			// alert(myvar.sign_dis);
			if (myvar.sign_dis) 
			{
			myvar.financial_edit1.signature.value=myvar.sign_dis.signature();
			}
			 if(msg == '')
			  {
				myvar.financial_edit1.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			 var cleartCl = document.getElementById('clearsi');
			eval(cleartCl.id +".className='ButHidden';");	
	}
	function validate_dis2()
	{
		myvar=eval(myvar);
//	alert("y");
	myvar.sign_dis2.clear();
	}
function validate_diss1()
	{
		myvar=eval(myvar);
	 msg='';
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 if (myvar.sign_dis2) 
			{
			myvar.disclosure_form.signature.value=myvar.sign_dis2.signature();
			}
			 if(msg == '')
			  {
				myvar.disclosure_form.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			 var cleartCl = document.getElementById('clearsi');
			eval(cleartCl.id +".className='ButHidden';");	
	}
function validate_dis3()
	{
		myvar=eval(myvar);
	myvar.sign_dis3.clear();
	}
function validate_diss2()
	{
		myvar=eval(myvar);
	 msg='';
		//	 alert(myvar.sign_dis3);
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 if (myvar.sign_dis3) 
			{
			myvar.disclosure_form3.signature.value=myvar.sign_dis3.signature();
			}
			 if(msg == '')
			  {
				myvar.disclosure_form3.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			 var cleartCl = document.getElementById('clearsi');
			 eval(cleartCl.id +".className='ButHidden';");
	}
	//document.getElementById('butId2').href='javascript:myvar.medical_edit_form.submit();';

	function validate_medical()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			 isField=myvar.summary.medical_edit_form.exam_date;
			 
			 validate_dt_med(isField); //validate date
			
			 if(msg == '')
			  {
				myvar.summary.medical_edit_form.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
	function validate_allergy()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			 //validate date
//			 alert(myvar.summary.allergy.form_title.value);
			 
			 if(msg == '')
			  {
				myvar.summary.allergy.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
	function validate_immunization()
	{
			myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			 //validate date
			 if(msg == '')
			  {
				myvar.summary.immunization.submit();
				
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
	function validate_medication()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			
			 if(msg == '')
			  {
				myvar.summary.medication.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
		function validate_surgery()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			 //validate date
			 if(msg == '')
			  {
				myvar.summary.surgery.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
	
	function validate_dis1()
	{
		myvar=eval(myvar);
			 //alert("ocular");
			 //myvar.demographics_edit_form.submit();
			 //exam_date
			 msg='';
			 isField=myvar.demographics_edit_form.dis_sign_date1;
			 
			 validate_dt_sign(isField); //validate date
			
			 if(msg == '')
			  {
				myvar.demographics_edit_form.submit();
			  }
			 else
			  {
				 alert (msg);
			  }		
			
	}
	
	function validate_dt_sign(isField){
	myvar=eval(myvar);
			splitDate = isField.value.split("-");
			if (splitDate[2] && splitDate[2].length == 2){splitDate[2] = "20"+splitDate[2]}
			refDate = new Date(splitDate[0]+"-"+splitDate[1]+"-"+splitDate[2]);
			
			if (splitDate[0] < 1 || splitDate[0] > 12 || refDate.getDate() != splitDate[1] || splitDate[2].length != 4 || (!/^20/.test(splitDate[2])))
			{			
				msg = msg + '   Invalid signature date \n';	
				isField.style.backgroundColor=changecolor;			
				//isField.focus();
			}
	}

	function validate_dt_med(isField){
myvar=eval(myvar);
			splitDate = isField.value.split("-");
			if (splitDate[2] && splitDate[2].length == 2){splitDate[2] = "20"+splitDate[2]}
			refDate = new Date(splitDate[0]+"-"+splitDate[1]+"-"+splitDate[2]);
			
			if (splitDate[0] < 1 || splitDate[0] > 12 || refDate.getDate() != splitDate[1] || splitDate[2].length != 4 || (!/^20/.test(splitDate[2])))
			{			
			  	msg = msg + '   Invalid eye exam date \n';			
				isField.style.backgroundColor=changecolor;	
			  	//isField.focus();
			}
	}

	function validate_dt(isField){
	myvar=eval(myvar);
			splitDate = isField.value.split("-");
			if (splitDate[2] && splitDate[2].length == 2){splitDate[2] = "20"+splitDate[2]}
			refDate = new Date(splitDate[0]+"-"+splitDate[1]+"-"+splitDate[2]);
			
			if (splitDate[0] < 1 || splitDate[0] > 12 || refDate.getDate() != splitDate[1] || splitDate[2].length != 4 || (!/^20/.test(splitDate[2])))
			{			
				msg = msg + '   Invalid financial review date \n';	
				isField.style.backgroundColor=changecolor;			
				//isField.focus();
			}
	}
 

	function validateZIP(field) {
		myvar=eval(myvar);
			var valid = "0123456789-";
			var hyphencount = 0;
			fl=field.value;
			
			if (fl.length!=5 && fl.length!=10) {
					//alert("Please enter your 5 digit or 5 digit+4 zip code.");
					msg = msg + '   Please enter your 5 digit '+top.zipLabel+'\n'; 
					field.style.backgroundColor=changecolor;
					return false;
			}
			for (var i=0; i < fl.length; i++) {
					temp = "" + fl.substring(i, i+1);
					if (temp == "-") hyphencount++;
					if (valid.indexOf(temp) == "-1") {
						msg = msg + '   Invalid characters in your '+top.zipLabel+'. Please try again\n';
						field.style.backgroundColor=changecolor;
						//alert("Invalid characters in your zip code. Please try again.");
						return false;
					}		
			}
			return true;
	}

	function echeck(str) {
myvar=eval(myvar);
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		//   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		//   alert("Invalid E-mail ID")

		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		 //   alert("Invalid E-mail ID")

			return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
			alert("Invalid E-mail ID")

			return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		  //  alert("Invalid E-mail ID")

			return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		  //  alert("Invalid E-mail ID")

			return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		 //   alert("Invalid E-mail ID")

			return false
		 }

		 return true					
	}
	
function chkdate(objName) {
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
	var curDt = new Date();
	var curYr = curDt.getFullYear();
	var curCen = curYr.toString().substr(0,2);
	var curYr2 = curYr.toString().substr(2);
	/*if (strDate.length < 1) {
	return true;
	}*/
	if (strDate.length>=1 && strDate.length < 6) {
	//alert("hello Length is < 1");
	return false;
	}else if(strDate.length<1 || strDate.length==0){
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
			strYear = strDate.substr(4).substr(0,4);
	   }
	}
	
	//alert(strYear.length);
	//Code to ensure dob is 1900
	if(strYear.length==4){
		//alert(strYear.substr(0,2));
		var chb=parseInt(strYear.substr(0,2));
		if(chb<19){
			strYear=strYear.substr(2);		
			var tmpCen = (eval(strYear) <= eval(curYr2)) ? curCen : curCen-1;
			strYear = tmpCen + strYear; //'19'
		}
	}
	//end Code to ensure dob is 1900
	if (strYear.length == 3) {
		return false;
	}
	
	if (strYear.length == 2) {
	var tmpCen = (eval(strYear) <= eval(curYr2)) ? curCen : curCen-1;
	strYear = tmpCen + strYear;//19
	}
	
	if (strYear.length == 1) {
	var tmpCen = (eval(strYear) <= eval(curYr2)) ? curCen : curCen-1;
	strYear = tmpCen +"0"+ strYear;//19
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

function parseDate(input, format) { 
 // format = format || 'yyyy-mm-dd'; // default format 
  format = format || top.date_format;
  var parts = input.match(/(\d+)/g),  
      i = 0, fmt = {}; 
  // extract date-part indexes from the format 
  format.replace(/(yyyy|dd|mm)/g, function(part) { fmt[part] = i++; }); 
 
  return new Date(parts[fmt['yyyy']], parts[fmt['mm']]-1, parts[fmt['dd']]); 
}

function doDateCheck(from, to) {	
	if(validate_date(to) && validate_date(from) ){
	if (from.value != "" && to.value != "") {
		//if (parseDate(from.value, 'mm-dd-yyyy') >= parseDate(to.value, 'mm-dd-yyyy')) {	
		if (parseDate(from.value, top.date_format) >= parseDate(to.value, top.date_format)) {	
		//alert("The dates are valid.");
		return true;
		}
		else {
			if (from.value == "" || to.value == ""){ 
			//alert("Both dates must be entered.");
			}
			else{ 
			to.value="";
			alert("Date of birth can not be greater than current date.");
			return false;
			   }
			}
		}
	}
}
//  End of date format functions by ram -->
//////Function to compare From And TO Date By Ram///
function doDateCompare(from, to) {

	if(validate_date(to) && validate_date(from) ){
	
	if (parseDate(from.value, 'mm-dd-yyyy') >= parseDate(to.value, 'mm-dd-yyyy')) {	
	//alert("The dates are valid.");
	return true;
	}
	else {
		
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		from.value="";
		alert("To date can not be less than from date.");
		return false;
		   }
		}
	}
}
//////Function to compare From And TO Date By Ram///
//////Function to calculate date diffrence By Ram///	
  function getAgeDifference(strDob){
		   datestr2=strDob.split("-");
		    var firstDate = new Date() ;
			var secondDate = new Date(datestr2[2],datestr2[0],datestr2[1]);
			var firstYear = firstDate.getYear();
			var secondYear = secondDate.getFullYear();
			if (firstYear < secondYear) { fisrtYear = firstYear + 100; } //Account for some browsers & Y2K
			var years = firstYear - secondYear;
			
			var firstMonth = firstDate.getMonth();
			var secondMonth = secondDate.getMonth();
			var retunVal=""; 
			if (firstMonth < secondMonth) { retunVal=(years - 1);}// alert("a Years Difference = " + (years - 1)); }
			if (firstMonth > secondMonth) { retunVal=(years) ;} //alert("b Years Difference = " + years); }
			if (firstMonth == secondMonth) { 
				   var firstDay = firstDate.getDate();
				   var secondDay = secondDate.getDate();
				   if (firstDay < secondDay)
				      {
						   retunVal=(years - 1) ;//alert("c Years Difference = " + (years - 1));
					  }
				   if (firstDay >= secondDay) 
				    {
					 retunVal=years;
					}//alert("d Years Difference = " + years); }
			}
					
					return retunVal;
		   }
//////Function to calculate date diffrence By Ram///		   



/*************************************************************************************
 Created By		  : Ashwani Sharma	
 function name	 :  demographic_info_front_desk
 Created date	 :  10-03-2008
 Arguments	 	 :	pat_id	
 Purpose		 :	 
 
 ************************************************************************************/
function demographic_info_front_desk(pat_id){
	if(typeof top.fmain.front.all_data.info_case!="undefined"){
		var myvars=top.fmain.front.all_data.info_case;
	}else{
			
		var myvars=top.fmain.front.all_data.info
	}
	
	var newmsg="";
	if(myvars.document.getElementById("patientDob") && (myvars.document.getElementById("patientDob").value=="" || myvars.document.getElementById("patientSex").value=="" ||  myvars.document.getElementById("patientStreet").value=="")){
		
		if(myvars.document.getElementById("patientDob").value==""){
				newmsg =newmsg +'DOB Information\n';
		}
		if(myvars.document.getElementById("patientSex").value==""){
			newmsg =newmsg +'Sexual Information\n';
		}
		if(myvars.document.getElementById("patientStreet").value==""){
			newmsg =newmsg +  'Address\n';
		}
		
		if(newmsg){
			newmsg = "Following Demographics Information are Required\n"+newmsg ;
			alert(newmsg);
		}
		if(top.fmain.front.all_data.info_case){
			top.Navigation.currTab='Patient Info';
			
			top.Navigation.changeBackImage();
			top.fmain.front.changeSelection("demographics");
		}else{
			validate2();
		}
	}	else{
		
		/*top.fmain.document.getElementById("schedule").style.width = '1000px';			
		top.fmain.document.getElementById("front").style.width = '0px';
		
		top.fmain.document.getElementById("dayschedule").style.width='0px';
		top.fmain.document.getElementById("dayschedules").style.display='none';
		*/
		//alert(top.Title.expand_schedule)
		var mydoc=top.fmain.front;
		mydoc.location.href= "../main/select_patient.php";
		//mydoc.location.href= "../chart_notes/index.php?schedular=1";
			
		/*if(top.fmain.document.getElementById("schedule").src=='ss.html'){		
			
			top.fmain.document.getElementById("schedule").src='../admin/schedular/day_schedule_location.php?patId='+pat_id;
			if(typeof top.fmain.schedule.see_sel != "undefined"){
			top.fmain.schedule.see_sel();
			}
		}*/
		
		
	}
}
//************************************* Ends here**************************************/


/*************************************************************************************
 Created By		  : Ashwani Sharma	
 function name	 :  chart_info_front_desk
 Created date	 :  16-04-2008
 Arguments	 	 :	pat_id	
 Purpose		 :	getting follow up informations from chart note
 
 ************************************************************************************/
function chart_info_front_desk(pat_id){
	
	/*top.fmain.document.getElementById("schedule").style.width = '1000px';			
	top.fmain.document.getElementById("front").style.width = '0px';
	top.fmain.document.getElementById("dayschedule").style.width='0px';
	top.fmain.document.getElementById("dayschedules").style.display='none';
	if(top.fmain.document.getElementById("schedule").src=='ss.html'){		
		top.fmain.document.getElementById("schedule").src='../admin/schedular/day_schedule_location.php?patId='+pat_id;
		top.fmain.schedule.see_sel();
	}*/
	//alert(pat_id);
	var mydoc=top.fmain.front;
	//mydoc.location.href= "../chart_notes/index.php?schedular=1";
	top.Navigation.location.reload();
	mydoc.location.href= "../main/patient_tabs.php?closePatient=true";
		
		
	/*top.fmain.schedule.document.getElementById("patientinfo_cross").value=1;

	top.fmain.schedule.document.getElementById("follow_up").value="yes";
	//alert(top.fmain.schedule.document.getElementById("elem_patient_sched_id").value)
	var	sch_id=top.fmain.schedule.document.getElementById("elem_patient_sched_id").value

	top.fmain.schedule.refresh_patient_infopage(pat_id,sch_id,'');
*/
}
//************************************* Ends here**************************************/

function changeBackImg(id,val)
{
	ab = document.getElementById(id);
	if(ab.className != 'TDTabSelected' && val == '')
	{
		ab.className = 'TDTabHover';
	}	
	else if(ab.className != 'TDTabSelected' && val != '')
	{
		ab.className = 'TDTab';
	}	
}		

function button_over(ioc_id){
	//alert(ioc_id);
		if(button_over.arguments.length>1){
				
				document.getElementById(ioc_id).className='dff_button';
		
		}
		else{
		
			document.getElementById(ioc_id).className='dff_buttonog';
			
		}		
	}
	
	function button_over_ins(ioc_id){
	//alert(ioc_id);
		if(button_over_ins.arguments.length>1){
				
				document.getElementById(ioc_id).className='dff_button_ins';
		
		}
		else{
		
			document.getElementById(ioc_id).className='dff_buttonog_ins';
			
		}		
	}
	
	
	
		
function GetXmlHttpObject()
{
	var xmlHttp1=null;
	try
 	{
 // Firefox, Opera 8.0+, Safari
 		xmlHttp1=new XMLHttpRequest();
 	}
	catch (e)
 	{
 //Internet Explorer
		try
  		{
 			xmlHttp1=new ActiveXObject("Msxml2.XMLHTTP");
  		}
		catch (e)
 		{
  			xmlHttp1=new ActiveXObject("Microsoft.XMLHTTP");
  		}
 	}
		return xmlHttp1;
}	


//function for setting type ahead values using ajax
function setFrame(vendName,make)
{

		var xmlHttp1=GetXmlHttpObject()
		if (xmlHttp1==null)
		{
			alert ("Browser does not support HTTP Request")
			return
		}
		if(document.getElementById(vendName).value == '')
		{
			return false;	
		}
		if(window.event.keyCode == 13)
		{
			return false;
		}
		if(window.event.keyCode == 40)
		{
			var divLen = document.getElementById('searchDiv').childNodes.length;
			for(j=0;j<divLen-1;j++)
			{	//alert(divLen)
				if(document.getElementById('s'+j).style.background == 'black')
				{
					var xx = j+1; 
					document.getElementById('s'+j).style.background = '#888888';
					document.getElementById('s'+xx).style.background = 'black';
					//document.getElementById('frame_name').value = document.getElementById('s'+xx).innerHTML
					break;
					 
				}
			}
		}
		else if(window.event.keyCode == 38)
		{
			var divLen = document.getElementById('searchDiv').childNodes.length;
			for(j=0;j<divLen;j++)
			{	//alert(divLen)
				if(document.getElementById('s'+j).style.background == 'black')
				{
					var xx = j-1; 
					if(xx < 0)
					{
						return false;	
					}
					document.getElementById('s'+j).style.background = '#888888';
					document.getElementById('s'+xx).style.background = 'black';
					break;
				}
			}	
		}
		else if(window.event.keyCode != 38 && window.event.keyCode != 40)
		{
			var vend_name = document.getElementById(vendName).value;
			var frameName = document.getElementById(make).value;
			
			var url="Functions.php?vendor="+vend_name+"&frame="+frameName;
			//alert(url)
			
			document.getElementById('searchDiv').innerHTML = '';	
			var leftSet = window.event.clientX;
			var topSet = window.event.clientY;
			xmlHttp1.onreadystatechange=function()
			{
				if (xmlHttp1.readyState==4)
				{
					var str = xmlHttp1.responseText.split("*n");
					
					if(str == '' || frameName == '')
					{
						document.getElementById('searchDiv').style.display = 'none';
						return false;	
					}
					for(i=0;i<str.length-1; i++) 
					{
						var suggest = '<div id="s'+i+'"';
						suggest += ' onclick="javascript:setSearch('+i+');" onmouseover="javascript:setBgClr('+i+');" >'+ str[i];
						suggest +=   '</div>';
						document.getElementById('searchDiv').innerHTML += suggest;
						
						document.getElementById('s0').style.background = 'black';
						document.getElementById('searchDiv').style.display = 'block';
						//alert(document.getElementById('searchDiv').childNodes.length);
					}
				}
			}
				xmlHttp1.open("GET",url,true)
				xmlHttp1.send(null)
		}
					
}

//function to set text box value on click

function setSearch(id)
{
	document.getElementById('make_frame').value	= document.getElementById('s'+id).innerHTML;
	document.getElementById('searchDiv').style.display = 'none';
}

//function to set text box value on enter

function setVal(make)
{
	if(window.event.keyCode == 13)
	{
		var divLen = document.getElementById('searchDiv').childNodes.length;
		
		for(var i=0;i<divLen;i++)	
		{
			if(document.getElementById('s'+i).style.background == 'black')
			{
				document.getElementById(make).value	= document.getElementById('s'+i).innerHTML;
				document.getElementById('searchDiv').style.display = 'none';
				return false;
			}
		}
	}
		
}

//function for setting bgcolor on mouseover
function setBgClr(id)
{	
	
	var divLen = document.getElementById('searchDiv').childNodes.length;
	for(var i=0;i<divLen;i++)	
	{
		if(id == i)
		{
			document.getElementById('s'+i).style.background = 'black';
			document.getElementById('s'+i).style.cursor = 'hand';
		}
		else
		{
			document.getElementById('s'+i).style.background = '#888888';
			document.getElementById('s'+i).style.cursor = 'hand';
		}
		
	}
}


//after lay out changing
function changeBackground(id){
	
	var preObj;
	if(document.getElementById("preObjBack")) {
		preObj = document.getElementById("preObjBack").value;
		if(preObj!=''){
			if(document.getElementById(preObj)) {
				document.getElementById(preObj).style.background = '#F4F9EE';
			}
		}
		document.getElementById("preObjBack").value = id;
	}
	
	if(document.getElementById(id)) {
		document.getElementById(id).style.background = '#FFFFCC';
	}
}

function setHeardOther(obj,heard_about_us_textarea_cols,heard_abt_desc_val){	
	var val = obj.value;
	var objOther = document.getElementById('heardAbtOther');
	var objDesc = document.getElementById('heardAbtDesc');
	var img = document.getElementById("imgBackHeardAboutUs");

	if(val == 'Other'){
		obj.style.display = 'none';
		objOther.style.display = 'block';
		img.style.display = "block";
	}else{
		obj.style.display = 'block';
		objOther.style.display = 'none';
		img.style.display = "none";
	}

	if(val != ''){
		//objDesc.style.display = 'block';
		var heardAboutTdData = "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"tdTableHeardAboutDesc\"><tr><td ><textarea id=\"heardAbtDesc\" name=\"heardAbtDesc\" class=\"text_10 body_c\" rows=\"1\" cols=\""+heard_about_us_textarea_cols+"\" onFocus=\"getFocusHeardAbtDesc();\" style=\"display:block; height:14px;width:100px;\" onChange=\"changeRefPhy(document.getElementById('elem_heardAbtUs'));\">"+heard_abt_desc_val+"</textarea></td></tr></table>";		
		document.getElementById('tdHeardAboutDesc').innerHTML = heardAboutTdData;
		arrVal = val.split('-');
		if(arrVal[1] == "Dr." || arrVal[1] == "Dr"){
			if (!document.getElementById("tdTableHeardAboutDesc").addEventListener) {
				document.getElementById("tdTableHeardAboutDesc").attachEvent("onkeydown",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
			}
			else {
				document.getElementById("tdTableHeardAboutDesc").addEventListener("keydown",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
			}
			//document.getElementById("tdTableHeardAboutDesc").addEventListener("keydown",function(){loadPhysicians(document.getElementById("heardAbtDesc"))});
		}
	}else{
		//objDesc.style.display = 'none';
		var heardAboutTdData = "<table width=\"100%\" height=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"tdTableHeardAboutDesc\"><tr><td style=\"cursor:hand;\"><textarea id=\"heardAbtDesc\" name=\"heardAbtDesc\" class=\"text_10 body_c\" rows=\"1\" cols=\""+heard_about_us_textarea_cols+"\"  onFocus=\"getFocusHeardAbtDesc();\" style=\"display:block; height:14px;width:100px;\" onChange=\"changeRefPhy(document.getElementById('elem_heardAbtUs'));\">"+heard_abt_desc_val+"</textarea></td></tr></table>";
		document.getElementById('tdHeardAboutDesc').innerHTML = heardAboutTdData;	
	}
}

function fillRefVal(){
	var obj = document.getElementById('elem_heardAbtUs');	
	var orignalVal = obj.value;	
	var arrOrignalVal = orignalVal.split("-");
	var val = arrOrignalVal[1];	
	var refObj = document.getElementById('elem_physicianName');	
	var heardDesc = document.getElementById('heardAbtDesc');
	if(document.getElementById("ref_phy_name") != "undefined" && document.getElementById("ref_phy_name") != null)
	var refObj = document.getElementById('ref_phy_name');	
	
	if(val == 'Dr.' || val == 'Dr'){
		refObj.value = heardDesc.value;
	}
}

function changeBackImage(obj,val){
	var cur_tab = document.getElementById("curr_tab").value;
	if(cur_tab != obj.id){
		var clasName = val == false ? 'hovertab' : 'normaltab';
		
		obj.className = clasName;
	}
}

function changeSmallBackImage(obj,val){
	var cur_tab = document.getElementById("curr_tab").value;
	if(cur_tab != obj.id){
		var clasName = val == false ? 'hoversmalltab' : 'normalsmalltab';
		
		obj.className = clasName;
	}
}

function changeClass(obj){
	if(trimBothSide(obj.value) != ''){
		obj.className = 'input_text_10';
	}
	else{
		obj.className = 'mandatory';
		obj.value='';
	}
}

function physician_console(){
	var parenthei = parent.document.body.clientHeight;
	var features='left=100,top=0,width=1280,height='+parenthei+',menuBar=no,scrollBars=0,toolbar=0,resizable=yes';
	var physicianconsole=window.open('../physician_console/index.php','Console',features);
	physicianconsole.moveTo(0,0);
	physicianconsole.focus();
}

//--- Order set for chart notes -----
function set_order_pop_up(num,FrmId){
	var FrmId;
	if(FrmId=='')
	{
	   alert('Please Open New Chart Note!');
	   return false;
	}
	
	var width = screen.width, height = screen.height;
	var left = 0;
	var top = 0;
	var styleStr = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=1280,height='+height;
	var asses_val = gebi("elem_assessment"+num).value;
	oPUF["order_set"] = window.open('chart_notes_order_set.php?asses_val='+asses_val+'&plan_num='+num,'order_set',styleStr);
	oPUF["order_set"].moveTo(0,0);
	oPUF["order_set"].focus();  
//	window.open('chart_notes_order_set.php?asses_val='+asses_val+'&plan_num='+num,'order_set',styleStr);
}

function selectAll(objVal,objId,cnt){
	dis = true;
	orders_dis = 'none';
	if(objVal == true){
		dis = false;
		orders_dis = 'table-row';
	}
	document.getElementById("set_site_drop_"+objId).disabled = dis;
	document.getElementById("set_day_drop_"+objId).disabled = dis;
	document.getElementById("set_when_drop_"+objId).disabled = dis;
	document.getElementById("set_priority_drop_"+objId).disabled = dis;
	
	var setOpObj = document.getElementsByName("set_options["+objId+"][]");
	for(s=0;s<setOpObj.length;s++){	
		setOpObj[s].disabled = dis;
	}
	
	var ordersRowObj = document.getElementsByName("orderSet_row["+objId+"][]");
	if(ordersRowObj.length<=0){ ordersRowObj = $("tr[name='orderSet_row["+objId+"][]']"); }
	
	for(i=0;i<ordersRowObj.length;i++){
		ordersRowObj[i].style.display = orders_dis;
	}
	var ordersObj = document.getElementsByName("new_orderSetOrders["+objId+"][]");
	for(i=0;i<ordersObj.length;i++){
		ordersObj[i].checked = objVal;
	}
	
}

function show_orders(objVal,cnt,cnt2,parentId,type,id){
	//--- options check for orders ------
	//if(id){
		//var op_obj = document.getElementsByName("orders_option_arr["+id+"][]");
		//for(i=0;i<op_obj.length;i++){
			//op_obj[i].checked = objVal;
		//}
	//}
	//---- Reason text check for orders -------
	var val = String(cnt)+''+String(cnt2);
	if(parentId){
		var reasonObj = document.getElementsByName("reason_td["+parentId+"][]");
		var reasonTxtObj = document.getElementsByName("reasonTxtArr["+parentId+"][]");
		var siteTxtObj = document.getElementsByName("new_orders_site_txt_id["+parentId+"][]");
		var siteValObj = document.getElementsByName("new_orders_site_id["+parentId+"][]");
		var dayObj = document.getElementsByName("new_schedule_day_td["+parentId+"][]");
		var scheduleObj = document.getElementsByName("new_order_schedule["+parentId+"][]");
		var priorityTxtObj = document.getElementsByName("new_orders_priority_txt["+parentId+"][]");
		var priorityValObj = document.getElementsByName("new_orders_priority["+parentId+"][]");
		
		var dis = 'table-row';
		if(objVal == true){
			dis = 'none';
		}
		for(i=0;i<reasonObj.length;i++){
			if(i == cnt2){
				reasonObj[i].style.display = dis;
				reasonTxtObj[i].value = '';
				siteTxtObj[i].value = '';
				siteValObj[i].value = '';
				dayObj[i].value = '';
				scheduleObj[i].value = '';
				priorityTxtObj[i].value = '';
				priorityTxtObj[i].value = '';
			}
		}
	}
	
	var dis = objVal == true ? 'table-row' : 'none';
	var typeVal = type.split(',');
	var inf = false;
	for(u in typeVal){
		if(typeVal[u] == 'Information'){
			inf = true;
		}
	}	
	
	if(inf == false){
		document.getElementById("site_td_id_"+val).style.display = dis;
		document.getElementById("day_td_id_"+val).style.display = dis;
		document.getElementById("schedule_td_id_"+val).style.display = dis;
		document.getElementById("priority_td_id_"+val).style.display = dis;
	}
	else{
		var infObj = document.getElementsByName("information_td["+parentId+"][]");
		infObj[cnt2].style.display = dis;
		if(dis == 'none'){
			var infTxtobj = document.getElementsByName("information_name["+parentId+"][]");
			//infTxtobj[cnt2].value = '';
		}
	}
	
	if(parentId){
		if(objVal == false){
			var objName = document.getElementsByName("new_orderSetOrders["+parentId+"][]");
			for(i=0;i<objName.length;i++){
				if(objName[i].checked == true){
					objVal = true;
					break;
				}
			}
			for(i=0;i<reasonObj.length;i++){
				if(reasonTxtObj[i].value != ''){
					objVal = true;
					break;
				}
			}
		}
		
		var objName = document.getElementsByName("new_order_set[]");
		for(i=0;i<objName.length;i++){
			if(parentId == objName[i].value){
				objName[i].checked = objVal;
				break;
			}
		}
	}
}

function show_orders_val(objVal,order_type,id){
	var dis = objVal == true ? false : true;
	if(order_type == 'Information'){
		$("#order_information_"+id).attr('disabled', dis);
	}
	else{
		$("#newOrdersSite_"+id).attr('disabled', dis);
		$("#newOrdersWhen_"+id).attr('disabled', dis);
		$("#new_orders_day_"+id).attr('disabled', dis);
		$("#newOrdersPriority_"+id).attr('disabled', dis);
	}
	
	var optObj = document.getElementsByName("orders_option_arr["+id+"][]");
	for(i=0;i<optObj.length;i++){
		optObj[i].disabled = dis;
	}
}

function changeReasonTxt(obj,id,cnt){
	var objName = document.getElementsByName("new_order_set[]");
	for(i=0;i<objName.length;i++){
		if(id == objName[i].value){
			objName[i].checked = true;
			break;
		}
	}
}

function selectSavedAll(objVal,objId){	
	var obj = document.getElementsByName("select_orders["+objId+"][]");
	for(i=0;i<obj.length;i++){
		obj[i].checked = objVal;
	}
}

function check_val(){	
	var orderSet = document.getElementsByName("select_order_set[]");
	var msg = '';
	var check = true;
	var set = document.getElementsByName("select_order_set[]");
	if(set.length > 0){
		check = false;
	}
	var set = document.getElementsByName("selected_orders[]");
	if(set.length > 0){
		check = false;
	}
	if(document.getElementsByName("new_order_set[]")){
		var new_orders = document.getElementsByName("new_order_set[]");
		if(check == true){
			for(i=0;i<new_orders.length;i++){
				if(new_orders[i].checked == true){
					check = false;
					break;
				}
			}
		}
	}
	if(document.getElementsByName("new_orders[]")){
		var new_orders = document.getElementsByName("new_orders[]");
		if(check == true){
			for(i=0;i<new_orders.length;i++){
				if(new_orders[i].checked == true){
					check = false;
					break;
				}
			}
		}
	}
	
	if(check == true){
		alert('Please select any order set.');
		return false;
	}
}
	
function getOtherVal(obj,num,chk,id){	
	if(obj.value == 'Other'){
		switch(chk){
			case "site":
				document.getElementById("selected_orders_option_val_"+num).style.display = 'none';
				document.getElementById("selected_orders_text_val_"+num).style.display = 'table-row';
				obj.selectedIndex = 0;
				document.getElementById("select_orders_site_txt["+id+"]").focus();
			break;
			case "Priority":
				document.getElementById("selected_priority_orders_option_val_"+num).style.display = 'none';
				document.getElementById("selected_priority_orders_text_val_"+num).style.display = 'table-row';
				obj.selectedIndex = 0;
				document.getElementById("select_orders_priority_txt["+id+"]").focus();
			break;
		}
	}	
}

function hideTxt(obj,num,chk){
	if(obj.value == ''){
		switch(chk){
			case 'site':
				document.getElementById("selected_orders_text_val_"+num).style.display = 'none';
				document.getElementById("selected_orders_option_val_"+num).style.display = 'table-row';	
			break;
			case 'Priority':
				document.getElementById("selected_priority_orders_text_val_"+num).style.display = 'none';
				document.getElementById("selected_priority_orders_option_val_"+num).style.display = 'table-row';	
			break;
		}
	}
}

function selOrderSet(objName,obj,num,id){
	var orderTypeObj = document.getElementsByName('orderType['+id+'][]');
	for(i=0;i<orderTypeObj.length;i++){
		var inf = false;
		if(orderTypeObj[i].value ){
			var infVal = orderTypeObj[i].value.split(',');
			for(u in infVal){
				if(infVal[u] == 'Information'){
					inf = true;
					break;
				}
			}
		}
	}
	var obj_val = obj.value;
	if(objName == 'site'){
		var txtdis = 'none';
		var spandis = 'table-row';
		if(obj_val == 'Other'){
			txtdis = 'table-row';
			spandis = 'none';
		}
		var siteTxtObj = document.getElementsByName('new_orders_text_val_id['+id+'][]');
		var siteDropSpanObj = document.getElementsByName('new_orders_option_val_id['+id+'][]');
		var siteDropObj = document.getElementsByName('new_orders_site_id['+id+'][]');
		var siteTxtValObj = document.getElementsByName('new_orders_site_txt_id['+id+'][]');	
		

		for(i=0;i<siteDropObj.length;i++){
			siteTxtValObj[i].value = '';
			if(siteTxtObj[i]){	siteTxtObj[i].style.display = txtdis;}
			if(siteDropObj[i].value == ''){
				siteDropObj[i].value = obj.value;
			}
			if(siteDropSpanObj[i]) {siteDropSpanObj[i].style.display = spandis;}
		}
	}
	else if(objName == 'schedule'){		
		var dis = 'none';
		if(obj_val == 'Days' ||obj_val == 'Weeks' || obj_val == 'Months' || obj_val == 'Years'){
			dis = 'table-row';
		}
		document.getElementById("new_orders_when_day_"+num).style.display = dis;
		var scheduleDayObj = document.getElementsByName("new_schedule_day_td["+id+"][]");
		var scheduleObj = document.getElementsByName("new_order_schedule["+id+"][]");
		for(i=0;i<scheduleObj.length;i++){
			if(scheduleObj[i].value == ''){
				scheduleObj[i].value = obj_val;
				if(scheduleDayObj[i]) { scheduleDayObj[i].style.display = dis;}
			}
		}
		
	}
	else{		
		var txtSpan = document.getElementsByName('new_priority_orders_text_span['+id+'][]');
		var txtObj = document.getElementsByName('new_orders_priority_txt['+id+'][]');
		var dropSpan = document.getElementsByName('new_priority_orders_option_span['+id+'][]');
		var dropObj = document.getElementsByName('new_orders_priority['+id+'][]');
		var txtdis = 'none';
		var spandis = 'table-row';
		if(obj_val == 'Other'){
			txtdis = 'table-row';
			spandis = 'none';
		}
		for(i=0;i<txtSpan.length;i++){
			txtObj[i].value = '';			
			txtSpan[i].style.display = txtdis;
			if(dropObj[i].value == ''){
				dropObj[i].value = obj.value;
			}
			dropSpan[i].style.display = spandis;
		}
	}
}

function otherOrderVal(obj,num,chk,id){
	if(obj.value == 'Other'){
		switch(chk){
			case 'site':
				obj.selectedIndex = 0;
				var txtSpanObj = document.getElementsByName("new_orders_text_val_id["+id+"][]");
				var dropSpanObj = document.getElementsByName("new_orders_option_val_id["+id+"][]");
				var txtObj = document.getElementsByName("new_orders_site_txt_id["+id+"][]");
				for(i=0;i<txtSpanObj.length;i++){
					if(i == num){
						dropSpanObj[i].style.display = 'none';
						txtSpanObj[i].style.display = 'table-row';
						txtObj[i].focus();
					}
				}
			break;
			case 'priority':
				obj.selectedIndex = 0;
				var txtSpanObj = document.getElementsByName("new_priority_orders_text_span["+id+"][]");
				var dropSpanObj = document.getElementsByName("new_priority_orders_option_span["+id+"][]");
				var txtObj = document.getElementsByName("new_orders_priority_txt["+id+"][]");
				for(i=0;i<txtSpanObj.length;i++){
					if(i == num){
						dropSpanObj[i].style.display = 'none';
						txtSpanObj[i].style.display = 'table-row';
						txtObj[i].focus();
					}
				}
			break;
		}
	}
	else if(obj.value == ''){
		switch(chk){
			case 'site':
				var txtSpanObj = document.getElementsByName("new_orders_text_val_id["+id+"][]");
				var dropSpanObj = document.getElementsByName("new_orders_option_val_id["+id+"][]");
				var dropObj = document.getElementsByName("new_orders_site_id["+id+"][]");
				for(i=0;i<txtSpanObj.length;i++){
					if(i == num){
						dropSpanObj[i].style.display = 'table-row';
						txtSpanObj[i].style.display = 'none';
						dropObj[i].selectedIndex = 0;
					}
				}
			break;
			case 'priority':
				var txtSpanObj = document.getElementsByName("new_priority_orders_text_span["+id+"][]");
				var dropSpanObj = document.getElementsByName("new_priority_orders_option_span["+id+"][]");
				var dropObj = document.getElementsByName("new_orders_priority_txt["+id+"][]");
				for(i=0;i<txtSpanObj.length;i++){
					if(i == num){
						dropSpanObj[i].style.display = 'table-row';
						txtSpanObj[i].style.display = 'none';
						dropObj[i].selectedIndex = 0;
					}
				}
			break;
		}
	}
}

function ordersShceduleDay(obj,id,cnt){
	var dis = 'none';
	obj_val = obj.value;
	if(obj_val == 'Days' ||obj_val == 'Weeks' || obj_val == 'Months' || obj_val == 'Years'){
		dis = 'table-row';
	}	
	var dayObj = document.getElementsByName("new_schedule_day_td["+id+"][]");
	for(i=0;i<dayObj.length;i++){
		if(i == cnt){
			dayObj[i].style.display = dis;
		}
	}
}

function getOtherOrderVal(obj,num,chk,id){
	selOrderSet(chk,obj,num,id);
	if(obj.value == 'Other'){
		switch(chk){
			case "site":
				document.getElementById("new_orders_option_val_"+num).style.display = 'none';
				document.getElementById("new_orders_text_val_"+num).style.display = 'table-row';
				obj.selectedIndex = 0;
				document.getElementById("new_orders_site_txt["+id+"]").focus();
			break;
			case "Priority":
				document.getElementById("new_priority_orders_option_val_"+num).style.display = 'none';
				document.getElementById("new_priority_orders_text_val_"+num).style.display = 'table-row';
				obj.selectedIndex = 0;
				document.getElementById("new_orders_priority_txt["+id+"]").focus();
			break;
		}
	}	
}

function changeDayValue(obj,id){
	var fldObj = document.getElementsByName("new_orders_set_when_day["+id+"][]");
	for(i=0;i<fldObj.length;i++){
		fldObj[i].selectedIndex = obj.selectedIndex;
	}
}

function getOtherOrdersValue(obj,num,chk,id){
	if(obj.value == 'Other'){
		switch(chk){
			case 'site':
				document.getElementById("newOrdersOptionVal_"+num).style.display = 'none';
				document.getElementById("newOrdersTextVal_"+num).style.display = 'table-row';
				document.getElementById("newOrdersSite["+id+"]").selectedIndex = 0;
				document.getElementById("newOrdersSiteTxt["+id+"]").focus();
			break;
			case 'Priority':
				document.getElementById("newPriorityOrdersOptionVal_"+num).style.display = 'none';
				document.getElementById("newPriorityOrdersTextVal_"+num).style.display = 'table-row';
				document.getElementById("newOrdersPriority["+id+"]").selectedIndex = 0;
				document.getElementById("newOrdersPriorityText["+id+"]").focus();
			break;
		}
	}
	else if(obj.value == ''){
		switch(chk){
			case 'site':
				document.getElementById("newOrdersTextVal_"+num).style.display = 'none';				
				document.getElementById("newOrdersOptionVal_"+num).style.display = 'table-row';
				document.getElementById("newOrdersSite["+id+"]").selectedIndex = 0;
			break;
			case'schedule':
				document.getElementById("new_orders_when_day_"+num).style.display = 'none';
			break;
			case 'Priority':
				document.getElementById("newPriorityOrdersTextVal_"+num).style.display = 'none';
				document.getElementById("newPriorityOrdersOptionVal_"+num).style.display = 'table-row';
				document.getElementById("newOrdersPriority["+id+"]").selectedIndex = 0;				
			break;
		}
	}
	else{
		switch(chk){
			case'schedule':
				obj_val = obj.value;
				var dis = 'none';
				if(obj_val == 'Days' ||obj_val == 'Weeks' || obj_val == 'Months' || obj_val == 'Years'){
					dis = 'table-row';
				}
				document.getElementById("new_orders_when_day_"+num).style.display = dis;
			break;
		}
	}
}

function hideOrderTxt(obj,num,chk,id){
	if(obj.value == ''){
		switch(chk){
			case 'site':
				var siteTxtObj = document.getElementsByName('new_orders_text_val_id['+id+'][]');
				var siteDropSpanObj = document.getElementsByName('new_orders_option_val_id['+id+'][]');
				var siteDropObj = document.getElementsByName('new_orders_site_id['+id+'][]');
				var siteTxtValObj = document.getElementsByName('new_orders_site_txt_id['+id+'][]');
				for(i=0;i<siteDropObj.length;i++){
					siteTxtValObj[i].value = '';
					siteTxtObj[i].style.display = 'none';
					siteDropObj[i].selectedIndex = 0;
					siteDropSpanObj[i].style.display = 'table-row';
				}				
				document.getElementById("new_orders_text_val_"+num).style.display = 'none';
				document.getElementById("new_orders_option_val_"+num).style.display = 'table-row';	
			break;
			case 'Priority':
				var txtSpan = document.getElementsByName('new_priority_orders_text_span['+id+'][]');
				var txtObj = document.getElementsByName('new_orders_priority_txt['+id+'][]');
				var dropSpan = document.getElementsByName('new_priority_orders_option_span['+id+'][]');
				var dropObj = document.getElementsByName('new_orders_priority['+id+'][]');
				for(i=0;i<txtSpan.length;i++){
					txtObj[i].value = '';
					txtSpan[i].style.display = 'none';
					dropObj[i].selectedIndex = 0;
					dropSpan[i].style.display = 'table-row';
				}
				document.getElementById("new_priority_orders_text_val_"+num).style.display = 'none';
				document.getElementById("new_priority_orders_option_val_"+num).style.display = 'table-row';	
			break;
		}
	}
	else{
		switch(chk){
			case 'site':
				var siteTxtObj = document.getElementsByName('new_orders_site_txt_id['+id+'][]');
				for(i=0;i<siteTxtObj.length;i++){
					siteTxtObj[i].value = obj.value;
				}				
			break;
			case 'Priority':
				var new_orders_priority_txt = document.getElementsByName('new_orders_priority_txt['+id+'][]');
				for(i=0;i<new_orders_priority_txt.length;i++){
					new_orders_priority_txt[i].value = obj.value;
				}
			break;
		}
	}
}

function getReason(obj,id,objId,cnt){
	var val = 'none';
	if(obj.checked == false){
		val = 'table-row';
	}
	else{
		var txt_obj = document.getElementsByName("reasonText["+objId+"][]");
		for(i=0;i<txt_obj.length;i++){
			if(i == cnt){
				txt_obj[i].value = '';		
			}
		}
	}
	document.getElementById("reasonText_"+id).style.display = val
}


function delcheck(id,num){
	if(confirm('Sure! you want to delete?')){
		window.location.href = 'chart_notes_order_set.php?delete_id='+id+'&plan_num='+num;
	}
	else{
		return false;
	}
}

//--- Date check funtion
function validDateCheck(StartDate,EndDate){
	var Start_Date = Date.parse(document.getElementById(StartDate).value)
	var End_Date = Date.parse(document.getElementById(EndDate).value)
	var return_val = false;
	if(Start_Date != '' && End_Date != '' && Start_Date > End_Date){
		return_val = true;		
	}
	return return_val;
}

function show_submit_button (btnArr){
	var btn_disp="";
	for(i in btnArr){
			var btnval = btnArr[i].split('__');
	}
	top.document.getElementById("page_buttons").innerHTML=btn_disp;
}

function hide_submit_button()
{
	top.document.getElementById("page_buttons").innerHTML="";
}

//--- Validate phone number ---
function validateTel(telObj,fieldNme){
	if(!fieldNme){
		fieldNme='phone number';
	}
	if(telObj.value){
		var extracted_val = allowNum(telObj.value,fieldNme);
		if(extracted_val == false){				
			telObj.value='';
			telObj.focus();
			return false;
		}else{
			var strLen = extracted_val.split('');
			telObj.value = '';
			strLen.splice(3,0,"-");
			strLen.splice(7,0,"-");
			strLen.join();
			for(var i=0; i<strLen.length; i++){
				telObj.value += strLen[i];
			}
		}
	}
}

function allowNum(val,fieldNme){
	var extracted_val = val.replace(/(-|\)|\()/g,'');
	if(isNaN(extracted_val) || extracted_val.length != 10){
		alert("Please enter the valid "+fieldNme);
		return false;
	}else{
		return extracted_val;
	}
}

//--- Start Function For search Patient -------
function searchPatient(){	
	var name = document.getElementById("patient").value;
	var findBy = document.getElementById("findBy").value;
	var msg = "";
	if(name == ""){
		msg = "Please Fill Name For Search.\n";
	}
	if(findBy == ""){
		msg += "Please Select Field For Search.";	
	}
	if(msg){
		alert(msg);
	}
	else{
		if(isNaN(name) || (isNaN(name)==false && findBy=='Ins.Policy')){
			window.open("../reports/searchResult.php?val="+name+"&fld="+findBy,"mywindow","width=800,height=500,scrollbars=yes");
		}
		else{
			getPatientName(name);
		}
	}
	return false;
}


var xmlHttp;
function getPatientName(id){
	xmlHttp = GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return;
	}		
	var url="../main/search_todo_patient.php?id="+id;
	xmlHttp.onreadystatechange=stateChanges;     
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
}

function stateChanges(){
	if (xmlHttp.readyState==4)
	{ 
		result = xmlHttp.responseText;
		var val = result.split('__');
		if(val[0]){
			document.getElementById("patient").value = val[0];
			document.getElementById("patientId").value = val[1];
		}
		else{
			document.getElementById("patient").select();
			alert("Patient not exists");
		}		
		if(parent.parent.show_loading_image){
			parent.parent.show_loading_image('none');
		}
	}
	else{
		if(parent.parent.show_loading_image){
			parent.parent.show_loading_image('block');
		}
	}
}

function searchPatient2(obj){
	var patientdetails = obj.value.split(':');
	if(isNaN(patientdetails[0]) == false){
		document.getElementById("patientId").value = patientdetails[0];
		document.getElementById("patient").value = patientdetails[1];
	}
}

function getvalue(name,id){
	document.getElementById("patient").value = name;
	document.getElementById("patientId").value = id;
}

function pat_check(val){
	if(val == ''){
		document.getElementById("patientId").value = '';
	}
}

function getPosition(e) {
	e = window.event;
	var cursor = {x:0, y:0};
	var de = document.documentElement;
	var b = document.body;
	cursor.x = e.clientX + 
		(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
	cursor.y = e.clientY + 
		(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
	return cursor;
}

function show_instructions(val,dis){
	val = '';
	if(dis == 'block' && val != ''){
		var position_val = getPosition();
		document.getElementById("show_ins_div").style.top = position_val.y;
		document.getElementById("show_ins_div").style.left = position_val.x + parseInt(10);
		document.getElementById("show_ins_div").innerHTML = val;
		document.getElementById("show_ins_div").style.display = 'block';
	}
	else{
		document.getElementById("show_ins_div").style.display = 'none';
	}
}

function re_arrange_ins(val){
	var check = false;
	
	var compIdObj = document.getElementsByName("compId[]");
	for(i=0;i<compIdObj.length;i++){
		if(compIdObj[i].value != ''){
			check = true;
			break;
		}
	}
	
	if(check == true){
		document.getElementById("re_arrange_id").style.display = val;
	}
	else{
		alert('Patient does not have any insurance');
	}
}
	
function popup(divid,selid,c_div)
{
	if(c_div){	
		cl_div=c_div.split('__');
		for(i in cl_div){
		document.getElementById(cl_div[i]).style.display='none';
		}
	}
	document.getElementById(divid).style.display='block';
	var selid1=selid+'1';
	var strToSendAttrib = "";
	var selectedArray=new Array(); 
	var availableList = document.getElementById(selid);
	var addIndex = availableList.selectedIndex;
	j=0;
	for(i = availableList.length-1; i >= 0 ; i--)
	{            		  
		if(availableList.options.item(i).selected == true)
		{
			strToSendAttrib = availableList.options.item(i).value;
			selectedArray[j]=strToSendAttrib;
			j++;		
		}											 
	}
	var availableListOpener=document.getElementById(selid1);
	n=0;
	for(k = availableListOpener.length-1; k >= 0 ;k--)
	{  				         		  
		if(availableListOpener.options.item(k).value == selectedArray[n])
		{
			availableListOpener.options.item(k).selected = true;
			n++;	
		}
		else
		{
			availableListOpener.options.item(k).selected = false;
		}													 
	}
	
}

function selected(divid,selid)
{
	var selid1=selid+'1';
	var strToSendAttrib = "";
	var selectedArray=new Array(); 
	var availableList = document.getElementById(selid1);
	var addIndex = availableList.selectedIndex;
/*	if(addIndex < 0)
	{
		alert("Please select to continue.");
		return;
	}*/
	j=0;
	for(i = availableList.length-1; i >= 0 ; i--)
	{            		  
		if(availableList.options.item(i).selected == true)
		{
			strToSendAttrib = availableList.options.item(i).value;
			selectedArray[j]=strToSendAttrib;
			j++;		
		}											 
	}
	var availableListOpener=document.getElementById(selid);
	n=0;
	for(k = availableListOpener.length-1; k >= 0 ;k--)
	{  				         		  
		if(availableListOpener.options.item(k).value == selectedArray[n])
		{
			availableListOpener.options.item(k).selected = true;
			n++;	
		}
		else
		{
			availableListOpener.options.item(k).selected = false;
		}													 
	}
	document.getElementById(divid).style.display='none';
}

function close_popup(divid)
{
		document.getElementById(divid).style.display='none';
}

function show_os_status(obj){
	var val = 'none';
	if(obj.value == 'all_show'){
		val = 'table-row';
	}
	var row_obj = document.getElementsByName("delected_rows_id[]");
	for(i=0;i<row_obj.length;i++){
		row_obj[i].style.display = val;
	}
}


Date.prototype.format=function(format){var returnStr='';var replace=Date.replaceChars;for(var i=0;i<format.length;i++){var curChar=format.charAt(i);if(replace[curChar]){returnStr+=replace[curChar].call(this);}else{returnStr+=curChar;}}return returnStr;};Date.replaceChars={shortMonths:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],longMonths:['January','February','March','April','May','June','July','August','September','October','November','December'],shortDays:['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],longDays:['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],d:function(){return(this.getDate()<10?'0':'')+this.getDate();},D:function(){return Date.replaceChars.shortDays[this.getDay()];},j:function(){return this.getDate();},l:function(){return Date.replaceChars.longDays[this.getDay()];},N:function(){return this.getDay()+1;},S:function(){return(this.getDate()%10==1&&this.getDate()!=11?'st':(this.getDate()%10==2&&this.getDate()!=12?'nd':(this.getDate()%10==3&&this.getDate()!=13?'rd':'th')));},w:function(){return this.getDay();},z:function(){return"Not Yet Supported";},W:function(){return"Not Yet Supported";},F:function(){return Date.replaceChars.longMonths[this.getMonth()];},m:function(){return(this.getMonth()<9?'0':'')+(this.getMonth()+1);},M:function(){return Date.replaceChars.shortMonths[this.getMonth()];},n:function(){return this.getMonth()+1;},t:function(){return"Not Yet Supported";},L:function(){return(((this.getFullYear()%4==0)&&(this.getFullYear()%100!=0))||(this.getFullYear()%400==0))?'1':'0';},o:function(){return"Not Supported";},Y:function(){return this.getFullYear();},y:function(){return(''+this.getFullYear()).substr(2);},a:function(){return this.getHours()<12?'am':'pm';},A:function(){return this.getHours()<12?'AM':'PM';},B:function(){return"Not Yet Supported";},g:function(){return this.getHours()%12||12;},G:function(){return this.getHours();},h:function(){return((this.getHours()%12||12)<10?'0':'')+(this.getHours()%12||12);},H:function(){return(this.getHours()<10?'0':'')+this.getHours();},i:function(){return(this.getMinutes()<10?'0':'')+this.getMinutes();},s:function(){return(this.getSeconds()<10?'0':'')+this.getSeconds();},e:function(){return"Not Yet Supported";},I:function(){return"Not Supported";},O:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+'00';},P:function(){return(-this.getTimezoneOffset()<0?'-':'+')+(Math.abs(this.getTimezoneOffset()/60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()/60))+':'+(Math.abs(this.getTimezoneOffset()%60)<10?'0':'')+(Math.abs(this.getTimezoneOffset()%60));},T:function(){var m=this.getMonth();this.setMonth(0);var result=this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/,'$1');this.setMonth(m);return result;},Z:function(){return-this.getTimezoneOffset()*60;},c:function(){return this.format("Y-m-d")+"T"+this.format("H:i:sP");},r:function(){return this.toString();},U:function(){return this.getTime()/1000;}};

function switch_ins(val,type){
	var ins_obj = document.getElementsByName("compId[]");	
	if(val == 'Primary'){
		document.reArrangeFrm.name_Secondary[0].checked = false;
		document.reArrangeFrm.name_Secondary[1].checked = false;
		document.reArrangeFrm.name_Secondary[2].checked = false;
		document.reArrangeFrm.name_Tertiary[0].checked = false;
		document.reArrangeFrm.name_Tertiary[1].checked = false;
		document.reArrangeFrm.name_Tertiary[2].checked = false;
		
		if(type == 'Primary'){			
			document.reArrangeFrm.name_Secondary[1].checked = true;
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Secondary[0].checked = true;
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Secondary[1].checked = true;
			document.reArrangeFrm.name_Tertiary[0].checked = true;
		}
	}
	else if(val == 'Secondary'){
		document.reArrangeFrm.name_Primary[0].checked = false;
		document.reArrangeFrm.name_Primary[1].checked = false;
		document.reArrangeFrm.name_Primary[2].checked = false;
		document.reArrangeFrm.name_Tertiary[0].checked = false;
		document.reArrangeFrm.name_Tertiary[1].checked = false;
		document.reArrangeFrm.name_Tertiary[2].checked = false;
		if(type == 'Primary'){
			document.reArrangeFrm.name_Primary[1].checked = true;				
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Primary[0].checked = true;				
			document.reArrangeFrm.name_Tertiary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Primary[0].checked = true;				
			document.reArrangeFrm.name_Tertiary[1].checked = true;
		}
	}
	else if(val == 'Tertiary'){
		document.reArrangeFrm.name_Primary[0].checked = false;
		document.reArrangeFrm.name_Primary[1].checked = false;
		document.reArrangeFrm.name_Primary[2].checked = false;
		document.reArrangeFrm.name_Secondary[0].checked = false;
		document.reArrangeFrm.name_Secondary[1].checked = false;
		document.reArrangeFrm.name_Secondary[2].checked = false;
		if(type == 'Primary'){			
			document.reArrangeFrm.name_Primary[2].checked = true;
			document.reArrangeFrm.name_Secondary[1].checked = true;
		}
		else if(type == 'Secondary'){
			document.reArrangeFrm.name_Primary[0].checked = true;
			document.reArrangeFrm.name_Secondary[2].checked = true;
		}
		else if(type == 'Tertiary'){
			document.reArrangeFrm.name_Primary[0].checked = true;
			document.reArrangeFrm.name_Secondary[1].checked = true;
		}
	}
}

function getCookie(c_name){
	if (document.cookie.length>0){
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1){
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
				return unescape(document.cookie.substring(c_start,c_end));
    	}
  	}
	return "";
}

//get cookie details
function Get_Cookie(check_name){
   // first we'll split this cookie up into name/value pairs
	// note: document.cookie only returns name=value, not the other components
	var a_all_cookies = document.cookie.split( ';' );
	var a_temp_cookie = '';
	var cookie_name = '';
	var cookie_value = '';
	var b_cookie_found = false; // set boolean t/f default f

	for ( i = 0; i < a_all_cookies.length; i++ )
	{
		// now we'll split apart each name=value pair
		a_temp_cookie = a_all_cookies[i].split( '=' );
		// and trim left/right whitespace while we're at it
		cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');
		// if the extracted name matches passed check_name
		if ( cookie_name == check_name )
		{
			b_cookie_found = true;
			// we need to handle case where cookie has no value but exists (no = sign, that is):
			if ( a_temp_cookie.length > 1 )
			{
				cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
			}
			// note that in cases where cookie is initialized but no value, null is returned
			return cookie_value;
			break;
		}
		a_temp_cookie = null;
		cookie_name = '';
	}
	if ( !b_cookie_found )
	{
		return null;
	}
} 


// this deletes the cookie when called
function Delete_Cookie( name, path, domain ) {
	if ( Get_Cookie( name ) ) document.cookie = name + "=" +
	( ( path ) ? ";path=" + path : "") +
	( ( domain ) ? ";domain=" + domain : "" ) +
	";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}

function copy_insurance_div(dis,ins_com){
	document.getElementById('copy_ins_name').value = '';
	document.getElementById('copy_ins_comp_id').style.display = dis;
	
	if(dis == 'block'){
		document.getElementById('copy_ins_name').value = ins_com;
	}
}

function insOpenCaseSummary(url){
	window.open(url,"insOpenCaseSummaryWin","width=800, height=210, top=150, left= 250, resizable=yes, scrollbars=yes");	
	
}
var ins_type;

function existInsCheck(patient_id){
	var copy_from_ins = document.getElementById("copy_from_ins_case").value;
	var copy_to_ins_case = document.getElementById("copy_to_ins_case").value;
	var copy_ins_data_from = (document.getElementById("copy_ins_data_from")) ? document.getElementById("copy_ins_data_from").value.split('__') : new Array();
	var copy_ins_data_to = document.getElementById("copy_ins_data_to").value;
	
	if(copy_ins_data_from[0] == ''){
		top.falert('There is no insurance company for copy.');
		return 
	}
	if(copy_from_ins == copy_to_ins_case && copy_ins_data_from[0] == copy_ins_data_to){
		top.fAlert('Copy same insurance case and company type not allowed.');
		return;
	}

	ins_type = copy_ins_data_to;
	var ins_case_id = copy_to_ins_case;
	
	xmlHttp=GetXmlHttpObject();
	if(xmlHttp==null)
	{
		top.fAlert ("Browser does not support HTTP Request")
		return
	}
	else{
		url = 'exists_ins_case_check.php?ins_type='+ins_type+'&ins_case_id='+ins_case_id;
		url += '&patient_id='+patient_id;
		xmlHttp.onreadystatechange=chkExistsIns;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);		
	}
}

function chkExistsIns(){
	if(xmlHttp.readyState==4){
		var returnval = xmlHttp.responseText;
		if(returnval > 0){
			alert('Please expire previous '+ins_type+' insurance company.');
		}
		else{
			document.getElementById("copy_ins_submit_txt").value = 'Submit';
			document.getElementById("loading_img").style.display = 'table-row';
			document.copy_ins_form.submit();
		}
	}
}

function change_case_chk(case_id){
	if(case_id > 0){
		xmlHttp=GetXmlHttpObject();
		if(xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request")
			return
		}
		else{
			url = 'get_ins_id.php?caseId='+case_id;
			xmlHttp.onreadystatechange=getInsIdFun;
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);		
		}
	}
}

function getInsIdFun(){
	if(xmlHttp.readyState==4){
		var ins_val = xmlHttp.responseText;
		document.getElementById("change_ins_comp").innerHTML = ins_val;
		document.getElementById("loading_img").style.display = 'none';
	}
	else{
		document.getElementById("loading_img").style.display = 'table-row';
	}
}

function ToggleED(ARRobj,Caller){
//	alert(typeof(ARRobj));
/*
	CallerType = Caller.type;
	for(i=0;i<ARRobj.length;i++){
		switch(CallerType){
			case 'checkbox':{
				if(!Caller.checked){
					if(document.getElementById(ARRobj[i]).type=="text")
					document.getElementById(ARRobj[i]).value='';
					else if(document.getElementById(ARRobj[i]).type=="checkbox")
					document.getElementById(ARRobj[i]).checked=false;					
					document.getElementById(ARRobj[i]).disabled='disabled';
				}else if(Caller.checked){
					document.getElementById(ARRobj[i]).disabled='';
				}		
				break;
			}
			default:{
				// do nothing.	
			}
		}
	}	
	*/
}

function dateDisplay(val){
	document.getElementById("Start_date").disabled=val;
	document.getElementById("End_date").disabled=val;
}

//----- Start Function For Calender -------------	
function y2k(number)
{
	return (number < 1000)? number+1900 : number;
}	

var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());

function newWindow(q)
{
	window.open('../common/mycal.php?md='+q,'imwemr','width=200,height=250,top=200,left=300');
}
function newWindowReportClinical(q)
{
	window.open('../../common/mycal.php?md='+q,'imwemr','width=200,height=250,top=200,left=300');
}
function restart(obj)
{
	document.getElementById(obj).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
}

function padout(number)
{
	return (number < 10) ? '0' + number : number;
}	
//----- End Function For Calender -------------

// ---- Start Check numeric value --------------
function check_input(val){
	var reg_alph = new RegExp("[^0-9\.]+");
	var reg_1dot = new RegExp("[\.]+");
	var reg_1dec = new RegExp("[0-9]+\.[0-9]{1,20}");
	
	if(reg_alph.test(val) == false){
		/*if(reg_1dot.test(val) == true){
			if(reg_1dec.test(val) == false){
				return true;
			}
		}*/
		return false;
	}else{
		return true;
	}
	
}
// ---- Start Check numeric value --------------

function isNumberKey(evt) { 
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false; 
	}
	else{
		return true; 
	}
}