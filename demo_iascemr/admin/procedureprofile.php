<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$loginUserId = $_SESSION['loginUserId'];
$userLogedDetails = $objManageData->getRowRecord('users', 'usersId', $loginUserId);
$userType 	= $userLogedDetails->user_type;
$tblName		=	'procedureprofile';

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

$profileSavedProcIdArr = array();
$qryProfile		=	"SELECT procedureId FROM ".$tblName." WHERE save_status !='0' AND save_date !='0000-00-00 00:00:00'  ORDER BY id ASC";
$resProfile 	=	imw_query($qryProfile) or die(imw_error().$qry);
if(imw_num_rows($resProfile)>0) {
	while($rowProfile 			 = imw_fetch_assoc($resProfile)) {
		$profileSavedProcIdArr[] = $rowProfile["procedureId"];	
	}
}


$procedureId 			= $_REQUEST['procedureId'];


if($procedureId)
{
		$procedureDetail		=	$objManageData->getExtractRecord('procedures','procedureId',$procedureId);
		$procedureName			=	$procedureDetail['name'];
		$procedureNameShow		=	$procedureDetail['name'];

		// Getting Procedure Profile Row Detail
		$query		=	"Select * From ".$tblName." where procedureId = '".$procedureId."' Order By id Asc Limit 1 ";
		$sql 			=	imw_query($query) or die('Error found at line no. '.(__LINE__).': '.imw_error());
		$cnt			=	imw_num_rows($sql);
		if($cnt == 0)
		{
			$insertQuery	=	"Insert Into ".$tblName." Set procedureId = '".$procedureId."' , procedureName='".addslashes($procedureName)."' ";	
			imw_query($insertQuery) or die('Error found at line no. '.(__LINE__).': '.imw_error());	
			$procedureProfileId	=	imw_insert_id();
		}
		else
		{
			$row	=	imw_fetch_object($sql);
			$procedureProfileId	=	$row->id;
		}
		$preOpMedKeyField		=	'id';
		$preOpMedKeyFieldVal	=	$procedureProfileId;
		// End Getting Procedure Profile Row Detail
		
		//SAVE RECORD
		if($_REQUEST["sbtSaveProcedureProfile"]=="true") 
		{
			extract($_POST);
			
			$arrayRecord['operativeTemplateId']	=	$opTemplateId ;
			$arrayRecord['instructionSheetId']		=	$instructionTemplateId;
			//$arrayRecord['consentTemplateId']		=	implode(',',$consentTemplateId);
			
			if($cpt_id)		
				$arrayRecord['cpt_id']					=	$cpt_id;
			if($cpt_id_default)
				$arrayRecord['cpt_id_default']			=	$cpt_id_default;
			if($dx_id)
				$arrayRecord['dx_id']					=	$dx_id;
			if($dx_id_default)	
				$arrayRecord['dx_id_default']			=	$dx_id_default;
			if($dx_id_icd10)
				$arrayRecord['dx_id_icd10']				=	$dx_id_icd10;
			if($dx_id_default_icd10)
				$arrayRecord['dx_id_default_icd10']	=	$dx_id_default_icd10;

			$arrayRecord['save_date']=date('Y-m-d H:i:s');
			$arrayRecord['save_status']=1;
			
			$arrayRecord['intraOpPostOpOrder']		=	addslashes($intraOpPostOpOrder);
			$arrayRecord['postOpDrop']				=	addslashes($postOpDrop);
			$arrayRecord['otherPreOpOrders']		=	addslashes($otherPreOpOrders);
			//print_r($arrayRecord);
			$c = $objManageData->UpdateRecord($arrayRecord,$tblName,'id',$profileId);
			
			/*
			*
			* Save Pre op orders Medication 
			*
			*/
			
			{
						$tableName			=	"preopmedicationorder";
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
												$insQry	=	" INSERT INTO ".$tableName." SET medicationName = '".$preOpOrdMed_med[$key]."' , strength = '".$preOpOrdMed_sgt[$key]."' , directions = '".$preOpOrdMed_dir[$key]."' ".$preOpMedCatQry;
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
						{
								$preOpOrdMed_id	=	array_filter($preOpOrdMed_id);
								$preOpOrdMed_id	= implode( ',' , $preOpOrdMed_id);
						}
						if($profileId > 0  )
						{
								$updateQry = "Update ".$tblName." SET preOpOrders = '".$preOpOrdMed_id."' WHERE id = '".$profileId."'";
								imw_query($updateQry) or die(imw_error());
								$profile_insId = $profileId;
						}
							
			}
		
			// End Save Pre op orders Medication 
			
			if($c)
			{
				echo "<script>top.frames[0].alert_msg('update')</script>";
			}
		
		}	
		//SAVE RECORD
		
		$procedureProfileQuery	=	"Select * From ".$tblName." where id = '".$procedureProfileId."'  ";
		$procedureProfileSql		=	imw_query($procedureProfileQuery) or die('Error found at line no. '.(__LINE__).': '.imw_error());
		$procedureProfileData		=	imw_fetch_assoc($procedureProfileSql);
		extract($procedureProfileData);	
	
	
	
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgeon Profile</title>
<?php include("adminLinkfile.php");?>
<style>
	a.black:hover{ color:"Red";	text-decoration:none; }
	a.white { color:#FFFFFF; text-decoration:none; }
</style>
<script>
function procedureSelected(obj){
	document.procedureListFrm.submit();
}


	$(window).load(function()
	{
		var LDL	=	function()
		{
			var WH	=	$(window).height();
			var SH		=	$("#procedure-header").outerHeight(true);
			
			var AH		=	WH	-	SH
		
			$("#procedureProfile").css({'height':AH+'px','min-height':AH+'px','max-height':AH+'px', 'overflow':'hidden', 'overflow-y':'auto'});
		}
		LDL();
		$(window).resize(function(e) {
         	LDL();
        });
	});

</script>

</head>
<body>
<?php
	$user_id = $_REQUEST['user_id'];
	$selectedSurgeonDetails = $objManageData->getRowRecord('users', 'usersId', $user_id);	
	$suegeonName = ucfirst($userLogedDetails->fname).' '.ucfirst(substr($userLogedDetails->mname, 0, 1)).' '.ucfirst($userLogedDetails->lname);
?>
<form name="procedureListFrm" action="procedureprofile.php" method="post" class="alignCenter">
	<Div class="all_admin_content_agree wrap_inside_admin">      
    	<Div class="subtracting-head" id="procedure-header">
        	<div class="head_scheduler new_head_slider padding_head_adjust_admin">
            	<span>Procedure Preference Card</span>
          	</div>
     	</Div>
        
        <Div class="wrap_inside_admin" id="procedureProfile">
            	
                	<div style="width:100%" class="row padding_o clear ">
                    <?php 
						if($procedureId) 
						{
                        	include_once "addProcedureProfile.php";
           				}
						else
						{
		 			?>
                    	
                    	<div class="col-lg-4 col-md-4 col-sm-2 col-xs-12">&nbsp;</div>
                    	<div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                        <div class="form_outer " id="procedureListDiv">
                     		<Div class="col-lg-1 visible-lg">&nbsp;</Div>
                            <Div class="col-md-1 visible-md"></Div>
                            <Div class="col-sm-1 visible-sm"></Div>
                            
               				
                            <select data-live-search="true" name="procedureId" id="procedureId" onChange="return procedureSelected(this.value);" class="selectpicker" data-width="80%">	
                                    	<option value="">Select Procedure</option>
                                        	<?php
												$userProcedureDetails = $objManageData->getArrayRecords('procedures', '1', '1','name','ASC');
												if($userProcedureDetails) {
													foreach($userProcedureDetails as $procedureVal){
														$del_status = $procedureVal->del_status;
                                                        $dataIcon = "";	
														if(in_array($procedureVal->procedureId,$profileSavedProcIdArr)) {
															$dataIcon = "fa fa-check-square-o";	
														}
														if(!$procedureVal->name || strtolower($del_status)=="yes") { 
															//IF THIS RECORD HAS BEEN COMMITTED AS DELETED(BY SETTING ITS deleteStatus TO Yes)
															//DO NOT SHOW DELETED RECORD IN DROP DOWN
														}
														else
														{
											?>
                                            				<option data-icon="<?php echo $dataIcon;?>" value="<?php echo $procedureVal->procedureId; ?>" <?php if($proceduresList == $procedureVal->procedureId) echo "SELECTED"; ?>><?php echo $procedureVal->name; ?></option>
                                        	<?php
														}
													}
												}	
											?>
                            </select>
                            
                            
                        	</div>
                      	</div> 	   
						
                        <div class="col-lg-4 col-md-4 col-sm-2 col-xs-12">&nbsp;</div>
					<?PHP
                                }
                    ?>
                    
             	</div>
      		</Div>
    	
	</Div>        
</form>

<?php
if($procedureId) 
{
?>
	<script>
		
		$(function()
		{
			top.frames[0].hideAllButton();
			var ShowButtons 	=	'#saveButton, #cancelButton';
			top.frames[0].$(ShowButtons).fadeIn(100);
		});
	</script>
<?php	
}

include("../common/procedurePreferencePreOpMediOrderPopUp.php");
include("../common/intraOpPostOpPopAdmin.php");
include("../common/post_op_drops_popAdmin.php");
include("../common/other_preop_orders_pop_admin.php");
?>
</body>
</html>