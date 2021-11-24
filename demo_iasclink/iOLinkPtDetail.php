<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgery Center EMR</title>
<script type="text/javascript" src="js/jscript.js"></script>
<script src="js/Driving_License_Scanning.js"></script>
<script>
	window.focus();
	
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
		
		
		document.getElementById(q).value=fillDate;
		//START CODE TO SET DATE FORMAT (MM-DD-YY)
		/*
		var fillDateSplit = fillDate.split('-');
		var fillDateNew = fillDateSplit[0]+'-'+fillDateSplit[1]+'-'+fillDateSplit[2].substr(2,2);
		document.getElementById(q).value=fillDateNew;
		*/
		//END CODE TO SET DATE FORMAT (MM-DD-YY)
		
		
		mywindow.close();
	}
function padout(number){
return (number < 10) ? '0' + number : number;
}

function doDateCheck(from, to) {
	if(chkdate(to) && chkdate(from) ){
	
	if (Date.parse(from.value) >= Date.parse(to.value)) {
	//alert("The dates are valid.");
	}
	else {
		if (from.value == "" || to.value == ""){ 
		//alert("Both dates must be entered.");
		}
		else{ 
		to.value="";
		alert("Date of birth can not be greater than current date.");
		   }
		}
	}
}

var patient_in_waiting_id='';
var mode='';
function waitingPatient_info(patient_id,patient_in_waiting_id,mode,patient_status) {
		if(document.getElementById('iOLinkPtSearchId')) {
			document.getElementById("iOLinkPtSearchId").style.display='none';
		}
		if(document.getElementById('PatientInfoIdDiv')) {
			document.getElementById('PatientInfoIdDiv').style.display='block';
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			 }
			//var url="waiting_patient_info_popup.php"
			var url="iOLinkAddNewPatient.php"
			
			url=url+"?saveClick=yes"
			url=url+"&mode="+mode;
			if(patient_id) {
				url=url+"&patient_id="+patient_id
				url=url+"&patient_in_waiting_id="+patient_in_waiting_id
				if(patient_status){
					url=url+"&pat_status="+patient_status
				}
				if(patient_in_waiting_id){
					//clearTD("tdSave");
				}
			}
			else{
					if(document.getElementById("tdSave")){
						document.getElementById("tdSave").innerHTML="Save";
					}
			}
			
			xmlHttp.onreadystatechange=iOLinkPatientDetailFun 
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
		//window.open("waiting_patient_info_popup.php?search=true","waitingPatientWindow","width=500,scrollbars=0,height=420,top=175,left=525");
		//window.open("waiting_patient_info_popup.php?search=true","waitingPatientWindow","width=600,scrollbars=0,height=440,top=175,left=525");
}
function iOLinkPatientDetailFun(){ 
	
	if(xmlHttp.readyState==1) {
		document.getElementById("PatientInfoIdDiv").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
	}
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		document.getElementById("PatientInfoIdDiv").style.display='block';
		document.getElementById("PatientInfoIdDiv").innerHTML=xmlHttp.responseText; 
		//var aa = window.open();
		//aa.document.write(xmlHttp.responseText);
	} 
	
}
function trimVal(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function validtion_pt_Detail(){
	var msg = 'Please enter following fields.\n';
	var flag = 0;
	var msgOptional = 'Following fields are mandatory.\n';	
	var flagOptional = 0;
	var pt_name = '';
	
	var f1  = document.frm_add_patient.first_name.value;
	var f2  = document.frm_add_patient.last_name.value;
	var f3  = document.frm_add_patient.city.value;
	var f4  = document.frm_add_patient.state.value;
	var f5  = document.frm_add_patient.zip.value;
	var f6  = document.frm_add_patient.dob.value;
	var f7  = document.frm_add_patient.home_phone.value;
	
	if(f1==''){	msg+='. First Name\n'; ++flag; }
	if(f2==''){	msg+='. Last Name\n'; ++flag; }
	if(f3==''){	msg+='. City\n'; ++flag; }
	if(f4==''){	msg+='. State\n'; ++flag; }
	if(f5==''){	msg+='. Zip\n'; ++flag; }
	if(f6==''){ msg+='. Date Of Birth\n'; ++flag; }

	if(f1 && f2) {
		pt_name = f2+', '+f1;	
	}
	var msgComments = pt_name+' - Chart activated in ASC.\n';	
	var flagComments = 0;
	
	
	//if(document.frm_add_patient.patient_id.value) {
		//f8 = document.frm_add_patient.surgery_time.value;
		var f9,f10,f11;
		if(document.frm_add_patient.surgeon_name_id) {
			f9 = document.frm_add_patient.surgeon_name_id.value;
		}
		if(document.frm_add_patient.prim_proc) {
			f10 = document.frm_add_patient.prim_proc.value;
		}
		if(document.frm_add_patient.dos) {
			f11 = document.frm_add_patient.dos.value;
		}	
		
		//if(f8==''){	msg+='. Surgery Time\n'; ++flag; }
		if(f9==''){	msg+='. Surgeon Name\n'; ++flag; }
		if(f10==''){msg+='. Primary Procedure\n'; ++flag; }
		if(f11==''){msg+='. DOS\n'; ++flag; }
	
	//}
		var f12 = document.frm_add_patient.sex_list.value;
		var f13 = document.frm_add_patient.address1_id.value;
		var f14 = document.frm_add_patient.site.value;
		if(f12==''){ msgOptional+='. Sex\n'; ++flagOptional; }
		//if(f13==''){ msgOptional+='. Address1\n'; ++flagOptional; }
		//if(f7=='') { msgOptional+='. Home Phone\n'; ++flagOptional; }
		if(f14==''){ msgOptional+='. Site\n\n'; ++flagOptional; }
		
		var f15 = document.frm_add_patient.scemr_comment_modified_status.value;
		var f16 = document.frm_add_patient.hidd_comment.value;
		var f17 = document.frm_add_patient.comment.value;
		var f18 = document.frm_add_patient.pt_wait_book.value;
		var f19 = document.frm_add_patient.surgery_time.value;
		var f20 = document.frm_add_patient.iAscSyncroCount.value;
		
		if(parseInt(f15) > 0 && trimVal(f16) != "" && trimVal(f16) != trimVal(f17) && f18 !="book_patient") { ++flagComments;	}
		if( f18 != "book_patient" && trimVal(f19) == '' && f20 > 0 )
		{
			//msg+='. Surgery Time\n'; ++flag; 
			
		}
	//if(bookMovePatient=='true') {
		if(flag>0){
			alert(msg)
			return false;
		}else if(flagOptional>0) {
			msgOptional+='Do you want to continue ?';
			if(!confirm(msgOptional)) {  
				return false;
			}
		}
		if(flagComments>0) { // IF PATIENT IS MODIFIED IN SCEMR AND COMMENTS ARE CHANGED IN IASCLINK THEN NEED CONFIRMATION
			msgComments+='Do you want to overwrite existing comments ?\n';
			if(confirm(msgComments)) {  
				document.frm_add_patient.scemr_comment_status_to_reset.value = 'yes'; //RESET COMMENT STATUS IN SCEMR
				return true;
			}
		}
		return true;	
		
	//}	
}
function checkToDoAppt(){//alert("RAM");
	get_patient_book_id="";
	document.getElementById('pat_booking_id').value='';
	if(document.getElementById('first_name_id')){
		pat_fname=document.getElementById('first_name_id').value;	
	}
	if(document.getElementById('last_name_id')){
		pat_lname=document.getElementById('last_name_id').value;	
	}
	if(document.getElementById('bp_temp')){
		pat_dob=document.getElementById('bp_temp').value;	
	}
	if(document.getElementById('bp_temp6')){
		pat_zip=document.getElementById('bp_temp6').value;	
	}
	//alert(pat_fname+' '+pat_lname);
	//alert(document.getElementById('pat_status').value+' '+document.getElementById('pt_wait_book').value);
	if((document.getElementById('pat_status').value=="Canceled") && (document.getElementById('pt_wait_book').value=='book_patient')){
			document.getElementById('pt_wait_move').value='move_patient';
			document.getElementById('pt_wait_book').value='';
		}
	if(pat_fname && pat_lname){
		var xmlhttp;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if(xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText && document.getElementById('pat_status').value!="Canceled"){
					get_patient_book_id=xmlhttp.responseText;
					document.getElementById('pat_booking_id').value=get_patient_book_id;
					if(document.getElementById('pat_booking_id').value!=""){
						var conf=confirm("This patient is in TO-DO List. Do you want to attach paper work?");		
						if(conf==true){
							document.getElementById('pt_wait_move').value='move_patient';
							document.getElementById('pt_wait_book').value='';
							document.getElementById('patient_in_waiting_id').value=get_patient_book_id;
							document.frm_add_patient.submit();	
						}
						if(conf==false){
							document.getElementById('pt_wait_move').value='';
							document.getElementById('pt_wait_book').value='book_patient';
							document.frm_add_patient.submit();	
						}
					}
				}else{
					document.frm_add_patient.submit();	
				}
			}
 		}
		xmlhttp.open("GET","check_todo_list_ajax.php?patient_fname="+pat_fname+"&patient_lname="+pat_lname+"&patient_dob="+pat_dob+"&patient_zip="+pat_zip,true);
		xmlhttp.send();
	}
	//return get_patient_book_id;
}

function get_PtDetail_save() {
	var iOLinkFlag=false;
	if(document.getElementById('PatientInfoIdDiv')) {
		if(document.frm_add_patient) {
			if(document.getElementById('PatientInfoIdDiv').style.display=='block') {
				iOLinkFlag=true;
				
				//START CHECK TO BOOK OR MOVE THE PATIENT
				var bookMovePatient='true';
				//if(document.frm_add_patient.patient_id) {
					//if(document.frm_add_patient.patient_id.value) {
						if(document.frm_add_patient.pt_wait_book) {
							if(document.frm_add_patient.pt_wait_book.value=='book_patient') {
								if(!confirm('Are you sure to book this patient')) {
									bookMovePatient='false';
								}
							}
						}
					//}
				//}	
				if(document.frm_add_patient.patient_in_waiting_id) {	
					if(document.frm_add_patient.patient_in_waiting_id.value) {	
						if(document.frm_add_patient.pt_wait_move) {
							if(document.frm_add_patient.pt_wait_move.value=='move_patient') {
								if(!confirm('Are you sure to move surgery of this patient')) {
									bookMovePatient='false';
								}
							}
						}
					}
				}
				if(!document.frm_add_patient.patient_in_waiting_id.value && document.frm_add_patient.pt_wait_move.value=='move_patient') {
					bookMovePatient='false';
				}	
				if(!document.frm_add_patient.patient_id.value && document.frm_add_patient.pt_wait_book.value=='book_patient') {
					//bookMovePatient='false';
				}
				
				if(!document.frm_add_patient.pt_wait_book.value && !document.frm_add_patient.pt_wait_move.value) {
					if(typeof(document.frm_add_patient.hidd_dos)!="undefined") {
						if(document.frm_add_patient.hidd_dos.value!="" && document.frm_add_patient.hidd_dos.value!=document.frm_add_patient.dos.value) {
							if(confirm('Do you want to create/book new appointment')){
								document.frm_add_patient.pt_wait_book.value='book_patient';	
							}
						}
					}
				}
				//END CHECK TO BOOK OR MOVE THE PATIENT	
				
				if(bookMovePatient=='true') {
					if(document.frm_add_patient.inCorrectZipCode.value=='true' && document.frm_add_patient.city.value && document.frm_add_patient.state.value) {
						if(!confirm('Zip code does not exist. Do you want to add zip code')) {
							document.frm_add_patient.city.value='';
							document.frm_add_patient.state.value='';
							document.frm_add_patient.zip.value='';
						}
					}					
					if(validtion_pt_Detail()){ //FROM iOLinkAddNewPatient.php
						if(document.frm_add_patient.pt_wait_book.value=='book_patient'){
							checkToDoAppt();
						}else{	
							document.frm_add_patient.submit();
						}
					}
				}		
			}	
		}
	}
	if(iOLinkFlag==false) {
		alert('Please select form to save');
	}
}

function iOLinkSearchFun(obj_txt_iolink_pt_search) {
	if(document.getElementById('WaitingPatientIdDiv')) {
		document.getElementById('WaitingPatientIdDiv').style.display='none';
	}
	if(document.getElementById('PatientInfoIdDiv')) {
		document.getElementById('PatientInfoIdDiv').style.display='none';
	}
	if(document.getElementById('iOLinkPtSearchId')) {
		document.getElementById('iOLinkPtSearchId').style.display='block';
	}
	
	if(document.getElementById(obj_txt_iolink_pt_search)) {
		if(document.getElementById(obj_txt_iolink_pt_search).value) {
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			 }
			var url="iOLinkPtSearch.php"
			url=url+"?txt_iolink_pt_search="+document.getElementById(obj_txt_iolink_pt_search).value
			
			xmlHttp.onreadystatechange=iOLinkTxtSearchFun 
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		}
	}else {
		alert('Please enter patient name to search');
	}
	function iOLinkTxtSearchFun(){ 
		
		if(xmlHttp.readyState==1) {
			document.getElementById("iOLinkPtSearchId").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
		}
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			document.getElementById("iOLinkPtSearchId").style.display='block';
			document.getElementById("iOLinkPtSearchId").innerHTML=xmlHttp.responseText; 
		} 
		
	}
	
}
function iOLinkKeyCheck(evt) {
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
	var keynumber = evt.keyCode;
	if(keynumber==13){
		search_iOLink_id.click();  //IF PRESS ENTER THEN SEARCH LINK AUTOMATICALLY BE CLICKED
	}
}
var iasc_fac_id = '';
function iOLinkCancelPatient(strValue,id, iasc_fac_id) {
	if(strValue=='Cancel') {
			xmlHttp=GetXmlHttpObject()
			if (xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return
			 }
			var url="iOLinkPtCancel.php"
			url=url+"?cancelId="+id
			url=url+"&iasc_facility_id="+iasc_fac_id
			
			xmlHttp.onreadystatechange=iOLinkCancelPatientFun 
			xmlHttp.open("GET",url,true)
			xmlHttp.send(null)
		
	}
}
function iOLinkCancelPatientFun(){ 
	if(top.iframeHome.document.getElementById("ptCancelDiv")) {
		if(xmlHttp.readyState==1) {
			top.iframeHome.document.getElementById("ptCancelDiv").innerHTML='<center><img src="images/pdf_load_img.gif"></center>';
		}
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
			top.iframeHome.document.getElementById("ptCancelDiv").style.display='block';
			top.iframeHome.document.getElementById("ptCancelDiv").innerHTML=xmlHttp.responseText; 
			top.iframeHome.document.getElementById("ptCancelDiv").style.left=400;
			top.iframeHome.document.getElementById("ptCancelDiv").style.top=280;
		
		}
	}	 
	
}

function clearTD(obj){

	if(obj){
		document.getElementById(obj).innerHTML ="";
	}
}


function chkTmFormat(obj) { 
	timeValue = obj.value;
	var flag=false;
	if(timeValue.length==8 || !timeValue) { 
		if(!timeValue) {return; }
		HH = timeValue.split(':')[0].substr(0,2);
		MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		if(HH>24) {flag=true;}	
		if(MM>59) {flag=true;}	
	}
	var MM = '00';
	if(timeValue.length >=1){
		HH = timeValue.substr(0,2);
		if(timeValue.search(':')>=0) {
			HH = timeValue.split(':')[0].substr(0,2);
			MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		}
		if(HH>24 || timeValue.length<=3) { 
			HH = timeValue.substr(0,1); 
			MM = parseFloat(timeValue.substr(1,2));
			
			if(timeValue.search(':')>=0) {
				HH = timeValue.split(':')[0].substr(0,2);
				if(HH>24) { HH = timeValue.split(':')[0].substr(0,1); }
				MM = parseFloat(timeValue.split(':')[1].substr(0,2));
			}
			if(HH==0) {
				HH = timeValue.substr(0,2); 
				MM = parseFloat(timeValue.substr(2,2)); 
			}
			if(MM <= 9 && MM!=0) {MM = '0'+MM; }
			if(MM=='' || MM==0) {MM = '00';}
		}
		if(HH=='') { HH = '00';}
		if(HH <= 9 && HH.length==1) {HH = '0'+parseFloat(HH);}
	}else{
		HH = '00';
	}
	if(timeValue.length >3){
		if(MM=='00') {MM = parseFloat(timeValue.substr(2,2));}
		if(timeValue.search(':')>=0) {
			MM = parseFloat(timeValue.split(':')[1].substr(0,2));
		}
		if(MM <= 9 && MM.length==2) { MM = parseFloat(MM.substr(1,1)); }
		if((MM <= 9 && MM!=0) || MM.length==1){ MM = '0'+MM; }
		if(MM=='' || MM==0) {MM = '00'; }
	}else{
		//MM = '00';
	}
	var flagPM=true;
	if(HH>12){
		flagPM=false;
		HH = HH- 12;
		if(HH <= 9 && HH!=0){
			HH = '0'+HH;
		}
	}
	if((HH >= 7 && HH <= 11 && flagPM==true)){
		Suffix = 'AM';
	}else{
		Suffix = 'PM';
	}
	if(timeValue.search('a')>=0 || timeValue.search('A')>=0) {Suffix = 'AM'; }
	if(timeValue.search('p')>=0 || timeValue.search('P')>=0) {Suffix = 'PM'; }
	if(isNaN(HH))  { HH = '00';}
	if(isNaN(MM))  {MM = '00'; }
	
	if(MM>59 || HH=='00' || flag==true) {  alert('Please enter correct time format HH:MM AM/PM'); obj.value='';return;}		
	timeValue = HH+':'+MM+' '+Suffix;
	obj.value = timeValue;
} 
function myTrim(obj){
	str = obj.value;
	str = str.replace(/^\s+|\s+$/g,"");
	obj.value=str;
}
</script>
<?php
$spec= "
</head>
<body>";
include("common/link_new_file.php");
?>
<div id="divAjaxLoader" style="position:absolute;z-index:1000;width:250px; top:2px;left:600px; height:30px; display:none;">				
	<img src="images/pdf_load_img.gif" alt="Please Wait">							
</div>
	<table class="table_collapse" style="border:none;">
		<tr class="valignTop">
			<td class="text_10bAdmin  alignLeft" style="width:100%;">
				<table class="table_collapse" style="border:none;">
					<tr style="height:24px;">
						<td  class="text_10b" style=" font-size:12px;background-image:url(<?php echo $bgHeadingImage;?>);" >Patient Detail&nbsp;</td>
						<td  class="text_10b alignLeft" style=" font-size:12px;background-image:url(<?php echo $bgHeadingImage;?>);" >Patient : <input class="field text" type="text" name="txt_iolink_pt_search" id="txt_iolink_pt_search" onKeyPress="iOLinkKeyCheck(event);" value="" style="height:12px; font-size:11px; ">&nbsp;<span class="text_10b alignLeft" style=" font-size:12px; color:#800080; cursor:pointer;;" ><a  id="search_iOLink_id" onClick="javascript:iOLinkSearchFun('txt_iolink_pt_search');" class="text_10b" style=" font-size:12px; color:#800080; cursor:pointer;">Search</a></span></td>
                        <td class="text_10b alignLeft" style=" font-size:12px;background-image:url(<?php echo $bgHeadingImage;?>); width:250px;" >
                        	<input type="text" style="width:0px; height:0px; border:none;" id="scanner" >
							<img title="Driving License" alt="Driving License" style="position: relative;" src="images/Driving_License_Scanner_Icon.png" onclick="set_Focus()">
                        </td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="valignTop">
			<td  style="border:solid 1px; border-color:#9FBFCC; ">
				<table class="table_pad_bdr" style="border:none; padding:2px; width:100%;">
					<?php
					if(constant("HIDE_PATIENT_DETAIL_BUTTONS") == "YES") {
					?>
                    	<tr class="alignCenter" style="height:22px; "   >
                        	<td  class=" alignLeft" style="padding-left:5px;border:solid 2px; width:12%; border-color:#F8F9F7;" >&nbsp;</td>
                        </tr>
                    <?php	
					}else {
					?>
                    <tr class="alignCenter" style="height:22px; "   >
						<td  class="topNavPtdetail alignLeft" style="padding-left:5px;border:solid 2px; width:12%; border-color:#F8F9F7;" onClick="javascript:waitingPatient_info();">New&nbsp;Pt.</td>
						<!-- <td width="16%">Consent&nbsp;Forms</td> -->
						<td class="topNavPtdetail alignLeft" style=" width:18%;padding-left:10px;border:solid 2px; border-color:#F8F9F7;" onClick="if(document.getElementById('pt_wait_book')) {document.getElementById('pt_wait_book').value='book_patient';document.getElementById('pt_wait_move').value='';get_PtDetail_save();}">Book-Appt</td>
						<td class="topNavPtdetail alignLeft" style=" width:18%;padding-left:10px;border:solid 2px; border-color:#F8F9F7;" onClick="if(document.getElementById('pt_wait_move')) {document.getElementById('pt_wait_move').value='move_patient';document.getElementById('pt_wait_book').value='';get_PtDetail_save();}">Move-Appt</td>
						<td class="topNavPtdetail alignLeft" style=" width:21%;padding-left:10px;border:solid 2px; border-color:#F8F9F7;" onClick="iOLinkCancelPatient('Cancel',document.getElementById('patient_in_waiting_id').value,document.getElementById('iasc_facility_id').value)">Cancel-Appt</td>
						<!-- <td width="16%" onClick="location.href='iOLinkPtDetail.php'">Cancel</td> -->
						<td id="tdSave" class="topNavPtdetail alignLeft " style=" width:31%;padding-left:10px;border:solid 2px; border-color:#F8F9F7;" onClick="javascript:if(this.innerHTML=='Save'){if(document.getElementById('pt_wait_book')) {document.getElementById('pt_wait_book').value='';document.getElementById('pt_wait_move').value='';get_PtDetail_save(); }}">Save</td>
					</tr>
                    <?php
					}
					?>
				</table>	
			</td>
		</tr>
		
		<tr>
			<td class="text_10b" >
				<?php
				$WaitingPatientIdDivDisplay = 'none';
				if(isset($_REQUEST['patient_in_waiting_id']) && $_REQUEST['patient_in_waiting_id']) { }//$WaitingPatientIdDivDisplay = 'block'; }
				?>			
				<div id="WaitingPatientIdDiv" style="position:static; display:<?php echo $WaitingPatientIdDivDisplay;?>;width:100%;overflow:hidden; vertical-align:top; "><?php //include("waiting_patient_info_popup.php");?></div>
						
				<div id="PatientInfoIdDiv" style="position:static; display:none;width:100%;overflow:hidden; vertical-align:top; "><?php include("iOLinkAddNewPatient.php");?></div>
				
				<!-- MAKE DIV TO SEARCH PATIENT INFO -->
				<div id="iOLinkPtSearchId" style="height:330px; overflow:auto; overflow-x:hidden; "></div>
				<!-- MAKE DIV TO SEARCH PATIENT INFO -->
				<?php //include("iOLinkPtSearch.php");?>
                
			</td>
		</tr>
		
		
	</table>		

<?php
//START CODE TO OPEN DEMOGRAPHIC WINDOW ON LOAD FIRST TIME
if(!$patient_id) {
?>
<script>
	waitingPatient_info();
</script>
<?php
}
//END CODE TO OPEN DEMOGRAPHIC WINDOW ON LOAD FIRST TIME
?>
</body>
</html>
