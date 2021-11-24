<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
require_once(dirname(__FILE__).'/../../../config/globals.php');
$dxCodeIdImplode='';
$cptCodeIdImplode='';

$rqVitalSignId 			= "";
$rqVitalSignIdFrom 		= "";
$rqVitalSignIdTo 		= "";
$rqMedication 			= "";
$rqCdRatio 				= "";	
$rqCdRatioCrFrom		= "";	
$rqCdRatioCrTo 			= "";	
$rqCdRatioTo 			= "";	
$rqIopPressure 			= "";
$rqGender 				= "";
$rqDiabetesType			= "";
$rqAgeFrom 				= "";
$rqAgeTo 				= "";	
$rqCdRatio_od_os 		= "";
$rqIopPressure_od_os  	= "";
$rqFrequencyType 		= ""; 
$strFrequencyValue		= "";
$rqIopPressureCrTo		= "";
$rqIopPressureTo		= "";
$arrFrequencyValue 	= array();;

$cbImmunization = $_REQUEST["cbImmunization"];  
$cbPHMS = $_REQUEST["cbPHMS"];  
if($cbImmunization == "1"){
	$strSiteCarePlanFor = "1";
}
elseif($cbPHMS  == "1"){
	$strSiteCarePlanFor = "0";
}
$siteCareStatus="0";
$userIds="";
$u=0;$ids_ussser="";

if($_REQUEST['user_type']){
	$_REQUEST['user_type']=preg_replace('/[^0-9]+/','',$_REQUEST['user_type']);
	$user_types_ids=implode(",",$_REQUEST['user_type']);
	$qryUserType="SELECT id FROM users WHERE delete_status!='1' AND user_type IN (".$user_types_ids.")";	
	$resUserType=imw_query($qryUserType);
	while($rowUserType=imw_fetch_assoc($resUserType)){
		if($u==0){
			$ids_ussser.=$rowUserType['id'];	
		}else{
			$ids_ussser.=",".$rowUserType['id'];
		}
		$u++;	
	}
}

if($_REQUEST['user_name']){
	$siteCareStatus="1";
	$userIds = implode(",",$_REQUEST['user_name']);
	$userIds=preg_replace('/[^0-9]+/','',$userIds);
}
$userIds=$userIds.",".$ids_ussser;

$alertContent 			= htmlentities(trim(addslashes($_REQUEST["alertContent"])));
$editMode 				= $_REQUEST["editMode"];
$editId 				= $_REQUEST["editId"];
$status 				= $_REQUEST["status"];
$patientId 				= $_REQUEST["patientId"];
 
if($strSiteCarePlanFor == "0"){
	$dxCodeId 				= $_REQUEST["dxCodeId"];
	if($dxCodeId) {
		$dxCodeIdImplode 	= implode(',',$dxCodeId);
	}	
	$cptCodeId = $_REQUEST["cptCodeId"];
	if($cptCodeId) {
		$cptCodeIdImplode 	= implode(',',$cptCodeId);
	}	
	$additionaltests = $_REQUEST["add_tests"];
	if($additionaltests){
		 $additionaltestsimplode=implode(',',$additionaltests);
	}
	
	$arr_lab_name=$_REQUEST["txt_lab_name"];
	$lab_alert_id=$_REQUEST["lab_alert_id"];
	$lab_from_criteria=$_REQUEST['txt_lab_from_criteria'];
	$lab_from_val=$_REQUEST['txt_lab_from_val'];
	$lab_to_criteria=$_REQUEST['txt_lab_to_criteria'];
	$lab_to_val=$_REQUEST['txt_lab_to_val'];
	
	$medsVal				= implode(",",$_REQUEST["medication"]);
	$rqMedication 			= htmlentities(trim(addslashes($medsVal)));
	$rqCdRatio 				= htmlentities(trim(addslashes($_REQUEST["cdRatio"])));
	$rqCdRatio				= str_replace(" ","",$rqCdRatio);
	$rqCdRatioCrFrom		= trim(addslashes($_REQUEST["ratio_criteria_from"]));	
	$rqCdRatioCrTo			= trim(addslashes($_REQUEST["ratio_criteria_to"]));	
	$rqCdRatioTo			= trim(addslashes($_REQUEST["cdRatio_to"]));	
	$rqIopPressure 			= htmlentities(trim(addslashes($_REQUEST["iopPressure"])));
	$rqIopPressure			= str_replace(" ","",$rqIopPressure);	
	$rqIopPressureCrTo 		= trim(addslashes($_REQUEST["iopPressureValCondition_to"]));
	$rqIopPressureTo 		= trim(addslashes($_REQUEST["iopPressure_to"]));
	$rqGender 				= implode(",",$_REQUEST["gender"]);
	$rqDiabetesType			= implode(",",$_REQUEST["diabetes_type"]);
	$rqAgeFrom 				= htmlentities(trim(addslashes($_REQUEST["ageFrom"])));
	$rqAgeTo 				= htmlentities(trim(addslashes($_REQUEST["ageTo"])));	
	$rqCdRatio_od_os 		= $_REQUEST["cdRatio_od_os"];
	$rqIopPressure_od_os  	= $_REQUEST["iopPressure_od_os"];
	$rqIopPressure_Condition= $_REQUEST['iopPressureValCondition'];
	$rqFrequencyType 		= ($_REQUEST["frequencyType"]) ? $_REQUEST["frequencyType"] : "2"; 
	$arrFrequencyValue 		= $_REQUEST["frequencyValue"];
	
	foreach($arrFrequencyValue as $key => $val){
		if($val){
			$strFrequencyValue .= $val."~~";
		}
	}
	if(trim($strFrequencyValue) == ""){
		$strFrequencyValue = "200"."~~";
		$rqFrequencyType = "2";
	}
}

$reference= htmlentities(trim(addslashes($_REQUEST["Reference"])));  

$strSiteCarePlan= htmlentities(trim(addslashes($_REQUEST["txtSiteCarePlan"])));

$cobRegisteredImmunization 	= $_REQUEST["cobRegisteredImmunization"];  
$pt_language 	=	implode(",",$_REQUEST['pt_language']);
$pt_race	 	=	implode(",",$_REQUEST['pt_race']);
$pt_ethnicity 	=	implode(",",$_REQUEST['pt_ethnicity']);
$_REQUEST['pt_dob1']=preg_replace('/[^0-9 \-]+/','',$_REQUEST['pt_dob1']);
list($mm,$dd,$yy)=explode("-",xss_rem($_REQUEST['pt_dob1'], 1));
$_REQUEST['pt_dob2']=preg_replace('/[^0-9 \-]+/','',$_REQUEST['pt_dob2']);
list($mm1,$dd1,$yy1)=explode("-",xss_rem($_REQUEST['pt_dob2'], 1));
list($mm,$dd,$yy)=explode("-",$_REQUEST['pt_dob1']);
list($mm1,$dd1,$yy1)=explode("-",$_REQUEST['pt_dob2']);
$pt_dob1		= 	($_REQUEST['pt_dob1'])?($yy."-".$mm."-".$dd):"";
$pt_dob2		= 	($_REQUEST['pt_dob2'])?($yy1."-".$mm1."-".$dd1):"";
$dob_expr		=	$_REQUEST['dob_criteria'];

$sel_allergy=$_REQUEST['drug'];
$allergy_id=$_REQUEST['allergy_id'];
$allergy_val="";

if(count($sel_allergy)>0){
	for($i=0;$i<count($sel_allergy);$i++){
		$allery_type=$sel_allergy[$i];
		if($allery_type){
			if($i==0){
				$allergy_val.=$allery_type;
			}else{
				$allergy_val.=",".$allery_type;
			}
		}
	}
}

$vitalSign_Id=$_REQUEST['vitalSignId'];
$vitalSign_From=xss_rem($_REQUEST['vitalSignIdFrom'], 1);
$vitalSign_From_expr=$_REQUEST['vt_from_criteria'];
$vitalSign_To=xss_rem($_REQUEST['vitalSignIdTo'], 1);
$vitalSign_to_expr=$_REQUEST['vt_to_criteria'];
$vitalAlert_id=$_REQUEST['vital_alert_id'];
$alert_type_doc=preg_replace('/[^A-Za-z]+/','',$_REQUEST['alert_type']);
$vitalUnit=$_REQUEST['unit'];
$rootDir 	= $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";
$imgDir 	= $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/site_care_plan/";

$siteCarePlanDir = substr(trim($rootDir), 0, -1);
if(!is_dir($siteCarePlanDir)){				
	mkdir($siteCarePlanDir, 0700);						
}


$upload_dir = $rootDir;
$siteCareDir = "siteCareId_";
switch($editMode)
{
	case "insert":
		$scanFileNameScan = $_SESSION['site_care_scan_image_new'];
		$scanFileNameUpload = $_SESSION['site_care_upload_doc_new'];		
		$sql="INSERT INTO alert_tbl SET  
				alertContent 				= '".$alertContent."',
				dxCodeId					= '".$dxCodeIdImplode."',
				cptCodeId 					= '".$cptCodeIdImplode."',
				medication 					= '".$rqMedication."',
				vitalSignId 				= '".$rqVitalSignId."',
				vitalSignIdFrom 			= '".$rqVitalSignIdFrom."',
				vitalSignIdTo 				= '".$rqVitalSignIdTo."',
				ageFrom 					= '".$rqAgeFrom."',
				ageTo 						= '".$rqAgeTo."',
				gender 						= '".$rqGender."',
				diabetes_type				= '".$rqDiabetesType."',
				cdRatio 					= '".$rqCdRatio."',
				cd_ratio_from_expr			= '".$rqCdRatioCrFrom."',
				cd_ratio_to_expr			= '".$rqCdRatioCrTo."',
				cd_ratio_to					= '".$rqCdRatioTo."',
				iopPressure 				= '".$rqIopPressure."',
				iop_pressure_to_condition	= '".$rqIopPressureCrTo."',
				iop_pressure_to				= '".$rqIopPressureTo."',
				operatorId 					= '".$_SESSION["authId"]."',
				saveDateTime 				= '".date("Y-m-d H:i:s")."',
				patient_Id 	    			= '".$patientId."',
				cdRatio_od_os 	    		= '".$rqCdRatio_od_os."',
				iopPressure_od_os 			= '".$rqIopPressure_od_os."',
				iopPressure_Condition		= '".$rqIopPressure_Condition."',
				status    	    			= '".$status."',
				add_tests 	    			= '".$additionaltestsimplode."',	
				txt_lab_name 	    		= '".$txt_lab_nameimplode."',	
				txt_lab_criteria 	    	= '".$lab_criteria."',
				txt_lab_result 	    		= '".$lab_result."',
				reference	    			= '".$reference."',
				site_care_plan_name 		= '".$strSiteCarePlan."',
				site_care_plan_for 			= '".$strSiteCarePlanFor."',
				site_care_status 			= '".$siteCareStatus."',
				enable_user_ids				= '".$userIds."',
				registered_immunization_id  = '".$cobRegisteredImmunization."',
				frequency_type 				= '".$rqFrequencyType."',
				frequency_value	    		= '".$strFrequencyValue."',
				pt_language		    		= '".$pt_language."',
				pt_race			    		= '".$pt_race."',
				pt_ethnicity	    		= '".imw_real_escape_string($pt_ethnicity)."',
				dob_from 					= '".$pt_dob1."',
				dob_to	 					= '".$pt_dob2."',
				dob_expr 					= '".$dob_expr."',
				pt_allergies 				= '".$allergy_val."',
				alert_type					= '".$alert_type_doc."',
				user_type					= '".$user_types_ids."'
				";			  
		$insertId = imw_query($sql);
		$insertId = imw_insert_id($insertId);
		if($scanFileNameScan){
			if(!is_dir($upload_dir.$siteCareDir.$insertId)){				
				mkdir($upload_dir.$siteCareDir.$insertId, 0700);						
			}
			$scanPathTemp = $upload_dir."tmp/".$scanFileNameScan;
			$scanFileNameScan = $siteCareDir.$insertId."/".$scanFileNameScan;
			if(file_exists($scanPathTemp)){				
				if(rename($scanPathTemp, $upload_dir.$scanFileNameScan)){
					$qryUpdateStScanPath = "update alert_tbl set scan_path='$scanFileNameScan' where alertId ='$insertId'";							
					$rsUpdateStScanPath = imw_query($qryUpdateStScanPath);
				}
			}				
		}
		if($scanFileNameUpload){
			if(!is_dir($upload_dir.$siteCareDir.$insertId)){				
				mkdir($upload_dir.$siteCareDir.$insertId, 0700);						
			}
			$scanPathTemp = $upload_dir."tmp/".$scanFileNameUpload;
			$scanFileNameUpload = $siteCareDir.$insertId."/".$scanFileNameUpload;
			if(file_exists($scanPathTemp)){
				if(rename($scanPathTemp, $upload_dir.$scanFileNameUpload)){
					$qryUpdateStScanPath = "update alert_tbl set upload_path='$scanFileNameUpload' where alertId ='$insertId'";							
					$rsUpdateStScanPath = imw_query($qryUpdateStScanPath);
				}
			}				
		}
		if($insertId){
			if(count($vitalSign_Id)>0){
				for($i=0;$i<count($vitalSign_Id);$i++){
					$vital_id=$vitalSign_Id[$i];
					$vital_from=$vitalSign_From[$i];
					
					$vital_to=$vitalSign_To[$i];
					$vital_from_exprs=$vitalSign_From_expr[$i];
					$vital_to_exprs=$vitalSign_to_expr[$i];
					$v_unit=$vitalUnit[$i];
					if($vital_id){
						$qryInsertVital="INSERT INTO alert_vital_sign set vital_sign_id='".$vital_id."',vital_sign_id_from='".imw_real_escape_string($vital_from)."',vital_sign_id_to='".imw_real_escape_string($vital_to)."',vital_sign_from_expr='".imw_real_escape_string($vital_from_exprs)."',vital_sign_to_expr='".imw_real_escape_string($vital_to_exprs)."',alert_id='".$insertId."',unit='".imw_real_escape_string($v_unit)."'";	
						$resInsertVital=imw_query($qryInsertVital);
					}
				}
			}
			if(count($arr_lab_name)>0){
				for($l=0;$l<count($arr_lab_name);$l++){
					$lab_name=$arr_lab_name[$l];
					$lab_from_expr=$lab_from_criteria[$l];
					$lab_from_value=$lab_from_val[$l];
					$lab_to_expr=$lab_to_criteria[$l];
					$lab_to_value=$lab_to_val[$l];
					
					if($insertId){
						$qryInsertlab="INSERT INTO alert_labs set alert_id='".$insertId."',lab_name='".$lab_name."',from_creteria='".$lab_from_expr."',from_val='".$lab_from_value."',to_creteria='".$lab_to_expr."',to_val='".$lab_to_value."'";	
						$resInsertlab=imw_query($qryInsertlab);
					}
				}
			}
		}
		$opCode=1; 	
		
	break;
	
	case "edit":
		$sql="UPDATE alert_tbl SET  
				alertContent 				= '".$alertContent."',
				dxCodeId					= '".$dxCodeIdImplode."',
				cptCodeId 					= '".$cptCodeIdImplode."',
				medication 					= '".$rqMedication."',
				vitalSignId 				= '".$rqVitalSignId."',
				vitalSignIdFrom 			= '".$rqVitalSignIdFrom."',
				vitalSignIdTo 				= '".$rqVitalSignIdTo."',
				ageFrom 					= '".$rqAgeFrom."',
				ageTo 						= '".$rqAgeTo."',
				gender 						= '".$rqGender."',
				diabetes_type				= '".$rqDiabetesType."',
				cdRatio 					= '".$rqCdRatio."',
				cd_ratio_from_expr			= '".$rqCdRatioCrFrom."',
				cd_ratio_to_expr			= '".$rqCdRatioCrTo."',
				cd_ratio_to					= '".$rqCdRatioTo."',
				iopPressure 				= '".$rqIopPressure."',
				operatorId 					= '".$_SESSION["authId"]."',
				saveDateTime 				= '".date("Y-m-d H:i:s")."',
				patient_Id 	    			= '".$patientId."',
				cdRatio_od_os 	    		= '".$rqCdRatio_od_os."',
				iopPressure_od_os 	    	= '".$rqIopPressure_od_os."',
				iopPressure_Condition		= '".$rqIopPressure_Condition."',
				iop_pressure_to_condition	= '".$rqIopPressureCrTo."',
				iop_pressure_to				= '".$rqIopPressureTo."',
				status    	    			= '".$status."',
				add_tests 	   				= '".$additionaltestsimplode."',
				txt_lab_name 	    		= '".$txt_lab_nameimplode."',
				txt_lab_criteria 	    	= '".$lab_criteria."',
				txt_lab_result 	    		= '".$lab_result."',
				reference	    			= '".$reference."',
				site_care_plan_name 		= '".$strSiteCarePlan."',
				site_care_plan_for 			= '".$strSiteCarePlanFor."',
				site_care_status 			= '".$siteCareStatus."',
				enable_user_ids				= '".$userIds."',
				registered_immunization_id  = '".$cobRegisteredImmunization."',
				frequency_type 				= '".$rqFrequencyType."',
				frequency_value	   			= '".$strFrequencyValue."',
				pt_language		    		= '".$pt_language."',
				pt_race			    		= '".$pt_race."',
				pt_ethnicity	    		= '".imw_real_escape_string($pt_ethnicity)."',
				dob_from 					= '".$pt_dob1."',
				dob_to	 					= '".$pt_dob2."',
				dob_expr 					= '".$dob_expr."',
				pt_allergies 				= '".$allergy_val."',
				alert_type					= '".$alert_type_doc."',
				user_type					= '".$user_types_ids."'
				WHERE  alertId 				= '".$editId."'
				";
		$res = imw_query($sql);	
		if(count($vitalSign_Id)>0){
			for($i=0;$i<count($vitalSign_Id);$i++){
				$vital_id=$vitalSign_Id[$i];
				$vital_from=$vitalSign_From[$i];
				$vital_to=$vitalSign_To[$i];
				$vital_alert_id=$vitalAlert_id[$i];
				$vital_from_exprs=$vitalSign_From_expr[$i];
				$vital_to_exprs=$vitalSign_to_expr[$i];
				$v_unit=$vitalUnit[$i];
				$whrVital="";
				$qryCon="INSERT INTO ";
				if($vital_alert_id){
					$qryCon="UPDATE ";
					$whrVital=" WHERE id='".$vital_alert_id."'";
				}
				if($vital_id){
					$qryInsertVital=$qryCon." alert_vital_sign set vital_sign_id='".$vital_id."',vital_sign_id_from='".imw_real_escape_string($vital_from)."',vital_sign_id_to='".imw_real_escape_string($vital_to)."',vital_sign_from_expr='".imw_real_escape_string($vital_from_exprs)."',vital_sign_to_expr='".imw_real_escape_string($vital_to_exprs)."',unit='".imw_real_escape_string($v_unit)."',alert_id='".$editId."' ".$whrVital;	
					$resInsertVital=imw_query($qryInsertVital);
				}
				
			}
			if(count($arr_lab_name)>0){
				for($l=0;$l<count($arr_lab_name);$l++){
					$lab_name=$arr_lab_name[$l];
					$lab_id=$lab_alert_id[$l];
					$lab_from_expr=$lab_from_criteria[$l];
					$lab_from_value=$lab_from_val[$l];
					$lab_to_expr=$lab_to_criteria[$l];
					$lab_to_value=$lab_to_val[$l];
					$qryCon="INSERT INTO ";
					$whrLab="";
					if($lab_id){
						$qryCon="UPDATE ";
						$whrLab=" WHERE id='".$lab_id."'";
					}	
					if($lab_name){
						$qryInsertLab=$qryCon." alert_labs set alert_id='".$editId."',lab_name='".$lab_name."',from_creteria='".$lab_from_expr."',from_val='".$lab_from_value."',to_creteria='".$lab_to_expr."',to_val='".$lab_to_value."'".$whrLab;	
						$resInsertLab=imw_query($qryInsertLab);
					}
				}
			}
		}	
		$opCode=2;
	break;
	
	case "delete":
		$sql = "DELETE FROM alert_tbl WHERE alertId = '".$editId."' ";
		$res = imw_query($sql);		
		$opCode=3; 	
	break;
}

$_SESSION['site_care_scan_image_new']=NULL;
$_SESSION['site_care_scan_image_new']="";
unset($_SESSION['site_care_scan_image_new']);
	
$_SESSION['site_care_upload_doc_new']=NULL;
$_SESSION['site_care_upload_doc_new']="";
unset($_SESSION['site_care_upload_doc_new']);	

//Header
if($insertId){
	header("Location: index.php?edId=".$insertId."&rf=y&rec=ins");
}
else{$upd="";
	if($editId){
		$upd="&rec=upd";
	}
	header("Location: index.php?edId=".$editId."&rf=y".$upd);	
}
?>