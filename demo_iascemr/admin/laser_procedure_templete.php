<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 

include_once("../globalsSurgeryCenter.php");
include_once("logout.php"); 
?>
<!DOCTYPE html>
<html>
<head>
<title>Laser Procedure Templete</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<?php include("adminLinkfile.php");?>
<script>
//START FUNCTION TO FIND POSITION FROM LEFT
function findPos_X_custom(id){
	var obj = document.getElementById(id);
	var leftPanel =	parseFloat($('.sidebar-wrap-op').outerWidth(true));
	var posX = obj.offsetLeft;
	while(obj.offsetParent){
		posX=posX+obj.offsetParent.offsetLeft;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	var posXNew = parseFloat(posX - leftPanel);
	return(posXNew);
}
//END FUNCTION TO FIND POSITION FROM LEFT

//START FUNCTION TO FIND POSITION FROM TOP
function findPos_Y_custom(id){
	var obj = document.getElementById(id);
	var posY = obj.offsetTop;
	while(obj.offsetParent){
		posY=posY+obj.offsetParent.offsetTop;
		if(obj==document.getElementsByTagName('body')[0]){break}
		else{obj=obj.offsetParent;}
	}
	return(posY);
}
//END FUNCTION TO FIND POSITION FROM TOP

	function move_templete(val){
		location.href='laser_procedure_templete.php?laser_ProcedureId='+val;
	}
	top.frames[0].document.frameSrc.source.value = 'laser_procedure_admin.php';	
	top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
	top.frames[0].document.getElementById('deleteSelected').style.display = 'inline-block';
	top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';	
</script>
<script >
var preDefineCloseOut;
function preDefineOpenCloseFun() {
	document.getElementById("hiddPreDefineId").value = "preDefineOpenYes";
}
function preCloseFun(Id) {
	if(document.getElementById("hiddPreDefineId")) {
		if(document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
			if(document.getElementById(Id)) {
				if(document.getElementById(Id).style.display == "block"){
					document.getElementById(Id).style.display = "none"; 
					//document.getElementById("hiddPreDefineId").value = "";
				}
			}
			if(top.frames[0].frames[0].document.getElementById(Id)) {
				if(top.frames[0].frames[0].document.getElementById(Id).style.display == "block"){
					top.frames[0].frames[0].document.getElementById(Id).style.display = "none"; 
					//top.frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
				}
			}
		}
		
	}
}
function showsurgeon(name1, name2, c, posLeft, posTop){	
//	alert(top.frames[0].frames[0].document.getElementById("evaluationchief_complaint_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsurgeon_div").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showChiefComplaintAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("evaluationchief_complaint_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationchief_complaint_div").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showhx_illnessAdminFn(name1, name2, c, posLeft, posTop){	
	//alert(top.frames[0].frames[0].document.getElementById("evaluationhx_illness_div"));
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationhx_illness_div").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showpast_med_hx(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpast_med_hx_div").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showmedication(name1, name2, c, posLeft, posTop){	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmedication_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showsle(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-50);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationsle_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showfundus_exam(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-80);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationfundus_exam_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showmental_state(name1, name2, c, posLeft, posTop){//posTop=parseFloat(posTop-150);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationmental_state_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showpre_op_diagnosis(name1, name2, c, posLeft, posTop){//posTop=parseFloat(posTop-200);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpre_op_diagnosis_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpre_op_diagnosis_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpre_op_diagnosis_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

function showspot_duration(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-350);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_duration_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_duration_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_duration_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

function showspot_size(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-400);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationspot_size_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showpower(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-450);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpower_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showspots(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-500);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationshots_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationshots_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationshots_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showtotal_energy(name1, name2, c, posLeft, posTop){//posTop=parseFloat(posTop-550);	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationtotal_energy_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationtotal_energy_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationtotal_energy_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showdegree_of_opening(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-600);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationdegree_of_opening_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationdegree_of_opening_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationdegree_of_opening_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showexposure(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-650);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationexposure_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showcount(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-700);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationcount_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}
function showanesthesia(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-750);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationanesthesia_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
} 
function showpost_progress(name1, name2, c, posLeft, posTop){//posTop=parseFloat(posTop-800);	
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_progress_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
} 
function showpost_operative(name1, name2, c, posLeft, posTop){	//posTop=parseFloat(posTop-850);
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.display = 'block';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.left = posLeft+'px';
	top.frames[0].frames[0].frames[0].document.getElementById("evaluationpost_operative_div_admin").style.top = posTop+'px';
	document.getElementById("divId").value = name1;
	document.getElementById("counter").value = c;
	document.getElementById("secondaryValues").value = name2;	
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100);
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
}

function showPreOpMediDiv($this,c)
{
	var CLeft	=	$this.parent('label').parent('div').parent('div').offset().left  -105;
	var CTop	=	$this.parent('label').parent('div').parent('div').offset().top;	
	var obj		=	$("#laserPreOpMediOrderDiv");
	
	CTop		=	CTop - obj.outerHeight(true) -12  ;
	
	obj.css({'left' : CLeft +'px' , 'top' : CTop + 'px' , 'display':'block'});
	
	document.getElementById("counter").value = c;
	//CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE)
		if(document.getElementById("hiddPreDefineId")) {
			document.getElementById("hiddPreDefineId").value = "";
			preDefineCloseOut = setTimeout("preDefineOpenCloseFun()", 100) ;
		}
	//END CODE TO SET HIDDEN VALUE TO YES (FOR CLOSING PREDIFINE BY CLICKING OUTSIDE) 
	
}

//FUNCTION TO DISABLE & ENABLE textarea IN Pre-op Nursing
function enable_chk_unchk(chbx_yes_id,txtarea_id) {
	if(document.getElementById(chbx_yes_id).checked==false) {
		
		document.getElementById(txtarea_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;
		
	}else if(document.getElementById(chbx_yes_id).checked==true) {
		
		document.getElementById(txtarea_id).disabled=false;
		//document.getElementById(acl_number_id).disabled=false;
		
	}else {
		
		document.getElementById(txtarea_id).disabled=true;
		//document.getElementById(acl_number_id).disabled=true;

	}
}

//END FUNCTION TO DISABLE & ENABLE textarea IN Pre-op Nursing


function enable_sheet(chbx_id,containerID) 
{
	if(document.getElementById(chbx_id).checked==true) 
	{
			$("#"+containerID+"").find('input').removeAttr('disabled');
	}
	else
	{
			$("#"+containerID+"").find('input').attr('disabled','disabled');
	}
}

$(document).ready(function()
{
		$('body').on( 'click' , '.removeMedOrder', function()
		{
				var $this	=	$(this);
				var RID	=	$this.attr('data-record-id');				//	 Record ID
				var RT		=	$this.attr('data-record-type');			//	Record Type
				var PID	=	$this.attr('data-profile-id');				//	Profile ID
				var Div		=	'#laserSpreadSheetPreOpMed';
				
				if(RT !== '' )
				{
						var Url		=	'laserMedicationDelete.php' 	
				
						$.ajax({
							url : Url,
							type:'POST',
							data : { 'RID' : RID , 'RT' : RT , 'PID' : PID },
							beforeSend:function(){
								$this.hide(500);
								var TD	=	$this.closest('td');
								var txt	=	"<span class='fa fa-spinner fa-pulse '></span>";
								TD.html(txt);
							},
							complete:function(){
									//$this.show(500);
							},
							success:function(data)
							{ 	
									$(Div).html(data)
							},
							error:function(res)
							{
								consle.log('ERRO');	
							}
							
						
						});
						
				}
					
				
		});
	
		$("#cptCode, #dxCode").click(function(e) {
            var Id	=	$(this).attr('id');				
			var Pn	=	'laser_cpt_dx_profile.php';
			var PId	=	$("#templateid").val() ;
			var Dct	=	$("#DxCodeType").val();
			
			var url	=	Pn;
			url 	+=	'?pro_id='+ PId;
			url	+=	'&'+Id+'=yes';
			url	+=	(Id === 'dxCode' ) ? '&diagnosis_code_type='+Dct : '' ;
			
			var SW	=	window.screen.width ;
			var SH	=	window.screen.height;
			
			var	W	=	( SW > 1200 ) ?  1200	: SW ;
			var	H	=	W * 0.65
	
			window.open(url,'Laser CPT & Dx Code','width='+W+',height='+H+',resizable=1');
		});
		
		
		
});


</script>
</head>
<body >
<?php
include_once("classObjectFunction.php");
$objManageData = new manageData;
$laser_ProcedureId = $_REQUEST['laser_ProcedureId'];
$templeteID_show=$_REQUEST['templeteID_show'];

include("../common/lasersurgeon_pop_admin.php");
include("../common/laserchief_complaint_pop_admin.php");
include("../common/laserhx_illness_admin_pop.php");
include("../common/laserpast_med_hx_pop_admin.php");
include("../common/lasermedication_pop_admin.php");
include("../common/lasersle_pop_admin.php");
include("../common/laserfundus_exam_pop_admin.php");
include("../common/lasermental_state_pop_admin.php");
include("../common/laserpre_op_diagnosis_pop_admin.php");
include("../common/laserspot_duration_pop_admin.php");
include("../common/laserspot_size_pop_admin.php");
include("../common/laserpower_pop_admin.php");
include("../common/lasershots_pop_admin.php");
include("../common/lasertotal_energy_pop_admin.php");
include("../common/laserdegree_of_opening_pop_admin.php");
include("../common/laserexposure_pop_admin.php");
include("../common/lasercount_pop_admin.php");
include("../common/laseranesthesia_pop_admin.php");
include("../common/laserpost_progress_pop_admin.php");
include("../common/laserpost_operative_pop_admin.php");
include("../common/laser_preOpMediOrderPopUp.php");


//Insert and edit
if($_REQUEST['save_id']){
	unset($arrayRecord);
	if($_REQUEST['laserprocedure_surgeonId']){
		$laserprocedure_surgeonId=implode(",",$_REQUEST['laserprocedure_surgeonId']);
	}else {
		$laserprocedure_surgeonId='all';
	}
	$arrayRecord['laser_procedureID'] = addslashes($_REQUEST['laser_ProcedureId']);
	$arrayRecord['template_name'] = addslashes($_REQUEST['template_name']);
	$arrayRecord['laser_surgeonID'] = $laserprocedure_surgeonId;
	
	$arrayRecord['laser_chk_chief_complaint'] = $_REQUEST['laser_chk_chief_complaint'];
	$arrayRecord['laser_chief_complaint'] = addslashes($_REQUEST['laser_Procedurechief_complaint']);
	
	$arrayRecord['laser_chk_present_illness_hx'] = $_REQUEST['laser_chk_present_illness_hx'];
	$arrayRecord['laser_present_illness_hx'] = addslashes($_REQUEST['laser_Procedurehx_illness']);
	
	$arrayRecord['laser_chk_past_med_hx'] = $_REQUEST['laser_chk_past_med_hx'];
	$arrayRecord['laser_past_med_hx'] = addslashes($_REQUEST['laser_Procedurepast_med_hx']);
	
	$arrayRecord['laser_chk_medication'] = $_REQUEST['laser_chk_medication'];
	$arrayRecord['laser_medication'] = addslashes($_REQUEST['laser_Proceduremedication']);
	
	$arrayRecord['laser_chk_sle'] = $_REQUEST['laser_chk_sle'];
	$arrayRecord['laser_sle'] = addslashes($_REQUEST['laser_Proceduresle']);
	
	$arrayRecord['laser_chk_fundus_exam'] = $_REQUEST['laser_chk_fundus_exam'];
	$arrayRecord['laser_fundus_exam'] = addslashes($_REQUEST['laser_Procedurefundus_exam']);
	
	$arrayRecord['laser_chk_mental_state'] = $_REQUEST['laser_chk_mental_state'];
	$arrayRecord['laser_mental_state'] = addslashes($_REQUEST['laser_Proceduremental_state']);
	
	$arrayRecord['laser_chk_pre_op_diagnosis'] = $_REQUEST['laser_chk_pre_op_diagnosis'];
	$arrayRecord['laser_pre_op_diagnosis'] = addslashes($_REQUEST['laser_Procedure_pre_op_diagnosis']);
	
	$arrayRecord['laser_chk_procedure_image'] = $_REQUEST['laser_chk_procedure_image'];
	
	$arrayRecord['laser_chk_spot_duration'] = $_REQUEST['laser_chk_spot_duration'];
	$arrayRecord['laser_spot_duration'] = addslashes($_REQUEST['laser_Procedurespot_duration']);
	$arrayRecord['laser_chk_spot_size'] = $_REQUEST['laser_chk_spot_size'];
	$arrayRecord['laser_spot_size'] = addslashes($_REQUEST['laser_Procedurespot_size']);
	$arrayRecord['laser_chk_power'] = $_REQUEST['laser_chk_power'];
	$arrayRecord['laser_power'] = addslashes($_REQUEST['laser_Procedurepower']);
	$arrayRecord['laser_chk_shots'] = $_REQUEST['laser_chk_shots'];
	$arrayRecord['laser_shots'] = addslashes($_REQUEST['laser_Procedureshots']);
	$arrayRecord['laser_chk_total_energy'] =$_REQUEST['laser_chk_total_energy'];
	$arrayRecord['laser_total_energy'] = addslashes($_REQUEST['laser_Proceduretotal_energy']);
	$arrayRecord['laser_chk_degree_of_opening'] = $_REQUEST['laser_chk_degree_of_opening'];
	$arrayRecord['laser_degree_of_opening'] = addslashes($_REQUEST['laser_Procedure_degree_of_opening']);
	$arrayRecord['laser_chk_exposure'] = $_REQUEST['laser_chk_exposure'];
	$arrayRecord['laser_exposure'] = addslashes($_REQUEST['laser_Procedureexposure']);
	$arrayRecord['laser_chk_count'] =$_REQUEST['laser_chk_count'];
	$arrayRecord['laser_count'] = addslashes($_REQUEST['laser_Procedurecount']);
	$arrayRecord['laser_anesthesia'] = addslashes($_REQUEST['laser_Procedureanesthesia']);
	
	$arrayRecord['laser_chk_post_progress'] =$_REQUEST['laser_chk_post_progress'];
	$arrayRecord['laser_post_progress'] = addslashes($_REQUEST['laser_Procedurepost_progress']);
	
	$arrayRecord['laser_chk_post_operative'] =$_REQUEST['laser_chk_post_operative'];
	$arrayRecord['laser_post_operative'] = addslashes($_REQUEST['laser_Procedurepost_operative']);
	
	$arrayRecord['instructionSheetId']		=	$_REQUEST['instructionSheetId'];
	
	
	
	//$arrayRecord['laser_chk_preop_medication'	]	=	$_REQUEST['laser_chk_preop_medication'];
	/**
	*
	* Save Pre op orders Medication 
	*
	**/
	
	
	{
				$tableName					=	"preopmedicationorder";
				$preOpOrdMed_id		=	$_REQUEST['preOpOrdMed_id'];
				$preOpOrdMed_med	=	$_REQUEST['preOpOrdMed_med'];
				$preOpOrdMed_sgt	=	$_REQUEST['preOpOrdMed_sgt'];
				$preOpOrdMed_dir		=	$_REQUEST['preOpOrdMed_dir'];
				$preOpOrdMed_cat		=	$_REQUEST['preOpOrdMed_cat'];
				
				foreach( $preOpOrdMed_med as $key => $orders)
				
				{
						$preOpOrdMed_id[$key]		=	addslashes($preOpOrdMed_id[$key]);
						$preOpOrdMed_med[$key]	=	addslashes($preOpOrdMed_med[$key]);
						$preOpOrdMed_sgt[$key]	=	addslashes($preOpOrdMed_sgt[$key]);
						$preOpOrdMed_dir[$key]	=	addslashes($preOpOrdMed_dir[$key]);
						$preOpOrdMed_cat[$key]	=	addslashes($preOpOrdMed_cat[$key]);
			
						if( !empty($preOpOrdMed_id[$key]))
						{
							
							$upQry 	= "UPDATE ".$tableName." SET 
															medicationName = '".$preOpOrdMed_med[$key]."',
															strength = '".$preOpOrdMed_sgt[$key]."',
															directions = '".$preOpOrdMed_dir[$key]."' 
															WHERE preOpMedicationOrderId='".$preOpOrdMed_id[$key]."'";
															//echo $upQry.'<br>';
							$upSql		= imw_query($upQry) or die(imw_error()); 
						
						}
						
						else
						{
								$chkPreOpOrdMedQry	= "SELECT * FROM ".$tableName." WHERE
																				medicationName = '".$preOpOrdMed_med[$key]."' 
																				AND strength = '".$preOpOrdMed_sgt[$key]."' 
																				AND directions = '".$preOpOrdMed_dir[$key]."' 
																				ORDER BY medicationName ";
								$chkPreOpOrdMedSql		= imw_query($chkPreOpOrdMedQry) or die(imw_error()); 
								$chkPreOpOrdMedCnt	= imw_num_rows($chkPreOpOrdMedSql);
								
								if($chkPreOpOrdMedCnt > 0 )
								{
										$chkPreOpOrdMed_row			= imw_fetch_array($chkPreOpOrdMedSql);
										$preOpOrdMedIdArray[$key] 	= $chkPreOpOrdMed_row['preOpMedicationOrderId'];
								}
								else
								{
										$preOpMedCatQry	=	"";
										if($preOpOrdMed_cat) 
										{ 
											$preOpMedCatQry = " , mediCatId = '".$preOpOrdMed_cat[$i]."' "; 
										}
										$insQry	=	" INSERT INTO ".$tableName." SET medicationName = '".$preOpOrdMed_med[$key]."' , strength = '".$preOpOrdMed_sgt[$key]."' , directions = '".$preOpOrdMed_dir[$key]."' ".$preOpMedCatQry ." , sourcePage = 1 ";
										//echo $insQry.'<br>';
										$insSql		=	imw_query($insQry) or die(imw_error()); 
										$preOpOrdMedIdArray[$key] = imw_insert_id();
								}
						}	
				}
				
				foreach($preOpOrdMed_id as $key =>$value)
				{
						if($preOpOrdMed_id[$key] &&  !$preOpOrdMed_med[$key]) 
						$preOpOrdMed_id[$key] 	=	'';
				}
						
				foreach($preOpOrdMed_med as $key=>$value)
				{
						if($preOpOrdMed_med[$key] &&  !$preOpOrdMed_id[$key]) 
							$preOpOrdMed_id[$key] = $preOpOrdMedIdArray[$key];
				}
						
				if(is_array($preOpOrdMed_id)) 
						$preOpOrdMed_id	= implode( ',' , $preOpOrdMed_id);
						
	}
	$arrayRecord['laser_preop_medication']	=	$preOpOrdMed_id;

	if($_REQUEST['templateid']){
		$templete_change=$_REQUEST['templateid'];
		$c=$objManageData->UpdateRecord($arrayRecord, 'laser_procedure_template', 'laser_templateID', $templete_change);
	}
	else
	{
		$arrayRecord['cpt_id'] 							=	$_REQUEST['cpt_id'];
		$arrayRecord['cpt_id_default']				=	$_REQUEST['cpt_id_default'];
		$arrayRecord['dx_id']							=	$_REQUEST['dx_id'];
		$arrayRecord['dx_id_default']				=	$_REQUEST['dx_id_default'];
		$arrayRecord['dx_id_icd10']					=	$_REQUEST['dx_id_icd10'];
		$arrayRecord['dx_id_default_icd10']		=	$_REQUEST['dx_id_default_icd10'];
		
		$d=$objManageData->addRecords($arrayRecord, 'laser_procedure_template ');
	}
	if($c)
	{
		echo "<script>top.frames[0].alert_msg('update')</script>";
	}
	if($d)
	{
		echo "<script>top.frames[0].alert_msg('success')</script>";
	}
}
//DELETE SELECTED TEMPLATE
	if($_POST['chkBox']){
		$counter=0;
		$delChkBoxes = $_POST['chkBox'];
		if(is_array($delChkBoxes)){
			foreach($delChkBoxes as $del_laser_templeteId){
				$rec_del=$objManageData->delRecord('laser_procedure_template', 'laser_templateID', $del_laser_templeteId);
				if($rec_del)$counter++;
			}
			if($rec_del)
			{
				echo "<script>top.frames[0].alert_msg('del','".$counter."')</script>";
			}
		}
	}

if($templeteID_show)
{
	$laserDetails = $objManageData->getRowRecord('laser_procedure_template', 'laser_templateID', $templeteID_show);
	
	$laser_chk_chief_complaint 		=	$laserDetails->laser_chk_chief_complaint;
	$laser_chief_complaint_detail	=	$laserDetails->laser_chief_complaint;
	
	$laser_chk_present_illness_hx	=	$laserDetails->laser_chk_present_illness_hx;
	$laser_present_illness_hx_detail=	$laserDetails->laser_present_illness_hx;
				
	$laser_chk_past_med_hx			=	$laserDetails->laser_chk_past_med_hx;
	$laser_past_med_hx_detail		=	$laserDetails->laser_past_med_hx;
	
	$laser_chk_medication			=	$laserDetails->laser_chk_medication;
	$laser_medication_detail		=	$laserDetails->laser_medication;
	
	$laser_chk_sle					=	$laserDetails->laser_chk_sle;
	$laser_sle_detail				=	$laserDetails->laser_sle;
	
	$laser_chk_fundus_exam			=	$laserDetails->laser_chk_fundus_exam;
	$laser_fundus_exam_detail		=	$laserDetails->laser_fundus_exam;
	
	$laser_chk_mental_state			=	$laserDetails->laser_chk_mental_state;
	$laser_mental_state_detail		=	$laserDetails->laser_mental_state;
	
	$laser_chk_pre_op_diagnosis		=	$laserDetails->laser_chk_pre_op_diagnosis;
	$laser_pre_op_diagnosis			=	$laserDetails->laser_pre_op_diagnosis;
	
	$laser_chk_spot_duration		=	$laserDetails->laser_chk_spot_duration;
	$laser_spot_duration_detail		=	$laserDetails->laser_spot_duration;
	
	$laser_chk_spot_size			=	$laserDetails->laser_chk_spot_size;
	$laser_spot_size_detail			=	$laserDetails->laser_spot_size;
	
	$laser_chk_power				=	$laserDetails->laser_chk_power;
	$laser_power_detail				=	$laserDetails->laser_power;
	
	$laser_chk_shots				=	$laserDetails->laser_chk_shots;
	$laser_shots_detail				=	$laserDetails->laser_shots;
	
	$laser_chk_total_energy			=	$laserDetails->laser_chk_total_energy;
	$laser_total_energy_detail		=	$laserDetails->laser_total_energy;
	
	$laser_chk_degree_of_opening	=	$laserDetails->laser_chk_degree_of_opening;
	$laser_degree_of_opening_detail	=	$laserDetails->laser_degree_of_opening;
	
	$laser_chk_exposure				=	$laserDetails->laser_chk_exposure;
	$laser_exposure_detail			=	$laserDetails->laser_exposure;
	
	$laser_chk_count				=	$laserDetails->laser_chk_count;
	$laser_count_detail				=	$laserDetails->laser_count;
	
	$laser_chk_post_progress		=	$laserDetails->laser_chk_post_progress;
	$laser_post_progress_detail		=	$laserDetails->laser_post_progress;
	
	$laser_chk_post_operative		=	$laserDetails->laser_chk_post_operative;
	$laser_post_operative_detail	=	$laserDetails->laser_post_operative;
	
	$laser_chk_procedure_image		=	$laserDetails->laser_chk_procedure_image;
		
}
else
{
	
	$laser_chk_chief_complaint 		=	'';
	$laser_chief_complaint_detail	=	$objManageData->getDefault('laserpredefine_chiefcomplaint_tbl','name');
	
	$laser_chk_present_illness_hx	=	'';
	$laser_present_illness_hx_detail=	$objManageData->getDefault('laserpredefine_hx_present_illness_tbl','name');
				
	$laser_chk_past_med_hx			=	'';
	$laser_past_med_hx_detail		=	$objManageData->getDefault('laserpredefine_past_medical_hx_tbl','name');
	
	$laser_chk_medication			=	'';
	$laser_medication_detail		=	$objManageData->getDefault('medications','name');
	
	$laser_chk_sle					=	'';
	$laser_sle_detail				=	$objManageData->getDefault('laserpredefine_sle_tbl','name');
	
	$laser_chk_fundus_exam			=	'';
	$laser_fundus_exam_detail		=	$objManageData->getDefault('laserpredefine_fundus_exam_tbl','name');
	
	$laser_chk_mental_state			=	'';
	$laser_mental_state_detail		=	$objManageData->getDefault('laserpredefine_mentalstate_tbl','name');
	
	$laser_chk_pre_op_diagnosis		=	'';
	$laser_pre_op_diagnosis			=	'';
	
	$laser_chk_spot_duration		=	'';
	$laser_spot_duration_detail		=	$objManageData->getDefault('laserpredefine_spot_duration_tbl','name');
	
	$laser_chk_spot_size			=	'';
	$laser_spot_size_detail			=	$objManageData->getDefault('laserpredefine_spot_size_tbl','name');
	
	$laser_chk_power				=	'';
	$laser_power_detail				=	$objManageData->getDefault('laserpredefine_power_tbl','name');
	
	$laser_chk_shots				=	'';
	$laser_shots_detail				=	$objManageData->getDefault('laserpredefine_shots_tbl','name');
	
	$laser_chk_total_energy			=	'';
	$laser_total_energy_detail		=	$objManageData->getDefault('laserpredefine_total_energy_tbl','name');
	
	$laser_chk_degree_of_opening	=	'';
	$laser_degree_of_opening_detail	=	$objManageData->getDefault('laserpredefine_degree_opening_tbl','name');
	
	$laser_chk_exposure				=	'';
	$laser_exposure_detail			=	$objManageData->getDefault('laserpredefine_exposure_tbl','name');
	
	$laser_chk_count				=	'';
	$laser_count_detail				=	$objManageData->getDefault('laserpredefine_count_tbl','name');
	
	$laser_chk_post_progress		=	'';
	$laser_post_progress_detail		=	$objManageData->getDefault('laserpredefine_postoperativestatus_tbl','name');
	
	$laser_chk_post_operative		=	'';
	$laser_post_operative_detail	=	$objManageData->getDefault('laserpredefine_postprogressnotes_tbl','name');
	
	$laser_chk_procedure_image		=	'';
	
}

//START GETTING DX CODE TYPE
$sqlStr = "SELECT * FROM surgerycenter WHERE surgeryCenterId = '1'";
$sqlQry = imw_query($sqlStr);
$rowsCount = imw_num_rows($sqlQry);
$DxCodeType = 'icd9';
if($rowsCount>0){
	$sqlRows = imw_fetch_array($sqlQry);
	$DxCodeType= $sqlRows['diagnosis_code_type'];
}
//END GETTING DX CODE TYPE
?>

<form name="frmlaserprocedure_templete" action="laser_procedure_templete.php" method="post">
	
    <input type="hidden" id="selected_frame_name_id" name="selected_frame_name" value="">
    <input type="hidden" name="templateid" id="templateid" value="<?php echo $templeteID_show; ?>">
    <input type="hidden" name="DxCodeType" id="DxCodeType" value="<?=$DxCodeType?>">	
    <input type="hidden" name="cpt_id" id="cpt_id" value="">	
    <input type="hidden" name="cpt_id_default" id="cpt_id_default" value="">	
    <input type="hidden" name="dx_id" id="dx_id" value="">	
    <input type="hidden" name="dx_id_default" id="dx_id_default" value="">
    <input type="hidden" name="dx_id_icd10" id="dx_id_icd10" value="">	
    <input type="hidden" name="dx_id_default_icd10" id="dx_id_default_icd10" value="">	
    
    <input type="hidden" name="sbtTemplate" id="sbtTemplate" value="">	
    <input type="hidden" name="divId" id="divId">
    <input type="hidden" name="counter" id="counter">
    
    <input type="hidden" name="secondaryValues" id="secondaryValues">
    <input type="hidden" name="hiddPreDefineId" id="hiddPreDefineId">
    <input type="hidden" name="save_id" id="save_id" value="">
    <span id="selectedLaserProcedureNameId"  class="text_10b blue_txt" style="padding-left:8;white-space:nowrap; ">
    <?php
		$GET_PROCEDURE="";
        if($_REQUEST['laser_ProcedureId']) {
            $laserProcedureNameDetails = $objManageData->getArrayRecords('procedures', 'procedureId', $_REQUEST['laser_ProcedureId']);
            foreach($laserProcedureNameDetails as $laserProcedureMainName){
                $GET_PROCEDURE=ucfirst($laserProcedureMainName->name); 
            }
                                                
                                            }
                                        ?>							
    </span>
	
       <div class="margin_bottom_mid_adjustment scheduler_margins_head ">
    	<div class="container-fluid padding_0">
        	<div class="inner_surg_middle ">
				 <div style="" id="" class="all_content1_slider ">	         
                          <div class="all_admin_content_agree wrap_inside_admin">
                          <div class=" subtracting-head">
                         <div class="head_scheduler new_head_slider padding_head_adjust_admin"><span>Laser</span></div>
                           
                        </div>   
                    	  <div class="wrap_inside_admin">
							<div class="col-lg-3 col-sm-4 col-xs-12 col-md-3">
                                <div class="sidebar-wrap-op">
                                <a class="header_side" href="javascript:void(0)"> Laser Procedure Templates </a>
                                   <ul class="list-group scrollable_yes_left">
                                      <?php
											$laser_procedure_templete=addslashes($_REQUEST['laser_ProcedureId']);
											$sel_laser_templete="select * from laser_procedure_template where laser_procedureID='$laser_procedure_templete' order by template_name";
											$res_sel_laser_templete=imw_query($sel_laser_templete)or die("not selected".imw_error());
											while($result_res_sel_laser_templete=imw_fetch_array($res_sel_laser_templete)){
										?>
                                       <a href="laser_procedure_templete.php?templeteID_show=<?php echo $result_res_sel_laser_templete['laser_templateID'];?>&amp;laser_ProcedureId=<?php echo $laser_ProcedureId; ?>" class="list-group-item border-bb">
                                          <label><input type="checkbox" name="chkBox[]" value="<?php echo $result_res_sel_laser_templete['laser_templateID']; ?>"></label>&nbsp;&nbsp;&nbsp;<?php echo stripslashes(ucfirst($result_res_sel_laser_templete['template_name'])); ?>
                                        </a>
                                        <?php } ?>	
                                    </ul>	
                                </div>
                               </div>
                               <div class="clearfix visible-xs margin_adjustment_only"></div>
                               	<div class="col-lg-9 col-sm-8 col-xs-12 col-md-9"> 
                                 <h5 class="ans_pro_h"> <span><?php echo $GET_PROCEDURE; ?></span>  </h5>
                             	 <div class="clearfix  margin_adjustment_only"></div>
                             
                                  <div class="template_wrap scrollable_yes_right">
                                    <div class="form_reg">
                                    	<div class="">
                                        	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                                            	 <div class="col-md-12 col-sm-12 col-xs-12 col-lg-5">
                                                    <label for="s_name">Select Laser Procedure</label>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-7">
                                                    <select class="form-control selectpicker" name="laser_ProcedureId" id="laser_ProcedureId" onChange="javascript:move_templete(this.value);" data-container="body">
                                                        <option value="">Select Laser Procedure</option>
							  <?php
													$category_laser_select='2';
													unset($conditionArr);
													$conditionArr['catId'] = $category_laser_select;
													$laserprocedure_Select = $objManageData->getMultiChkArrayRecords("procedures", $conditionArr,"name","ASC"," AND del_status !='yes' ");
													if($laserprocedure_Select) {
														foreach($laserprocedure_Select as $laser_procedureDetail){
															
													?>
															  <option value="<?php echo $laser_procedureDetail->procedureId; ?>" <?php if($laser_procedureDetail->procedureId==$laser_ProcedureId) { echo "Selected";} else {}?>><?php echo ucfirst($laser_procedureDetail->name);?></option>
							  						<?php
															}
														}
													
													?>
                                                    </select>
                                                </div>	
                                            </div>	
                                        	<div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                                               <div class="col-md-12 col-sm-12 col-xs-12 col-lg-4 text-right">
                                                    <label for="template_name">Template Name</label>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-8 text-left">
                                                    <input type="text" class="form-control" id="template_name" name="template_name" value="<?php echo stripslashes(ucfirst($laserDetails->template_name));?>" />
                                                </div>
                                            </div>	
                                        </div>	
                                    </div>      	
                                          
                                    <div class="clearfix margin_adjustment_only"></div>
                                    <div class="clearfix margin_adjustment_only border-dashed"></div>
                                    <div class="clearfix margin_adjustment_only"></div>
                                    <div class="full_inner_wrap_laser">
                                    	<div class="scanner_win new_s">
                                             <h4 ><span>Surgeon</span></h4>
                                             <div id="laserCodeDiv">
                                             <span id="cptCode">CPT</span>
                                             <span id="dxCode">Dx</span>
                                             </div>
                                             
                                         </div>
                                         <div class="">
                                         	<div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                                 <div class=" caption2">
                                                    <label for="laserprocedure_surgeonId" data-placement="top" >
                                                    Select Surgeon
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
												<?php
												$select_surID=explode(",",$laserDetails->laser_surgeonID);
												//echo $select_surID;
												$userSurgeonsDetails = $objManageData->getArrayRecords('users', 'user_type', 'Surgeon','lname','ASC');
												if($userSurgeonsDetails) {
													foreach($userSurgeonsDetails as $surgeon){
														$deleteStatus = $surgeon->deleteStatus;
														if($deleteStatus=="Yes") {  //IF THIS USER HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
															//DO NOT SHOW DELETED USER IN DROP DOWN
														}else {
														   $selSurg="";
														   if(in_array($surgeon->usersId,$select_surID)){ $selSurg =  "SELECTED";}
														   $surgeonOption .='<option value="'.$surgeon->usersId.'" data-attending = "1" '.$selSurg.'>'.$surgeon->lname.', '.$surgeon->fname.'</option>';
														}
													}
												}
												?>
												
												<select name="laserprocedure_surgeonId[]" id="laserprocedure_surgeonId" class="selectpicker form-control" multiple="multiple" data-container="body">
														<option value="all" data-attending = "0" <?php if($laserDetails->laser_surgeonID=="all" || !$laserDetails->laser_surgeonID) { echo "selected";}?>>Select All Surgeon</option>
														<?php echo $surgeonOption; ?>
												</select> 
                                            </div>
                                         </div>
                                         <div class="clearfix margin_adjustment_only"></div>
                                         <div class="clearfix margin_adjustment_only"></div>
                                         <div class="">
                                         	<div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                                 <div class=" caption2">
                                                    <label for="laserprocedure_insSheet" data-placement="top" >
                                                    Select Instruction Sheet
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
												<?php
													$insRow	=	$objManageData->getArrayRecords('instruction_template','','','instruction_name','Asc');
												?>
                                                <select name="instructionSheetId" id="instructionSheetId" class="selectpicker form-control" data-container="body" data-header="Select Instruction Sheet" title="Select Instruction Sheet" data-size="10" >
                                                	<?php
														
														if(is_array($insRow) && count($insRow) > 0 )
														{
															foreach($insRow as $insData)
															{
																echo '<option value="'.$insData->instruction_id.'" '.($laserDetails->instructionSheetId == $insData->instruction_id ? "Selected" : '').'>'.$insData->instruction_name.'</option>';	
															}
														}
													?>
												
                                                </select> 
                                            </div>
                                         </div>       
                                         <div class="scanner_win new_s">
                                             <h4><span>History</span></h4>
                                         </div>
                                         <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                                 <div class="caption caption2">
                                                    <label >
                                                    		<input name="laser_chk_chief_complaint" type="checkbox" id="chk_chief_complaint" <?php if($laser_chk_chief_complaint=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_chief_complaint','txt_areachief_complaint_admin');">
                                                            <a data-placement="top" class="show-pop-list_cc" style="cursor:pointer;" onClick="return showChiefComplaintAdminFn('txt_areachief_complaint_admin', '', 'no', parseFloat(findPos_X_custom('chk_chief_complaint')), parseFloat(findPos_Y_custom('chk_chief_complaint')-280)),document.getElementById('selected_frame_name_id').value='';"> Chief Complaints	</a> 
                                                    </label>
                                                </div>
                                            </div>
                                       <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurechief_complaint" id="txt_areachief_complaint_admin" <?php if($laser_chk_chief_complaint!="on"){ echo "disabled";}?> ><?php echo stripslashes(ucfirst($laser_chief_complaint_detail));?></textarea>
                                       </div>  
                                       <div class="clearfix"></div>
                                       <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                                 <div class="caption caption2">
                                                    <label>
                                                    		<input name="laser_chk_present_illness_hx" type="checkbox" id="chk_present_illness_hx" <?php if($laser_chk_present_illness_hx=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_present_illness_hx','txt_areahx_illness');">
                                                            <a data-placement="top" class="show-pop-list_hx" style="cursor:pointer;" onClick="return showhx_illnessAdminFn('txt_areahx_illness', '', 'no', parseFloat(findPos_X_custom('chk_present_illness_hx')), parseFloat(findPos_Y_custom('chk_present_illness_hx')-280)),document.getElementById('selected_frame_name_id').value='';"> Hx. of Present Illness	</a> 
                                                    </label>
                                                  
                                                </div>
                                            </div>
                                       <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurehx_illness" id="txt_areahx_illness" <?php if($laser_chk_present_illness_hx!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_present_illness_hx_detail)); ?></textarea>
                                       </div>  
                                       <div class="clearfix"></div>
                                       <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                             <div class="caption caption2">
                                                <label href="" >
                                                        <input name="laser_chk_past_med_hx" type="checkbox" id="chk_past_med_hx" <?php if($laser_chk_past_med_hx=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_past_med_hx','txt_areapast_med_hx');">	
                                                        <a data-placement="top" class="show-pop-list_phx" style="cursor:pointer;" onClick="return showpast_med_hx('txt_areapast_med_hx', '', 'no', parseFloat(findPos_X_custom('chk_past_med_hx')), parseFloat(findPos_Y_custom('chk_past_med_hx')-280)),document.getElementById('selected_frame_name_id').value='';"> Past Med. Hx </a> 
                                                </label>
                                              
                                            </div>
                                         </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurepast_med_hx" id="txt_areapast_med_hx" <?php if($laser_chk_past_med_hx!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_past_med_hx_detail)); ?></textarea>
                                        </div>
                                      <div class="clearfix"></div>  
                                      <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                             <div class="caption caption2">
                                                <label href="" >
                                                        
                                                        <input name="laser_chk_medication" type="checkbox" id="chk_medication" <?php if($laser_chk_medication=="on"){ echo "checked";}?> onClick="javascript:enable_chk_unchk('chk_medication','txt_areamedication_admin');">	
                                                        <a data-placement="top" class="show-pop-list_oc" style="cursor:pointer;" onClick="return showmedication('txt_areamedication_admin', '', 'no', parseFloat(findPos_X_custom('chk_medication')), parseFloat(findPos_Y_custom('chk_medication')-280)),document.getElementById('selected_frame_name_id').value='';"> Ocular Med and Dosage </a> 
                                                </label>
                                              
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Proceduremedication" id="txt_areamedication_admin" <?php if($laser_chk_medication!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_medication_detail));?></textarea>
                                        </div>    
                                       <div class="clearfix"></div>                                                                                            
                                        <div class="scanner_win new_s">
                                             <h4><span>Physical Exam</span>      
                                             </h4>
                                         </div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                                 <div class="caption caption2">
                                                    <label href="" >
                                                            <input name="laser_chk_sle" type="checkbox" id="chk_sle" <?php if($laser_chk_sle=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_sle','txt_areasle_admin');">	
                                                            <a data-placement="top" class="show-pop-list_sle" style="cursor:pointer" onClick="return showsle('txt_areasle_admin', '', 'no', parseFloat(findPos_X_custom('chk_sle')), parseFloat(findPos_Y_custom('chk_sle'))-280),document.getElementById('selected_frame_name_id').value='';"> SLE </a> 
                                                    </label>
                                                  
                                                </div>
                                            </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Proceduresle" id="txt_areasle_admin" <?php if($laser_chk_sle!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_sle_detail));?></textarea>
                                        </div>    
                                        <div class="clearfix"></div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                              <div class="caption caption2">
                                                    <label href="" >
                                                           <input name="laser_chk_fundus_exam" type="checkbox" id="chk_fundus_exam" <?php if($laser_chk_fundus_exam=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_fundus_exam','txt_areafundus_exam_admin');">	
                                                            <a data-placement="top" class="show-pop-list_fundus" style="cursor:pointer;" onClick="return showfundus_exam('txt_areafundus_exam_admin', '', 'no', parseFloat(findPos_X_custom('chk_fundus_exam')), parseFloat(findPos_Y_custom('chk_fundus_exam'))-280),document.getElementById('selected_frame_name_id').value='';"> Fundus Exam</a> 
                                                    </label>
                                                </div>
                                            </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurefundus_exam" id="txt_areafundus_exam_admin" <?php if($laser_chk_fundus_exam!="on"){ echo "disabled";}?> ><?php echo stripslashes(ucfirst($laser_fundus_exam_detail));?></textarea>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                          <div class="caption caption2">
                                            <label href="" >
                                               <input name="laser_chk_mental_state" type="checkbox" id="chk_mental_state" <?php if($laser_chk_mental_state=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_mental_state','txt_areamental_state_admin');">
                                                <a data-placement="top" class="show-pop-list_me" style="cursor:pointer;" onClick="return showmental_state('txt_areamental_state_admin', '', 'no', parseFloat(findPos_X_custom('chk_mental_state')), parseFloat(findPos_Y_custom('chk_mental_state'))-280),document.getElementById('selected_frame_name_id').value='';"> Mental Exam  </a> 
                                            </label>
                                          </div>
	                                    </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                           <textarea class="form-control" name="laser_Proceduremental_state" id="txt_areamental_state_admin" <?php if($laser_chk_mental_state!="on"){ echo "disabled";}?>  ><?php echo stripslashes(ucfirst($laser_mental_state_detail));?></textarea>
                                        </div>        
                                        <div class="clearfix"></div> 
                                         <div class="scanner_win new_s">
                                             <h4><span>Pre-Op Diagnosis</span> </h4>
                                         </div>
                                         <div class="clearfix"></div>
                                         <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                             <div class="caption caption2">
                                                    <label href="" >
                                                       <input name="laser_chk_pre_op_diagnosis" type="checkbox" id="chk_pre_op_diagnosis" <?php if($laser_chk_pre_op_diagnosis=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_pre_op_diagnosis','txt_area_pre_op_diagnosis_admin');">	
                                                        <a data-placement="top" class="show-pop-list_preop" style="cursor:pointer;" onClick="return showpre_op_diagnosis('txt_area_pre_op_diagnosis_admin', '', 'no', parseFloat(findPos_X_custom('chk_pre_op_diagnosis')), parseFloat(findPos_Y_custom('chk_pre_op_diagnosis')-280)),document.getElementById('selected_frame_name_id').value='';"> Pre-Op Diagnosis  </a> 
                                                    </label>
                                                 
                                                </div>
                                            </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedure_pre_op_diagnosis" id="txt_area_pre_op_diagnosis_admin" <?php if($laser_chk_pre_op_diagnosis!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_pre_op_diagnosis));?></textarea>
                                        </div> 
                                       <div class="clearfix"></div>
                                         <div class="scanner_win new_s">
                                             <h4><span>Drawing</span> </h4>
                                         </div> 
                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                         <div class="caption caption2">
                                                <label href="" >
                                                        <input name="laser_chk_procedure_image" type="checkbox" id="chk_procedure_image" <?php if($laserDetails->laser_chk_procedure_image=="on"){ echo "checked";}?> > <a style="color:#000;"> Drawing  </a>
                                                </label>
                                            </div>
                                        </div> 
                                        <div class="clearfix"></div>
                                        <div class="scanner_win new_s">
                                             <h4><span>Procedure Notes</span></h4>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                    <input name="laser_chk_spot_duration" type="checkbox" id="chk_spot_duration" <?php if($laser_chk_spot_duration=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_spot_duration','txt_areaspot_duration_admin');">	
                                                    <a data-placement="top" class="show-pop-list_sd" style="cursor:pointer;" onClick="return showspot_duration('txt_areaspot_duration_admin', '', 'no', parseFloat(findPos_X_custom('chk_spot_duration')), parseFloat(findPos_Y_custom('chk_spot_duration')-280)),document.getElementById('selected_frame_name_id').value='';"> Spot Duration   </a> 
                                                </label>
                                                  
                                           </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurespot_duration" id="txt_areaspot_duration_admin" <?php if($laser_chk_spot_duration!="on"){ echo "disabled";}?> ><?php echo stripslashes(ucfirst($laser_spot_duration_detail));?></textarea>
                                        </div> 
                                            <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label>
                                                   <input name="laser_chk_spot_size" type="checkbox" id="chk_spot_size" <?php if($laser_chk_spot_size=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_spot_size','txt_areaspot_size_admin');">
                                                    <a data-placement="top" class="show-pop-list_ss" style="cursor:pointer;" onClick="return showspot_size('txt_areaspot_size_admin', '', 'no', parseFloat(findPos_X_custom('chk_spot_size')), parseFloat(findPos_Y_custom('chk_spot_size')-280)),document.getElementById('selected_frame_name_id').value='';"> Spot Size   </a> 
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurespot_size" id="txt_areaspot_size_admin" <?php if($laser_chk_spot_size!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_spot_size_detail));?></textarea>
                                        </div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                  <label href="" >
                                                      <input name="laser_chk_power" type="checkbox" id="chk_power" <?php if($laser_chk_power=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_power','txt_areapower_admin');">
                                                            <a data-placement="top" class="show-pop-list_power" style="cursor:pointer;" onClick="return showpower('txt_areapower_admin', '', 'no', parseFloat(findPos_X_custom('chk_power')), parseFloat(findPos_Y_custom('chk_power')-280)),document.getElementById('selected_frame_name_id').value='';"> Power  </a> 
                                                    </label>
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" <?php if($laser_chk_power!="on"){ echo "disabled";}?> name="laser_Procedurepower" id="txt_areapower_admin" ><?php echo stripslashes(ucfirst($laser_power_detail));?></textarea>
                                        </div>  
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                    <input name="laser_chk_shots" type="checkbox" id="chk_shots" <?php if($laser_chk_shots=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_shots','txt_areashots_admin');">	
                                                        <a data-placement="top" class="show-pop-list_shot" style="cursor:pointer;" onClick="return showspots('txt_areashots_admin', '', 'no', parseFloat(findPos_X_custom('chk_shots')), parseFloat(findPos_Y_custom('chk_shots')-280)),document.getElementById('selected_frame_name_id').value='';"> # of Shots  </a> 
                                                </label>
                                           </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                           <textarea class="form-control" <?php if($laser_chk_shots!="on"){ echo "disabled";}?>  name="laser_Procedureshots" id="txt_areashots_admin"><?php echo stripslashes(ucfirst($laser_shots_detail));?></textarea>
                                        </div>  
                                        <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                    <input name="laser_chk_total_energy" type="checkbox" id="chk_total_energy" <?php if($laser_chk_total_energy=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_total_energy','txt_areatotal_energy_admin');">	
                                                        <a data-placement="top" class="show-pop-list_shot" style="cursor:pointer;" onClick="return showtotal_energy('txt_areatotal_energy_admin', '', 'no', parseFloat(findPos_X_custom('chk_total_energy')), parseFloat(findPos_Y_custom('chk_total_energy'))-280),document.getElementById('selected_frame_name_id').value='';"> Total Energy </a> 
                                                </label>
                                           </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                           <textarea class="form-control" <?php if($laser_chk_total_energy!="on"){ echo "disabled";}?>  name="laser_Proceduretotal_energy" id="txt_areatotal_energy_admin"><?php echo stripslashes(ucfirst($laser_total_energy_detail));?></textarea>
                                        </div>
                                        <div class="clearfix"></div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                    <input name="laser_chk_degree_of_opening" type="checkbox" id="chk_degree_of_opening" <?php if($laser_chk_degree_of_opening=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_degree_of_opening','txt_areadegree_of_opening_admin');">	
                                                    <a data-placement="top" class="show-pop-list_doo" style="cursor:pointer;" onClick="return showdegree_of_opening('txt_areadegree_of_opening_admin', '', 'no', parseFloat(findPos_X_custom('chk_degree_of_opening')), parseFloat(findPos_Y_custom('chk_degree_of_opening')-280)),document.getElementById('selected_frame_name_id').value='';"> Degree of opening  </a> 
                                                </label>
                                                  
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" <?php if($laser_chk_degree_of_opening!="on"){ echo "disabled";}?> name="laser_Procedure_degree_of_opening" id="txt_areadegree_of_opening_admin" ><?php echo stripslashes(ucfirst($laser_degree_of_opening_detail));?></textarea>
                                        </div>  
                                         <div class="clearfix"></div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                   <input name="laser_chk_exposure" type="checkbox" id="chk_exposure" <?php if($laser_chk_exposure=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_exposure','txt_areaexposure_admin');">	
                                                   <a data-placement="top" class="show-pop-list_exposure" style="cursor:pointer;" onClick="return showexposure('txt_areaexposure_admin', '', 'no', parseFloat(findPos_X_custom('chk_exposure')), parseFloat(findPos_Y_custom('chk_exposure')-280)),document.getElementById('selected_frame_name_id').value='';"> Exposure  </a> 
                                                </label>
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                           <textarea class="form-control" <?php if($laser_chk_exposure!="on"){ echo "disabled";}?> name="laser_Procedureexposure" id="txt_areaexposure_admin"><?php echo stripslashes(ucfirst($laser_exposure_detail)); ?></textarea>
                                        </div>  
                                         <div class="clearfix"></div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                <label href="" >
                                                    <input name="laser_chk_count" type="checkbox" id="chk_count" <?php if($laser_chk_count=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_count','txt_areacount_admin');">	
                                                        <a data-placement="top" class="show-pop-list_count" style="cursor:pointer;" onClick="return showcount('txt_areacount_admin', '', 'no', parseFloat(findPos_X_custom('chk_count')), parseFloat(findPos_Y_custom('chk_count')-280)),document.getElementById('selected_frame_name_id').value='';"> Count  </a> 
                                                </label>
                                                  
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                           <textarea class="form-control" <?php if($laser_chk_count!="on"){ echo "disabled";}?> name="laser_Procedurecount" id="txt_areacount_admin"><?php echo stripslashes(ucfirst($laser_count_detail));?></textarea>
                                        </div>  
                                   		<div class="clearfix"></div>
                                        <div class="scanner_win new_s">
                                             <h4><span>Post-Op Orders</span></h4>
                                        </div>	
                                         <div class="clearfix"></div>
                                          <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                    <label>
                                                            <input name="laser_chk_post_progress" type="checkbox" id="chk_post_progress" <?php if($laser_chk_post_progress=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_post_progress','txt_areapost_progress_admin');">	
                                                            <a data-placement="top" class="show-pop-list_postop" style="cursor:pointer;" onClick="return showpost_progress('txt_areapost_progress_admin', '', 'no', parseFloat(findPos_X_custom('chk_post_progress')), parseFloat(findPos_Y_custom('chk_post_progress')-280)),document.getElementById('selected_frame_name_id').value='';"> Post-Op Orders  </a> 
                                                    </label>
                                                  
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurepost_progress" id="txt_areapost_progress_admin" <?php if($laser_chk_post_progress!="on"){ echo "disabled";}?>><?php echo stripslashes(ucfirst($laser_post_progress_detail));?></textarea>
                                        </div> 
                                        <div class="clearfix"></div>
                                        <div class="scanner_win new_s">
                                             <h4><span>Progress Report</span></h4>
                                        </div>	
                                        
                                         <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                    <label href="" >
                                                            <input name="laser_chk_post_operative" type="checkbox" id="chk_post_operative" <?php if($laser_chk_post_operative=="on"){ echo "checked";}?> onClick="javascript: enable_chk_unchk('chk_post_operative','txt_areapost_operative_admin');">	
                                                            <a data-placement="top" class="show-pop-list_pr" style="cursor:pointer;" onClick="return showpost_operative('txt_areapost_operative_admin', '', 'no', parseFloat(findPos_X_custom('chk_post_operative')), parseFloat(findPos_Y_custom('chk_post_operative')-280)),document.getElementById('selected_frame_name_id').value='';"> Progress Report </a> 
                                                    </label>
                                                  
                                              </div>
                                        </div>
                                        <div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                            <textarea class="form-control" name="laser_Procedurepost_operative" id="txt_areapost_operative_admin" <?php if($laser_chk_post_operative!="on"){ echo "disabled";}?> ><?php echo stripslashes(ucfirst($laser_post_operative_detail));?></textarea>
                                        </div> 
                                        <div class="clearfix margin_adjustment_only"></div>
                                        
                                        <div class="scanner_win new_s">
                                             <h4><span >Pre Op Medication Orders</span></h4>
                                        </div>	
                                       	
                                         <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                                            <div class="caption caption2">
                                                    <label href="" >
                                                            <!--
                                                            <input name="laser_chk_preop_medication" type="checkbox" id="chk_preop_medication" <?php if($laserDetails->laser_chk_preop_medication=="on"){ echo "checked";}?> onClick="javascript: enable_sheet('chk_preop_medication','laserSpreadSheetPreOpMed');" />-->
                                                            <a data-placement="top" class="show-pop-list_pr" style="cursor:pointer;" onClick="return showPreOpMediDiv($(this), 20),document.getElementById('selected_frame_name_id').value='laserSpreadSheetPreOpMed';"> Pre Op Medication Orders </a> 
                                                    </label>
                                                  
                                              </div>
                                        </div>
                                        
                                    
                                   		<div class="col-md-6 col-sm-8 col-xs-12 col-lg-6">
                                    		<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12  table-condensed cf  width_table table-striped" >
                                            	<thead>
                                            		<Tr>
                                                    	<th style="width:33%;"> Medication </th>
                                                        <th style="width:33%;"> Strength </th>
                                                        <th style="width:auto;"> Direction </th>
                                                  	</Tr>
                                          		</thead>
                                                <tbody>
                                                	<tr>
                                                    		<td colspan="3" style="padding:1px 0">
                     											<div class="over_wrap" id="laserSpreadSheetPreOpMed" >	
																	<?PHP
                                                                          include_once 'laser_preop_order_sheet.php';
                                                                    ?>
                                                    			</div>
                                                         	</td>
               										</tr>	
                                                </tbody>
                                         	</table>
                                    </div>
                                    
                                    <div class="clearfix margin_adjustment_only"></div>
                                    
                                    </div>
                                </div>
                             </div>	         
                          </div>		
                     </div>
                    </div> 
                  </div>  
                   <!-- NEcessary PUSH     -->	 
                  <div class="push"></div>
                  <!-- NEcessary PUSH     -->
            </div>
        </div>   
	</div> 
	
	</form>
<script>
$(function()
{
	$("a[class^='show-pop-list_']").each(function(){
			$(this).css({'color':'#800080','font-weight':'600'});
	});
});
</script>
</body>
</html>