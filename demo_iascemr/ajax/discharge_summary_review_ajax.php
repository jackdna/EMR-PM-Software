<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(0);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once(dirname(__DIR__)."/common/conDb.php");
include_once(dirname(__DIR__)."/common/commonFunctions.php"); 
include_once(dirname(__DIR__)."/admin/classObjectFunction.php");
$objManageData = new manageData;
$idoc_mod = idoc_modifiers();
		
//SAVE USER SIGNATURE
	$loggedInUserId = $_POST['loggedInUserIdR'];
	if(!$loggedInUserId) {
		$loggedInUserId = $_SESSION["loginUserId"];
	}
	$patient_id = $_REQUEST['patient_idR'];
	$pConfId 	= $_REQUEST['pConfIdR'];
	
	
	$cpt_code_default	=	isset($_REQUEST['cpt_code_defaultR'])	?	base64_decode($_REQUEST['cpt_code_defaultR'])	:	''	;
	$dx_code_default	=	isset($_REQUEST['dx_code_defaultR'])	?	base64_decode($_REQUEST['dx_code_defaultR'])	:	''	;
	$dx_code_type		=	isset($_REQUEST['dx_code_typeR'])		?	$_REQUEST['dx_code_typeR']					:	''	;
	
	if($dx_code_type && $cpt_code_default)
	{
		
		$cptCodeIdArray = $cptCodeNameArray = $cptCodeArray = array();
		$cptCodeIdImplode = $cptCodeNameImplode = '';
		$cptProcQry	=	"Select P.procedureId,P.name,P.code, PC.name as categoryName From procedures P Join procedurescategory PC On P.catId = PC.proceduresCategoryId where P.procedureId In (".$cpt_code_default.") Order by P.catId=20 Desc,P.catId,P.code,P.name ";
		$cptProcSql	=	imw_query($cptProcQry) or die($cptProcQry.'---'.imw_error());
		$cptProcCnt	=	imw_num_rows($cptProcSql);
		if($cptProcCnt)
		{
			while($cptProcRow = imw_fetch_object($cptProcSql))
			{
				$cptCodeIdArray[]	=	$cptProcRow->procedureId;
				$cptCodeNameArray[]	=	$cptProcRow->name;
				$isAnesthesiaCheck	=	($cptProcRow->categoryName == 'Anesthesia') ? 1 : 0 ;
				$cptCodeArray[$cptProcRow->procedureId]	= array('isAnesthesia'=>$isAnesthesiaCheck,'cptCode'=>$cptProcRow->code);
			}
			
		}
		
		$cptCodeIdImplode	=	implode(',',$cptCodeIdArray);
		$cptCodeNameImplode	=	implode('!,!',$cptCodeNameArray);
		
		//Get Dx Codes
		
		$dxCode10IdArray = $dxCode10Array = $dxCode9IdArray = $dxCode9Array = array();
		$dxCode10IdImplode = $dxCode10Implode = $dxCode9IdImplode = $dxCode9Implode = '';
		if($dx_code_type == 'icd10')
		{
			$dxCodeDefaultArray	=	explode(",",$dx_code_default);
			foreach($dxCodeDefaultArray as $dxCode)
			{
				list($dxCodeId,$dxCodeString)	=	explode("~:~",$dxCode);
				$dxCode10IdArray[]	=	$dxCodeId;
				$dxCode10Array[]	=	$dxCodeString;
			}
				
			
			$dxCode10IdImplode	=	implode(',',$dxCode10IdArray);
			$dxCode10Implode	=	implode(',',$dxCode10Array);
			
		}
		else
		{
			$dxCodeDefaultArray	=	explode(",",$dx_code_default);
			foreach($dxCodeDefaultArray as $dxCode)
			{
				list($dxCodeId,$dxCodeString)	=	explode("~:~",$dxCode);
				$dxCode9IdArray[]	=	$dxCodeId;
				$dxCode9Array[]		=	$dxCodeString;
			}
				
			
			$dxCode9IdImplode	=	implode(',',$dxCode9IdArray);
			$dxCode9Implode		=	implode(',',$dxCode9Array);
		}
		
		
		// Need to get Form status according to fields filled in table -pending
		$updateQry	=	"Update dischargesummarysheet Set procedures_name = '".$cptCodeNameImplode."', procedures_code = '".$cptCodeIdImplode."', diag_ids = '".$dxCode9IdImplode."', icd10_code = '".$dxCode10Implode."', icd10_id= '".$dxCode10IdImplode."', form_status = 'not completed' Where confirmation_id = '".$pConfId."'  ";
		//echo $updateQry ;
		$result	=	imw_query($updateQry) or die(imw_error());
		
		
		$cptArr	=	explode(',',$cpt_code_default);
		if(is_array($cptArr) && count($cptArr) > 0 )
		{
			foreach($cptArr as $key=>$cptId)
			{
				$cptCode		=	$cptCodeArray[$cptId]['cptCode'];
				$isAnesthesia 	=	$cptCodeArray[$cptId]['isAnesthesia'];
				if($cptCode)
				{
					$mods = array();
					$mods = array_key_exists($cptCode,$idoc_mod) ? $idoc_mod[$cptCode] : $mods;
					if(!$mods)
					{
						$mods = $idoc_mod[current(preg_grep('/^'.$cptCode.'/', array_keys($idoc_mod)))];
						if(!is_array($mods)) $mods = false;
					}
					
					$billUserType = ($isAnesthesia) ? 1 : 2;
					
					unset($insertUpdateRecord);
					$insertUpdateRecord['confirmation_id']	=	$pConfId;
					$insertUpdateRecord['bill_user_type']		=	$billUserType;
					$insertUpdateRecord['cpt_id']	=	$cptId;
					$insertUpdateRecord['cpt_code']	=	$cptCode;
					$insertUpdateRecord['dxcode_icd10']	=	$dxCode10Implode;
					$insertUpdateRecord['dxcode_icd9']	=	$dxCode9Implode;
					$insertUpdateRecord['quantity']			=	1;
					$insertUpdateRecord['modifier1']		=	$mods['mod1'];
					$insertUpdateRecord['modifier2']		=	$mods['mod2'];
					$insertUpdateRecord['modifier3']		=	$mods['mod3'];
					$insertUpdateRecord['modified_by']	=	$loggedInUserId;
					$insertUpdateRecord['modified_on']	=	date('Y-m-d H:i:s');
					
					unset($chkArray);
					$chkArray['confirmation_id']	=	$pConfId;
					$chkArray['bill_user_type']	=	$billUserType;
					$chkArray['cpt_id']				=	$cptId;
					$chkArray['cpt_code']			=	$cptCode;
					$chkArray['deleted']			=	0	;
					
					$chkRecords	=	$objManageData->getMultiChkArrayRecords('superbill_tbl',$chkArray);
					if($chkRecords)
					{ //Do Nothing 
					}
					else
					{					
						//echo 'insert'; print_r($insertUpdateRecord);
						$objManageData->addRecords($insertUpdateRecord,'superbill_tbl');	
					}
					
					if( $billUserType == 2 )
					{
						$billUserType = 3;
						$insertUpdateRecord['bill_user_type']		=	$billUserType;
						$chkArray['bill_user_type']	=	$billUserType;
						
						$chkRecords	=	$objManageData->getMultiChkArrayRecords('superbill_tbl',$chkArray);
						if($chkRecords) { /*Do Nothing*/ }
						else
						{					
							$objManageData->addRecords($insertUpdateRecord,'superbill_tbl');	
						}
					
					}
					
				}
					
			}
		}
		
	}
	elseif($dx_code_type && !$cpt_code_default)
	{
		// Need to get Form status according to fields filled in table -pending
		$updateQry	=	"Update dischargesummarysheet Set form_status = 'not completed' Where confirmation_id = '".$pConfId."'  ";
		$result1	=	imw_query($updateQry) or die(imw_error());
		
	}
	$success = ($result) ? '1' : (($result1) ? '2' : '0');
	echo json_encode(array('success'=>$success));
?>