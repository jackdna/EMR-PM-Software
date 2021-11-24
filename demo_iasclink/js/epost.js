
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019

//309 and 319

//var xmlHttp
var tCloseOut;
function showEpost(id,pConfIdEpost){ 
	//alert(id)
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	 }
	var url="common/epost_pop.php"
	url=url+"?tableID="+id
	//url=url+"&sid="+Math.random()
	url=url+"&pConfId="+pConfIdEpost
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function stateChanged(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("post").style.display="block"; 
		//alert(xmlHttp.responseText);
		document.getElementById("post").innerHTML=xmlHttp.responseText; 
	} 
}
var consentTemplateIdEpost='';
function deleteEpost(id,consentTemplateIdEpost,consentMultipleAutoIncrId,pConfIdDelete){
	//alert(consentTemplateIdEpost);
	if(!confirm("Do you really want to delete the epost")) {
		return false;
	}else{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		if(xmlHttp.readyState==4){			
		}
		var url="common/epost_delete.php"
		url=url+"?tableID="+id
		url=url+"&epostTemplateId="+consentTemplateIdEpost
		url=url+"&consentMultipleAutoIncrId="+consentMultipleAutoIncrId
		url=url+"&pConfId="+pConfIdDelete
		//url=url+"&sid="+Math.random()
		xmlHttp.onreadystatechange=stateChangeds
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}
function addEpost(text,tablename,consentMultipleId,consentMultipleAutoIncrId,hiddPurgestatus,pConfId1,patient_id1){
		//alert(consentMultipleAutoIncrId);
	if(text=="") {
		alert("Please enter ePost");
		return false;
	}else{
		
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null){
			alert ("Browser does not support HTTP Request")
			return
		}
		if(xmlHttp.readyState==4){			
		}
		var url="common/epost_add.php"
		url=url+"?text="+text
		url=url+"&tablename="+tablename
		url=url+"&consentMultipleId="+consentMultipleId
		url=url+"&consentMultipleAutoIncrId="+consentMultipleAutoIncrId
		url=url+"&hiddPurgestatus="+hiddPurgestatus
		url=url+"&pConfId="+pConfId1
		url=url+"&patient_id="+patient_id1
		
		//url=url+"&sid="+Math.random()
		xmlHttp.onreadystatechange=stateChangeds
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}

function closeEpost(){
	if(document.getElementById("post").style.display=="block"){
	document.getElementById("post").style.display="none"; }
}
function stateChangeds(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
	 	//window.location.reload();
		//top.main_frmInner_id.reload();
		//alert(xmlHttp.responseText);
		document.getElementById("epostDelId").innerHTML=xmlHttp.responseText;
	 } 
}
function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	 }catch (e){
		//Internet Explorer
	 try{
		 xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	}catch (e){
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	}
	return xmlHttp;
}


//SET HIDDEN VALUE FOR CALCULATOR
function calOpenCloseFun() {
	document.getElementById("hiddCalPopId").value = "calOpenYes";
}
function calCloseFun() {
	if(document.getElementById("hiddCalPopId")) {
		if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
			if(document.getElementById("cal_pop").style.display == "block"){
				document.getElementById("cal_pop").style.display = "none"; 
				document.getElementById("hiddCalPopId").value = "";
			}
			
			if(document.getElementById("temp_pop")){	
				if(document.getElementById("temp_pop").style.display == "block"){
					document.getElementById("temp_pop").style.display = "none"; 
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			if(document.getElementById("opRoomDiopterPop")){	
				if(document.getElementById("opRoomDiopterPop").style.display == "block"){
					document.getElementById("opRoomDiopterPop").style.display = "none"; 
					document.getElementById("hiddCalPopId").value = "";
				}
			}
		
		}
		
	}
}
//END SET HIDDEN VALUE FOR CALCULATOR

//show div for the selection of values

function getShowpos(posTop,posLeft,flag)
	{
		
		var t=document.getElementById("rowK").value;
		document.getElementById("bp").value = flag;
		document.getElementById("cal_pop").style.display = "block";
		document.getElementById("temp_pop").style.display = "none";
		document.getElementById("opRoomDiopterPop").style.display = "none";
		document.getElementById("cal_pop").style.left = posLeft;
		document.getElementById("cal_pop").style.top = t*20+110;
		if(document.getElementById("hiddCalPopId")) {
			document.getElementById("hiddCalPopId").value = "";
			if(document.getElementById("hiddCalPopId").value == "") {
				tCloseOut = setTimeout("calOpenCloseFun()", 100);
			}
		}
		//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
			//if(top.frames[0].frames[0]){
				//top.frames[0].setPNotesHeight();
			//}
		//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
	}

var  multiplyValue1 = 20;
function getShowNewPos(posTop,posLeft,flag,multiplyValue) {
	var t =0;
	
	if(multiplyValue) {
		multiplyValue1 = multiplyValue;
	}
	if(document.getElementById("rowK")) {
		var t=document.getElementById("rowK").value;
		if(t!=0) {
			t=t*multiplyValue1;
		}
	}
	
	document.getElementById("bp").value = flag;
	document.getElementById("cal_pop").style.display = "block";
	document.getElementById("temp_pop").style.display = "none";
	document.getElementById("opRoomDiopterPop").style.display = "none";
	document.getElementById("cal_pop").style.left = posLeft;
	document.getElementById("cal_pop").style.top = t+parseInt(posTop);
	if(document.getElementById("hiddCalPopId")) {
		document.getElementById("hiddCalPopId").value = "";
		if(document.getElementById("hiddCalPopId").value == "") {
			tCloseOut = setTimeout("calOpenCloseFun()", 100);
		}
	}
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
		//if(top.frames[0].frames[0]){
			//top.frames[0].setPNotesHeight();
		//}
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
}

//CODE TO DIOPTER INOPERATING ROOM RECORD
var  multiplyOpRoomValue1 = 20;
function getShowOpRoomPos(posTop,posLeft,flag,multiplyOpRoomValue) {
	var t =0;
	
	if(multiplyOpRoomValue) {
		multiplyOpRoomValue1 = multiplyOpRoomValue;
	}
	if(document.getElementById("rowK")) {
		var t=document.getElementById("rowK").value;
		if(t!=0) {
			t=t*multiplyOpRoomValue1;
		}
	}
	
	document.getElementById("bp").value = flag;
	
	document.getElementById("opRoomDiopterPop").style.display = "block";
	document.getElementById("cal_pop").style.display = "none";
	document.getElementById("temp_pop").style.display = "none";
	document.getElementById("opRoomDiopterPop").style.left = posLeft;
	document.getElementById("opRoomDiopterPop").style.top = t+parseInt(posTop);
	if(document.getElementById("hiddCalPopId")) {
		document.getElementById("hiddCalPopId").value = "";
		if(document.getElementById("hiddCalPopId").value == "") {
			tCloseOut = setTimeout("calOpenCloseFun()", 100);
		}
	}
	//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
		if(top.frames[0].frames[0]){
			top.frames[0].setPNotesHeight();
		}
	//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
}


//END CODE FOR DIOPTER IN OPERATING ROOM RECORD


function getShow(posTop,posLeft,flag)
	{
		document.getElementById("bp").value = flag;
		document.getElementById("cal_pop").style.display = "block";
		document.getElementById("temp_pop").style.display = "none";
		document.getElementById("cal_pop").style.left = posLeft;
		document.getElementById("cal_pop").style.top = posTop;
		if(document.getElementById("hiddCalPopId")) {
			document.getElementById("hiddCalPopId").value = "";
			if(document.getElementById("hiddCalPopId").value == "") {
				tCloseOut = setTimeout("calOpenCloseFun()", 100);
			}
		}
		//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
			if(top.frames[0].frames[0]){
				top.frames[0].setPNotesHeight();
			}
		//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	}
	function getShowTemp(posTop,posLeft,flag)
	{
		//document.getElementById("bp_temp").value ='';
		if(document.getElementById("rowK")) {
			var t=document.getElementById("rowK").value;
		}else {
			var t=0;
		}
		if(t!=0) {
			t=t*20;
		}
		document.getElementById("bp").value = flag;
		document.getElementById("temp_pop").style.display = "block";
		document.getElementById("cal_pop").style.display = "none";
		document.getElementById("temp_pop").style.left = posLeft;
		document.getElementById("temp_pop").style.top = t+parseInt(posTop);
		if(document.getElementById("hiddCalPopId")) {
			document.getElementById("hiddCalPopId").value = "";
			if(document.getElementById("hiddCalPopId").value == "") {
				tCloseOut = setTimeout("calOpenCloseFun()", 100);
			}
		}
		//RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
			if(top.frames[0].frames[0]){
				top.frames[0].setPNotesHeight();
			}
		//END RUN THIS FUNCTION TO SET THE SCROLL HEIGHT OF FORMS
	
	}
		var displayText1 ="";
		var displayText2 ="";
		var displayText3 ="";
		var displayText4 ="";
		var displayText5 ="";
		var displayText6 ="";
		var displayText7 ="";
		
		
		var displayText = new Array();
		for(i=2;i<=106;i++) {
			displayText[i] = "";
		}
		
	//getting value from the buttons
	function getVal_c(n){
		//START
		if(document.getElementById("bp").value == 'flag1'){	
			document.getElementById("bp_temp").value = '';
			displayText1 += n;
			document.getElementById("bp_temp").value=displayText1; 
//new highlight			
			document.getElementById("bp_temp").style.backgroundColor = '#FFFFFF';
			document.getElementById("bp_temp").focus();
//new highlight end

			//alert(displayText1)
		}else {
			for(var k=2;k<=106;k++) {
				
				if(document.getElementById("bp").value == 'flag'+k){
					document.getElementById("bp_temp"+k).value="";
//new highlight			
					
					document.getElementById("bp_temp"+k).style.backgroundColor = '#FFFFFF';
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && (k==6)){
						//DO NOT SET FOCUS
					}else {
						document.getElementById("bp_temp"+k).focus();
					}
//new highlight end
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && (k==6 || k==7)) {
						if(n==" AM") {  n="A";}
						if(n==" PM") {  n="P";}
					}
					if(top.frames[0].frames[0].document.forms[0].name=="frm_gen_anes_rec" && (k==5 || k==6)) {
						if(n==" AM") {  n="A";}
						if(n==" PM") {  n="P";}
					}
					displayText[k]= displayText[k] + n;
					document.getElementById("bp_temp"+k).value=displayText[k];
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && k==6) {
						
						//showInterval(document.getElementById("bp_temp"+k).value,'intervalId');
					}
					if(top.frames[0].frames[0].document.forms[0].name=="frm_gen_anes_rec" && k==5) {
						
						//showIntervalMain(document.getElementById("bp_temp"+k).value,'intervalIdGeneralAnes');
					}
				}
			}			
		}
		
		//END 
		/*
		if(document.getElementById("bp").value == 'flag1'){	
			document.getElementById("bp_temp").value = '';
			displayText1 += n;
			document.getElementById("bp_temp").value=displayText1 
			
		}else if(document.getElementById("bp").value == 'flag2'){
			displayText2 += n;
			document.getElementById("bp_temp2").value=displayText2 
		}else if(document.getElementById("bp").value == 'flag3'){
			displayText3 += n;
			document.getElementById("bp_temp3").value=displayText3 
		}else if(document.getElementById("bp").value == 'flag4'){
			displayText4 += n;
			document.getElementById("bp_temp4").value=displayText4 
		}else if(document.getElementById("bp").value == 'flag5'){
			displayText5 += n;
			document.getElementById("bp_temp5").value=displayText5 
		}else if(document.getElementById("bp").value == 'flag6'){
			displayText6 += n;
			document.getElementById("bp_temp6").value=displayText6 
		}else if(document.getElementById("bp").value == 'flag7'){
			displayText7 += n;
			document.getElementById("bp_temp7").value=displayText7 
		}
		*/
	}
	function clearAll(t1,t2,t2)
	{
		
		document.getElementById("bp_temp").value="";
		document.getElementById("bp_temp2").value="";
		document.getElementById("bp_temp3").value="";
	}
	//end of function get val
	function clearVal_c(){
		
		if(document.getElementById("bp").value == 'flag1'){	
			displayText1 = '';
			document.getElementById("bp_temp").value=displayText1 
			document.getElementById("bp_temp").focus();
		}else {
			for(var k=2;k<=106;k++) {
				if(document.getElementById("bp").value == 'flag'+k){
					
					displayText[k] = '';
					document.getElementById("bp_temp"+k).value=displayText[k];
					document.getElementById("bp_temp"+k).focus();
				}
			}
		}
	}
	
	
	var tOut; 
	function closeCal2(){
		
		if(document.getElementById("cal_pop").style.display == "block"){
			document.getElementById("cal_pop").style.display = "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			//document.getElementById("cal_pop").style.display = "none";
		}else if(document.getElementById("opRoomDiopterPop").style.display == "block"){
			document.getElementById("opRoomDiopterPop").style.display = "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			//document.getElementById("opRoomDiopterPop").style.display = "none";
		}
		
	}
	
	function closeCal(){
		
		//tOut = setTimeout("closeCal2()", 1000);
		tOut = setTimeout("closeCal2()", 500);
		
	}
	function stopCloseCal()
	{
		clearTimeout(tOut);
	}
	
	
	//
	function closeTempCal2()
	{
		if(document.getElementById("temp_pop").style.display= "block"){
			document.getElementById("temp_pop").style.display= "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
		}
	}
	var timeOut;
	function closeTempCal()
	{
		//timeOut=setTimeout("closeTempCal2()", 1000);
		timeOut=setTimeout("closeTempCal2()", 500);
	}
	function stopCloseTempCal()
	{
		clearTimeout(timeOut);
	}
	// 
	//
	
	function closeCal123(){
		var mousewidth = window.event.clientX;
		var mouseheight = window.event.clientY;

		if((mouseheight > 230 && mouseheight < 350) && (mousewidth > 25 && mousewidth < 85)){
			document.getElementById("cal_pop").style.display = "block";
		}
		else{
			document.getElementById("cal_pop").style.display = "none";
		}
		
	}
	
	function clearField(){
		
	
	
	}
	
	function showIns(pid,wid,type){
		//window.open('insurance_primary.php?pid='+pid+'&wid='+wid);
		var args = arguments.length;
		if(args == '3'){
			//window.open('insurance_'+type+'.php?pid='+pid+'&wid='+wid,'insurance_'+type,'width=780,height=300');
			window.open('insurance_primary.php?pid='+pid+'&wid='+wid+'&type='+type,'insurance_'+type,'width=900,height=300');
		}else{
			var obj = document.getElementById(pid);
			if(obj) {
				if(obj.style.display == 'block'){
					obj.style.display = 'none';
				}else if(obj.style.display == 'none'){
					obj.style.display = 'block';
				} 
			}
		}
		
	}
	
	function getCityStateFn(obj,city1,state1){
	var z = obj.value;
	
	if(z){
		var xmlHttp;
		try{		
			xmlHttp=new XMLHttpRequest();
		}
		catch (e){
			try{
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e){
				try{
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e){
					alert("Your browser does not support AJAX!");
					return false;
				}
			}
		}
		
		xmlHttp.onreadystatechange=function(){
			if(xmlHttp.readyState==4){
				var val = xmlHttp.responseText;
				var city = "";
				var state = "";
				
				if(val!=''){
					var i = val.indexOf(",");
					var city = val.substr(0,i);
					var state = val.substr(i+1);
					
				}else{
					alert('Please enter correct zip code.')
					document.getElementById(obj.id).value='';
				}
				if(typeof(city1)!="undefined") 	{city1.value 	= city;}
				if(typeof(state1)!="undefined") {state1.value 	= state;}				
				if(typeof(document.getElementById(city1))!="undefined") { document.getElementById(city1).value=city;}
				if(typeof(document.getElementById(state1))!="undefined") { document.getElementById(state1).value=state;}
			
			}
		}
		xmlHttp.open("GET","admin/getStateZip.php?zip="+z,true);
		xmlHttp.send(null);
	}
}
	
