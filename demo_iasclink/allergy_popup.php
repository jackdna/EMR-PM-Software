<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
error_reporting(0);
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script>
window.focus();
function closeDivAllergy(){
	if(document.getElementById('preDefineDivOpenClose').value==""){
		if(document.getElementById('iolinkPreDefineAllergyAdminDiv').style.display=="block"){
			document.getElementById('iolinkPreDefineAllergyAdminDiv').style.display="none"
		}
	}
	if(document.getElementById('preDefineDivOpenClose').value=="open"){
		document.getElementById('preDefineDivOpenClose').value="";
	}
	
}

function clearVal(obj)
{
	if(obj.checked==true)
	{
		//clear	all values
		$('.nkdaOn').val('');
		$('.nkdaOn').attr('disabled',true);
	}	
	else
	$('.nkdaOn').attr('disabled',false);
}
</script>

<?php
$spec= "
</head>
<body>";

include_once("common/link_new_file.php");
include_once("common/functions.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");
include("iolinkPreDefineAllergy.php");

$objManageData = new manageData;
$practiceName = getPracticeName($loginUser,'Coordinator');
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$patient_id = $_REQUEST['patient_id'];

if($_REQUEST['saveData']<>""){
	unset($arrayPatientAllergyData);
	for($intCountMedNameTextBoxSingle = 0; $intCountMedNameTextBoxSingle < 20; $intCountMedNameTextBoxSingle++){
		//if($_REQUEST["allergyName".$intCountMedNameTextBoxSingle]){			
				$allergyName = $_REQUEST["allergyName".$intCountMedNameTextBoxSingle];
				$allergyReaction = $_REQUEST["allergyReaction".$intCountMedNameTextBoxSingle];
				$allergyIdHidden = $_REQUEST["allergyIdHidden".$intCountMedNameTextBoxSingle];
				$allergyNameHidden = $_REQUEST["allergyNameHidden".$intCountMedNameTextBoxSingle];
				$allergyReactionHidden = $_REQUEST["allergyReactionHidden".$intCountMedNameTextBoxSingle];
				
				$arrayPatientAllergyData['allergy_name'] = addslashes($allergyName);
				$arrayPatientAllergyData['reaction_name'] = addslashes($allergyReaction);
				$arrayPatientAllergyData['patient_id'] = $patient_id;
				$arrayPatientAllergyData['patient_in_waiting_id'] = $patient_in_waiting_id;
				if($allergyIdHidden=="" && $allergyNameHidden=="" && $allergyReactionHidden=="" && $allergyName!=""){
					$resourceIdInsert = $objManageData->addRecords($arrayPatientAllergyData, 'iolink_patient_allergy');	
				}
				else{
					if(!$allergyName) {
						$queryUpdateAllergy = "DELETE FROM iolink_patient_allergy WHERE
											patient_id = '$patient_id' and
											patient_in_waiting_id = '$patient_in_waiting_id' and
											pre_op_allergy_id = '$allergyIdHidden'";
					}else {
						$queryUpdateAllergy = "Update iolink_patient_allergy set allergy_name='".addslashes($allergyName)."',
											reaction_name='".addslashes($allergyReaction)."' WHERE
											patient_id = '$patient_id' and
											patient_in_waiting_id = '$patient_in_waiting_id' and
											pre_op_allergy_id = '$allergyIdHidden'";
					}
					$queryUpdateAllergySts = imw_query($queryUpdateAllergy) or die(imw_error());
					
				}
				
		//}
	}
	//update nkda
	imw_query("update patient_in_waiting_tbl set iolink_allergiesNKDA_status='".$_POST['nka']."' where patient_in_waiting_id = '$patient_in_waiting_id'")or die(imw_error());
	echo "<script>alert('Data saved');</script>";
}

//START GET Patient Data
$ptDataArr = $objManageData->get_patient_data($patient_id,"Concat(patient_lname,', ',patient_fname,' ',patient_mname) as patientName, date_of_birth");
$patientName= trim($ptDataArr["patientName"]);
$patientDOB = $objManageData->getDateFormat($ptDataArr["date_of_birth"],'/');
//END GET NO MEDICATION STATUS

$queryGetAllergy = "SELECT pre_op_allergy_id,allergy_name,reaction_name from iolink_patient_allergy WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id'";
$queryGetAllergyRes = imw_query($queryGetAllergy) or die(imw_error());
$queryGetAllergyResNumRow = imw_num_rows($queryGetAllergyRes);
$intCount = 0;
$intCountNew = 0;

//get value for nka
$q1=imw_query("select iolink_allergiesNKDA_status from patient_in_waiting_tbl where patient_in_waiting_id = '$patient_in_waiting_id'")or die(imw_error());
$d1=imw_fetch_object($q1);
$nkda=$d1->iolink_allergiesNKDA_status;
$disabled=$nkdaChecked='';
if($nkda=='Yes')
{	
	$disabled=" disabled";
	$nkdaChecked=' checked';
}
?>
<form name="allergy" action="allergy_popup.php" method="post" onClick="closeDivAllergy();">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
	<input type="hidden" name="preDefineDivOpenClose" id="preDefineDivOpenClose" value="">
	<input type="hidden" name="saveData" id="saveData" value="Save">
	
	<table class="table_collapse alignCenter" style="background-color:#F1F4F0;">
        <tr>
			<td style="background-color: #cfe0e7; font-size: 13px; font-weight: 600; padding: 10px 5px;"><?php echo $patientName.($patientDOB?' - <small>' .$patientDOB.'</small>':'');?></td>
		</tr>
		<tr>
			<td style="text-align:center"><span style="color:#800080;cursor:pointer; font-weight:bold;" class="text_10"  onClick="document.getElementById('iolinkPreDefineAllergyAdminDiv').style.display='block'; document.getElementById('preDefineDivOpenClose').value='open';">Allergy </span><span class="text_10"><label><input type="checkbox" name="nka" id="nka" value="Yes" onClick="clearVal(this)" <?php echo $nkdaChecked;?>>NKA</label></span></td>
		</tr>
		<tr>
			<td >
    
				<div style="height:400px; overflow-y:scroll; overflow-x:hidden;">
                    <table class="table_collapse alignCenter" style="background-color:#F1F4F0;">
                        <tr > 
                            <td class="alignCenter text_10" style="height:22px; background-image:url(images/header_bg.jpg);padding-left:1px;  ">Name</td>
                            <td class="alignCenter text_10" style="height:22px; background-image:url(images/header_bg.jpg);padding-left:1px;  ">Reaction</td>
                        </tr>
                            <?php if($queryGetAllergyResNumRow){
                                    while($queryGetAllergyRow = imw_fetch_array($queryGetAllergyRes)){
                                        $intPatientAllergyId = $queryGetAllergyRow['pre_op_allergy_id'];
                                        $strPatientAllergyName = $queryGetAllergyRow['allergy_name'];
                                        $strPatientAllergyreaction = $queryGetAllergyRow['reaction_name'];
                                    ?>
                                    <tr>
                                        <td class="alignCenter">
                                            <input type="hidden" class="" value="<?php echo stripslashes($intPatientAllergyId); ?>" name="allergyIdHidden<?php echo $intCountNew;?>" id="allergy_IdHidden<?php echo $intCountNew;?>" />
                                            <input type="hidden" class="" value="<?php echo stripslashes($strPatientAllergyName); ?>" name="allergyNameHidden<?php echo $intCountNew;?>" id="allergy_nameHidden<?php echo $intCountNew;?>" />
                                            <input type="hidden" class="" value="<?php echo stripslashes($strPatientAllergyreaction); ?>" name="allergyReactionHidden<?php echo $intCountNew;?>" id="allergy_reactionHidden<?php echo $intCountNew;?>" />
                                            <input type="text" value="<?php echo stripslashes($strPatientAllergyName); ?>" name="allergyName<?php echo $intCountNew;?>" id="allergy_name<?php echo $intCountNew;?>" class="field text nkdaOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px; font-size:12px;" <?php echo $disabled;?> tabindex="1"/>
                                        </td>
                                        <td class="alignCenter"><input type="text" value="<?php echo stripslashes($strPatientAllergyreaction); ?>" name="allergyReaction<?php echo $intCountNew;?>" id="allergy_reaction<?php echo $intCountNew;?>" class="field text nkdaOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" <?php echo $disabled;?> tabindex="1"  /></td>
                                    </tr>
                                    <?php
                                        $intCountNew++;
                                    } 
                                //}
                                $intCountAfter = $intCountNew;
                                if($intCountAfter < 20){
                                for($intCount = $intCountAfter; $intCount < 20; $intCount++){			
                                    ?>
                                        <tr>
                                            <td class="alignCenter">
                                                <input type="hidden" class="" name="allergyIdHidden<?php echo $intCount;?>" id="allergy_IdHidden<?php echo $intCount;?>" />
                                                <input type="hidden" class="" name="allergyNameHidden<?php echo $intCount;?>" id="allergy_nameHidden<?php echo $intCount;?>" />
                                                <input type="hidden" class="" name="allergyReactionHidden<?php echo $intCount;?>" id="allergy_reactionHidden<?php echo $intCount;?>" />
                                                <input type="text" class="nkdaOn field text" name="allergyName<?php echo $intCount;?>" id="allergy_name<?php echo $intCount;?>" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" <?php echo $disabled;?> tabindex="1"/>
                                            </td>
                                            <td class="alignCenter"><input type="text" name="allergyReaction<?php echo $intCount;?>" id="allergy_reaction<?php echo $intCount;?>" class="field text nkdaOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" <?php echo $disabled;?> tabindex="1"  /></td>
                                        </tr>
                                    <?php 
                                    }
                                }
                            }
                            elseif($intCountAfter < 20){
                                for($intCount = 0; $intCount < 20; $intCount++){			
                                    ?>
                                    <tr>
                                        <td class="alignCenter">
                                            <input type="hidden" class="" name="allergyIdHidden<?php echo $intCount;?>" id="allergy_IdHidden<?php echo $intCount;?>" />
                                            <input type="hidden" class="" name="allergyNameHidden<?php echo $intCount;?>" id="allergy_nameHidden<?php echo $intCount;?>" />
                                            <input type="hidden" class="" name="allergyReactionHidden<?php echo $intCount;?>" id="allergy_reactionHidden<?php echo $intCount;?>" />
                                            <input type="text" name="allergyName<?php echo $intCount;?>" id="allergy_name<?php echo $intCount;?>" class="field text nkdaOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" <?php echo $disabled;?> tabindex="1"/>
                                        </td>
                                        <td class="alignCenter"><input type="text" name="allergyReaction<?php echo $intCount;?>" id="allergy_reaction<?php echo $intCount;?>" class="field text nkdaOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" <?php echo $disabled;?> tabindex="1"  /></td>
                                    </tr>
                                    <?php 
                                }
                            }
                        ?>
                    </table>
                </div>
                <table class="table_collapse">
                    <tr>
                        <td class="alignRight nowrap" style="width:150px;">
                            <a id="anchorShow" href="#" style="display:block;" onClick="MM_swapImage('saveBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" id="saveBtn" style="border:none;" alt="save" onClick="document.allergy.submit();"></a>
                        </td>
                        <td class="alignLeft" >
                            <a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;"  alt="Close" onClick="if(opener) { opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}window.close();"/></a>
                        </td>
                    </tr>
                </table>

			</td>
		</tr>
	</table>				
</form>	

</body>
</html>