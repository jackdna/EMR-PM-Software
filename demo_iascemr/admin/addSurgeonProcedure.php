<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;

$surgeonId = $_REQUEST['surgeonsList'];
if($surgeonId==''){ 
	$surgeonId = $_REQUEST['surgeonId'];
}

$profileId = $_REQUEST['profileId'];
//$profileId = 144;
$procedureId=array();
$andProfileDelCond = "  AND del_status ='' ";
if($profileId<>'') {  //IF SURGEON PROFILE ALREADY EXISTS
	//GETTING PROCEDURE ID IN ARRAY
		$selectprocedureIdQry = "select * from surgeonprofileprocedure where profileId = '$profileId'";
		$selectprocedureIdRes = imw_query($selectprocedureIdQry) or die(imw_error());
		$selectprocedureIdNumRow = imw_num_rows($selectprocedureIdRes);
		if($selectprocedureIdNumRow>0) {
			while($selectprocedureIdRow = imw_fetch_array($selectprocedureIdRes)) {
				$procedureId[] = $selectprocedureIdRow['procedureId'];
			}
		}	
	//GETTING PROCEDURE ID IN ARRAY
	
	//GETTING PROFILE NAME 
		$selectProfileNameQry = "select * from surgeonprofile where surgeonProfileId = '$profileId'".$andProfileDelCond;
		$selectProfileNameRes = imw_query($selectProfileNameQry) or die(imw_error());
		$selectProfileNameNumRow = imw_num_rows($selectProfileNameRes);
		if($selectProfileNameNumRow>0) {
			$selectProfileNameRow = imw_fetch_array($selectProfileNameRes);
			$profileName = $selectProfileNameRow['profileName'];
			$defaultProfile = $selectProfileNameRow['defaultProfile'];
			$postOpDrop = $selectProfileNameRow['postOpDrop'];
		}
	//GETTING PROFILE NAME	
	
}else {
	$procedureIdBack = $_REQUEST['chkBoxBack'];
	if($procedureIdBack) {
		$procedureIdExplode = explode(',',$procedureIdBack);
		
		$procedureId = $procedureIdExplode;
		$profileName = $_REQUEST['profileName'];
		$defaultProfile = $_REQUEST['defaultProfile'];
		$postOpDrop = $_REQUEST['postOpDrop'];
		$pref_card= $_REQUEST['pref_card'];
	}	
	
}

//CHECK IF PROCEDURE ALREADY EXIST BY IN ANOTHER FILE BY THIS SURGEON
if($surgeonId<>"") {
	
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND surgeonProfileId != '$profileId'".$andProfileDelCond;
	$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
	while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
		$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
	}
	if(is_array($surgeonProfileIdArr)){
		$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
	}else {
		$surgeonProfileIdImplode = 0;
	}
	$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode)";
	$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
	$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId[] = $selectSurgeonProcedureRow['procedureId'];
		}
	}		

}
//CHECK IF PROCEDURE ALREADY EXIST BY IN ANOTHER FILE BY THIS SURGEON
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Select Procedure</title>
<?php include("adminLinkfile.php");?>
<script>
function checkColor(chbxId,tdMainId,tdPrevcolor) {
	//alert(tdPrevcolor);
	
	if(document.getElementById(chbxId).checked==true) {
		document.getElementById(tdMainId).style.background = "#FFFFCC";
	}else {
		document.getElementById(tdMainId).style.background = tdPrevcolor;
	}
}

function procNext() {
	
	//CODE TO CHECK --> AT LEAST ONE PROCEDURE CHECKBOX MUST BE SELECTED
	var filledIn = false;     
	var defaultProfileChkbx
	if(document.addProceduresFrm.defaultProfile) {
		defaultProfileChkbx = document.addProceduresFrm.defaultProfile.checked;
	}
	var objGrp=document.getElementsByName("chkBox[]");
	for(i=0;i<objGrp.length;i++){
		if(objGrp[i].checked==true){
			filledIn=true;
		}
	}
	//CODE TO CHECK --> AT LEAST ONE PROCEDURE CHECKBOX MUST BE SELECTED
	
	if(document.addProceduresFrm.elem_profileName.value=="") {
		alert('Please enter profile name');
		document.addProceduresFrm.elem_profileName.focus();
		return false;
		
	}else if(!filledIn && defaultProfileChkbx==false){
		//alert(document.addProceduresFrm.defaultProfile.checked);
		alert('Please select atleast one procedure');
		return false;
	}else {
		document.addProceduresFrm.submit();
		top.frames[0].document.frameSrc.source.value = 'addSurgeonProfile.php';
		top.frames[0].document.getElementById('backButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('saveButton').style.display = 'inline-block';
		top.frames[0].document.getElementById('deleteSelected').style.display = 'none';
		//alert(top.frames[0].document.getElementById('cancelButton'));
		top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';
		
		return true;
	}
		
}	

function chkAleadyExist(procId,existingProcId,chbxId,tdMainId,tdPrevcolor) {
	//alert('helo');
	if(procId==existingProcId) {
		alert("Procedure already used in other profile by this surgeon");
		return false;
	}else {
		
		if(document.getElementById(chbxId).checked==true) {
			document.getElementById(tdMainId).style.background = "#FFFFCC";
		}else {
			document.getElementById(tdMainId).style.background = tdPrevcolor;
		}
		return true;
	}
}


</script>
</head>
<body>
<?php if($surgeonId) { ?>
<Div class="all_admin_content_agree wrap_inside_admin">
    <Div class="wrap_inside_admin  adj_tp_table">
        <div class="scheduler_table_Complete">    
	<form name="addProceduresFrm" action="addSurgeonProfile.php" method="post">
	<input type="hidden" name="contentOf" id="contentOf" value="<?php echo $content; ?>">
	<input type="hidden" name="table" id="table" value="<?php echo $table; ?>">
	<input type="hidden" name="idField" id="idField" value="<?php echo $idField; ?>">
	<input type="hidden" name="deleteSelected" id="deleteSelected" value="">
	<input type="hidden" name="surgeonsList" id="surgeonsList" value="<?php echo $_REQUEST['surgeonsList'];?>">
	<input type="hidden" name="profileId" id="profileId" value="<?php echo $profileId;?>">
	<input type="hidden" name="seqNmbr" id="seqNmbr" value="<?php echo $_REQUEST['seqNmbr'];?>">
    <div id="procedure-header-content" style="float:left; display:inline-block; width:100%; margin-bottom:5px">
    <div  class="col-md-1 col-lg-1 col-sm-3 col-xs-3 text-right" style="font-weight:bold">Profile Name</div>
    <div  class="col-md-3 col-lg-3 col-sm-6 col-xs-6 text-left"><input class="form-control" type="text" name="elem_profileName" value="<?php echo $profileName; ?>"></div>
    <div  class="col-md-2 col-lg-2 col-sm-6 col-xs-6 text-right" style="font-weight:bold">Preference Card</div>
     <div class="col-md-3 col-lg-3 col-sm-6 col-xs-6 text-right">
     <select name="pref_card" id="pref_card" class="form-control" <?php echo($profileId)?' disabled':'';?> onChange="checkForExisting(this);">
     <option value=""> Select Card</option>
     <?php
     //$procedureIdBack
		//$getDetails = $objManageData->getArrayRecords('procedureprofile pp, procedures pr','pp.save_status' ,'1' ,'pp.procedureName','ASC'," AND pp.procedureId = pr.procedureId AND pr.del_status !='yes' ");
        $getDetails = array();
		$procProfileQry = "SELECT pp.procedureId,pp.procedureName FROM procedureprofile pp 
				INNER JOIN procedures pr ON(pr.procedureId = pp.procedureId AND pr.del_status !='yes')
				WHERE pp.save_status = '1' ORDER BY pp.procedureName ASC";
		$procProfileRes = imw_query($procProfileQry) or die(imw_error());
		if($procProfileRes){
			while($procProfileRow = imw_fetch_object($procProfileRes)){
				$getDetails[] = $procProfileRow;
			}		
		}
        if(count($getDetails)>0){
            $i=1;
            foreach($getDetails as $key => $detailsPreDefine){
                $preDefineDesc = $detailsPreDefine->procedureName;
                
				//check is that selected
				$selected='';
				$selected=($pref_card==$detailsPreDefine->procedureId)?' selected':'';
				echo'<option value="'.$detailsPreDefine->procedureId.'" '.$selected.'>'.$preDefineDesc.'</option>';
			}//end of foreach
		}//end of if
	 ?>
     </select></div>
    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf table-striped">
    <thead>
    	<tr>
        	<th class="text-left">Select Procedure
            <!--<div style="float:right">Default Profile&nbsp;
            <input type="checkbox" name="defaultProfile" id="defaultProfile" value="1" <?php if($defaultProfile==1) { echo "checked"; }?>>
            <input type="hidden" name="hidd_defaultProfileStatus" value="true">
			</div>-->
            </th>
        </tr>
    </thead>
    </table>  
   </div>
     
   <div id="procedure-body-content" style="overflow:auto;float:left; display:inline-block; width:100%">
	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf table-striped">
    <tr>
        <?php
        $getDetails = $objManageData->getArrayRecords('procedures','' ,'' ,'name','ASC');
        if(count($getDetails)>0){
            $i=1;
            foreach($getDetails as $key => $detailsPreDefine){
                $preDefineDesc = $detailsPreDefine->name;
                $preDefineCatId = $detailsPreDefine->catId;
                $preDefineDelStatus = $detailsPreDefine->del_status;
                
                //DO NOT SHOW DELETED PROCEDURES IF NOT SELECTED
                if(!in_array($detailsPreDefine->procedureId,$procedureId) && $preDefineDelStatus=="yes") {
                    continue;
                }
                
                //CATEGORY ID DESC
                    $getCategoryIDDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $preDefineCatId);
                    $catDesc = $getCategoryIDDetails->name;
                //CATEGORY ID DESC
                $preDefinecode = $detailsPreDefine->code;						
                $preDefineID = $detailsPreDefine->proceduresCategoryId;						
                if($i%2==0) {
                    $bgcolor = "#FFFFFF";
                }else {
                    $bgcolor = "";
                }
                ++$pRtr;
                $procedureAlreadyExistId=$exist='';
                if(is_array($surgeonProfileProcedureId)) {
                    if(in_array($detailsPreDefine->procedureId,$surgeonProfileProcedureId)) {
                        $procedureAlreadyExistId =  $detailsPreDefine->procedureId;
						$exist=1;
                    }
                }
                ?>
                <td style="background-color:<?php echo $bgcolor;?>; width:2%; vertical-align:top" class="col-xs-2 col-md-1 col-lg-1 col-sm-1"><input type="hidden" name="procedureId[]" value="<?php echo $detailsPreDefine->procedureId; ?>"><input type="checkbox" id="chkBox<?php echo $detailsPreDefine->procedureId; ?>" name="chkBox[]" value="<?php echo $detailsPreDefine->procedureId; ?>" <?php if(in_array($detailsPreDefine->procedureId,$procedureId)) { echo "checked"; }?> onClick="return chkAleadyExist(this.value,'<?php echo $procedureAlreadyExistId;?>','chkBox<?php echo $detailsPreDefine->procedureId; ?>','tdDesc<?php echo $detailsPreDefine->procedureId; ?>','<?php echo $bgcolor;?>'); return checkColor('chkBox<?php echo $detailsPreDefine->procedureId; ?>','tdDesc<?php echo $detailsPreDefine->procedureId; ?>','<?php echo $bgcolor;?>');" data-exist="<?php echo $exist;?>"></td>							
                <td style="background-color:<?php echo $bgcolor;?>; width:31%; vertical-align:top" id="tdDesc<?php echo $detailsPreDefine->procedureId; ?>">
                  <?php echo $preDefineDesc; ?>
                </td>
                <?php
                    if($pRtr>2){
                        $pRtr = 0;
                        $i++; //CODE FOR BGCOLOR PER LINE
                        echo '</tr><tr style="height:25px;">';
                    }
                }
            }
            ?>
        </tr>	
    </table>
   </div>
   <div class="clear"></div>
   <div id="procedure-footer-content" style="float:left; display:inline-block; width:100%; margin-top:10px">
   <a href="javascript:void(0)" alt="Next" title="Next" onClick="return procNext();" class="btn btn-info">&nbsp; Next &nbsp;</a>
   </div>
	</form>
    </div>
    </Div>
    </Div>
    <script>top.frames[0].document.getElementById('cancelButton').style.display = 'inline-block';</script>
<?php
}
?>	
<script>
$(window).load(function()
	{
		var LDL	=	function()
		{
			var surHeader=$("#procedure-header-content").outerHeight(true);
			var surFooter=$("#procedure-footer-content").outerHeight(true);
			var total	 =top.frames[0].frames[0].$("#surgeonProfileFrame").height()-70;
			
			//H=H-$("#procedure-header-content").height();alert(H);
			H=total-(parseInt(surHeader)+parseInt(surFooter));
			$("#procedure-body-content").height(H);
		}
		LDL();
		$(window).resize(function(e) {
           LDL();
        });
	});
	
	function checkForExisting(obj)
	{
		if(obj.value)	
		{
			var selVal=obj.value;
			var chkObj=$(":checkbox[value="+selVal+"]");
			chkObj.prop("checked",true);	
			if(chkObj.attr("data-exist"))
			{
				alert("Procedure already used in other profile by this surgeon");
				chkObj.prop("checked",false);	
				obj.selectedIndex = 0;
			}
		}
	}
		
	function addRemove(obj,existVal,text)
	{
		if($('#profileId').val()=='')
		{
			var id=obj.value;
			if(id==existVal)return false;
			$("#pref_card").removeAttr('disabled');
			if(obj.checked==false){
				//remove option
				$("#pref_card option[value='"+id+"']").remove();	
			}
			else
			{
				//add option
				$('#pref_card').append($('<option>', {
					value: id,
					text: text
				}));
			}
			
			$("#pref_card").append($("#pref_card option").remove().sort(function(a, b) {
				var at = $(a).text(), bt = $(b).text();
				return (at > bt)?1:((at < bt)?-1:0);
			}));
			
			/*var options = $('#pref_card option');
			var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
			arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
			options.each(function(i, o) {
			  o.value = arr[i].v;
			  $(o).text(arr[i].t);
			});*/

			var length = $('#pref_card > option').length;
			if(length==1)
			$("#pref_card").attr('disabled',true);
		}
	}
</script>
</body>
</html>