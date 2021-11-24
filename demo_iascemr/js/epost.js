
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

//309 and 319

//var xmlHttp
var tCloseOut;
function showEpost(id,pConfIdEpost,actionDenied){ 
	
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	 }
	var url="common/epost_pop.php"
	url=url+"?tableID="+id
	//url=url+"&sid="+Math.random()
	url=url+"&pConfId="+pConfIdEpost
	if(actionDenied == 'yes')
	{
		url=url+"&AD="+actionDenied
	}
	xmlHttp.onreadystatechange=stateChanged 
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function stateChanged(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
		document.getElementById("post").style.display="inline-block"; 
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

function addEpost(text,tablename,consentMultipleId,consentMultipleAutoIncrId,hiddPurgestatus,pConfId1,patient_id1,stub_id1){
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
		url=url+"?text="+encodeURIComponent(text)
		url=url+"&tablename="+tablename
		url=url+"&consentMultipleId="+consentMultipleId
		url=url+"&consentMultipleAutoIncrId="+consentMultipleAutoIncrId
		url=url+"&hiddPurgestatus="+hiddPurgestatus
		url=url+"&pConfId="+pConfId1
		url=url+"&patient_id="+patient_id1
		url=url+"&stub_id="+stub_id1
		
		//url=url+"&sid="+Math.random()
		xmlHttp.onreadystatechange=stateChangeds
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
}

function closeEpost(){
	if(top.document.getElementById("post").style.display=="inline-block"){
		top.document.getElementById("post").style.display="none"; }
	if(document.getElementById("post").style.display=="inline-block"){
		document.getElementById("post").style.display="none"; }
}
function closeTopEpost(){
	if(top.document.getElementById("post").style.display=="inline-block"){
		top.document.getElementById("post").style.display="none"; }
}
function stateChangeds()
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
	 	var response = xmlHttp.responseText.split('~@');
		top.document.getElementById('post').html='';
		top.document.getElementById('post').style.display='none';
		if(top.document.getElementById("SliderHeadEpost")){top.document.getElementById("SliderHeadEpost").innerHTML = response[0];}
		
		if(top.frames[0].frames[0].document.getElementById("epostMainDiv")){
			top.frames[0].frames[0].document.getElementById("epostMainDiv").innerHTML = response[1];
		}
		
	} 
}

function stateChangedDiv(){ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
	 	//window.location.reload();
		//top.main_frmInner_id.reload();
		var a = xmlHttp.responseText.split('~@');
		alert(a[0]);
		document.getElementById("epostMainDiv").innerHTML=xmlHttp.responseText;
		
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
			if(document.getElementById("cal_pop").style.display == "inline-block"){
				document.getElementById("cal_pop").style.display = "none"; 
				document.getElementById("hiddCalPopId").value = "";
			}
			
			if(document.getElementById("temp_pop")){	
				if(document.getElementById("temp_pop").style.display == "inline-block"){
					document.getElementById("temp_pop").style.display = "none"; 
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			if(document.getElementById("opRoomDiopterPop")){	
				if(document.getElementById("opRoomDiopterPop").style.display == "inline-block"){
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
		document.getElementById("cal_pop").style.display = "inline-block";
		document.getElementById("temp_pop").style.display = "none";
		document.getElementById("opRoomDiopterPop").style.display = "none";
		document.getElementById("cal_pop").style.left = posLeft+'px';
		document.getElementById("cal_pop").style.top = t*20+110+'px';
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
	document.getElementById("cal_pop").style.display = "inline-block";
	document.getElementById("temp_pop").style.display = "none";
	document.getElementById("opRoomDiopterPop").style.display = "none";
	document.getElementById("cal_pop").style.left = posLeft+'px';
	document.getElementById("cal_pop").style.top = t+parseInt(posTop)+'px';
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
	
	document.getElementById("opRoomDiopterPop").style.display = "inline-block";
	document.getElementById("cal_pop").style.display = "none";
	document.getElementById("temp_pop").style.display = "none";
	document.getElementById("opRoomDiopterPop").style.left = posLeft+'px';
	document.getElementById("opRoomDiopterPop").style.top = t+parseInt(posTop)+'px';
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
	document.getElementById("cal_pop").style.display = "inline-block";
	document.getElementById("temp_pop").style.display = "none";
	if(document.getElementById("localAnesEkgGridPop")) {
		document.getElementById("localAnesEkgGridPop").style.display = "none";
	}
	document.getElementById("cal_pop").style.left = posLeft+'px';
	document.getElementById("cal_pop").style.top = posTop+'px';
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
function getShowLocalCalc(posTop,posLeft,flag)
{
	
	document.getElementById("bp").value = flag;
	document.getElementById("localAnesEkgGridPop").style.display = "inline-block";
	document.getElementById("temp_pop").style.display = "none";
	document.getElementById("cal_pop").style.display = "none";
	document.getElementById("localAnesEkgGridPop").style.left = posLeft+'px';
	document.getElementById("localAnesEkgGridPop").style.top = posTop+'px';
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
		document.getElementById("temp_pop").style.display = "inline-block";
		document.getElementById("cal_pop").style.display = "none";
		document.getElementById("temp_pop").style.left = posLeft+'px';
		document.getElementById("temp_pop").style.top = t+parseInt(posTop)+'px';
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
		var preValArr = new Array();
		for(i=2;i<=200;i++) {
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
			for(var k=2;k<=239;k++) {
				
				if(document.getElementById("bp").value == 'flag'+k){
					displayText[k] = document.getElementById("bp_temp"+k).value;
					document.getElementById("bp_temp"+k).value="";
//new highlight			
					
					document.getElementById("bp_temp"+k).style.backgroundColor = '#FFFFFF';
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && (k==6)){
						//DO NOT SET FOCUS
					}else {
						document.getElementById("bp_temp"+k).focus();
					}
//new highlight end
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && (k==6 || k==7 || k==98 || k==99 || k==100 || k==101 || k==102 || k==103)) {
						if(n==" AM") {  n="A";}
						if(n==" PM") {  n="P";}
					}
					if(top.frames[0].frames[0].document.forms[0].name=="frm_gen_anes_rec" && (k==5 || k==6 || k==98 || k==99 || k==100 || k==101 || k==102 || k==103)) {
						if(n==" AM") {  n="A";}
						if(n==" PM") {  n="P";}
					}
					displayText[k]= displayText[k] + n;
					if(displayText[k].search('REPEAT')>=0) { displayText[k]='';}
					if(top.frames[0].frames[0].document.forms[0].name=="frm_local_anes_rec" && n=='REPEAT' && k>=8 && k<=237) {
						//START CODE TO SET PREVIOUS TEXTBOX VALUE TO EKG TEXTBOX OF LOCALANESTHESIA
						var b=k-1;
						if(b>=8) {
							var prevValEkg;
							if(document.getElementById("bp_temp"+b)) {
								prevValEkg = document.getElementById("bp_temp"+b).value;
								if(!document.getElementById("bp_temp"+k).value && document.getElementById("bp_temp"+b).value) {
									document.getElementById("bp_temp"+k).value=prevValEkg;
								}
							}
						}
						//END CODE TO SET PREVIOUS TEXTBOX VALUE TO EKG TEXTBOX OF LOCALANESTHESIA
					}else {
						document.getElementById("bp_temp"+k).value=displayText[k];
					}
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
			for(var k=2;k<=240;k++) {
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
		
		if(document.getElementById("cal_pop").style.display == "inline-block"){
			document.getElementById("cal_pop").style.display = "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			//document.getElementById("cal_pop").style.display = "none";
		}else if(document.getElementById("opRoomDiopterPop").style.display == "inline-block"){
			document.getElementById("opRoomDiopterPop").style.display = "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			//document.getElementById("opRoomDiopterPop").style.display = "none";
		}
		else if(document.getElementById("localAnesEkgGridPop").style.display == "inline-block"){
			document.getElementById("localAnesEkgGridPop").style.display = "none";
			if(document.getElementById("hiddCalPopId")) {
				if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
					document.getElementById("hiddCalPopId").value = "";
				}
			}
			//document.getElementById("localAnesEkgGridPop").style.display = "none";
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
		if(document.getElementById("temp_pop").style.display= "inline-block"){
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
			document.getElementById("cal_pop").style.display = "inline-block";
		}
		else{
			document.getElementById("cal_pop").style.display = "none";
		}
		
	}
	
	function clearField(){
		
	
	
	}
	
	