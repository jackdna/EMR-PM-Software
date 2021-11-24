<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
session_start();
$patientId  = $_REQUEST['patient_id'];
$pConfId    = $_REQUEST['pConfId'];
$formAction = $_REQUEST['formaction'];//'pre_op_health_quest.php';
$intCountChild = $_REQUEST['intCountChild'];//epost count
//echo $patientId.$pConfId.$formAction;
include_once("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("header_print_emr.php");
echo $head_table;
$printHealthHeight="200";
$sect = "print_emr";
include_once($formAction);
?>
<script type="text/javascript">
var formAction = '<?php echo $formAction;?>';
var tdTag 		= document.getElementsByTagName("td");
var txtAreaTag 	= document.getElementsByTagName("textarea");
var inputTag 	= document.getElementsByTagName("input");
var selectTag 	= document.getElementsByTagName("select");
var spanTag 	= document.getElementsByTagName("span");
var divTag 		= document.getElementsByTagName("div");
var labelTag 	= document.getElementsByTagName("label");

var i=j=k=l=m=n=v=w='';
var arVal='';
for(i=0;i<tdTag.length;i++){
	tdTag[i].style.fontSize = "9px";//if(tdTag[i].innerHTML.search('Prep Solutions')>=0) { alert(i+tdTag[i].innerHTML);}
	if(formAction=="post_op_nursing_record.php" && i>175){
		tdTag[i].style.textAlign="left";
		tdTag[i].style.width = "450px";	
		if(document.getElementById("img_h")){
			document.getElementById("img_h").style.paddingLeft="400px";
		}
		
	}
	if(formAction=="consent_multiple_form.php"){
		//tdTag[i].style.fontSize = "10px";
		if(tdTag[i].id="consentFormContent") {
			//tdTag[i].style.width = "650px";	
		}
	}
	if(formAction=="laser_procedure.php"){
		tdTag[i].style.textAlign="left";
		tdTag[i].style.width 	= "400px";
		if(i>340 && i<350){
			tdTag[i].style.textAlign="center";
			tdTag[i].style.width 	= "1000px";
		
		}
	}	
	if(formAction=="check_list.php"){
		tdTag[i].style.fontSize ="9px";
		tdTag[i].style.width = "250px";
		
		/* 
		if(tdTag[i].innerHTML=="Comments"){
			tdTag[i].style.width = "20px";
		}
		tdTag[i].style.fontSize ="8";
		tdTag[i].style.align="left";
		if(tdTag[i].innerHTML.search('checkbox')>0 || tdTag[i].innerHTML=="Yes" || tdTag[i].innerHTML=="No" || tdTag[i].innerHTML=="N/A"){
			if(tdTag[i].innerHTML=="" || tdTag[i].innerHTML=="&nbsp;"){
				tdTag[i].style.width="0px";
			}	
			tdTag[i].style.width = "20px";	
		}else{
			if(tdTag[i].innerHTML=="" || tdTag[i].innerHTML=="&nbsp;"){
			
				tdTag[i].style.width="1px";
				//tdTag[i].colspan="4";
				//tdTag[i].style.border="1px solid red";
			}else{
				tdTag[i].style.width = "200px";	
			}	
		}
	*/}
	if(formAction=="op_room_record.php"){
		tdTag[i].style.width = "320px";	
		tdTag[i].style.textAlign="left";
		if(tdTag[i].innerHTML.search('Prep Solutions')>=0) { 
			tdTag[i].style.width = "205px";	
		}
		if(tdTag[i].title=="prepSolution") {
			//tdTag[i].style.width = "100px";	
		}
	}
	if(formAction=="gen_anes_rec.php"){
		//tdTag[i].style.width = "250px";
	}
	if(formAction=="local_anes_record.php"){
		tdTag[i].style.fontSize ="8px";
		if(tdTag[i].title=="tdNoWidth") {
			//DO NOT CHANGE WIDTH
		}else {
			tdTag[i].style.width = "250px";
		}
	}
}
for(j=0;j<txtAreaTag.length;j++){
	txtAreaTag[j].style.fontSize = "9px";
	txtAreaTag[j].style.width = "100px";
	txtAreaTag[j].style.height = "40px";
	arVal = txtAreaTag[j].value;
	if((arVal.length)>30){
		if(txtAreaTag[j].id!='SigDataPtLoadValue') {//this id exist in PRE-OP HEALTH
			txtAreaTag[j].style.height = (parseInt(((arVal.length)*19)/20))+"px";
		}
		if(formAction=="op_room_record.php"){
			txtAreaTag[j].style.height = (parseInt(((arVal.length)*22)/20))+"px";
			
		}

	}
}
for(k=0;k<inputTag.length;k++) {
	inputTag[k].style.fontSize = "9px";
	if(inputTag[k].type == 'checkbox') {
		inputTag[k].style.width = "12px";
		inputTag[k].style.height = "12px";
	}
	
	if(inputTag[k].type == 'text') {
		if(inputTag[k].title=="txtNoWidth") {
			//DO NOT CHANGE WIDTH		
		}else {
			inputTag[k].style.width 	= "100px";
		}
		inputTag[k].style.height 	= "15px";
		
		if(formAction=="local_anes_record.php"){
			inputTag[k].style.fontSize = "8px";
			for(var cntTxt=1;cntTxt<=200;cntTxt++) {
				if((inputTag[k].id)=='bp_temp'+cntTxt || (inputTag[k].id)=='bp_temp') {
					inputTag[k].style.width 	= "30px";	
				}
			}
			if((inputTag[k].id)=='txt_field01' || (inputTag[k].id)=='otherRegionalAnesthesiaTxt1' || (inputTag[k].id)=='otherRegionalAnesthesiaTxt2'){
				inputTag[k].style.width 	= "25px";
			}
			
		}
		if(formAction=="gen_anes_nurse_notes.php") {
			inputTag[k].style.width 	= "80px";
			inputTag[k].style.height 	= "15px";
		}
		if(formAction=="pre_op_general_anes.php") {
			inputTag[k].style.width 	= "80px";
			inputTag[k].style.height 	= "15px";
		}
		if(formAction=="laser_procedure.php") {
			if(inputTag[k].id=="bp_temp" || inputTag[k].id=="bp_temp2" || inputTag[k].id=="bp_temp11" || inputTag[k].id=="bp_temp12" || inputTag[k].id=="bp_temp5" 
								  || inputTag[k].id=="bp_temp6" || inputTag[k].id=="bp_temp7"  || inputTag[k].id=="bp_temp8"  || inputTag[k].id=="bp_temp9"
			 					  || inputTag[k].id=="bp_temp10" || inputTag[k].id=="bp_temp3"  || inputTag[k].id=="bp_temp4" || inputTag[k].id=="bp_temp23"
								  || inputTag[k].id=="bp_temp24"){
				inputTag[k].style.width = "35px";
			}
		}
		if(formAction=="op_room_record.php"){
			inputTag[k].style.width = "80px";
			if(inputTag[k].id=="bp_temp5" || inputTag[k].id=="bp_temp6" || inputTag[k].id=="bp_temp" || inputTag[k].id=="bp_temp2" ||inputTag[k].id=="bp_temp7"){
				inputTag[k].style.width = "45px";
			}
			if(inputTag[k].value==""){
				inputTag[k].style.width = "1px";
			}
		}
		if(formAction=="gen_anes_rec.php"){
			inputTag[k].style.width = "70px";
		}
			
	}
}
for(l=0;l<selectTag.length;l++) {
	selectTag[l].style.fontSize = "9px";

	if(formAction=="local_anes_record.php"){
		selectTag[l].style.fontSize = "8px";
		if(selectTag[l].title=="selNoWidth") {
			//DO NOT CHANGE WIDTH
		}else {
			selectTag[l].style.width = "40px";
		}
	}
	
}
for(m=0;m<spanTag.length;m++) {
	spanTag[m].style.fontSize = "9px";
	if(formAction=="laser_procedure.php") {
		//spanTag[m].style.width 	= "100px";
		//inputTag[k].style.height = "15px";
	}	
	if(formAction=="check_list.php") {
		spanTag[m].style.width = "15px";
	}
}
for(n=0;n<divTag.length;n++) {
	divTag[n].style.fontSize = "9px";
	if(formAction=="check_list.php"){
		if(divTag[n].id=="TDnurse1SignatureId"){
			//divTag[n].style.width="700px";
		}
	}
	if(formAction=="local_anes_record.php"){
		divTag[n].style.fontSize = "8px";
		if(divTag[n].id=="gridTxtDivId" || divTag[n].id=="gridApltDivId"){
			divTag[n].style.width="309px";
			divTag[n].style.height="257px";
		}
	
	}
}
for(w=0;w<labelTag.length;w++) {
	labelTag[w].style.fontSize = "9px";
}
//START HIDE EPOST DIV
var epostCnt = '<?php echo $intCountChild;?>';
if(epostCnt>0) {
	for(var a=0;a<epostCnt;a++) {
		if(document.getElementById('epostMainDivChild'+a)) {
			document.getElementById('epostMainDivChild'+a).style.display="none";
		}
	}
}
//END HIDE EPOST DIV
</script>