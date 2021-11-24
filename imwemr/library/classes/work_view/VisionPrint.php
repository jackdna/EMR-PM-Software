<?php
/**
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * Use this software under MIT License
 *
 * File: Vision.php
 * Coded in PHP7
 * Purpose: This class file provides functions to manage vision exam in work view.
 * Access Type : Include file
 * 
 */

//------Vision.php------
//------$_GLOBALS["MR_RX_PRINT_PREVIOUS"] = "NO";
class VisionPrint extends Vision
{
	public $billing_global_server_name_str;
	public $arGlobal;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->billing_global_server_name_str="brian";
		$this->arGlobal["MR_RX_PRINT_PREVIOUS"] = "NO";
		//
			
	}
	
	function set_signature_in_html($prescriptionTemplateContentData){
		$form_id = $this->fid;
		$patientId = $this->pid;
		////////////////////////Statrt Signature Logic/////////	
		$signaTure=false;		
		$qryGetSig ="SELECT 
						doctorId,
						sign_coords,
						id 
					FROM 
						`chart_assessment_plans`
					WHERE 
						form_id ='".$form_id."' 
					AND
						patient_id ='".$patientId."'";	
						
			//echo("QUERY1 To GET Signature:".$qryGetSig."<br>");
			$rsGetSig = imw_query($qryGetSig)	or die($qryGetSig.imw_error());
			$numRowGetSig = imw_num_rows($rsGetSig);
			if($numRowGetSig){
				extract(imw_fetch_array($rsGetSig));				
				if($doctorId>0){
				//print Of Physcian Title First name Second name and Suffix//
					if($doctorId){
						$getNameQry = imw_query("SELECT 
													CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME, 
													fname, 
													mname, 
													lname, 
													pro_suffix, 
													licence, 
													user_npi 
												FROM 
													users 
												WHERE 
													id = '".$doctorId."'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
						$PHYSICIANNPI  = $getNameRow['user_npi'];
						$phy_fname 	   = $getNameRow['fname'];
						$phy_mname 	   = $getNameRow['mname'];
						$phy_lname     = $getNameRow['lname'];
						$phy_suffix    = $getNameRow['pro_suffix'];
						$PHYSICIANLIC  = $getNameRow['licence'];
						
						$oChartSign = new Signature($form_id);
						$sign = $oChartSign->getFirstChartSign($doctorId);
						if(empty($sign)){
							$oUsr = new User($doctorId);
							list($sign,$sign_pixel) = $oUsr->getSign(2);
						}else{
							$sign = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$sign);
						}
						
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$PHYSICIANNAME,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',$PHYSICIANNPI,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$phy_fname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$phy_mname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$phy_lname,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$phy_suffix,$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$PHYSICIANLIC,$prescriptionTemplateContentData);
					}else{
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',"",$prescriptionTemplateContentData);
					}
					
					
					
					/*
					$id = $id;
					$tblName = "chart_assessment_plans";
					$pixelFieldName = "sign_coords";
					$idFieldName = "id";
					$imgPath = "";
					$saveImg = "3";
					include_once(dirname(__FILE__)."/imgGd.php");
					//if($gdFilename!=""){
					//$gdFilenamePath=realpath(dirname(__FILE__)."/../common/new_html2pdf/tmp/".$gdFilename);
					$gdFilenamePath="../common/new_html2pdf/tmp/".$gdFilename;			
					
					if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
						//$gdFilenamePath=checkUrl4Remote($GLOBALS['rootdir']."/common/new_html2pdf/tmp/".$gdFilename);
						$gdFilenamePath = realpath(dirname(__FILE__)."/".$gdFilenamePath);
						$ChartNoteImagesString[]=$gdFilenamePath;
					}			
					*/
					
					if(!empty($sign)){
						$TData = "<img align='left' src='".$sign."' height='83' width='225'>";
						$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);
						$signaTure=true;	
					}
					//}
				}
				
			}
		//Give Prioity To Master Chart Notes Provider//


			$qryGetProvider = " SELECT 
									id,
									providerId 
								FROM 
									`chart_master_table` 
								WHERE  
									id ='".$form_id."' 
								AND 
									patient_id ='".$patientId."'";	
			
			$rsGetProviderId = imw_query($qryGetProvider)	or die($qryGetProvider.imw_error());
			//echo("QUERY2 To GET Signature:".$qryGetProvider."<br>");
			$numRowProviderGetSig = imw_num_rows($rsGetProviderId);
			if($numRowProviderGetSig && $signaTure==false){
				extract(imw_fetch_array($rsGetProviderId));
				if($providerId>0){
					$gdFilenamePath = "";
				//print Of Physcian Title First name Second name and Suffix//
					if($providerId){
						/*
						$getNameQry = imw_query("SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME FROM users WHERE id = '".$providerId."'");	
						$getNameRow = imw_fetch_assoc($getNameQry);
						$PHYSICIANNAME = $getNameRow['PHYSICIANNAME'];
						*/
						$oUsr = new User($providerId);
						list($gdFilenamePath,$sign_pixel) = $oUsr->getSign(2);
						$PHYSICIANNAME = $oUsr->getName();						
						$physicianDetails = $oUsr->get_user_info();	// GET PHYSICIAN INFO
						
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',$physicianDetails[3],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',$physicianDetails[0],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',$physicianDetails[1],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',$physicianDetails[2],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',$physicianDetails[4],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NPI}',$physicianDetails[5],$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',$physicianDetails[6],$prescriptionTemplateContentData);
					}else{
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN MIDDLE NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',"",$prescriptionTemplateContentData);
						$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',"",$prescriptionTemplateContentData);
					}
					/*
					$id = $providerId;
					$tblName = "users";
					$pixelFieldName = "sign";
					$idFieldName = "id";
					$imgPath = "";
					$saveImg = "3";
					include_once(dirname(__FILE__)."/imgGd.php");
				//	if($gdFilename!=""){
				
					//$gdFilenamePath=realpath(dirname(__FILE__)."/../common/new_html2pdf/tmp/".$gdFilename);
					$gdFilenamePath="../common/new_html2pdf/tmp/".$gdFilename;
					
					if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
						//$gdFilenamePath=checkUrl4Remote($GLOBALS['rootdir']."/common/new_html2pdf/tmp/".$gdFilename);
						$gdFilenamePath = realpath(dirname(__FILE__)."/".$gdFilenamePath);
						$ChartNoteImagesString[]=$gdFilenamePath;
						
					}			
					$ChartNoteImagesString[]="/imahdjd.jpg";
					*/
					$TData = "";
					if(!empty($gdFilenamePath)){
						$TData = "<img align='left' src='".$gdFilenamePath."'  height='83' width='225'>";
						$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',$TData,$prescriptionTemplateContentData);	
						$signaTure=true;
					}
					//}
				}	
			}
		//End Give Prioity To Master Chart Notes Provider
			if($signaTure==false){
				$prescriptionTemplateContentData = str_ireplace('{SIGNATURE}',"",$prescriptionTemplateContentData);		
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME}',"",$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN FIRST NAME}',"",$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN LAST NAME}',"",$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PHYSICIAN NAME SUFFIX}',"",$prescriptionTemplateContentData);
				$prescriptionTemplateContentData = str_ireplace('{PRIMARY LICENCE NUMBER}',"",$prescriptionTemplateContentData);
				
			}
		/////////////////////////End Signature Logic/////////
		return $prescriptionTemplateContentData;
		
	}
	
	function getHTMLForGivenRx($printType,$givenMrValue,$prescriptionTemplateContentData, $finalize_flag){
		global $gdFilename,$ChartNoteImagesString,$zOnParentServer,$billing_global_server_name_str,$objManageData;
		
		$patientId = $this->pid;
		$form_id = $this->fid;
		
		/////get patient data////
		$oPt = new Patient($patientId);
		$ptAge = $oPt->getAge();
		$patientname = $oPt->getName();
		$arrPtInfo = $oPt->getPtInfo();
		extract($arrPtInfo);
		$patientGeoData="";
		$ptAgeShow = "".$ptAge." Yr.";
		$patientEmail="";
		$patientAddressFull = $arrPtInfo["street"];
		if(!empty($arrPtInfo["street2"])){$patientAddressFull .= $arrPtInfo["street2"].", ";}
		if(!empty($arrPtInfo["city"])){$patientGeoData .= $arrPtInfo["city"].", "; }
		if(!empty($arrPtInfo["state"])){$patientGeoData .= $arrPtInfo["state"].", ";}
		if(!empty($arrPtInfo["postal_code"])){$patientGeoData .= $arrPtInfo["postal_code"].", ";}
		$patientAddressFull .= $patientGeoData;
		if(!empty($arrPtInfo["email"])){ $patientEmail= $arrPtInfo["email"]; }
		
		
		// IF PRINT THEN FORM ID
		//echo("select * from  chart_left_cc_history where patient_id='$patientId' and form_id='$form_id'"."<BR> CHECK FORMID IS COMING<br>");
		$oCn = new ChartNote($patientId, $form_id);
		$date_of_service = $oCn->getDos();		
		/////End date of sevice Code////////////////
		//get today date//
			$today = wv_formatDate(date('Y-m-d'));
		//end today date//

		/*
		if($givenMrValue =='PC1'){
			$qryGetSpacialCharValue = "select vis_pc_od_s as OdSpherical,
									   vis_pc_od_c as odCylinder,
									   vis_pc_od_a AS odAxis,
									   vis_pc_od_p as odPrism1, vis_pc_od_sel_2 as odPrism2,
									   vis_pc_od_slash  as odBase1, vis_pc_od_prism as odBase2, 
									   vis_pc_od_add as odAdd, 
									   vis_pc_os_s as osSpherical,
									   vis_pc_os_c as osCylinder,
									   vis_pc_os_a as osAxis,  
									   vis_pc_os_p as osPrism1, vis_pc_os_sel_2 as osPrism2,
									   vis_pc_os_slash as osBase1, vis_pc_os_prism as osBase2,
									   vis_pc_os_add as osAdd,
									   vis_pc_desc as notes  
									   from chart_vision where 
									   form_id = '".$form_id."'
									   and
									   patient_id = '".$patientId."'				   
									  ";
		}
		*/		
		//echo("QUERY:".$qryGetSpacialCharValue."<br>");
		/*
			if($qryGetSpacialCharValue!=""){
				$rsGetSpacialCharValue = mysql_query($qryGetSpacialCharValue)	or die($qryGetSpacialCharValue.mysql_error());
				$numRowGetSpacialCharValue = mysql_num_rows($rsGetSpacialCharValue);
				if($numRowGetSpacialCharValue){
					extract(mysql_fetch_array($rsGetSpacialCharValue));
					
					
				}
				
				//else{
				//	return "";
				//}
				
			}
		*/	
		//$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='".$printType."'";	
		//$rsGetTempData = mysql_query($qryGetTempData)	or die($qryGetTempData.mysql_error());
		//$numRowGetTempData = mysql_num_rows($rsGetTempData);
		//extract(mysql_fetch_array($rsGetTempData));	
		//$printOptionType = $printOption;

		if($prescriptionTemplateContentData!=""){
			$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
			//$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('%20',' ',$prescriptionTemplateContentData);
			/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
			$OBJsmart_tags = new SmartTags;
			$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
				}	
			}
			/*--SMART TAG REPLACEMENT END--*/
			
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT GEOGRAPHICAL DATA}',$patientGeoData,$prescriptionTemplateContentData);	
			
			$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
			
			//Rx--
			$occhxprint = new CcHxPrint($patientId, $form_id);
			$rx = $occhxprint->cpoe_getOrderForPrint($finalize_flag);	
			$prescriptionTemplateContentData = str_ireplace('{DIRECTION}',$rx,$prescriptionTemplateContentData);
			
			/*
			$prescriptionTemplateContentData = str_ireplace('{PATIENT DOB}',$pat_dob,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PT AGE}',$ptAgeShow,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD BASE}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS BASE}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TODAY DATE}',$today,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
			*/

			//Modified Variables
			$prescriptionTemplateContentData = str_ireplace('{DOB}',wv_formatDate($DOB),$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{GIVEN_DATE}',wv_formatDate($vis_mr_pres_dt),$prescriptionTemplateContentData);
			
			//New variable added	
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME SUFFIX}',$suffix,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{STATE ZIP CODE}',$patientGeoData,$prescriptionTemplateContentData);
			
			$prescriptionTemplateContentData = str_ireplace('{RACE}',$race,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicity,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$language,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{SEX}',$sex,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PT_EMAIL_ADDRESS}',$patientEmail,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT_NICK_NAME}',$patientNickName,$prescriptionTemplateContentData);
			
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','<input type="text"  value="" size="1"  maxlength="1"  tempEndTextBox>',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','<input type="text" name="textbox[]" value="" size="30"  maxlength="30"  tempEndTextBox>',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','<input type="text" name="textbox[]"  value="" size="60"  maxlength="60"  tempEndTextBox>',$prescriptionTemplateContentData);
			//echo date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
			if(constant("RX_EXPIRY_DATE")!="" && (constant("RX_EXPIRY_DATE")==12 || constant("RX_EXPIRY_DATE")==24)){
				list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
				$dos_mnt = $dos_mnt + constant("RX_EXPIRY_DATE");
				$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt,$dos_dy,$dos_yr));
			}else{
				$expirationDate = wv_formatDate(date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y'))),'mm-dd-yyyy');
			}
			$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);	
				
			//---PRS Render Appointment Variables Based on Appointment---
			$appt_id = "";
			$app_start_date = getDateFormatDB($date_of_service);	
			if($patientId && ($app_start_date && $app_start_date!= '0000-00-00'))
			{	
				$qry_appt_id = "SELECT 
									id 
								FROM 
									`schedule_appointments` 
								WHERE 
									sa_patient_id = '".$patientId."' 
								AND 
									sa_app_start_date = '".$app_start_date."' 
								AND 
									sa_patient_app_status_id NOT IN (18,203) 
								ORDER BY sa_app_start_date ASC LIMIT 0,1";
				$exe_appt_id = imw_query($qry_appt_id);
				if(imw_num_rows( $exe_appt_id ) > 0 )
				{
					$row_appt_id = imw_fetch_assoc($exe_appt_id);
					$appt_id = 	$row_appt_id['id'];
				}	
			}
			
			$oPtSch = new ManageData($patientId);
			$apptFacPhone="";
			$apptFacInfo = $oPtSch->__getApptInfo($_SESSION['patient'],'','','',$appt_id);
			$apptFacname = $apptFacInfo[2];
			if(!empty($apptFacInfo[10])){
				$apptFacstreet = $apptFacInfo[10].', ';	
			}
			if(!empty($apptFacInfo[11])){
				$apptFaccity = $apptFacInfo[11].', ';	
			}
			if(!empty($apptFacInfo[3])){ $apptFacPhone =  $apptFacInfo[3]; }
			$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY PHONE}',$apptFacPhone,$prescriptionTemplateContentData);
			//Signature
			$prescriptionTemplateContentData = $this->set_signature_in_html($prescriptionTemplateContentData);
		
		}// End Template HTml Blank Check
			
			
		return $prescriptionTemplateContentData	;
	}
	
	function replace_tag_with_property($html, $tag, $element, $elemValue, $nVal) {
		return preg_replace('/<'.$tag.'[^>]+'.$element.'="'.preg_quote($elemValue, '/').'"[^>]*>/s', $nVal, $html);
	}
	
	function get_prescription_template($printType){
		//Get Prescription template --
		$getInputForTextBoxes=false;
		$qryGetTempData = " SELECT 
								prescription_template_content as prescriptionTemplateContentData,
								printOption 
							FROM 
								`prescription_template`
							WHERE 
								prescription_template_type ='".$printType."'";	
								
		$rsGetTempData = imw_query($qryGetTempData) or die($qryGetTempData.imw_error());
		$numRowGetTempData = imw_num_rows($rsGetTempData);
		if($numRowGetTempData<=0){
			echo "<script>alert('Please create your Medical Rx template to precede print.');</script>";
			//exit();
			$flgStopExec = 1;
		}else if($numRowGetTempData>0){
			$resArrayTemplate=imw_fetch_array($rsGetTempData);
			$printOptionType=$resArrayTemplate["printOption"];
			$prescriptionTemplateContentData = stripslashes($resArrayTemplate["prescriptionTemplateContentData"]);
			if(strpos($prescriptionTemplateContentData,'{TEXTBOX_XSMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_SMALL}')>0 || strpos($prescriptionTemplateContentData,'{TEXTBOX_MEDIUM}')>0 || ($arr_smartTags && count($arr_smartTags)>0) || ($_REQUEST['sectionName'] && $_REQUEST['sectionName']=='fromPRS')){
				$getInputForTextBoxes=true;
			}
		}
		//--
		return array($getInputForTextBoxes, $flgStopExec, 
					$printOptionType, $prescriptionTemplateContentData);
	}
	
	function process_final_html(){
		$prescriptionTemplateContentData= stripslashes($_POST["finalHtmlForPrinting"]);
			
		$printOptionType=$_POST["printOptionType"];
		if(count($_REQUEST['textbox'])>0){
			$arr_text_post=$_REQUEST['textbox'];
				foreach($arr_text_post as $text_val){
					$prescriptionTemplateContentData = $this->replace_tag_with_property($prescriptionTemplateContentData, "input", "value", $text_val, $text_val);
			}
		}
		$prescriptionTemplateContentData=str_ireplace('<input type="text" value="',"",$prescriptionTemplateContentData);// For Safari and IE9
		$prescriptionTemplateContentData=str_ireplace('<INPUT value="',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT value=',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('<INPUT',"",$prescriptionTemplateContentData);
//		<A id=2 class=cls_smart_tags_link href="javascript:;" jQuery1308551487984="1">
		$regpattern='|<a id=(.*) class=(.*) href=(.*)>(.*)<\/a>|U';
		$regpattern2='|<A id=(.*) class=(.*) href=(.*)>(.*)<\/A>|U';
		$prescriptionTemplateContentData = preg_replace($regpattern, "\\4", $prescriptionTemplateContentData);
		$prescriptionTemplateContentData = preg_replace($regpattern2, "\\4", $prescriptionTemplateContentData);		

		$prescriptionTemplateContentData=str_ireplace('" maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		$prescriptionTemplateContentData=str_ireplace('" maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);// IE8
		
		$prescriptionTemplateContentData=str_ireplace('maxLength=30 size=30 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=60 size=60 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('maxLength=1 size=1 type=text tempEndTextBox>',"",$prescriptionTemplateContentData);


		
		$prescriptionTemplateContentData=str_ireplace('" size="30" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="60" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="1" type="text" tempEndTextBox="">',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1" tempendtextbox="" autocomplete="off">',"",$prescriptionTemplateContentData);// IE9
		
		$prescriptionTemplateContentData=str_ireplace('maxLength="30" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="1" value="',"",$prescriptionTemplateContentData);// IE9
		$prescriptionTemplateContentData=str_ireplace('maxLength="60" value="',"",$prescriptionTemplateContentData);// IE9

		// For Safari
		$prescriptionTemplateContentData=str_ireplace('" size="1" maxlength="1" tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" size="60" maxlength="60" tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" size="30" maxlength="30" tempendtextbox="">',"",$prescriptionTemplateContentData);

		// For Safari
		
		$prescriptionTemplateContentData=str_ireplace('type="text" size="60"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace('type="text" size="30"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace('type="text" size="1"',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="60" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="60" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="30" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="30" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		
		$prescriptionTemplateContentData=str_ireplace(' type="text" size="1" " tempendtextbox="">',"",$prescriptionTemplateContentData);
		$prescriptionTemplateContentData=str_ireplace('type="text" size="1" " tempendtextbox="">',"",$prescriptionTemplateContentData);		
		$prescriptionTemplateContentData=str_ireplace('" tempendtextbox="" autocomplete="off">',"",$prescriptionTemplateContentData);
		
		
		$tmp_include_root = (constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]["incdir"] : $GLOBALS["include_root"];
		if(constant("REMOTE_SYNC") != 1){
			$imgALLReplace= $tmp_include_root.'/common/new_html2pdf/' ;
			$prescriptionTemplateContentData= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$prescriptionTemplateContentData);
		}
		//$imgPicReplace= $tmp_include_root.'/common/new_html2pdf/pic_vision_pc.jpg';
		//$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/pic_vision_pc.jpg',$imgPicReplace,$prescriptionTemplateContentData);
		
		$signatureReplace= $tmp_include_root.'/common/new_html2pdf/tmp/';
		$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$prescriptionTemplateContentData);
		if(strtoupper(substr(PHP_OS, 0, 3))=='LIN'){ 
			$prescriptionTemplateContentData= mb_convert_encoding($prescriptionTemplateContentData, "HTML-ENTITIES", 'UTF-8');
		}		
		
		
		$getFinalHTMLForGivenMR=$prescriptionTemplateContentData;
		//$fp = fopen('../common/new_html2pdf/pdffile.html','w');	

		//replace src elements with full Path
		

		//
		$fn="pdffileprsc";
		$fp = "/tmp/".$fn.".html";
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$getFinalHTMLForGivenMR = $oSaveFile->corImgPath4Pdf($getFinalHTMLForGivenMR);			
		//-----PRISM IMAGE REPLACE INCASE OF TEXTBOX OR PRINT FROM PRS SECTION WHEN SEND FAX AVAILABLE
		$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['php_server']."/library/images/pic_vision_pc.jpg",$GLOBALS['fileroot']."/library/images/pic_vision_pc.jpg",$getFinalHTMLForGivenMR);
		
		if(empty($GLOBALS['webroot']))
		{   //BELOW CONDITION WORK INCASE OF HCCS SERVER WHEN $GLOBALS['webroot'] is empty
			$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['fileroot'].'../../data/',$GLOBALS['fileroot'].'/data/',$getFinalHTMLForGivenMR);			
			//PRISM IMAGE REPLACE FOR HCCS SERVER WHEN WEBROOT IS EMPTY
			$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['fileroot'].$GLOBALS['fileroot']."/library/images/pic_vision_pc.jpg",$GLOBALS['fileroot']."/library/images/pic_vision_pc.jpg",$getFinalHTMLForGivenMR);
		}	
		$resp = $oSaveFile->cr_file($fp,$getFinalHTMLForGivenMR);			
		$printOptionType_v = empty($printOptionType) ? 'l' : 'p';
		//CALL TO SEND FAX FILE WITH REQUIRED PARAMETERS
		if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1 && ($_REQUEST['faxchartIdPRS']) ){
			echo '<script type="text/javascript">
			window.location="sendfax_gl_cl_rx.php?pdfversion=html2pdf&txtFaxRecipent='.trim($_REQUEST['selectedReferringPhy']).'&txtFaxNo='.trim($_REQUEST['sendFaxNumber']).'&file_location='.$resp.'&faxedformId='.trim($_REQUEST['faxchartIdPRS']).'";</script>';
			exit;
		}
		
		echo "
			<script type=\"text/javascript\">
			window.focus();
			var parWidth = 595;
			var parHeight = 841;
			var printOptionStyle;
			printOptionStyle = '".$printOptionType_v."';				
			window.open('".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op='+printOptionStyle+'&file_location='+'".$fn."','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
			</script>			
		";
		
	}
	
	
	function print_rx($final_flag=0){
		global $ChartNoteImagesString;
		$ChartNoteImagesString=array();
		$OBJsmart_tags = new SmartTags;
		$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
		//print_r($arr_smartTags);
		$billing_global_server_name_str=strtolower($this->billing_global_server_name);
		//$billing_global_server_name_str;
		
		//---
		////////on Submit Print The Data//
		if($_POST["printOptionType"]!="" && $_POST["finalHtmlForPrinting"]!=""){
		
			$this->process_final_html();
			
			$flgStopExec = 1;
		}
		//---
		
		if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
		
		/*---SAMRT TAG CODE END---*/
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
		header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
		header("Cache-control: private, no-cache"); 
		header("Pragma: no-cache");
		
		
		$printType=3;
		
		$patientId = $this->pid;
		$pid = $patientId;
		
		$form_id = $this->fid;
		
		// IF PRINT THEN FORM ID
		$print_form_id = $_REQUEST['print_form_id'];
			if($print_form_id){
				$form_id = $print_form_id;
				$this->fid = $form_id;
		}
		
		//Get Prescription template --
		list($getInputForTextBoxes, $flgStopExec, 
					$printOptionType, $prescriptionTemplateContentData) = $this->get_prescription_template($printType);
		//--
		
		//PC1
		$getFinalHTMLForGivenMR="";
		$tmp="".$this->getHTMLForGivenRx($printType,$givenMrValue="PC1",$prescriptionTemplateContentData, $final_flag)."";
		if(!empty($tmp)){$getFinalHTMLForGivenMR.="<page>".$tmp."</page>";}
		$getFinalHTMLForGivenMR=$getFinalHTMLForGivenMR;
		
		$fn="pdffileRx";
		$fp = "/tmp/".$fn.".html";
			
		//
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$resp = $oSaveFile->cr_file($fp,$getFinalHTMLForGivenMR);
		//if($medExist==true) { echo $resp;}
		

		//echo $prescriptionTemplateContentData;die;
		if( $getInputForTextBoxes==false){
			
			$printOptionType_v = empty($printOptionType) ? 'l' : 'p';
			
			echo "
				<script type=\"text/javascript\">
					window.focus();
					var parWidth = 595;
					var parHeight = 841;
					var printOptionStyle
					printOptionStyle = '".$printOptionType_v."';
					window.open('".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op='+printOptionStyle+'&file_location='+'".$fn."','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
				</script>	
			";
		
		}else{
			//include($GLOBALS['fileroot']."/interface/chart_notes/view/print_patient_rx.php");
			$pg_title = "Print RX Prescriptions For Patient";
			$pg_submit_uri = $GLOBALS['webroot']."/interface/chart_notes/requestHandler.php?printType=1&elem_formAction=print_patient_rx";
			include($GLOBALS['fileroot']."/interface/chart_notes/view/print_patient_vision.php");
		}
		}//if end	
	}
	
	function getMrGivenFromLastVisit(){
		$ret="";
		
		if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ return $ret; }
		
		$flg=0;
		$qryGetSpacialCharValue = " SELECT  
										c1.status_elements,
										c1.id
									FROM 
										`chart_vis_master` c1
										LEFT JOIN chart_master_table c2 ON c1.form_id=c2.id
									WHERE  
										c1.patient_id = '".$this->pid."' 
									ORDER BY  
										c2.date_of_service DESC, c2.id DESC
									LIMIT 0, 1  ";
		/*
		AND (c1.vis_statusElements like  '%elem_mrNoneGiven1=1%' OR 
			c1.vis_statusElements like  '%elem_mrNoneGiven2=1%' OR 
			c1.vis_statusElements like  '%elem_mrNoneGiven3=1%')
		*/					   
		$row = sqlQuery($qryGetSpacialCharValue);		
		if($row!=false){
			$tmp = $row["status_elements"];
			if(preg_match("/elem_mrNoneGiven\d+=1/",$tmp)){				
				$flg=$row["id"];
			}
		}
		
		if(!empty($flg)){
			//Multiple MR
			$sql = "SELECT 
						mr_none_given 
					FROM 
						chart_pc_mr
					WHERE 
						patient_id = '".$this->pid."' 
					AND 
						delete_by = '0' 
					AND 
						id_chart_vis_master='".$flg."' 
					AND 
						ex_type='MR'";
			$rez = sqlStatement($sql);		
			for($i=1; $row!=sqlFetchArray($rez); $i++){	
				if(!empty($row["mr_none_given"])){  if(!empty($ret)){ $ret.=","; }	$ret .= $row["mr_none_given"];	}
			}
		}	
		
		return $ret;
	}
	
//get MR values when Given was actually Given--
function chkMRGivenActual( $sql, $mr, $sel){
	$patientId = $this->pid;
	if($mr == "MR 3"){
		$mr_ind="3";
		$stts_chk="elem_providerNameOther_3=1";	
	}else if($mr == "MR 2"){
		$mr_ind="2";
		$stts_chk="elem_providerNameOther=1";
	}else if($mr == "MR 1"){//MR 1
		$mr_ind="1";
		$stts_chk="elem_providerName=1";	
	}else if(!empty($mr) && preg_match("/MR \d+/",$mr)){
		$mr_ind="";
		$mr_ind=str_replace("MR","",$mr); 
		$mr_ind = trim($mr_ind);
		$stts_chk="elem_providerNameOther_".$mr_ind."=1";		
	}
	
	//
	$flg_chk=0;
	$stts_chk2="elem_mrNoneGiven".$mr_ind."=1";
	$qryGetSpacialCharValue = $sql;
	$row = sqlQuery($qryGetSpacialCharValue);
	if($row!=false){
		//check given 
		if(strpos($row["vis_statusElements"], $stts_chk2)!==false && strpos($row["vis_statusElements"], $stts_chk)!==false){
			$flg_chk=1;
		}else{
			//get given values when given was actually given			
			$givendt="";
			if(!empty($row["vis_mr_pres_dt"]) && ($row["vis_mr_pres_dt"]!="0000-00-00")){ 
			
				$givendt=$row["vis_mr_pres_dt"]; 
				$qryGetSpacialCharValue = "
					SELECT 
					".$sel."		
					FROM 
						chart_vis_master c4 
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
					WHERE 
						c4.patient_id = '".$patientId."' 
					AND 
						c1.ex_type='MR' AND c1.ex_number='".$mr_ind."' 
					AND 
						c1.mr_pres_date='".$givendt."'
					AND 
						c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%'
					AND 
						c4.status_elements like  '%".$stts_chk."%'
					AND 
						c1.delete_by='0'  
					Order By c4.id;
				";
				
				$row = sqlQuery($qryGetSpacialCharValue);	   
				if($row!=false){	
					$flg_chk=1;
				}
			}			
		}
	}
	
	//
	if($flg_chk==0){		
		$qryGetSpacialCharValue = "
					SELECT 
					".$sel."		
					FROM 
						chart_vis_master c4 
						LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
						LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
						LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'					 	
					WHERE 
						c4.patient_id = '".$patientId."' 
					AND 
						c1.ex_type='MR' 
					AND 
						c1.ex_number='".$mr_ind."' 
					AND 
						c4.status_elements like  '%elem_mrNoneGiven".$mr_ind."=1%' 
					AND 
						c4.status_elements like  '%".$stts_chk."%'
					AND 
						c1.delete_by='0'  
					Order By 
						mr_pres_date DESC, 
						c4.id DESC;
				";					   
	}	
	
	return $qryGetSpacialCharValue;
}
//--	

function get_mr_dos($patientId, $form_id,$extendedMRVal=""){
	
	//---BELOW WHERE CONDITION CLAUSE ADDED TO DYNAMIC DISPLAY THE SIGNATURES FOR FURTHER MR'S AFTER MR 3---
	$extendMRWhrCond ="";
	if(!empty(trim($extendedMRVal)))
	{
	$extendMRWhrCond = "
						|| cv.status_elements like  '%elem_visMrOtherOdS_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdC_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdAdd_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdTxt1_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdA_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdP_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdPrism_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsAdd_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsS_".$extendedMRVal."=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsPrism_".$extendedMRVal."=1,%'
					";
	}	
	//---End---
	$qryGetDOS="SELECT 
					cmt.date_of_service as dos,
					cmt.id as form_id,
					cmt.patient_id as patient_id 
				FROM
					`chart_vis_master` as cv 
					LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
					LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id
				WHERE 
					cv.status_elements!='' 
				AND 
					cv.patient_id='".$patientId."' 
				AND 
					cv.form_id='".$form_id."' 
				AND 
					cpm.ex_type='MR' 
				AND 
					cpm.delete_by='0' 
				AND
					(cv.status_elements like  '%elem_visMrOdA=1,%'
					|| cv.status_elements like  '%elem_visMrOdA=1,%'
					|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
					|| cv.status_elements like  '%elem_visMrOdS=1,%'
					|| cv.status_elements like  '%elem_visMrOdC=1,%'
					|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
					|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
					|| cv.status_elements like  '%elem_visMrOdP=1,%'
					|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
					|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
					|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
					|| cv.status_elements like  '%elem_providerName=1,%'
					
					|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
					|| cv.status_elements like  '%elem_providerNameOther=1,%'
					|| cv.status_elements like  '%elem_providerIdOther=1,%'
					
					|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
					|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
					$extendMRWhrCond
					)
	";
	//$qry1=mysql_query("select vis_statusElements,exam_date from chart_vision where vis_mr_none_given!='' and patient_id='".$patientId."' and form_id='".$form_id."'");
	$qry1=imw_query($qryGetDOS);
	$co=imw_num_rows($qry1);
	if(($co > 0)){
		$crow=imw_fetch_array($qry1);
		//$date_of_service = date("m-d-Y", strtotime($crow["dos"]));	
		$date_of_service = wv_formatDate($crow["dos"]);
		$form_id_cv=$crow["form_id"];
		$patient_id_cv=$crow["patient_id"];
		
	}else{
	
		$qryGetDOS="SELECT 
						cmt.date_of_service as dos,
						cmt.id as form_id,cmt.patient_id as patient_id 
					FROM 
						`chart_vis_master` as cv 
						LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
						LEFT JOIN chart_pc_mr as cpm ON cpm.id_chart_vis_master = cv.id 
					WHERE 
						cv.status_elements!='' 
					AND 
						cv.patient_id='".$patientId."' 
					AND 
						(cv.status_elements like  '%elem_visMrOdA=1,%'
						|| cv.status_elements like  '%elem_visMrOdA=1,%'
						|| cv.status_elements like  '%elem_visMrOdAdd=1,%'
						|| cv.status_elements like  '%elem_visMrOdS=1,%'
						|| cv.status_elements like  '%elem_visMrOdC=1,%'
						|| cv.status_elements like  '%elem_visMrOdTxt1=1,%'
						|| cv.status_elements like  '%elem_visMrOdTxt2=1,%'
						|| cv.status_elements like  '%elem_visMrOdP=1,%'
						|| cv.status_elements like  '%elem_visMrOdSel1=1,%'
						|| cv.status_elements like  '%elem_visMrOdSlash=1,%'
						|| cv.status_elements like  '%elem_visMrOdPrism=1,%'
						|| cv.status_elements like  '%elem_providerName=1,%'
						
						|| cv.status_elements like  '%elem_visMrOtherOdA=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdAdd=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdTxt2=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdTxt1=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdS=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdC=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdP=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdSel1=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdSlash=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdPrism=1,%'
						|| cv.status_elements like  '%elem_providerNameOther=1,%'
						|| cv.status_elements like  '%elem_providerIdOther=1,%'
						
						|| cv.status_elements like  '%elem_visMrOtherOdS_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdC_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdAdd_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdTxt1_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdA_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdP_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOdPrism_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsAdd_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsS_3=1,%'
						|| cv.status_elements like  '%elem_visMrOtherOsPrism_3=1,%'
						) 
					ORDER BY 
						cmt.date_of_service DESC, 
						cmt.id DESC limit 1
	";	
		/*
		$qryGetDOS="select form_id,patient_id from chart_vision 
			 where vis_statusElements!='' and patient_id='".$patientId."'
			  and 
			(vis_statusElements like  '%elem_visMrOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOdAdd=1,%'
			|| vis_statusElements like  '%elem_visMrOdS=1,%'
			|| vis_statusElements like  '%elem_visMrOdC=1,%'
			|| vis_statusElements like  '%elem_visMrOdTxt1=1,%'
			|| vis_statusElements like  '%elem_visMrOdTxt2=1,%'
			|| vis_statusElements like  '%elem_visMrOdP=1,%'
			|| vis_statusElements like  '%elem_visMrOdSel1=1,%'
			|| vis_statusElements like  '%elem_visMrOdSlash=1,%'
			|| vis_statusElements like  '%elem_visMrOdPrism=1,%'
			|| vis_statusElements like  '%elem_providerName=1,%'
			
			|| vis_statusElements like  '%elem_visMrOtherOdA=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdAdd=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt2=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt1=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdS=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdC=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdP=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdSel1=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdSlash=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdPrism=1,%'
			|| vis_statusElements like  '%elem_providerNameOther=1,%'
			|| vis_statusElements like  '%elem_providerIdOther=1,%'
			
			|| vis_statusElements like  '%elem_visMrOtherOdS_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdC_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdAdd_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdTxt1_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdA_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdP_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOdPrism_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsAdd_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsS_3=1,%'
			|| vis_statusElements like  '%elem_visMrOtherOsPrism_3=1,%'
			) order by form_id DESC limit 1
			";
		*/	
		$qryGetPrevious=imw_query($qryGetDOS);
		$resGetPrivious=imw_num_rows($qryGetPrevious);
		if($resGetPrivious>0){
			$rowExamDate=imw_fetch_array($qryGetPrevious);
			$form_id_cv	   = $rowExamDate["form_id"];
			$patient_id_cv = $rowExamDate["patient_id"];
			
		}
		
	}	
	return array($date_of_service,$form_id_cv,$patient_id_cv);
}
	
function getHTMLForGivenMR($printType,$givenMrValue,$prescriptionTemplateContentData){
global $gdFilename, $oVis, $zOnParentServer,$ChartNoteImagesString,$billing_global_server_name_str,$objManageData;

$patientId = $this->pid;
$form_id =  $this->fid;

/////get patient data////
$oPt = new Patient($patientId);
$ptAge = $oPt->getAge();
$patientname = $oPt->getName();
$arrPtInfo = $oPt->getPtInfo();
extract($arrPtInfo);
$patientGeoData="";
$ptAgeShow = "".$ptAge." Yr.";
$st1st2="";
$patientEmail="";
$patientAddressFull = $arrPtInfo["street"];
if(!empty($arrPtInfo["street"])){ $st1st2 = ',&nbsp;'; }else { $st1st2 = '&nbsp;'; }
if(!empty($arrPtInfo["street2"])){$patientAddressFull .= $st1st2.$arrPtInfo["street2"].", ";}
if(!empty($arrPtInfo["city"])){$patientGeoData .= '&nbsp;'.$arrPtInfo["city"].",&nbsp;"; }
if(!empty($arrPtInfo["state"])){$patientGeoData .= $arrPtInfo["state"]."&nbsp;";}
if(!empty($arrPtInfo["postal_code"])){$patientGeoData .= '-&nbsp;'.$arrPtInfo["postal_code"];}
if(!empty($arrPtInfo["email"])){ $patientEmail= $arrPtInfo["email"]; }
$patientAddressFull .= $patientGeoData;
$patientNickName = $arrPtInfo["nick_name"];
// IF PRINT THEN FORM ID
//echo("select * from  chart_left_cc_history where patient_id='$patientId' and form_id='$form_id'"."<BR> CHECK FORMID IS COMING<br>");
   //$qry1=mysql_query("select * from  chart_master_table where patient_id='$patientId' and id='$form_id'");
//   echo "select cmt.date_of_service from chart_master_table where patient_id='$patientId' and id='$form_id'";
	
	//---BELOW CODE ADDED TO DISPLAY THE SIGNATURES FOR FURTHER MR'S AFTER MR 3---
	$defaultMrArr = array("MR 1", "MR 2", "MR 3");

	$extendedMRVal = "";
	if (!in_array($defaultMrArr, trim($givenMrValue)))
	{
		$extendedMRVal = str_ireplace('MR','',$givenMrValue);
		$extendedMRVal = trim($extendedMRVal);
	}
	
	list($date_of_service, $form_id_cv,$patient_id_cv) = $this->get_mr_dos($patientId, $form_id,$extendedMRVal);
	if($_REQUEST['chartIdPRS']){ //This ID comes from PRS for printing previous dos.
		$form_id_cv=$_REQUEST['chartIdPRS'];
	}

	if($form_id_cv && $patient_id_cv){
		$oCn = new ChartNote($patient_id_cv, $form_id_cv);
		$date_of_service = $oCn->getDos();
	}
	//die($date_of_service);
	
/////End date of sevice Code////////////////
//get today date//
	$today = wv_formatDate(date('Y-m-d'));
//end today date//
//echo("Which MR GIVEN :<input type='text' value='$givenMrValue'/><br>");


if(!empty($givenMrValue) && preg_match("/MR \d+/",$givenMrValue)){
	$ex_number = str_replace("MR","",$givenMrValue);
	$ex_number = trim($ex_number);
	$sel = "
		c1.provider_id, c1.ex_desc as notes, c1.mr_pres_date as vis_mr_pres_dt, c1.form_id as  vis_form_id,
		c2.sph as OdSpherical, c2.cyl as odCylinder, c2.axs as odAxis, c2.ad as odAdd, c2.prsm_p as odPrism1, c2.prism as odBase2, c2.slash as odBase1, c2.sel_1 as odPrism2,				
		c3.sph as osSpherical, c3.cyl as osCylinder, c3.axs as osAxis, c3.ad as osAdd, c3.prsm_p as osPrism1, c3.prism as osBase2, c3.slash as osBase1, c3.sel_1 as osPrism2,  
		c4.status_elements as vis_statusElements
	";
	
	$qryGetSpacialCharValue = "
		SELECT 
			".$sel."		
		FROM 
			`chart_vis_master` c4
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c4.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'		 	
		WHERE 
			c4.form_id='".$form_id."' 
		AND 
			c4.patient_id = '".$patientId."' 
		AND 
			c1.ex_type='MR' 
		AND 
			c1.ex_number='".$ex_number."' 
		AND 
			c1.delete_by='0'  
		Order By 
			ex_number;
	";
	
	$indx1=$indx2="";
	if($ex_number>1){
		$indx1="Other";
		if($ex_number>2){
			$indx2="_".$ex_number;
		}
	}
	$stts_chk="elem_providerName".$indx1.$indx2."=1";
	
	//get MR values when Given was actually Given--
	$qryGetSpacialCharValue = $this->chkMRGivenActual($qryGetSpacialCharValue, $givenMrValue, $sel);

}						
	
//echo("QUERY:".$qryGetSpacialCharValue."<br>");
		if($qryGetSpacialCharValue!=""){
			$rsGetSpacialCharValue = imw_query($qryGetSpacialCharValue)	or die($qryGetSpacialCharValue.imw_error());
			$numRowGetSpacialCharValue = imw_num_rows($rsGetSpacialCharValue);		
			
			$flgLF=0;
			//ifNo Record
			if($numRowGetSpacialCharValue<=0){			
				//$date_of_service = $oVis->getDos();			
				//get values of last finalized if any				
				$dt = wv_formatDate($date_of_service,0,0,"insert");
				$res = $this->getLastRecord($sel,"0",$dt);
				if($res!=false){$rsGetSpacialCharValue=$res;}else{$rsGetSpacialCharValue=false;}
				$numRowGetSpacialCharValue = imw_num_rows($res);
				$flgLF=1;
			}
			
			if($numRowGetSpacialCharValue){
					
					if($flgLF==0){
						$rowTmp = imw_fetch_array($rsGetSpacialCharValue);
					}else{
						$rowTmp = $rsGetSpacialCharValue;
					}
					
					extract($rowTmp);
					
					//if(strpos($vis_statusElements, $stts_chk)===false){
						/*
						//Empty Record
						$OdSpherical="";
						$odCylinder="";
						$odAxis="";
						$odPrism1="";
						$odPrism2="";
						$odBase1="";
						$odBase2="";
						$odAdd="";

						$osSpherical="";
						$osCylinder="";
						$osAxis="";
						$osPrism1="";
						$osPrism2="";
						$osBase1="";
						$osBase2="";
						$osAdd="";
						$notes="";
						*/
						
						//3/28/2012 :: Printing both MR's when only wanted new... please see attached image.
						//return ""; //Comment to print all  given MR
					//}else{
					
						$odPrism ="";
						$osBase ="";
						if($odPrism1){
							$odPrism = $odPrism1;
						}
						$protocol = "";
						if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
						if($odPrism2 && $odPrism1){
							//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
							//$prismimage="../common/new_html2pdf/pic_vision_pc.jpg";
						    $prismimage=$GLOBALS['php_server'].'/library/images/pic_vision_pc.jpg';
							$odPrism .= "<img src='".$prismimage."' style='width:12px;height:12px;' />". $odPrism2;
						}
						
						if($odBase1){
							$odBase = $odBase1;
						}
						if($odBase2 && $odBase1){
							$odBase .= ' '. $odBase2;
						}
					///////////////////////////
						if($osPrism1){
							$osPrism = $osPrism1;
						}
						if($osPrism2 && $osPrism1){
							//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
							//$prismimage="../common/new_html2pdf/pic_vision_pc.jpg";
						    $prismimage=$GLOBALS['php_server'].'/library/images/pic_vision_pc.jpg';
							$osPrism .= "<img src='".$prismimage."' style='width:12px;height:12px;' />". $osPrism2;
						}
						
						if($osBase1){
							$osBase = $osBase1;
						}
						if($osBase2 && $osBase1){
							$osBase .= ' '. $osBase2;
						}
					///////////////////////////
						if($odAxis){
							$odAxis .= "&deg;";
						}
						
						if($osAxis){
							$osAxis .= "&deg;";
						}
						
						if($notes){
							$notes= htmlspecialchars($notes);
						}
					//}
					
					//DOS should be prescription date						
					if(!empty($vis_mr_pres_dt)&&$vis_mr_pres_dt!="0000-00-00"){
						$vis_mr_pres_dt_show =  wv_formatDate($vis_mr_pres_dt) ; 					
					}else if(!empty($vis_form_id)){
						$oChartNote = new ChartNote($patientId,$vis_form_id);
						$vis_mr_pres_dt_show = $oChartNote->getDos();						
					}else{
						$vis_mr_pres_dt_show =  $date_of_service ;
					}
					
					
					//set form to show previous doctor--
					if(!empty($vis_form_id)){
						$form_id_cv=$vis_form_id;					
					}
					//set form to show previous doctor--					
					
					
		}
	}
	

//$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='".$printType."'";	
//$rsGetTempData = mysql_query($qryGetTempData)	or die($qryGetTempData.mysql_error());
//$numRowGetTempData = mysql_num_rows($rsGetTempData);
//extract(mysql_fetch_array($rsGetTempData));	
//$printOptionType = $printOption;

if($prescriptionTemplateContentData!=""){
	$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
	//$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
	
	$prescriptionTemplateContentData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('%20',' ',$prescriptionTemplateContentData);
	/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
	$OBJsmart_tags = new SmartTags;
	$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
	if($arr_smartTags){
		foreach($arr_smartTags as $key=>$val){
			$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<a id="'.$key.'" class="cls_smart_tags_link" href="javascript:;" oncontextmenu="return false">'.$val.'</a>',$prescriptionTemplateContentData);	
		}	
	}
	/*--SMART TAG REPLACEMENT END--*/
	
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT GEOGRAPHICAL DATA}',$patientGeoData,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$OdSpherical,$prescriptionTemplateContentData);
	
	if($odCylinder!=""){
		$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$odCylinder,$prescriptionTemplateContentData);
	}else{	
		
		$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
	}
	
	$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',$odAxis,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD PRISM}',$odPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD HORIZONTAL PRISM}',$odPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$odAdd,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$osSpherical,$prescriptionTemplateContentData);
	
	if($osCylinder!=""){
	
		$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
	}else{	
		
		$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
	}
	//$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',$osAxis,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS PRISM}',$osPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS HORIZONTAL PRISM}',$osPrism,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$osAdd,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
	
	
	/*
	$prescriptionTemplateContentData = str_ireplace('{PATIENT DOB}',$pat_dob,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PT AGE}',$ptAgeShow,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD BASE}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS BASE}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TODAY DATE}',$today,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
	*/

	//Modified Variables
	$prescriptionTemplateContentData = str_ireplace('{DOB}',wv_formatDate($DOB),$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OD VERTICAL PRISM}',$odBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{OS VERTICAL PRISM}',$osBase,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{GIVEN_DATE}',wv_formatDate($vis_mr_pres_dt),$prescriptionTemplateContentData);
	//New variable added	
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME SUFFIX}',$suffix,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{STATE ZIP CODE}',$state.' '.$postal_code,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{PT_EMAIL_ADDRESS}',$patientEmail,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{PATIENT_NICK_NAME}',$patientNickName,$prescriptionTemplateContentData);
	$raceShow						 = trim($race);
	$otherRace						 = trim($otherRace);
	if($otherRace) { 
		$raceShow					 = $otherRace;
	}
	$languageShow					 = str_ireplace("Other -- ","",$language);
	$ethnicityShow					 = trim($ethnicity);			
	$otherEthnicity					 = trim($otherEthnicity);
	if($otherEthnicity) { 
		$ethnicityShow				 = $otherEthnicity;
	}
	$prescriptionTemplateContentData = str_ireplace('{RACE}',$raceShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{LANGUAGE}',$languageShow,$prescriptionTemplateContentData);	
	$prescriptionTemplateContentData = str_ireplace('{ETHNICITY}',$ethnicityShow,$prescriptionTemplateContentData);	
	
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','<input type="text"  value="" size="1"  maxlength="1"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','<input type="text" name="textbox[]" value="" size="30"  maxlength="30"  tempEndTextBox>',$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','<input type="text" name="textbox[]" value="" size="60"  maxlength="60"  tempEndTextBox>',$prescriptionTemplateContentData);
	//echo date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
	if(constant("RX_EXPIRY_DATE")!="" && (constant("RX_EXPIRY_DATE")==12 || constant("RX_EXPIRY_DATE")==24)){
		list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
		$dos_mnt = $dos_mnt + constant("RX_EXPIRY_DATE");
		$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt,$dos_dy,$dos_yr));
	}else{
		$expirationDate = wv_formatDate(date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y'))),'mm-dd-yyyy');
	}
	$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);
	
	
	//---PRS Render Appointment Variables Based on Appointment---
	$appt_id = "";
	$app_start_date = getDateFormatDB($date_of_service);	
	if($patientId && ($app_start_date && $app_start_date!= '0000-00-00'))
	{	
		$qry_appt_id = "SELECT 
							id 
						FROM 
							`schedule_appointments` 
						WHERE 
							sa_patient_id = '".$patientId."' 
						AND 
							sa_app_start_date = '".$app_start_date."' 
						AND 
							sa_patient_app_status_id NOT IN (18,203)
						ORDER BY sa_app_start_date ASC LIMIT 0,1";
		$exe_appt_id = imw_query($qry_appt_id);
		if(imw_num_rows( $exe_appt_id ) > 0 )
		{
			$row_appt_id = imw_fetch_assoc($exe_appt_id);
			$appt_id = 	$row_appt_id['id'];
		}	
	}

	$oPtSch = new ManageData($patientId);
	$apptFacPhone="";
	$apptFacInfo = $oPtSch->__getApptInfo($_SESSION['patient'],'','','',$appt_id);
	$apptFacname = $apptFacInfo[2];
	if(!empty($apptFacInfo[10])){
		$apptFacstreet = $apptFacInfo[10].', ';	
	}
	if(!empty($apptFacInfo[11])){
		$apptFaccity = $apptFacInfo[11].', ';	
	}
	if(!empty($apptFacInfo[3])){ $apptFacPhone =  $apptFacInfo[3]; }
	$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY PHONE}',$apptFacPhone,$prescriptionTemplateContentData);
	
	
	$objManageData = new ManageData();  //OBJECT USED TO CALL FUNCTIONS.PHP CLASS FUNCTIONS
	//=========LOGGED IN FACILITY INFO VOCABULARY REPLACEMENTS STARTS HERE==========================
	$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = $loginFacility = $loginFacAddress = "";
	$loggedfacilityInfoArr 	= $objManageData->logged_in_facility_info($_SESSION['login_facility']);
	$loggedfacstreet 		= $loggedfacilityInfoArr[1];
	$loggedfacity 			= $loggedfacilityInfoArr[2];
	$loggedfacstate			= $loggedfacilityInfoArr[3];
	$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
	$loggedfacExt	   		= $loggedfacilityInfoArr[5];
	if($loggedfacPostalcode && $loggedfacExt){
		$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
	}else{
		$loggedzipcodext = $loggedfacPostalcode;
	}
	
	$loginFacility 	= $loggedfacilityInfoArr[0];
	$loginFacAddress = $loggedfacstreet.', '.$loggedfacity.',&nbsp;'.$loggedfacstate.'&nbsp;'.$loggedzipcodext;
	
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loginFacility,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loginFacAddress,$prescriptionTemplateContentData);		 		
			
	//=============================ENDS HERE=============================================================
		
	//=========Corrected Visual Acuity Data Management==========================
	$vision_od = $vision_os = $visionData = "";
	$visionData	= $objManageData->__getVision($patientId,$form_id);
	$vision_od	= $visionData[1];
	$vision_os	= $visionData[2];
		
	$prescriptionTemplateContentData = str_ireplace('{V-CC-OD}',$vision_od,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{V-CC-OS}',$vision_os,$prescriptionTemplateContentData);	 		
		
	//=============================ENDS HERE====================================
		
	//=========Manifest Refraction Vocabulary Replacement (MR1, MR2)============
	$MR1 = $MR2 = $MRData = "";
	$MRData	= $objManageData->__getMr1Mr2Mr3($patientId,$form_id);
		
	$MR1	= $MRData[0];
	$MR2	= $MRData[1];
		
	$prescriptionTemplateContentData = str_ireplace('{MR1}',$MR1,$prescriptionTemplateContentData);
	$prescriptionTemplateContentData = str_ireplace('{MR2}',$MR2,$prescriptionTemplateContentData);	 	
	//=============================ENDS HERE====================================
	
	//Signature
	//====get physician who save the MR1,MR2,MR3 value===// 
	if($form_id_cv && $patient_id_cv){
		$form_id=$form_id_cv;
		$patientId=$patient_id_cv;
		$this->fid = $form_id_cv;
		$this->pid = $patient_id_cv;
	}
	$prescriptionTemplateContentData = $this->set_signature_in_html($prescriptionTemplateContentData);
	//--
	

}// End Template HTml Blank Check
	
return $prescriptionTemplateContentData	;
} //End Function
	
	function get_given_mr_more($pid, $fid){
		$ar = array();
		$sql = "SELECT 
					mr_none_given 
				FROM 
					`chart_vis_master` c1
					LEFT JOIN chart_pc_mr c2 ON c1.id = c2.id_chart_vis_master
				WHERE 
					c1.patient_id = '".$pid."' 
				AND 
					c1.form_id='".$fid."' 
				AND 
					c2.ex_type='MR' 
				AND 
					c2.mr_none_given != '' 
				And 
					c2.delete_by='0' ";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["mr_none_given"])){
				$ar[] = $row["mr_none_given"];
			}
		}
		return $ar;
	}
	
	function print_mr($final_flag){
		global $ChartNoteImagesString;
		$ChartNoteImagesString=array();
		$OBJsmart_tags = new SmartTags;
		$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
		//print_r($arr_smartTags);
		$billing_global_server_name_str=strtolower($this->billing_global_server_name);
		
		/*---SAMRT TAG CODE END---*/
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
		header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
		header("Cache-control: private, no-cache"); 
		header("Pragma: no-cache");
		
		// POST --
		if($_POST["printOptionType"]!="" && $_POST["finalHtmlForPrinting"]!=""){
			
			$this->process_final_html();			
		
			$flgStopExec = 1;
		}
		// POST --
		
		
		if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
		$printType = $_REQUEST['printType']; 
		$givenMrValue =trim(str_ireplace('%20'," ",$_REQUEST['givenMr'])); 
		$mrGivenOrNot=false;
		$mrArray=array();
		if(strpos($givenMrValue,",")){
			$mrArray=explode(",",$givenMrValue);
		}else if(!empty($givenMrValue)){
			$mrArray=array($givenMrValue);
		}
		//
		if(isset($_REQUEST['printone']) && !empty($_REQUEST['printone']) && $_REQUEST['printone']>=1 ){ //&& $_REQUEST['printone']<=3)
			$mrArray=array();
			$tmp = "MR ".$_REQUEST['printone'];
			$mrArray[] = $tmp;
		}

		if(count($mrArray)>0){
				//if(in_array("MR 1",$mrArray) || in_array("MR 2",$mrArray)|| in_array("MR 3",$mrArray)){
					$mrGivenOrNot=true;
				//}
		//}elseif($givenMrValue=="MR 1" || $givenMrValue=="MR 2" || $givenMrValue=="MR 3"){
		}elseif( !empty($givenMrValue) && strpos($givenMrValue, "MR ") !== false ){
			$mrGivenOrNot=true;
		}
		
		if($mrGivenOrNot==false){	
			//get MR given from previous visits
			$givenMrValue=$this->getMrGivenFromLastVisit();
			if(!empty($givenMrValue)){$mrArray=explode(",",$givenMrValue);}else{ $mrArray=array(); }
			if(count($mrArray)>0){
				$mrGivenOrNot=true;
			}else{	
				echo "<script>window.focus();</script>";
				echo("<center>No MR Prescription is given.</center>");
				$flgStopExec = 1;
			}	
		}
		
		if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
			$patientId = $this->pid;
			$form_id = $this->fid;
			$finalize_flag = $final_flag;
			
			// IF PRINT THEN FORM ID
			$print_form_id = $_REQUEST['print_form_id'];
			if($print_form_id){
					$form_id = $print_form_id;
					$this->fid = $form_id;
			}
			if($_REQUEST['chartIdPRS']){ //This ID comes from PRS for printing previous dos.
				$form_id=$_REQUEST['chartIdPRS'];
				$this->fid = $form_id;
			}
			
			//Get Prescription template --
			list($getInputForTextBoxes, $flgStopExec, 
					$printOptionType, $prescriptionTemplateContentData) = $this->get_prescription_template($printType);
			//--
			if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
				if($arr_smartTags){
					foreach($arr_smartTags as $key=>$val){
						$showHtmlPage = stripos($prescriptionTemplateContentData,"[".$val."]");
						if($showHtmlPage !== false){//smarttag found
							$getInputForTextBoxes = true;
							break;
						}
					}
					/*
					foreach($arr_smartTags as $key=>$val){
						$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
					}
					*/	
				}
				
				if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){
				$arr_vis_statusElements=$arr_vis_mr_none_given=array();$vis_statusElements="";
				
				$qryGetDOS_check="	SELECT 
										cv.status_elements, 
										cv.id 
									FROM 
										`chart_vis_master` as cv 
										LEFT JOIN chart_master_table as cmt on(cv.patient_id=cmt.patient_id AND cv.form_id=cmt.id)
									WHERE 
										cv.status_elements!='' 
									AND 
										cv.patient_id='".$patientId."' 
									AND
										cv.form_id='".$form_id."'";
				
				$resGetDOS_check=imw_query($qryGetDOS_check) or die(imw_error());
				if(imw_num_rows($resGetDOS_check)>0){
					$rowGetDOS_check=imw_fetch_assoc($resGetDOS_check);
					$vis_statusElements=trim($rowGetDOS_check['status_elements']);
					if($vis_statusElements){
						$arr_vis_statusElements=explode(",",$vis_statusElements);
					}
					//$vis_mr_none_given=trim($rowGetDOS_check['vis_mr_none_given']);
					//$arr_vis_mr_none_given=explode(",",$vis_mr_none_given);
				}
				
				$tmp = $this->get_given_mr_more($patientId, $form_id); //4+
				$arr_vis_mr_none_given = array_merge($arr_vis_mr_none_given, $tmp);
				
				$flg_no_mr_gvn=1;
				if(count($arr_vis_mr_none_given) > 0){
					foreach($arr_vis_mr_none_given as $k_vis_mr_none_given => $v_vis_mr_none_given){
						if(!empty($v_vis_mr_none_given) && in_array($v_vis_mr_none_given, $mrArray)){
						$v_e = str_replace("MR","",$v_vis_mr_none_given); $v_e = trim($v_e);
						if(!empty($v_e) && !empty($vis_statusElements) && preg_match("/elem_mrNoneGiven".$v_e."=1/",$vis_statusElements)){
							$flg_no_mr_gvn=0;
						}
						}
					}
				}
				
				//if(!(in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements)) && (!in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements)) && (!in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))){
				//if(empty($vis_statusElements) || !preg_match("/elem_mrNoneGiven\d+=1/",$vis_statusElements)){
				if(!empty($flg_no_mr_gvn)){
					die("<center>No MR Prescription is given</center>");	
				}
				}//
				
				if(count($mrArray)>0){
					$getFinalHTMLForGivenMR="";
					foreach($mrArray as $k => $v){
						
						$vnm = str_replace("MR","",$v);
						$vnm = trim($vnm);
						
						//--
						if(!empty($vnm)){
							if(in_array("MR ".$vnm,$mrArray)){
								$flg_tmp = 1;
								if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven".$vnm."=1",$arr_vis_statusElements))) ? 1 : 0; }
								if($flg_tmp == 1){
									$tmp = $this->getHTMLForGivenMR($printType,$givenMrValue="MR ".$vnm,$prescriptionTemplateContentData);
									if(!empty($tmp)){
										$getFinalHTMLForGivenMR.="<page>".$tmp."</page>";
									}
								}
							}
						}
						//--						
					}
					
					/*
					//if(in_array("MR 1",$mrArray) && (in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements))){
					if(in_array("MR 1",$mrArray)){
						$flg_tmp = 1;
						if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven1=1",$arr_vis_statusElements))) ? 1 : 0; }
						if($flg_tmp == 1){
						$tmp = $this->getHTMLForGivenMR($printType,$givenMrValue="MR 1",$prescriptionTemplateContentData);
						if(!empty($tmp)){
						$getFinalHTMLForGivenMR="<page>".$tmp."</page>";
						}
						}
					}
					//if(in_array("MR 2",$mrArray) && (in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements))){
					if(in_array("MR 2",$mrArray)){
						$flg_tmp = 1;
						if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven2=1",$arr_vis_statusElements))) ? 1 : 0; }
						if($flg_tmp == 1){
						$tmp = $this->getHTMLForGivenMR($printType,$givenMrValue="MR 2",$prescriptionTemplateContentData);
						if(!empty($tmp)){
						$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";	
						}
						}
					}
					//if(in_array("MR 3",$mrArray) && (in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))){
					if(in_array("MR 3",$mrArray)){
						$flg_tmp = 1;
						if($this->arGlobal["MR_RX_PRINT_PREVIOUS"]=="NO"){ $flg_tmp = ((in_array("elem_mrNoneGiven3=1",$arr_vis_statusElements))) ? 1 : 0; }
						if($flg_tmp == 1){
						$tmp = $this->getHTMLForGivenMR($printType,$givenMrValue="MR 3",$prescriptionTemplateContentData);
						if(!empty($tmp)){
						$getFinalHTMLForGivenMR .="<page>".$tmp."</page>";
						}
						}
					}
					*/
				//}else if($givenMrValue=="MR 1" || $givenMrValue=="MR 2" || $givenMrValue=="MR 3"){
				}else if(!empty($givenMrValue) && preg_match("/MR \d+/",$givenMrValue)){
					$tmp = $this->getHTMLForGivenMR($printType,$givenMrValue,$prescriptionTemplateContentData);
					if(!empty($tmp)){
					$getFinalHTMLForGivenMR="<page>".$tmp."</page>";
					}
				}
				
				if($getInputForTextBoxes==false){
					$tmp_include_root = (constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]["incdir"] : $GLOBALS["include_root"];
					if(constant("REMOTE_SYNC") != 1){
						$imgALLReplace= $tmp_include_root.'/common/new_html2pdf/' ;
						$prescriptionTemplateContentData= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$prescriptionTemplateContentData);
					}
					//$imgPicReplace= '../../../data/'.PRACTICE_PATH.'/gn_images/pic_vision_pc.jpg';
					//$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/pic_vision_pc.jpg',$imgPicReplace,$prescriptionTemplateContentData);
					
					$signatureReplace= $tmp_include_root.'/common/new_html2pdf/tmp/';
					$prescriptionTemplateContentData=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$prescriptionTemplateContentData);
				
				}else{
					$getFinalHTMLForGivenMR=$getFinalHTMLForGivenMR;
				}
				
				
				//echo $prescriptionTemplateContentData;die;
				if($getInputForTextBoxes==false){
				
					$fn = "pdffilemr";
					$fp = "/tmp/".$fn.".html";
				
					//-- change web to complate path for images --//
					
					if(empty($GLOBALS['webroot']))
					{ 	//BELOW CONDITION WORK INCASE OF HCCS SERVER WHEN $GLOBALS['webroot'] is empty
						$getFinalHTMLForGivenMR = str_ireplace("src='/data/".PRACTICE_PATH."/UserId", "src='".$GLOBALS['fileroot']."/data/".PRACTICE_PATH."/UserId", $getFinalHTMLForGivenMR);	
					}
					else
					{	//BELOW CONDITION WORK INCASE OF CLOUD SERVER				
						$getFinalHTMLForGivenMR = str_ireplace("src='/".PRACTICE_PATH."/data/".PRACTICE_PATH."/UserId", "src='".$GLOBALS['fileroot']."/data/".PRACTICE_PATH."/UserId", $getFinalHTMLForGivenMR);
					
					}
					//-----PRISM IMAGE REPLACE WHEN DIRECT PRINT WITHOUT TEXTBOX-----
					$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['php_server']."/library/images/pic_vision_pc.jpg",$GLOBALS['fileroot']."/library/images/pic_vision_pc.jpg",$getFinalHTMLForGivenMR);
					
					$oSaveFile = new SaveFile($_SESSION["authId"],1);
					$resp = $oSaveFile->cr_file($fp,$getFinalHTMLForGivenMR);
					
					$printOptionType_v = empty($printOptionType) ? 'l' : 'p';
					
					echo "
						<script type=\"text/javascript\">
							window.focus();
							var parWidth = 595;
							var parHeight = 841;
							var printOptionStyle
							printOptionStyle = '".$printOptionType_v."';
							window.open('".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op='+printOptionStyle+'&file_location='+'".$fn."','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
						</script>	
					";
				
				}else{
					
					$phrase_qry = "&givenMr=".$_REQUEST['givenMr']."&printone=".$_REQUEST['printone'];				
					//include($GLOBALS['fileroot']."/interface/chart_notes/view/print_patient_mr.php");
					$pg_title = "Print MR Prescriptions For Patient";
					$pg_submit_uri = $GLOBALS['webroot']."/interface/chart_notes/requestHandler.php?printType=1&elem_formAction=print_mr".$phrase_qry;
					include($GLOBALS['fileroot']."/interface/chart_notes/view/print_patient_vision.php");
				}			
			}
		}//
		}//	
	}
	
	function getHTMLForGivenPC_Multi($printType, $prescriptionTemplateContentData){
		$patientId = $this->pid;
		$form_id = $this->fid;
		$ret="";
		$sql = "SELECT 
					c1.ex_number
				FROM 
					chart_vis_master c4 
					LEFT JOIN chart_pc_mr c1 ON c4.id = c1.id_chart_vis_master 
				WHERE 
					c4.form_id='".$form_id."' 
				AND 
					c4.patient_id = '".$patientId."' 
				AND 
					c1.ex_type='PC' 
				AND 
					c1.delete_by='0'  
				Order By ex_number;
			";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			$tmp_ex_num="";
			$tmp_ex_num = $row["ex_number"];
			if(!empty($tmp_ex_num)){	
			$tmp ="".$this->getHTMLForGivenPC($printType,$givenMrValue="PC".$tmp_ex_num,$prescriptionTemplateContentData)."";
			$tmp = trim($tmp);
			if(!empty($tmp)){$ret.="<page>".$tmp."</page>";}
			}//
		}
		return $ret;	
	}
	
	function getHTMLForGivenPC($printType,$givenMrValue,$prescriptionTemplateContentData){
		global $gdFilename,$ChartNoteImagesString,$zOnParentServer,$billing_global_server_name_str,$objManageData;
		
		$patientId = $this->pid;
		$form_id = $this->fid;
		
		/////get patient data////
		$oPt = new Patient($patientId);
		$ptAge = $oPt->getAge();
		$patientname = $oPt->getName();
		$arrPtInfo = $oPt->getPtInfo();
		extract($arrPtInfo);
		$patientGeoData="";
		$patientEmail="";
		$ptAgeShow = "".$ptAge." Yr.";

		$patientAddressFull = $arrPtInfo["street"];
		if(!empty($arrPtInfo["street2"])){$patientAddressFull .= $arrPtInfo["street2"].", ";}
		if(!empty($arrPtInfo["city"])){$patientGeoData .= $arrPtInfo["city"].", "; }
		if(!empty($arrPtInfo["state"])){$patientGeoData .= $arrPtInfo["state"].", ";}
		if(!empty($arrPtInfo["postal_code"])){$patientGeoData .= $arrPtInfo["postal_code"].", ";}
		$patientAddressFull .= $patientGeoData;
		if(!empty($arrPtInfo["email"])){ $patientEmail= $arrPtInfo["email"]; }
		// IF PRINT THEN FORM ID
		//echo("select * from  chart_left_cc_history where patient_id='$patientId' and form_id='$form_id'"."<BR> CHECK FORMID IS COMING<br>");
		
		$oCn = new ChartNote($patientId, $form_id);
		$date_of_service = $oCn->getDos();
		
		/////End date of sevice Code////////////////
		//get today date//
			$today = wv_formatDate(date('Y-m-d'));
		//end today date//		
		
		if(!empty($givenMrValue) && preg_match("/PC\d+/",$givenMrValue)){ //Multiple
			$ex_num = str_replace("PC", "", $givenMrValue);
			$ex_num = trim($ex_num);
			$qryGetSpacialCharValue="SELECT 
										c1.ex_desc AS notes,
										c2.sph as OdSpherical, 
										c2.cyl as odCylinder, 
										c2.axs as odAxis, 
										c2.ad as odAdd, 
										c2.prsm_p as odPrism1, 
										c2.prism as odBase2, 
										c2.slash as odBase1, 
										c2.sel_2 as odPrism2, 						
										c3.sph as osSpherical,
										c3.cyl as osCylinder, 
										c3.axs as osAxis, 
										c3.ad as osAdd, 
										c3.prsm_p as osPrism1, 
										c3.prism as osBase2, 
										c3.slash as osBase1, 
										c3.sel_2 as osPrism2
									FROM 
										chart_vis_master c4 
										LEFT JOIN chart_pc_mr c1 ON c4.id = c1.id_chart_vis_master 
										LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
										LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
									WHERE 
										c4.form_id='".$form_id."' 
									AND 
										c4.patient_id = '".$patientId."' 
									AND 
										c1.ex_type='PC' 
									AND 
										c1.ex_number='".$ex_num."'  
									AND 
										c1.delete_by='0'  
									Order By c1.ex_number;
									";
		}						
		
		//echo("QUERY:".$qryGetSpacialCharValue."<br>");
			if($qryGetSpacialCharValue!=""){
				$rsGetSpacialCharValue = imw_query($qryGetSpacialCharValue)	or die($qryGetSpacialCharValue.imw_error());
				$numRowGetSpacialCharValue = imw_num_rows($rsGetSpacialCharValue);
				if($numRowGetSpacialCharValue){
					extract(imw_fetch_array($rsGetSpacialCharValue));
					
					$odPrism ="";
					$osBase ="";
					if($odPrism1){
						$odPrism = $odPrism1;
					}
					$protocol = "";
					if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
					if($odPrism2 && $odPrism1){
						//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
						//$prismimage="../common/new_html2pdf/pic_vision_pc.jpg";
					    $prismimage=$GLOBALS['php_server'].'/library/images/pic_vision_pc.jpg';
						$odPrism .= "<img src='".$prismimage."' style='width:12px;height:12px;' />". $odPrism2;
					}
					
					if($odBase1){
						$odBase = $odBase1;
					}
					if($odBase2 && $odBase1){
						$odBase .= ' '. $odBase2;
					}
				///////////////////////////
					if($osPrism1){
						$osPrism = $osPrism1;
					}
					if($osPrism2 && $osPrism1){
						//$prismimage=realpath(dirname(__FILE__)."/../common/new_html2pdf/pic_vision_pc.jpg");
						//$prismimage="../../images/pic_vision_pc.jpg";
					    $prismimage=$GLOBALS['php_server'].'/library/images/pic_vision_pc.jpg';
						$osPrism .= "<img src='".$prismimage."' style='width:12px;height:12px;' />". $osPrism2;
					}
					
					if($osBase1){
						$osBase = $osBase1;
					}
					if($osBase2 && $osBase1){
						$osBase .= ' '. $osBase2;
					}
				///////////////////////////
					if($odAxis){
						$odAxis .= "&#176;";
					}
					
					if($osAxis){
						$osAxis .= "&#176;";
					}
					
					//Check
					if($OdSpherical=="+"||$OdSpherical=="-") $OdSpherical = "";
					if($osSpherical=="+"||$osSpherical=="-") $osSpherical = "";
					if($odCylinder=="+"||$odCylinder=="-")$odCylinder="";
					if($osCylinder=="+"||$osCylinder=="-")$osCylinder="";
					if($odAdd=="+"||$odAdd=="-")$odAdd="";
					if($osAdd=="+"||$osAdd=="-")$osAdd="";
					if(empty($OdSpherical)&&empty($odCylinder)&&empty($odAxis)&&empty($odAdd)&&
						empty($osSpherical)&&empty($osCylinder)&&empty($osAxis)&&empty($osAdd)){
						return "";
					}
				}
				else{
					return "";
				}
			}
		//$qryGetTempData = "select prescription_template_content as prescriptionTemplateContentData,printOption from prescription_template where prescription_template_type ='".$printType."'";	
		//$rsGetTempData = mysql_query($qryGetTempData)	or die($qryGetTempData.mysql_error());
		//$numRowGetTempData = mysql_num_rows($rsGetTempData);
		//extract(mysql_fetch_array($rsGetTempData));	
		//$printOptionType = $printOption;

		if($prescriptionTemplateContentData!=""){
			$prescriptionTemplateContentData = stripslashes($prescriptionTemplateContentData);
			//$prescriptionTemplateContentData = str_ireplace($web_root.'/interface/common/new_html2pdf/','',$prescriptionTemplateContentData);
			//$prescriptionTemplateContentData = str_ireplace($GLOBALS['webroot'].'/data/','../../data/'.PRACTICE_PATH,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('%20',' ',$prescriptionTemplateContentData);
			/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
			$OBJsmart_tags = new SmartTags;
			$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
			if($arr_smartTags){
				foreach($arr_smartTags as $key=>$val){
					$prescriptionTemplateContentData = str_ireplace("[".$val."]",'<A id="'.$key.'" class="cls_smart_tags_link" href="javascript:;">'.$val.'</A>',$prescriptionTemplateContentData);	
				}	
			}
			/*--SMART TAG REPLACEMENT END--*/
			
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME}',ucwords($patientname),$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT GEOGRAPHICAL DATA}',$patientGeoData,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{OD SPHERICAL}',$OdSpherical,$prescriptionTemplateContentData);
			
			if($odCylinder!=""){
				$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',$odCylinder,$prescriptionTemplateContentData);
			}else{	
				
				$prescriptionTemplateContentData = str_ireplace('{OD CYLINDER}',"",$prescriptionTemplateContentData);
			}
			
			$prescriptionTemplateContentData = str_ireplace('{OD AXIS}',$odAxis,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD PRISM}',$odPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD HORIZONTAL PRISM}',$odPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD ADD}',$odAdd,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS SPHERICAL}',$osSpherical,$prescriptionTemplateContentData);
			
			if($osCylinder!=""){
			
				$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
			}else{	
				
				$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',"",$prescriptionTemplateContentData);
			}
			//$prescriptionTemplateContentData = str_ireplace('{OS CYLINDER}',$osCylinder,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS AXIS}',$osAxis,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS PRISM}',$osPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS HORIZONTAL PRISM}',$osPrism,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS ADD}',$osAdd,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{NOTES}',$notes,$prescriptionTemplateContentData);
			
			
			/*
			$prescriptionTemplateContentData = str_ireplace('{PATIENT DOB}',$pat_dob,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PT AGE}',$ptAgeShow,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD BASE}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS BASE}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TODAY DATE}',$today,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DATE OF SERVICE}',$date_of_service,$prescriptionTemplateContentData);
			*/

			//Modified Variables
			$prescriptionTemplateContentData = str_ireplace('{DOB}',wv_formatDate($DOB),$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{AGE}',$ptAgeShow,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD BASE CURVE}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS BASE CURVE}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OD VERTICAL PRISM}',$odBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{OS VERTICAL PRISM}',$osBase,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DATE}',$today,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{FULL ADDRESS}',$patientAddressFull,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{DOS}',$date_of_service,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{GIVEN_DATE}',wv_formatDate($vis_mr_pres_dt),$prescriptionTemplateContentData);
			//New variable added	
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS1}',$street,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{ADDRESS2}',$street2,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT CITY}',$city,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME TITLE}',$title,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT FIRST NAME}',$fname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{MIDDLE NAME}',$mname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{LAST NAME}',$lname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT NAME SUFFIX}',$suffix,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PatientID}',$_SESSION['patient'],$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{HOME PHONE}',$phone_home,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{MOBILE PHONE}',$phone_cell,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{WORK PHONE}',$phone_biz,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{STATE ZIP CODE}',$state.' '.$postal_code,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN}',$External_MRN_1,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{PATIENT MRN2}',$External_MRN_2,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{PT_EMAIL_ADDRESS}',$patientEmail,$prescriptionTemplateContentData);	
			$prescriptionTemplateContentData = str_ireplace('{PATIENT_NICK_NAME}',$patientNickName,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_XSMALL}','<input type="text"  value="" size="1"  maxlength="1"  tempEndTextBox>',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_SMALL}','<input type="text" name="textbox[]" value="" size="30"  maxlength="30"  tempEndTextBox>',$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{TEXTBOX_MEDIUM}','<input type="text" name="textbox[]"  value="" size="60"  maxlength="60"  tempEndTextBox>',$prescriptionTemplateContentData);
			//echo date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y')));
			if(constant("RX_EXPIRY_DATE")!="" && (constant("RX_EXPIRY_DATE")==12 || constant("RX_EXPIRY_DATE")==24)){
				list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$date_of_service);
				$dos_mnt = $dos_mnt + constant("RX_EXPIRY_DATE");
				$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt,$dos_dy,$dos_yr));
			}else{
				$expirationDate = wv_formatDate(date('m-d-Y',mktime(0,0,0,date('m'),date('d')+14,date('Y'))));
			}
			$prescriptionTemplateContentData = str_ireplace('{EXPIRATION DATE}',$expirationDate,$prescriptionTemplateContentData);
		
			//---PRS Render Appointment Variables Based on Appointment---
			$appt_id = "";
			$app_start_date = getDateFormatDB($date_of_service);	
			if($patientId && ($app_start_date && $app_start_date!= '0000-00-00'))
			{	
				$qry_appt_id = "SELECT 
									id 
								FROM 
									`schedule_appointments` 
								WHERE 
									sa_patient_id = '".$patientId."' 
								AND 
									sa_app_start_date = '".$app_start_date."' 
								AND 
									sa_patient_app_status_id NOT IN (18,203)
								ORDER BY sa_app_start_date ASC LIMIT 0,1";
				$exe_appt_id = imw_query($qry_appt_id);
				if(imw_num_rows( $exe_appt_id ) > 0 )
				{
					$row_appt_id = imw_fetch_assoc($exe_appt_id);
					$appt_id = 	$row_appt_id['id'];
				}	
			}
		
			$oPtSch = new ManageData($patientId);
			$apptFacPhone="";
			$apptFacInfo = $oPtSch->__getApptInfo($_SESSION['patient'],'','','',$appt_id);			
			$apptFacname = $apptFacInfo[2];
			if(!empty($apptFacInfo[10])){
				$apptFacstreet = $apptFacInfo[10].', ';	
			}
			if(!empty($apptFacInfo[11])){
				$apptFaccity = $apptFacInfo[11].', ';	
			}
			if(!empty($apptFacInfo[3])){ $apptFacPhone =  $apptFacInfo[3]; }
			$apptFacaddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY NAME}',$apptFacname,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacaddress,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{APPT FACILITY PHONE}',$apptFacPhone,$prescriptionTemplateContentData);
			
			//==Logged in Facility Info Vocabulary Replacement Work Starts Here=====
			$loggedfacCity = $loggedfacState = $loggedfacCountry = $loggedfacPostalcode = $loggedfacExt = $loginFacility = $loginFacAddress = "";
			$loggedfacilityInfoArr 	= $oPtSch->logged_in_facility_info($_SESSION['login_facility']);
			$loggedfacstreet 		= $loggedfacilityInfoArr[1];
			$loggedfacity 			= $loggedfacilityInfoArr[2];
			$loggedfacstate			= $loggedfacilityInfoArr[3];
			$loggedfacPostalcode	= $loggedfacilityInfoArr[4];
			$loggedfacExt	   		= $loggedfacilityInfoArr[5];
			if($loggedfacPostalcode && $loggedfacExt){
				$loggedzipcodext = $loggedfacPostalcode.'-'.$loggedfacExt;
			}else{
				$loggedzipcodext = $loggedfacPostalcode;
			}
			
			$loginFacility 	= $loggedfacilityInfoArr[0];
			$loginFacAddress = $loggedfacstreet.', '.$loggedfacity.',&nbsp;'.$loggedfacstate.'&nbsp;'.$loggedzipcodext;
			
			$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_NAME}',$loginFacility,$prescriptionTemplateContentData);
			$prescriptionTemplateContentData = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}',$loginFacAddress,$prescriptionTemplateContentData);		 		
			//==============ENDS HERE===================
			
			//Signature
			$prescriptionTemplateContentData = $this->set_signature_in_html($prescriptionTemplateContentData);
		
		}// End Template HTml Blank Check
			
			
		return $prescriptionTemplateContentData	;
	} //End Function 
	
	function print_pc($final_flag){
		global $ChartNoteImagesString;
		$ChartNoteImagesString=array();
		$OBJsmart_tags = new SmartTags;
		$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
		//print_r($arr_smartTags);
		$billing_global_server_name_str=strtolower($billing_global_server_name);
		
		/*---SAMRT TAG CODE END---*/
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
		header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
		header("Cache-control: private, no-cache"); 
		header("Pragma: no-cache");
		
		////////on Submit Print The Data//
		if($_POST["printOptionType"]!="" && $_POST["finalHtmlForPrinting"]!=""){
		
			$this->process_final_html();	
		
			$flgStopExec = 1;
		}
		
		if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
			$printType = $_REQUEST['printType'];
			$patientId = $this->pid;
			$form_id = $this->fid;
			// IF PRINT THEN FORM ID
			$print_form_id = $_REQUEST['print_form_id'];
			if($print_form_id){
					$form_id = $print_form_id;
					$this->fid = $form_id;
			}
			
			//Get Prescription template --
			list($getInputForTextBoxes, $flgStopExec, 
					$printOptionType, $prescriptionTemplateContentData) = $this->get_prescription_template($printType);
					
			if(!isset($flgStopExec) || empty($flgStopExec)){ // $flgStopExec = 1;
				/*
				//PC1
				$getFinalHTMLForGivenMR="";
				$tmp="".$this->getHTMLForGivenPC($printType,$givenMrValue="PC1",$prescriptionTemplateContentData)."";
				if(!empty($tmp)){$getFinalHTMLForGivenMR.="<page>".$tmp."</page>";}

				//PC2
				$tmp ="".$this->getHTMLForGivenPC($printType,$givenMrValue="PC2",$prescriptionTemplateContentData)."";
				if(!empty($tmp)){$getFinalHTMLForGivenMR.="<page>".$tmp."</page>";}

				//PC3
				$tmp ="".$this->getHTMLForGivenPC($printType,$givenMrValue="PC3",$prescriptionTemplateContentData)."";
				if(!empty($tmp)){$getFinalHTMLForGivenMR.="<page>".$tmp."</page>";}
				*/
				//PC Multi
				$tmp ="".$this->getHTMLForGivenPC_Multi($printType,$prescriptionTemplateContentData)."";
				if(!empty($tmp)){$getFinalHTMLForGivenMR.=$tmp;}
				
				if($getInputForTextBoxes==false){
					$tmp_include_root = (constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)) ? $GLOBALS["remote"]["incdir"]  : $GLOBALS["include_root"];
					if(constant("REMOTE_SYNC") != 1){
						$imgALLReplace=$tmp_include_root.'/common/new_html2pdf/';
						$getFinalHTMLForGivenMR= str_ireplace($GLOBALS['webroot'].'/interface/common/new_html2pdf/',$imgALLReplace,$getFinalHTMLForGivenMR);
					}
				//	$imgPicReplace=$tmp_include_root.'/common/new_html2pdf/pic_vision_pc.jpg';
				//	$getFinalHTMLForGivenMR=str_ireplace('../../images/pic_vision_pc.jpg',$imgPicReplace,$getFinalHTMLForGivenMR);
					
					$signatureReplace=$tmp_include_root.'/common/new_html2pdf/tmp/';
					$getFinalHTMLForGivenMR=str_ireplace('../common/new_html2pdf/tmp/',$signatureReplace,$getFinalHTMLForGivenMR);
				
				}else{
					$getFinalHTMLForGivenMR=$getFinalHTMLForGivenMR;
				}
				
				$fn="pdffilepc";
				$fp = "/tmp/".$fn.".html";
			
				//
				$oSaveFile = new SaveFile($_SESSION["authId"],1);
				
				$getFinalHTMLForGivenMR = $oSaveFile->corImgPath4Pdf($getFinalHTMLForGivenMR);			
				if(empty($GLOBALS['webroot']))
				{   //BELOW CONDITION WORK INCASE OF HCCS SERVER WHEN $GLOBALS['webroot'] is empty
					$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['fileroot'].'../../data/',$GLOBALS['fileroot'].'/data/',$getFinalHTMLForGivenMR);			
					$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['fileroot'].$GLOBALS['fileroot']."/data/".PRACTICE_PATH."/gn_images/",$GLOBALS['fileroot']."/data/".PRACTICE_PATH."/gn_images/",$getFinalHTMLForGivenMR);
					
					  //BELOW CONDITION WORK INCASE OF HCCS SERVER WHEN $GLOBALS['webroot'] is empty
					$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['fileroot']."/data/".PRACTICE_PATH."/gn_images/","../../data/".PRACTICE_PATH."/gn_images/",$getFinalHTMLForGivenMR);
					
					$getFinalHTMLForGivenMR = str_ireplace("/data/".PRACTICE_PATH."/UserId_","../../data/".PRACTICE_PATH."/UserId_",$getFinalHTMLForGivenMR);
				}
				
				$getFinalHTMLForGivenMR = str_ireplace($GLOBALS['php_server']."/library/images/pic_vision_pc.jpg","../../library/images/pic_vision_pc.jpg",$getFinalHTMLForGivenMR);	
				
				$resp = $oSaveFile->cr_file($fp,$getFinalHTMLForGivenMR);
				
				//echo $prescriptionTemplateContentData;die;
				if($getInputForTextBoxes==false){
					
					$printOptionType_v = empty($printOptionType) ? 'l' : 'p';
					
					echo "
						<script type=\"text/javascript\">
							window.focus();
							var parWidth = 595;
							var parHeight = 841;
							var printOptionStyle
							printOptionStyle = '".$printOptionType_v."';
							window.open('".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op=p&file_location='+'".$fn."','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
						</script>	
					";
				
				}else{
					
					$pg_title = "Print PC Prescriptions For Patient";
					$pg_submit_uri = $GLOBALS['webroot']."/interface/chart_notes/requestHandler.php?printType=1&elem_formAction=print_pc";
					include($GLOBALS['fileroot']."/interface/chart_notes/view/print_patient_vision.php");
				}
			}
		}		
	}
}
?>