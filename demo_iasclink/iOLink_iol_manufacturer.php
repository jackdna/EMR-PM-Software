<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
$bgHeadingImage = "images/header_bg.jpg";
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<script>
window.focus();
var record_changes = false;	
function closeModel(){
	if(document.getElementById('preDefineDivOpenClose').value==""){
		if(document.getElementById('evaluationPreDefineModelDiv').style.display=="block"){
			document.getElementById('evaluationPreDefineModelDiv').style.display="none";
		}
	}
	if(document.getElementById('preDefineDivOpenClose').value=="open"){
		document.getElementById('preDefineDivOpenClose').value="";
	}
	
}
function showIolinkPreDefineModelFn(name1, name2, c, posLeft, posTop){	
	document.getElementById("evaluationPreDefineModelDiv").style.display = 'block';
	document.getElementById("evaluationPreDefineModelDiv").style.left = posLeft;
	document.getElementById("evaluationPreDefineModelDiv").style.top = posTop;
}	
function check_record_changes() { 	
	if( record_changes === false ) {
		if(opener) { opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}
	}
}
function chkSaveForm() {
	
	if(!document.frm_iol_manufacturer.manufacture.value && !document.frm_iol_manufacturer.model.value && !document.frm_iol_manufacturer.Diopter.value) {
		alert('Please enter record');
	}else {
		record_changes = true;
		document.frm_iol_manufacturer.submit();
	}	
	
}
var xmlHttp='';
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
//START FUNCTION TO GET MANUFACTURER LENS BRAND

</script>
<?php
$spec= "
</head>
<body onUnload='check_record_changes();'>";

include_once("common/link_new_file.php");
include_once("common/functions.php");
include_once("admin/classObjectFunction.php");
include_once("iolinkPreDefineModel.php");
include("common/iOLinkCommonFunction.php");

$objManageData = new manageData;
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$patient_id = $_REQUEST['patient_id'];
$iol_manufacturer_id = $_REQUEST['iol_manufacturer_id'];
$mode = $_REQUEST['mode'];
$showMsg = '';
if($_REQUEST['saveData']<>""){
	$manufacture = addslashes($_REQUEST['manufacture']);
	$lensBrand = addslashes($_REQUEST['lensBrand']);
	
	$model = addslashes($_REQUEST['model']);
	$Diopter = addslashes($_REQUEST['Diopter']);
	
	unset($iolinkManufacturerArrayRecord);
	$iolinkManufacturerArrayRecord['patient_id'] 			= $patient_id;
	$iolinkManufacturerArrayRecord['patient_in_waiting_id'] = $patient_in_waiting_id;
	$iolinkManufacturerArrayRecord['manufacture'] 			= $manufacture;
	$iolinkManufacturerArrayRecord['lensBrand'] 			= $lensBrand;
	$iolinkManufacturerArrayRecord['model'] 	  			= $model;
	$iolinkManufacturerArrayRecord['Diopter'] 	  			= $Diopter;
	
	if($iol_manufacturer_id && $mode=='edit') {
		$objManageData->updateRecords($iolinkManufacturerArrayRecord, 'iolink_iol_manufacturer', 'iol_manufacturer_id', $iol_manufacturer_id);
		setReSyncroStatus($patient_in_waiting_id,'iol_manufacturer');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)		
		echo "<script>alert('Record Modified');</script>";
	}else if($iol_manufacturer_id && $mode=='delete') {
		$objManageData->delRecord('iolink_iol_manufacturer', 'iol_manufacturer_id', $iol_manufacturer_id);
		echo "<script>alert('Record Deleted');</script>";
	}else {	
		$objManageData->addRecords($iolinkManufacturerArrayRecord, 'iolink_iol_manufacturer');
		setReSyncroStatus($patient_in_waiting_id,'iol_manufacturer');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
		echo "<script>alert('Record Saved');</script>";
	}
	echo "<script>var record_changes=true;location.href='iOLink_iol_manufacturer.php?patient_in_waiting_id=".$patient_in_waiting_id."&patient_id=".$patient_id."'</script>";
}


//START GET Patient Data
$ptDataArr = $objManageData->get_patient_data($patient_id,"Concat(patient_lname,', ',patient_fname,' ',patient_mname) as patientName, date_of_birth");
$patientName= trim($ptDataArr["patientName"]);
$patientDOB = $objManageData->getDateFormat($ptDataArr["date_of_birth"],'/');
//END GET NO MEDICATION STATUS

//START VIEW RECORD

if($iol_manufacturer_id) {
	$iolinkViewIolManufacturerIdQry 	= "select * from iolink_iol_manufacturer where iol_manufacturer_id = '".$iol_manufacturer_id."'";
	$iolinkViewIolManufacturerIdRes 	= imw_query($iolinkViewIolManufacturerIdQry) or die(imw_error());
	$iolinkViewIolManufacturerIdNumRow 	= imw_num_rows($iolinkViewIolManufacturerIdRes);
	if($iolinkViewIolManufacturerIdNumRow>0) {
		$iolinkViewIolManufacturerIdRow = imw_fetch_array($iolinkViewIolManufacturerIdRes);
		$patient_id 					= $iolinkViewIolManufacturerIdRow['patient_id'];
		$patient_in_waiting_id 			= $iolinkViewIolManufacturerIdRow['patient_in_waiting_id'];
		$manufacture 					= $iolinkViewIolManufacturerIdRow['manufacture'];
		$lensBrand 						= $iolinkViewIolManufacturerIdRow['lensBrand'];
		$model 							= $iolinkViewIolManufacturerIdRow['model'];
		$Diopter 						= $iolinkViewIolManufacturerIdRow['Diopter'];
	}
}
//END VIEW RECORD
$intCount = 0;

?>
<script>
	function getLensBrand(manufacturerName,pagename) {
		manufacturerName = manufacturerName.replace('&','~');
		xmlHttp=GetXmlHttpObject();
		if (xmlHttp==null)
			{
				alert ("Browser does not support HTTP Request");
				return;
			} 
		var patient_id1 = '<?php echo $patient_id;?>';
		var patient_in_waiting_id1 = '<?php echo $_REQUEST["patient_in_waiting_id"];?>';
		var url=pagename
		url=url+"?manufacture="+manufacturerName
		url=url+"&patient_id="+patient_id1
		url=url+"&patient_in_waiting_id="+patient_in_waiting_id1
		xmlHttp.onreadystatechange=getLensBrandFun;
		xmlHttp.open("GET",url,true)
		xmlHttp.send(null)
	}
	function getLensBrandFun() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
			{ 
				if(document.getElementById('lensBrandTD')) {
					document.getElementById('lensBrandTD').innerHTML=xmlHttp.responseText;
				}
			}
	}
//END FUNCTION TO GET MANUFACTURER LENS BRAND	
</script>

<form name="frm_iol_manufacturer" action="iOLink_iol_manufacturer.php" method="post" onClick="closeModel();">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
	<input type="hidden" name="saveData" id="saveData" value="Save">
	<input type="hidden" name="preDefineDivOpenClose" id="preDefineDivOpenClose" value="">
	<input type="hidden" id="bp" name="bp_hidden">
	
	<input type="hidden" id="mode" name="mode" value="<?php echo $mode;?>">
	<input type="hidden" id="iol_manufacturer_id" name="iol_manufacturer_id" value="<?php echo $iol_manufacturer_id;?>">
	<table class="text_10" style="width:100%; border:none;">
		<tr style="height:28px;">
			<td colspan="8" class="text_10b valignMiddle" style="background-image:url(<?php echo $bgHeadingImage;?>);color:#FFFFFF;">
				<table class="table_pad_bdr" style=" border:none; width:100%;">
					<tr>
						<td class="text_10b nowrap" style=" width:50%; font-size:12px; padding-left:3px; text-align:left;">IOL Manufacturer</td>
						<td class="text_10b nowrap" style=" width:50%; font-size:12px; padding-right:3px;"><?php echo $patientName.($patientDOB?' - <small>' .$patientDOB.'</small>':'');?></td>
					</tr>
				</table>	
			</td>
		</tr>
		<tr style="background-color:<?php echo $rowcolor_op_room_record; ?>;" >
            <td class="alignCenter valignMiddle" style=" width:8%;font-size:12px; "> Man</td>


		  	<td style="width:17%;">
                <select name="manufacture" id="manufacture_id" class="text_10" style=" width:110px; font-size:12px;border:1px; " onchange="javascript:getLensBrand(this.value,'iOLink_iol_ajaxLensBrand.php');" >
                    <option value="">Select</option>
                    <?php
                    $manQry = "SELECT `name` FROM manufacturer_lens_category ORDER BY `name`";
                    $manRes = imw_query($manQry) or die(imw_error());
                    $savedManExist='false';
                    if(imw_num_rows($manRes)>0) {
                        while($manRow = imw_fetch_array($manRes)) {
                            $adminManName = $manRow['name'];
                            if($manufacture==$adminManName) { $savedManExist='true';}?>
                                <option value="<?php echo $adminManName;?>" <?php if($manufacture==$adminManName) { echo "selected";  }?>><?php echo $adminManName;?></option>
                    <?php			
                        }
                    }if($manufacture && $savedManExist=='false') {?>
                                <option value="<?php echo $manufacture;?>" selected><?php echo $manufacture;?></option>
                    <?php		
                    }?>
                </select> 
        	</td>
        	<td class="alignCenter valignMiddle nowrap" style="width:8%;" > Lens Brand</td>
        	<td id="lensBrandTD" style="width:17%;">
                 <select name="lensBrand"  id="lensBrand" class="text_10" style=" width:130px;border:1px; <?php echo $IOL_BackColor;?>  " >
                            <option value="">Select</option>
                <?php
                
                $manLensQry = "SELECT mlb.name as lensName, mlc.name as catName FROM manufacturer_lens_brand mlb,manufacturer_lens_category mlc 
                            WHERE mlc.name='".$manufacture."' 
                            AND mlc.name!='' 
                            AND mlc.manufacturerLensCategoryId= mlb.catId
                            ORDER BY mlb.name";
                $manLensRes = imw_query($manLensQry) or die(imw_error());
                $savedLensExist='false';
                if(imw_num_rows($manLensRes)>0) {
                    while($manLensRow = imw_fetch_array($manLensRes)) {
                        $lensName = $manLensRow['lensName'];
                        $catName = $manLensRow['catName'];
                        if($lensBrand==$lensName) { $savedLensExist='true';}?>
                            <option value="<?php echo $lensName;?>" <?php if($lensBrand==$lensName) { echo "selected";  }?>><?php echo $lensName;?></option>
                <?php			
                    }
                }if($lensBrand && $savedLensExist=='false') {?>
                            <option value="<?php echo $lensBrand;?>" selected><?php echo $lensBrand;?></option>
                <?php		
                }
                
                ?>
                </select>
       	 	</td>
			<?php
			
			$preDefineTopPosition = "600"; 
			?>
			<td class="text_10b alignCenter valignMiddle" style=" width:10%;font-size:12px; color:#800080; padding-left:8px;" ><span style="cursor:pointer;" onClick="document.getElementById('preDefineDivOpenClose').value='open';showIolinkPreDefineModelFn('textareaModelId', '', 'no', parseInt(findPos_X('textareaModelId'))-200,parseInt(findPos_Y('textareaModelId')+24));">Model</span><span style="cursor:pointer; padding-left:4px;"><b style="margin-right:2px;margin-left:2px; color:#333;" id="tdErase" class="fa fa-eraser" title="Reset Model" onClick="javascript:document.getElementById('textareaModelId').value='';"></b></span></td>
			<td class="alignLeft" style="width:20%;">
				<textarea id="textareaModelId"  name="model" class="textarea text_10" style=" font-size:12px;border:1px solid #cccccc; width:130px; height:30px; " rows="1" cols="50" tabindex="6" readonly  ><?php echo stripslashes($model);?></textarea>
			</td>
			<td class="alignRight valignMiddle" style=" width:10%;font-size:12px; "> Diopter</td>
			<td style="width:10%;">
				<input type="text" onKeyUp="displayText1=this.value;" name="Diopter" id="bp_temp"  class="field text" maxlength="5" size=4 style="font-size:12px; border:1px solid #ccccc; " tabindex="1" value="<?php echo $Diopter;?>"  />
			</td>	
		</tr>
		<tr style="height:10%;">
			<td colspan="8"></td>
		</tr>
		<tr style="height:22px;">
			<td colspan="8" class="text_10b alignLeft" style="padding-left:200px;">
				<table style="border:none; padding:2px;">
					<tr>
						<td class="valignTop nowrap">
							<a id="anchorShow" href="#" style="display:block;" onClick="MM_swapImage('saveBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" style="border:none;" id="saveBtn" alt="save" onClick="chkSaveForm();"></a>
						</td>
						<td class="valignTop nowrap">
							<a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;" alt="Close" onClick="if(opener) { opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}window.close();"/></a>
						</td>
					</tr>
				</table>	
			</td>
		</tr>
		<tr style="height:5">
			<td colspan="8"></td>
		</tr>
		<tr style="height:22px;">
			<td colspan="8" class="text_10 alignCenter" style="width:100%;">
				<div style="height:120px; overflow:scroll; overflow-x:hidden; ">
					<table class="table_collapse" style=" padding:2px;">
						<?php
						//START VIEW RECORD
						$iolinkViewIolManufacturerQry 		= "select * from iolink_iol_manufacturer where patient_id = '".$patient_id."'AND patient_id != '' AND patient_in_waiting_id = '".$patient_in_waiting_id."' AND patient_in_waiting_id!= '' ORDER BY iol_manufacturer_id DESC";
						$iolinkViewIolManufacturerRes 		= imw_query($iolinkViewIolManufacturerQry) or die(imw_error());
						$iolinkViewIolManufacturerNumRow 	= imw_num_rows($iolinkViewIolManufacturerRes);
						if($iolinkViewIolManufacturerNumRow>0) {
						?>
							<tr class="alignLeft valignMiddle" style="height:28px;">
								<td class="text_10b" style="width:20%;background-image:url(<?php echo $bgHeadingImage;?>);padding-left:5px; ">Man</td>
                                <td class="text_10b" style="width:15%;background-image:url(<?php echo $bgHeadingImage;?>);padding-left:5px; ">Lens Brand</td>
								<td class="text_10b" style="width:35%;background-image:url(<?php echo $bgHeadingImage;?>);">Model</td>
								<td class="text_10b" style="width:10%;background-image:url(<?php echo $bgHeadingImage;?>);">Diopter</td>
								<td class="text_10b" style="width:10%;background-image:url(<?php echo $bgHeadingImage;?>);">Edit</td>
								<td class="text_10b" style="width:10%;background-image:url(<?php echo $bgHeadingImage;?>);">Delete</td>
							</tr>
						<?php	
							$cntr=0;
							while($iolinkViewIolManufacturerRow = imw_fetch_array($iolinkViewIolManufacturerRes)) {
								$cntr++;
								$iolManufacturerId 				= $iolinkViewIolManufacturerRow['iol_manufacturer_id'];
								$patient_id 					= $iolinkViewIolManufacturerRow['patient_id'];
								$patient_in_waiting_id 			= $iolinkViewIolManufacturerRow['patient_in_waiting_id'];
								$manufacture 					= stripslashes($iolinkViewIolManufacturerRow['manufacture']);
								$lensBrand 						= stripslashes($iolinkViewIolManufacturerRow['lensBrand']);
								$model 							= stripslashes($iolinkViewIolManufacturerRow['model']);
								$Diopter 						= stripslashes($iolinkViewIolManufacturerRow['Diopter']);
								
								$iolBgColor='#FFFFFF';
								if($cntr%2==0) { $iolBgColor=''; }
							?>
							<tr class="alignLeft valignTop" style="background-color:<?php echo $iolBgColor;?>;">
								<td class="text_10"><?php echo $manufacture;?></td>
                                <td class="text_10"><?php echo $lensBrand;?></td>
								<td class="text_10"><?php echo $model;?></td>
								<td class="text_10"><?php echo $Diopter;?></td>
								<td class="text_10"><img style="cursor:pointer;border:none; " src="images/edit_icon.png" alt="Edit Record" onClick="javascript:record_changes=true;location.href='iOLink_iol_manufacturer.php?patient_in_waiting_id=<?php echo $patient_in_waiting_id;?>&amp;patient_id=<?php echo $patient_id;?>&amp;iol_manufacturer_id=<?php echo $iolManufacturerId;?>&amp;mode=edit';" /></td>
								<td class="text_10"><img src="images/chk_off1.gif" alt="Delete Record" style="cursor:pointer; " onClick="javascript:record_changes=true;if(confirm('Delete Record! Are you sure')) {document.frm_iol_manufacturer.iol_manufacturer_id.value='<?php echo $iolManufacturerId;?>';document.frm_iol_manufacturer.mode.value='delete';document.frm_iol_manufacturer.submit();}"></td>
							</tr>
							<?php
							}
						}
						//END VIEW RECORD
						?>
					</table>
				</div>
			</td>
		</tr>
	</table>	
</form>	
</body>
</html>