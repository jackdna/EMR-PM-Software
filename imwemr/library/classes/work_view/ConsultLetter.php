<?php
//ConsultLetter.php

use PHPMailer\PHPMailer;

class ConsultLetter{
	private $pid, $fid;
	public function __construct($pid, $fid=0){
		$this->pid=$pid;
		$this->fid=$fid;
	}
	
	function fetchConsultMultiPhy($phyIds=false) {
		$where=" and pmrf.patient_id = '".$this->pid."' AND pmrf.ref_phy_id != '0' ";
		$groupby= " ";
		if($phyIds && empty($phyIds)==false){
			$phyIds= array_unique($phyIds);
			$phyIds=implode(',',$phyIds);
			$where=" and (refPhy.physician_Reffer_id IN($phyIds)) ";
			$groupby= " group by refPhy.physician_Reffer_id ";
		}
		$qrySelpatPhy = "select pmrf.phy_type,TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, 
				refPhy.Address1, refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.comments, refPhy.default_address, refPhy.primary_id,refPhy.physician_Reffer_id, 
				refPhy.physician_email,refPhy.delete_status, pmrf.id as pmrfDataId, pmrf.ref_phy_id as pmrfRefId ,refPhy.physician_Reffer_id, pmrf.patient_id
				from patient_multi_ref_phy pmrf
				LEFT JOIN refferphysician refPhy ON ( pmrf.ref_phy_id = refPhy.physician_Reffer_id OR pmrf.ref_phy_id = refPhy.primary_id ) 
				where pmrf.phy_type IN (1,2,3,4)  and pmrf.status = '0' and refPhy.delete_status= '0' 
				".$where."
				".$groupby." ORDER BY pmrf.id ";
		$multi_rs=imw_query($qrySelpatPhy);
		$multiRef=array();
		$allMultiRef=array();
		while($row=imw_fetch_assoc($multi_rs) ) {
			$multiRef[$row['phy_type']][]=$row;
			if($phyIds && empty($phyIds)==false){
				$allMultiRef[$row['physician_Reffer_id']]=$row;
			}
		}
		if($phyIds && empty($phyIds)==false){
		    return $allMultiRef;
		} else {
		    return $multiRef;
		}
	}
	
	function fetchPhyFaxDetails() {
		$phyIds=$_POST['chkPCPids'];
		$multiRef_arr=$this->fetchConsultMultiPhy($phyIds);
		$html='';
		$counter=0;
		foreach($multiRef_arr as $key=>$row) {
			//foreach($arr_row as $row) {
                $counter++;
                $Cc_count=$counter-1;
                if($counter==1){
                    $html.='<div class="row">
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Referring&nbsp;Phy:</label>
                        <div class="col-sm-4">
                        <input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy" value="'.$row['refName'].'">
                        <input type="text" name="selectReferringPhy"  id="selectReferringPhy" onKeyUp="loadPhysicians(this,"hiddselectReferringPhy","","send_fax_number","","","send_fax_number");" value="'.$row['refName'].'" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
                        <div class="col-sm-4">			
                        <input type="text"  name="send_fax_number" id="send_fax_number" class="form-control" value="'.$row['physician_fax'].'" onchange="set_fax_format(this,\''.$GLOBALS['phone_format'].'\');" autocomplete="off">
                        </div>
                    </div>
                </div>';
                }else{
                    $html.='<div class="row">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Cc'.$Cc_count.':</label>
                            <div class="col-sm-4">			
                            <input type="hidden" name="hiddselectReferringPhyCc'.$Cc_count.'" id="hiddselectReferringPhyCc'.$Cc_count.'" value="'.$row['refName'].'">
                            <input type="text" name="selectReferringPhyCc'.$Cc_count.'"  id="selectReferringPhyCc'.$Cc_count.'" onKeyUp="loadPhysicians(this,\'hiddselectReferringPhyCc'.$Cc_count.'\',\'\',\'send_fax_numberCc'.$Cc_count.'\',\'\',\'\',\'send_fax_numberCc'.$Cc_count.'\');" value="'.$row['refName'].'" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-2" for="email">Fax&nbsp;Number:</label>
                            <div class="col-sm-4">
                            <input type="text"  name="send_fax_numberCc'.$Cc_count.'" id="send_fax_numberCc'.$Cc_count.'" class="form-control" value="'.$row['physician_fax'].'" onchange="set_fax_format(this,\''.$GLOBALS['phone_format'].'\');" autocomplete="off" />
                            </div>
                        </div>
                    </div>';
                }
                
                //if($counter==4)break;
           // }
		}
        $html.='<input type="hidden" name="lastPhyCcCount" id="lastPhyCcCount" value="'.$Cc_count.'">';
		return $html;
	}
	
	
	function load_consult(){
		include(dirname(__FILE__)."/../../../config/globals.php");
		$objParser = new PnTempParser;
		$OBJsmart_tags = new SmartTags;
		$OBJhold_sign = new CLSHoldDocument;
		$OBJCommonFunction = new CLSCommonFunction;
		$objMsgCons = new msgConsole();
		$oAdmn = new Admn();
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$up_dir_usr_pth = $oSaveFile->getUploadDirPath();
		$oSaveFile->ptDir("mails");
		
		
		//credentials
		$arrDirectCre = $objMsgCons->pt_direct_credentials($_SESSION['authId']);
		$direct_exist = ($arrDirectCre['email'] != "" && $arrDirectCre['email_password'] != "") ? 1 : 0 ;
		
		//
		$groupEmailConfig = $oAdmn->getGroupInfo();
		
		//variables defined --
		$table = 'consultTemplate';	
		$patientId 			= 0;
		$performed 			= "Not Performed";
		$tempId 			= $_REQUEST['tempId'];
		$pat_id 			= $this->pid;
		$doc_id 			= $_SESSION['authId'];
		$patientId 			= $pat_id;
		$formId 			= $this->fid;
		
		$today 				= date('Y-m-d');
		$consultTemplate	= $_REQUEST['tempId'];
		$templateId 		= $_REQUEST['templateList'];
		$templateIds 		= explode("!~!", $templateId);
		$templateId 		= $templateIds[0];
		$NameTemplate 		= $templateIds[1];
		$changeTemplate 	= $templateId;
		$consultTemplate	= $templateId;
		$selectedList 		= $_REQUEST['selectedList'];
		$patientTempName	= $_REQUEST['patientTempName'];
		
		$Addressee 			= $_REQUEST['Addressee'];
		$AddresseeOther 	= $_REQUEST['AddresseeOther'];
		list($cc1) 			= explode("@@",$_REQUEST['cc1']);
		$cc1Other 			= $_REQUEST['cc1Other'];
		list($cc2) 			= explode("@@",$_REQUEST['cc2']);
		$cc2Other 			= $_REQUEST['cc2Other'];
		list($cc3) 			= explode("@@",$_REQUEST['cc3']);
		$cc3Other 			= $_REQUEST['cc3Other'];

		$hidd_AddresseeOther= $_REQUEST['hidd_AddresseeOther'];
		$hidd_cc1Other 		= $_REQUEST['hidd_cc1Other'];
		$hidd_cc2Other 		= $_REQUEST['hidd_cc2Other'];
		$hidd_cc3Other 		= $_REQUEST['hidd_cc3Other'];
		$hidd_sigProvId 	= $_REQUEST['hidd_sigProvId'];
		$hidd_sigProvExist 	= $_REQUEST['hidd_sigProvExist'];
		$hidd_TemplateTopMargin	= $_REQUEST['hiddTemplateTopMargin'];
		$hidd_TemplateLeftMargin= $_REQUEST['hiddTemplateLeftMargin'];
		$hidd_leftpanel		= $_REQUEST['leftpanel'];
		//variables
		
		//
		$rfTitle = $rfFName = $rfMName = $rfLName = $gmPCP = $patientRefTo = $coManPhy = $coManPhyId = $rfCoManFName = $rfCoManLName = $rfCoManFax = "";
		if($_REQUEST['templateId']){
			$consultTemplate = $_REQUEST['templateId'];
		}
		$mutiRefPhy=array();
		if($patientId != 0){
			$mutiRefPhy=$this->fetchConsultMultiPhy();
		}
			
		
		if($patientId != 0){
			list($rfTitle,$rfFName,$rfMName,$rfLName,$gmPCP,$rfFax,$pcpFax,$rfId,$rfPcpId,$coManPhy,$coManPhyId,$rfCoManFName,$rfCoManLName,$rfCoManFax,$rfEmail,$rfPcpEmail,$rfCoManEmail) = $objParser->getRefPcpRef($patientId); 
		}
		
		//--
		if($_REQUEST['chkPCP']){
			//$patientRefTo = $_REQUEST['chkPCP'];
			$patientRefTo	= $rfPcpId;
		}
		elseif($_REQUEST['chkRefPhy']){
			if($_REQUEST['chkRefPhy_hidd'] != "" && $_REQUEST['chkRefPhy_hidd'] > 0){
				$patientRefTo = $_REQUEST['chkRefPhy_hidd'];
			}else{
				$patientRefTo = $_REQUEST['chkRefPhy'];
			}
		}
		elseif($_REQUEST['chkCoManPhy']){
			if($_REQUEST['chkCoManPhy_hidd'] != "" && $_REQUEST['chkCoManPhy_hidd'] > 0){
				$patientRefTo = $_REQUEST['chkCoManPhy_hidd'];
			}else{
				$patientRefTo = $_REQUEST['chkCoManPhy'];
			}
		}
		elseif($_REQUEST['txtOther']){
			if($_REQUEST['hiddtxtOther'] != 0 && $_REQUEST['hiddtxtOther'] != ''){
				$patientRefTo = $_REQUEST['hiddtxtOther'];
			}else{
				$patientRefTo = $_REQUEST['txtOther'];
			}
		}
        
        if(isset($_REQUEST['hiddfirstSelected']) && $_REQUEST['hiddfirstSelected']!=''){
            $hiddfirstSelectedArr=explode('__',trim($_REQUEST['hiddfirstSelected']));
            if(isset($hiddfirstSelectedArr[0]) && isset($hiddfirstSelectedArr[1])){
                $patientRefTo = $hiddfirstSelectedArr[1];
            }
        }
		//--
		
		//--- Get Patient Info ------
		$patient_id = $patientId;
		$qry = "select id,lname,fname,mname,erx_patient_id,primary_care,primary_care_id,primary_care_phy_name,primary_care_phy_id,co_man_phy,co_man_phy_id,date_format( DOB, '%m-%d-%Y' ) AS dob 
				from patient_data where id = '".$patient_id."' ";
		$qryRes = sqlQuery($qry);
		$erx_patient_id = $qryRes['erx_patient_id'];
		$patName = $qryRes['lname'].', ';
		$patName .= $qryRes['fname'].' ';
		$patName .= $qryRes['mname'];
		$patName = ucwords(trim($patName));
		$patient_name=$qryRes['fname']." ".$qryRes['lname'];
		$patient_fname = $qryRes['fname'];
		$patient_lname = $qryRes['lname'];
		
		$pat_dob= $qryRes['dob'];
		$ref_primary_care = $qryRes['primary_care'];
		$ref_primary_care_id = $qryRes['primary_care_id'];
		$pcp_primary_care_phy_name = $qryRes['primary_care_phy_name'];
		$pcp_primary_care_phy_id = $qryRes['primary_care_phy_id'];
		$co_man_physician_name = $qryRes['co_man_phy'];
		$co_man_physician_id = $qryRes['co_man_phy_id'];
		
		if($patName[0] == ','){
			$patName = substr($patName,1);
		}
		$patName .= ' - '.$qryRes['id'];
		$ptRefPhyNme = $ptRefPhyNme = "";
		if($ref_primary_care_id != 0){
			list($refTitle,$refFName,$refMName,$refLName,$refAddress1,$refAddress2,$refCity,$refState,$refZipCode,$refPhysicianFaxArr[$ref_primary_care_id],$refPhyDirectEmailID) = $objParser->getRefPcpInfo($ref_primary_care_id);
			$ptRefPhyNme = trim($refFName.' '.$refLName.' '.$refTitle);
			$faxPhyNmeArr[$ref_primary_care_id]=$ptRefPhyNme;
		}
		if($pcp_primary_care_phy_id != 0){
			list($pcpTitle,$pcpFName,$pcpMName,$pcpLName,$pcpAddress1,$pcpAddress2,$pcpCity,$pcpState,$pcpZipCode,$refPhysicianFaxArr[$pcp_primary_care_phy_id],$pcpPhyDirectEmailID) = $objParser->getRefPcpInfo($pcp_primary_care_phy_id);
			$ptPcpPhyNme = trim($pcpFName.' '.$pcpLName.' '.$pcpTitle);
			$faxPhyNmeArr[$pcp_primary_care_phy_id]=$ptPcpPhyNme;
		}
		if($co_man_physician_id != 0){
			list($coManTitle,$coManFName,$coManMName,$coManLName,$coManAddress1,$coManAddress2,$coManCity,$coManState,$coManZipCode,$refPhysicianFaxArr[$co_man_physician_id],$coPhyDirectEmailID) = $objParser->getRefPcpInfo($co_man_physician_id);
			$ptCoManPhyNme = trim($coManFName.' '.$coManLName.' '.$coManTitle);
			$faxPhyNmeArr[$co_man_physician_id]=$ptCoManPhyNme;
		}
		
		$addresseRefToId 	= $ref_primary_care_id;
		
		//--- Get Patient Info ------
		//VALUE COMMENTED AND DONE EMPTY TO STOP REPLACE PCP NAME WITH {CC1} VARIABLE ----------
		//$cc1RefToId 		= $pcp_primary_care_phy_id;
		$cc1RefToId			= $cc2RefToId 		= $cc3RefToId = "";
		
		if($Addressee && $Addressee!="Other"){
			$addresseRefToId = $Addressee;
		}else if($Addressee=="Other" && trim($AddresseeOther)) {
			$addresseRefToId = $hidd_AddresseeOther;
			//list($addresseRefToId) = $objParser->getRefPcpId($AddresseeOther);
		}
		
		if($cc1 && $cc1!="Other"){
			$cc1RefToId = $cc1;
		}else if($cc1=="Other" && trim($cc1Other)) {
			$cc1RefToId = $hidd_cc1Other;
			//list($cc1RefToId) = $objParser->getRefPcpId($cc1Other);
		}
		
		if($cc2 && $cc2!="Other") {
			$cc2RefToId = $cc2;
		}else if($cc2=="Other" && trim($cc2Other)) {
			$cc2RefToId = $hidd_cc2Other;
			//list($cc2RefToId) = $objParser->getRefPcpId($cc2Other);
		}
		
		if($cc3 && $cc3!="Other") {
			$cc3RefToId = $cc3;
		}else if($cc3=="Other" && trim($cc3Other)) {
			$cc3RefToId = $hidd_cc3Other;
			//list($cc3RefToId) = $objParser->getRefPcpId($cc3Other);
		}
		//VALUE COMMENTED AND DONE EMPTY TO STOP REPLACE PCP NAME WITH {CC1} VARIABLE -----------
		
		// patient_consult_letter_tbl
		if(!$Seq){
			$getMasStatusQry = "SELECT * from patient_consult_letter_tbl WHERE patient_id = '".$patientId."'";
			$getMasStatusRows = sqlQuery($getMasStatusQry);			
			if($getMasStatusRows!=false){
				$PatientConsultId = $getMasStatusRows['patient_consult_id'];
				$patientConsultstatus = $getMasStatusRows['status'];
				$otherTemplateName = $getMasStatusRows['templateName'];
			}
		}
		// patient_consult_letter_tbl
		
		// GET PATIENT TEMPLATE IF EXISTS
		unset($conditionArr);		
		if($tempId){
			$patient_consult_id = $tempId;
		}else{
			$patient_consult_id = $PatientConsultId;
		}
		
		$sql = "SELECT * FROM patient_consult_letter_tbl WHERE patient_id='".$patientId."' 
								AND patient_form_id='".$formId."' 
								AND patient_consult_id='".$patient_consult_id."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$patient_consult_id = $row["patient_consult_id"];
			$templateData = $row["templateData"];
			$templateName = $row["templateName"];
			$timeCreated = $row["status"];
			$templateIdSelected = $row["templateId"];			
		}
		//--

		// GET PATIENT TEMPLATE IF EXISTS
		if(empty($consultTemplate) != true && ($templateId) == true){
			$templateId = $consultTemplate;
		}
		
		// Template Details
		$sql = "SELECT * FROM ".$table." WHERE consultLeter_id='".$templateId."'  ";
		$row = sqlQuery($sql);
		if($row!=false){
			
			$consultTemplateName = $row["consultTemplateName"];
			$consultTemplateData = $row["consultTemplateData"];
			$consultTemplateTopMargin = $row["top_margin"];
			$consultTemplateLeftMargin = $row["left_margin"];
			$consultTemplatefooter = intval($row["footer"]);
			$consultTemplateheader =intval($row["patient_header"]);
			$leftpanel_val = intval($row["leftpanel"]);			
		}
		
		if($consultTemplatefooter==1 || $consultTemplateheader==1 || $leftpanel_val==1){
			$sql = "SELECT * FROM document_panels LIMIT 0,1";
			$row = sqlQuery($sql);
			if($row!=false){
				$footer_panel	= trim($row['footer']);
				$header_panel	= trim($row['header']);
				$leftpanel_panel= trim($row['leftpanel']);			
			}
			if($consultTemplatefooter!=1)	{$footer_panel 		= '';}
			if($consultTemplateheader!=1)	{$header_panel 		= '';}
			if($leftpanel_val!=1)			{$leftpanel_panel 	= '';}
		}
		
		//Initialize 2	
		/*--REPLACING SMART TAGS (IF FOUND) WITH LINKS--*/
		$arr_smartTags = $OBJsmart_tags->get_smartTags_array();
		//Rather then using ajax to call such a simple element as a smart tag. I am writing the values for the smarttag to the id.
		//Then I will use redactor smarttag to cut up this output.
		if($arr_smartTags){
			foreach($arr_smartTags as $key=>$val){
				$smart_tag_parsing = $OBJsmart_tags->get_smartTags_array($key);
				
				$parsed = '';
				if($smart_tag_parsing && count($smart_tag_parsing)>0){				
					foreach ($smart_tag_parsing as $value) {
						$parsed .= '|'.$value;
					}
				}
				$consultTemplateData = str_ireplace("[".$val."]",'<a id="'.$parsed.'" act_id="'.$key.'" href="javascript:;" class="cls_smart_tags_link">'.$val.'</a>',$consultTemplateData);	
			}	
		}
		$consultTemplateData = str_ireplace("[Patient Referring Physician]",'<a id="aPatRefPhy" href="javascript:;" class="cls_smart_tags_link">Patient Referring Physician</a>',$consultTemplateData);	
		$consultTemplateData = str_ireplace("[Primary Care Physician]",'<a id="aPatPCP" href="javascript:;" class="cls_smart_tags_link">Primary Care Physician</a>',$consultTemplateData);	
		$consultTemplateData = str_ireplace("[Co-Managed Physician]",'<a id="aPatCoManPhy" href="javascript:;" class="cls_smart_tags_link">Co-Managed Physician</a>',$consultTemplateData);
		
		/*Commented due to not replacing Addressee variable value in consult on template load, if addresse not selected from above dropdown
		if(!$_REQUEST['Addressee']){
		    $consultTemplateData = str_ireplace("{ADDRESSEE}", "", $consultTemplateData);
		}*/
		
		//start get signature id for physician
		$sigProvId=0;
		$sigProvExist=0;
		$sigIdQry = "SELECT chart_master_table.finalize as sigFinalizeStatus, chart_master_table.finalizerId as sigFinalProvId, 
						chart_master_table.providerId as sigProvId FROM chart_master_table 
					LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id 
					WHERE chart_master_table.id='".$formId."' AND chart_master_table.patient_id ='".$patientId."' ";
		$row = sqlQuery($sigIdQry);	
		if($row!=false){
			$sigFinalizeStatus = $row["sigFinalizeStatus"];
			$sigFinalProvId = $row["sigFinalProvId"];
			$sigProvId = $row["sigProvId"];
			if($sigFinalizeStatus=="1"){
				$sigProvId=$sigFinalProvId;
			}
			$sigProvExist='yes';
		}	
		
		//end get signature id for physician
		
		$consultTemplateData = $objParser->getDataParsed($consultTemplateData,$patientId,$formId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
		
		//PATIENT FUTURE APPOINTMENT DETAILS WORK STARTS HERE
		$future_appt = $apptInfoArr = "";
		$ptFutuerApptDetails =strpos($consultTemplateData,'{APPT_FUTURE}');
		$ptNextApptDate =strpos($consultTemplateData,'{PATIENT_NEXT_APPOINTMENT_DATE}');
		$ptNextAppttime =strpos($consultTemplateData,'{PATIENT_NEXT_APPOINTMENT_TIME}');
		$ptNextApptProvider =strpos($consultTemplateData,'{PATIENT_NEXT_APPOINTMENT_PROVIDER}');
		if($ptFutuerApptDetails!='' || $ptNextApptDate!='' || $ptNextAppttime!='' || $ptNextApptProvider!='')
		{
		  
			include_once($GLOBALS['fileroot']."/library/classes/Functions.php");
			$oPtSch = new ManageData($this->pid);
			$future_appt = $oPtSch->__getApptFuture($this->pid);
		
			$apptInfoArr = $oPtSch->__getApptInfo($this->pid,'','','','',1);
			
			$consultTemplateData = str_ireplace("{APPT_FUTURE}",$future_appt,$consultTemplateData);
			//===========FACILITY ADDRESS VARIABLE CONCATENATION==================
			if($apptInfoArr[10] && $apptInfoArr[11])
			{
				$facilityAddress .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
			}
			else if($apptInfoArr[10])
			{
				$facilityAddress .= $apptInfoArr[10].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];	
			}
			else if($apptInfoArr[11])
			{
				$facilityAddress .= $apptInfoArr[11].',&nbsp;'.$apptInfoArr[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr[3];
			}
			//============10 ==> STREET/ 11 ==> CITY/ 12 ==> STATE/ 13-14 ==> ZIP CODE - EXT/ 3 ==> PHONE===
			// NEW APPOINTMENT VARIABLES REPLACEMENT WORK
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr[0],$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr[8],$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr[5],$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr[4],$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr[16],$consultTemplateData);
			$consultTemplateData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr[17],$consultTemplateData);
		   //================================END===================================
		
		}
		
		$consultTemplateData = str_ireplace("+ve ",'Positive',$consultTemplateData);
		$consultTemplateData = str_ireplace("-ve ",'Negative',$consultTemplateData);
		$consultTemplateData = str_ireplace(" +ve",'Positive',$consultTemplateData);
		$consultTemplateData = str_ireplace(" -ve",'Negative',$consultTemplateData);
		$consultTemplateData = str_ireplace(" +ve ",'Positive',$consultTemplateData);
		$consultTemplateData = str_ireplace(" -ve ",'Negative',$consultTemplateData);
		/********PARSING HEADER, FOOTER AND LEFT PANEL***********/
		$header_panel = $objParser->getDataParsed($header_panel,$patientId,$formId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
		$header_panel = str_ireplace("{APPT_FUTURE}",$future_appt,$header_panel);
		$header_panel = str_ireplace("+ve",'Positive',$header_panel);
		$header_panel = str_ireplace("-ve",'Negative',$header_panel);
		
		$footer_panel = $objParser->getDataParsed($footer_panel,$patientId,$formId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
		$footer_panel = str_ireplace("{APPT_FUTURE}",$future_appt,$footer_panel);
		$footer_panel = str_ireplace("+ve",'Positive',$footer_panel);
		$footer_panel = str_ireplace("-ve",'Negative',$footer_panel);
		
		$leftpanel_panel = $objParser->getDataParsed($leftpanel_panel,$patientId,$formId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
		$leftpanel_panel = str_ireplace("{APPT_FUTURE}",$future_appt,$leftpanel_panel);
		$leftpanel_panel = str_ireplace("+ve",'Positive',$leftpanel_panel);
		$leftpanel_panel = str_ireplace("-ve",'Negative',$leftpanel_panel);
		/*************PENEL PARSING END*************************/
		
		//Save Template For Patient
		$saveBtn = $_REQUEST['saveBtn'];
		$saveBtn = ($_SERVER['REQUEST_METHOD'] == "POST")?1:'';
		
		if($saveBtn){
			$faxStatus="0";
			$getFaxNo="";
			$getPhyIdAndFax="";
			$faxStatus=$_REQUEST['faxStatus'];
			$hidCoverLetter="";
			$getPhyId="";
			$phyIdFax="";
			if($faxStatus=="1"){
				$getPhyFaxNo=$_REQUEST['send_fax_number'];
				$getPhyId=$_REQUEST['hiddselectReferringPhy'];
				$getPhyFax=$_REQUEST['send_fax_number'];		
				$hidCoverLetter=urldecode($_REQUEST['hidTextAreaFax']);	
				//$phyIdFax=split("@@",$getPhyIdAndFax);
				//$getPhyId=$_REQUEST['hiddselectReferringPhy'];
			}
			// CHECK UPDATE OR ADD
			if($tempId){ $templateId = $tempId; }
			
			$sql = "SELECT * FROM patient_consult_letter_tbl WHERE patient_consult_id = '".$templateId."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				$templateName = $getConsultTempRows['templateName'];
				if(!$tempId) $NameTemplate = $consultTemplateName;				
			}
			$FCKeditorData=$hidCoverLetter;
			$consultTemplateFooter="";
			
			//$FCKeditorData.=$header_data;
			if($_REQUEST['footer_panel'] && $_REQUEST['footer_panel']!=''){
				$FCKeditorData.= "<page_footer>".$_REQUEST['footer_panel']."</page_footer>";
			}
			if($_REQUEST['header_panel'] && $_REQUEST['header_panel']!='' && $_REQUEST['leftpanel_panel'] && $_REQUEST['leftpanel_panel']!='' ){
				
				$FCKeditorData.= "<page_header>".$_REQUEST['header_panel'].$_REQUEST['leftpanel_panel']."</page_header>";
				
			}else if($_REQUEST['header_panel'] && $_REQUEST['header_panel']!=''){
				
				$FCKeditorData.= "<page_header>".$_REQUEST['header_panel']."</page_header>";
				
			}else if($_REQUEST['leftpanel_panel'] && $_REQUEST['leftpanel_panel']!=''){
				
				$FCKeditorData.=  "<page_header>".$_REQUEST['leftpanel_panel']."</page_header>";
				
			}
			if($_REQUEST['FCKeditor1'] && $_REQUEST['FCKeditor1']!=''){
				//Filtering Anchor tag out
				$dom = new DomDocument();
				$dom->loadHTML($_REQUEST['FCKeditor1']);
				$output = array();
				foreach ($dom->getElementsByTagName('a') as $item) {
					$anchorStr = $dom->saveHTML($item);
					$anchorVal = $item->nodeValue;
					
					$output[$anchorVal] = $anchorStr;
				}

				if(count($output) > 0){
					$str_value = array_keys($output);
					$str_replace = array_values($output);
					$_REQUEST['FCKeditor1'] = str_ireplace($str_replace, $str_value, $_REQUEST['FCKeditor1']);
				}
				
				$FCKeditorData.= $_REQUEST['FCKeditor1'];
			}
			
			//
			$path = explode('\\',dirname(__FILE__));
			$replaceText = "/$web_RootDirectoryName/interface/common/new_html2pdf/";
			$FCKeditorData = str_replace($replaceText,'',$FCKeditorData);
			unset($arrayRecord);
			$timeCreated = $timeCreated + 1;
			$arrayRecord['patient_id'] = $patientId;
			$arrayRecord['patient_form_id'] = $formId;
			$arrayRecord['templateData'] = stripslashes($FCKeditorData);
			$arrayRecord['fax_status']=$faxStatus;
			if($faxStatus==1){
				$arrayRecord['report_sent_date']=date("Y-m-d");
			}
			$arr_find=array("-","(",")"," ");
			$arr_repl=array("","","","");
			$arrayRecord['preffered_reff_fax']=str_replace($arr_find,$arr_repl,$_REQUEST['preffered_reff_fax']);
			$arrayRecord['fax_ref_phy_id']=$getPhyId;
			$arrayRecord['fax_number']=$getPhyFaxNo;
			$arrayRecord['cc1_ref_phy_id']=$cc1RefToId;
			$arrayRecord['cc2_ref_phy_id']=$cc2RefToId;
			$arrayRecord['cc3_ref_phy_id']=$cc3RefToId;
			$arrayRecord['date'] = $today;	
			$arrayRecord['templateId'] = $templateId;
			$arrayRecord['operator_id'] = $_SESSION['authId'];
			$arrayRecord['top_margin'] =$hidd_TemplateTopMargin;
			$arrayRecord['left_margin'] =$hidd_TemplateLeftMargin;
			if($_REQUEST['chkPCP']){		
				$arrayRecord['patient_consult_letter_to'] = $_REQUEST['chkPCP'];
			}
			elseif($_REQUEST['chkRefPhy']){
				$arrayRecord['patient_consult_letter_to'] = $_REQUEST['chkRefPhy'];
			}
			elseif($_REQUEST['chkCoManPhy']){
				$arrayRecord['patient_consult_letter_to'] = $_REQUEST['chkCoManPhy'];
			}
			elseif($_REQUEST['txtOther']){
				$strOtherText = $phyLName = $phyFName = $phyMName = "";
				$arrOtherText = $arrProviderFName = array();		
				$strOtherText = trim($_REQUEST['txtOther']);	
				$arrReplace = array("MD","Dr","Dr.","OD",".");
				$strOtherText = str_replace($arrReplace,'',$strOtherText);			
				$arrOtherText = explode(',',$strOtherText);
				$phyLName = trim($arrOtherText[0]);
				$phyFName = trim($arrOtherText[1]);
				$arrProviderFName = explode(" ",$phyFName);
				$phyFName = trim($arrProviderFName[0]);
				$phyMName = trim($arrProviderFName[1]);
				$patient_consult_letter_to_id = '';
				
				if( $_REQUEST['hiddselectReferringPhy'] == ""){
					$sql = "select physician_Reffer_id from refferphysician where FirstName = '".sqlEscStr($phyFName)."' and LastName  = '".sqlEscStr($phyLName)."' and MiddleName = '".sqlEscStr($phyMName)."'";
					$rez = sqlStatement($sql);
					$num_row = imw_num_rows($rez);
					if($num_row==0){
						$arrayRecord['patient_consult_letter_to_other'] = $_REQUEST['txtOther'];
					}else{
						$row = sqlFetchArray($rez);
						$arrayRecord['patient_consult_letter_to'] = $_REQUEST['txtOther'];						
						$patient_consult_letter_to_id = $row['physician_Reffer_id'];
					}
					
				}else{
					$patient_consult_letter_to_id = $_REQUEST['hiddselectReferringPhy'];
				}
			}
			
			$arrayRecord['status'] = 0;
			$arrayRecord['provider_signature_id'] 	= $hidd_sigProvId;
			$arrayRecord['provider_signature_exist']= $hidd_sigProvExist;
			$arrayRecord['leftpanel']= $hidd_leftpanel;
			
			if($tempId){
				//update
				UpdateRecords($patient_consult_id, 'patient_consult_id',  $arrayRecord, 'patient_consult_letter_tbl', 'false' );
				if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
					$tmpSESSarr = unserialize($_SESSION["test2edit"]);
					$tmpSESSarr['consult_letter'] = '';
					unset($tmpSESSarr['consult_letter']);
					$_SESSION["test2edit"] = serialize($tmpSESSarr);
				}
			}else{
				if($templateId == 'Other') $arrayRecord['templateName'] = $patientTempName;
				else $arrayRecord['templateName'] = $NameTemplate;
				
				if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
					$tmpSESSarr = unserialize($_SESSION["test2edit"]);
					$patient_consult_id = $tmpSESSarr['consult_letter'];
					if(trim($patient_consult_id)){
						UpdateRecords($patient_consult_id, 'patient_consult_id',  $arrayRecord, 'patient_consult_letter_tbl', 'false' );
						$tmpSESSarr['consult_letter'] = '';
						unset($tmpSESSarr['consult_letter']);
						$_SESSION["test2edit"] = serialize($tmpSESSarr);
					}else{
						$patient_consult_id = AddRecords($arrayRecord, 'patient_consult_letter_tbl', 'false');
					}
				}else{				
					$patient_consult_id = AddRecords($arrayRecord, 'patient_consult_letter_tbl', 'false');
					//BELOW ID USED TO SHOW THE DATA INTO POP UP ONCLICK OF SAVE AND PRINT BUTTON IN CONSULT LETTER	
					$ptConsultIdForSavePrint=$patient_consult_id; //THIS IS AUTO INCREMENTED VALUE ON INSERTION OF CONSULT TEMPLATE
					
					if(!empty($fax_log_id)){
						$updatePtConsultId	= "UPDATE `send_fax_log_tbl` SET patient_consult_id='".$patient_consult_id."' WHERE id IN ('".$fax_log_id."') AND patient_id='".$patientId."'";
						$rowPtConsultId		= imw_query($updatePtConsultId);	
					}
				}
			}
			
			if($patient_consult_id && $_REQUEST['communication']){
				$template_data_val=$arrayRecord['templateData'];
				
				$dateDt=$arrayRecord['date'];
				
				$datDT= New DateTime($dateDt);
				$date_create=$datDT->format('YmdHis');
				
				$communication_text='';
				if(stristr($template_data_val,'Macular') || stristr($template_data_val,'Edema')){
					$communication_text='Macular Edema Findings Present';
				}else if(stristr($template_data_val,'Retinopathy')){
					$communication_text='Level of Severity of Retinopathy Findings';
				}
				if($communication_text){
						$XML_values='<entry>
								<act classCode="ACT" moodCode="EVN">
									<!-- Communication from provider to provider -->
									<templateId root="2.16.840.1.113883.10.20.24.3.4" extension="2016-02-01"/>
									<id root="1.3.6.1.4.1.115" extension="5a23957bcde4a3001848b675"/>
									<code code="312904009" codeSystem="2.16.840.1.113883.6.96" sdtcvalueSet="2.16.840.1.113883.3.526.3.1283">
										<originalText>Communication From Provider to Provider: '.$communication_text.'</originalText>
									</code>
									<text>Communication From Provider to Provider: '.$communication_text.'</text>
									<statusCode code="completed"/>

									<effectiveTime>
										<low value="'.$date_create.'+0000"/>
										<high value="'.$date_create.'+0000"/>
									</effectiveTime>

									<participant typeCode="AUT">
										<participantRole classCode="ASSIGNED">
											<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
										</participantRole>
									</participant>

									<participant typeCode="IRCP">
										<participantRole classCode="ASSIGNED">
											<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
										</participantRole>
									</participant>
								</act>
							</entry>';
					$qry_insert_communication="INSERT INTO communication set patient_id='".$patientId."',form_id='".$formId."',description='".$XML_values."'";
					$res_insert_communication=imw_query($qry_insert_communication);		
				}
				
			}
			/*-----SAVING HOLD SIGNATURE INFO-----*/
			 $OBJhold_sign->section_col = "consult_id";
			 $OBJhold_sign->section_col_value = $patient_consult_id;
			 $OBJhold_sign->save_hold_sign();
			 /*-----end of HOLD SIGNATURE INFO-----------*/
			 
			 /*------Direct Msg-----*/
			if(intval($_REQUEST['hid_send_direct_bool'])==1){
				$multiAddArr = $multiAddRef = array();
				if(isset($_REQUEST['hidden_direct_email_id']) && empty($_REQUEST['hidden_direct_email_id']) == false){
					$multiAddRef = json_decode($_REQUEST['hidden_direct_email_id']);

					if($multiAddRef){
						foreach($multiAddRef as $refId => $addId){
							//Get direct address
							$directSql = imw_query(" SELECT email FROM ref_multi_direct_mail WHERE id = ".$addId." ");
							if($directSql && imw_num_rows($directSql) > 0){
								$rowDirect = imw_fetch_assoc($directSql);
								$directEmailMulti = (isset($rowDirect['email']) && empty($rowDirect['email']) == false) ? $rowDirect['email'] : '';
								if($directEmailMulti) $multiAddArr[$refId] = $directEmailMulti;
							}
						}
					}
				}

				//$all_ref_phys = array(intval($addresseRefToId),intval($cc1RefToId),intval($cc2RefToId),intval($cc3RefToId),intval($patient_consult_letter_to_id),intval($patientRefTo));
				 //$str_all_ref_phys 	= implode(',',array_unique($all_ref_phys));
				 $Addressee 			= ($_REQUEST['Addressee'] != "Other" && $_REQUEST['Addressee'] != '')?$_REQUEST['Addressee']:"0";
				 $hidd_AddresseeOther 	= ($_REQUEST['Addressee'] == "Other")?$_REQUEST['hidd_AddresseeOther']:"0";
				 $chkPCP 				= ($_REQUEST['chkPCP'])?$_REQUEST['chkPCP_hidd']:"0";
				 $chkRefPhy 			= ($_REQUEST['chkRefPhy'])?$_REQUEST['chkRefPhy_hidd']:"0";
				 $chkCoManPhy 			= ($_REQUEST['chkCoManPhy'])?$_REQUEST['chkCoManPhy_hidd']:"0";
				 $cbkOther 				= ($_REQUEST['cbkOther'])?$_REQUEST['hiddtxtOther']:"0";
				 $cc1	 				= ($_REQUEST['cc1'] != "Other" && $_REQUEST['cc1'] != "")?$_REQUEST['cc1']:"0";
				 $hidd_cc1Other 		= ($_REQUEST['cc1'] == "Other")?$_REQUEST['hidd_cc1Other']:"0";
				 $cc2 					= ($_REQUEST['cc2'] != "Other" && $_REQUEST['cc2'] != "")?$_REQUEST['cc2']:"0";
				 $hidd_cc2Other 		= ($_REQUEST['cc2'] == "Other")?$_REQUEST['hidd_cc2Other']:"0";
				 $cc3 					= ($_REQUEST['cc3'] != "Other" && $_REQUEST['cc3'] != "")?$_REQUEST['cc3']:"0";
				 $hidd_cc3Other 		= ($_REQUEST['cc3'] == "Other")?$_REQUEST['hidd_cc3Other']:"0";
				 
				 //$all_ref_phys = array(intval($addresseRefToId),intval($cc1RefToId),intval($cc2RefToId),intval($cc3RefToId),intval($patient_consult_letter_to_id),intval($patientRefTo));
				 $all_ref_phys = array(	intval($Addressee),
										intval($hidd_AddresseeOther),
										intval($chkPCP),
										intval($chkRefPhy),
										intval($chkCoManPhy),
										intval($cbkOther),
										intval($cc1),
										intval($hidd_cc1Other),
										intval($cc2),
										intval($hidd_cc2Other),
										intval($cc3),
										intval($hidd_cc3Other)
									);
				$all_ref_phys		= array_unique($all_ref_phys);	
				$str_all_ref_phys 	= implode(',',$all_ref_phys);

				$sql = 'SELECT physician_Reffer_id,direct_email FROM refferphysician WHERE physician_Reffer_id IN ('.$str_all_ref_phys.') AND physician_Reffer_id>0';
				$get_Ref_emails_res = sqlStatement($sql);
				$arrDirectEmail = $arrDirectEmailNot = array();
                $error_msg = array();
				if($get_Ref_emails_res && imw_num_rows($get_Ref_emails_res)>0){
					
					if($arrDirectCre['email'] != "" && $arrDirectCre['email_password'] != ""){
                        

                    try{
						if (is_updox('direct')) {
                            include_once(dirname(__FILE__) . '/../../updox/updoxDirect.php');
                            $objDirect = new updoxDirect();
                            $sendvia = "updox_direct";
                        } else {
                            $objDirect = new Direct($arrDirectCre['email'], $arrDirectCre['email_password']);
                            $sendvia = "direct";
                        }
						$objDirect->arrMail['attachment'] 	= array();	
						$templateContentToPDF 				= $arrayRecord['templateData'];
						
						if($templateContentToPDF != ''){
							$regpattern						= '|<a class="cls_smart_tags_link" href=(.*) id=(.*)>(.*)</a>|U';  
							$templateContentToPDF 			= preg_replace($regpattern, "\\3", $templateContentToPDF);
							$templateContentToPDF 			= str_ireplace('<od br=""', '&lt;od', $templateContentToPDF);
							$templateContentToPDF 			= str_ireplace("<od br=''", "&lt;od", $templateContentToPDF);
							$templateContentToPDF 			= str_ireplace('</od>', '', $templateContentToPDF);
							$templateContentToPDF 			= str_ireplace('vision="">', 'vision', $templateContentToPDF);
							$consultTemplateDataPage 		= '';
							$consultTemplateDataPage 		='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="7">'.$consultTemplateData.'</page>';
							$consultTemplateDataPage 		= str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consultTemplateDataPage);
							$consultTemplateDataPage 		= str_ireplace("/".$web_RootDirectoryName."/interface/common/new_html2pdf/","",$consultTemplateDataPage);
							$consultTemplateDataPage 		= str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$consultTemplateDataPage);
							/*
							$pdfDirectHTML_path 			= dirname(__FILE__).'/../common/new_html2pdf/pdffileDirect'.$_SESSION['patient'].'.html';
							$fp 							= fopen($pdfDirectHTML_path,'w');
							$writeData 						= fwrite($fp,$consultTemplateDataPage);
							fclose($fp);
							*/
							//--
							$fp = '/tmp/pdffileConsultLetter.html';
							$resp = $oSaveFile->cr_file($fp,$consultTemplateDataPage);
							//$resp_w = $oSaveFile->getFilePath($resp, "w");
							$pdfDirectHTML_path=$resp;
							//--
							
							$dir 							= explode('/',$_SERVER['HTTP_REFERER']);
							$httpPro 						= $dir[0];
							//$myHTTPAddress 					= $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/createPdf.php';
							//$myHTTPAddress 					= $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/createPdf.php';
							$myHTTPAddress 					= $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
							$pdfDirect_name 				= 'pdffileConsultLetter';
							$pdfDirect_path 				= $resp ; //dirname(__FILE__).'/../common/new_html2pdf/'.$pdfDirect_name.'.pdf';
                            $pdf_path                       = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/UserId_".$_SESSION['authId']."/tmp/".$pdfDirect_name;
							$urlPdfFile						= $myHTTPAddress."?setIgnoreAuth=true&op=p&saveOption=fax&file_location=".$pdfDirectHTML_path."&pdf_name=".$pdf_path;
							
							$curNew 						= curl_init();
							curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
							curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
							curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
							$data = curl_exec($curNew);
							curl_close($curNew);
							
							$objDirect->arrMail['attachment'][] = array(
													"complete_path"=>$pdf_path.'.pdf',
													"mime"=>'application/pdf',
													"file_name"=>$pdfDirect_name.'.pdf',
													"size"=>filesize($pdf_path.'.pdf'),
													"file_path"=>$pdf_path.'.pdf'
													);
						}

						//----BEGIN CREATING CCDA----
						if($_REQUEST['ccda'] == 1){
							
							$arrCCDA = array();
							$arrCCDA['arrData']['pat_id'] 	= $patientId;
							$arrCCDA['arrData']['form_id'] 	= $formId;
							$arrCCDA['arrData']['pat_name'] = 'doe,ccda';
							$_REQUEST['create_type'] 		= "attachment";
							$_REQUEST['ccdDocumentOptions'] = $_REQUEST['ccdDocumentOptions'];
							$_REQUEST['arrData'] 			= json_encode($arrCCDA);
							$_REQUEST['callFrom'] 			= "consult_letter";
							if(file_exists($GLOBALS['incdir']."/reports/ccd/create_ccda_r2_xml.php")){
								include_once($GLOBALS['incdir']."/reports/ccd/create_ccda_r2_xml.php");
								$sql 	= "SELECT * FROM log_ccda_creation WHERE id = '".$_REQUEST['ccda_log_id']."' AND type = 1";
								$res 	= sqlStatement($sql);
								if(imw_num_rows($res)>0){
									$row 	= sqlFetchArray($res);
									$objDirect->arrMail['attachment'][] = array(
																"complete_path"=>data_path().'users'.$row['file_path'],
																"mime"=>$row['mime'],
																"file_name"=>$row['file_name'],
																"size"=>$row['size'],
																"file_path"=>$row['file_path']
																);
									$objDirect->arrMail['attachment'][] = array(
																		"complete_path"=>$GLOBALS['incdir']."/reports/ccd/CDA.xsl",
																		"mime"=>"text/plain",
																		"file_name"=>"CDA.xsl",
																		"size"=>filesize($GLOBALS['incdir']."/reports/ccd/CDA.xsl"),
																		"file_path"=>$GLOBALS['incdir']."/reports/ccd/CDA.xsl"
																		);
								}
							}							
						}
						//----END CREATING CCDA------
						
						//----BEGIN CREATING ZIP FOR ATTACHMENT
						$zipPathDB = $zipPath = '';
						if(count($objDirect->arrMail['attachment']) > 0){
							$sid 		= session_id();
							$files 		= $objDirect->arrMail['attachment'];
							$zipname 	= 'CCDA-'.time().'.zip';
							$zipPath 	= $up_dir_usr_pth."/UserId_".$_SESSION['authId']."/mails/".$zipname;
                            if(is_updox('direct')) {
                                $zipPath = $objDirect->save_directory."/".$zipname;
                            }
							
							$zipPathDB 	= "/UserId_".$_SESSION['authId']."/mails/".$zipname;
							$zip		= new ZipArchive;
							$zip->open($zipPath, ZipArchive::CREATE);
							foreach ($files as $file) {
							  $zip->addFile($file['complete_path'],$file['file_name']);
							}
							$zip->close();
						}
						@unlink($pdfDirectHTML_path);  //---UNLINK HTML CREATED FOR PDF
						while($get_Ref_emails_rs = imw_fetch_assoc($get_Ref_emails_res)){
							//replace current email with multiple direct email if exists
							$refIdDirect = $get_Ref_emails_rs['physician_Reffer_id'];
							if($refIdDirect && empty($refIdDirect) == false){
								if($multiAddArr[$refIdDirect] && empty($multiAddArr[$refIdDirect]) == false){
									$get_Ref_emails_rs['direct_email'] = $multiAddArr[$refIdDirect];
								}
							}

							if($get_Ref_emails_rs['direct_email'] == ""){
								$arrDirectEmailNot[]			= $OBJCommonFunction->getRefPhyName($get_Ref_emails_rs['physician_Reffer_id']);	
								continue;
							}
							
							$ref_phy_direct_email 				= $get_Ref_emails_rs['direct_email'];
							$objDirect->arrMail['to_email'] 	= $ref_phy_direct_email;
							$arrDirectEmail[]					= $OBJCommonFunction->getRefPhyName($get_Ref_emails_rs['physician_Reffer_id']);
							$objDirect->arrMail['from_email'] 	= $arrDirectCre['email'];
							$objDirect->arrMail['subject'] 		= ($arrayRecord['templateName']!="")?$arrayRecord['templateName']:"CCDA for patient".$patName;
							$objDirect->arrMail['body'] 		= 'Patient consult letter and CCDA attached.';
							$objDirect->arrMail['attachment'] 	= array();
							$objDirect->arrMail['attachment'][] = array(
																	"complete_path"=>$zipPath,
																	"mime"=>'application/zip',
																	"file_name"=>$zipname,
																	"size"=>filesize($zipPath),
																	"file_path"=>$zipPathDB
																	);
                            $MID_arr = $objDirect->sendMail();
                            if($MID_arr['status']=='failed'){
                                throw new Exception($MID_arr['statusCode'].'--->'.$MID_arr['message']);
                            }elseif($MID_arr['data']->messageId>0){
                                $MID=$MID_arr['data']->messageId;
                            }else{
                                $MID=$MID_arr;
                            }
							if($MID != "" && $MID>0){
								$folder_type = "3";
							}else{
								$folder_type = "2";	
							}
							if(!$MID || $MID<=0){
								$arrDirectEmailNot[]			= $OBJCommonFunction->getRefPhyName($get_Ref_emails_rs['physician_Reffer_id']);	
							}

							if($folder_type = "3"){
								$email_status=($MID_arr['status']=='failed') ? 'failed': 'sent';
								
								$sql_ins = "INSERT INTO direct_messages SET 
										to_email 	= '".$objDirect->arrMail['to_email']."',
										from_email 	= '".$objDirect->arrMail['from_email']."',
										subject 	= '".$objDirect->arrMail['subject']."',
										message 	= '".$objDirect->arrMail['body']."',
										folder_type = '".$folder_type."',
										MID 		= '".$MID."',
										del_status 	= 0,
										imedic_user_id = '".$_SESSION['authId']."',
										local_datetime = '".date('Y-m-d H:i:s')."',
										email_status = '".$email_status."'
										";
								imw_query($sql_ins);	
								$direct_message_id = imw_insert_id();
								if(isset($objDirect->arrMail['attachment']) && count($objDirect->arrMail['attachment'])>0 && $direct_message_id>0){
									for($M=0;$M<count($objDirect->arrMail['attachment']);$M++){
										$complete_path 	= $objDirect->arrMail['attachment'][$M]['file_path'];
										$file_name 		= $objDirect->arrMail['attachment'][$M]['file_name'];
										$mime 			= $objDirect->arrMail['attachment'][$M]['mime'];
										$size 			= $objDirect->arrMail['attachment'][$M]['size'];
										if($file_name != ""){
											$sql_ins = "INSERT INTO direct_messages_attachment SET 
														direct_message_id 	= '".$direct_message_id."',
														file_name 		  	= '".$file_name."',
														size 				= '".$size."',
														mime 				= '".$mime."',
														complete_path 		= '".imw_real_escape_string($complete_path)."',
														patient_id 			= '".$patientId."',
														form_id 			= '".$formId."'
														";
											imw_query($sql_ins);	
										}
									}
								}
							}
						}
                        
                    } catch(Exception $e){
                        if($e->getMessage()){
                            if(strpos($e->getMessage(),"--->")){
                                $msg = explode('--->',$e->getMessage());
                                $error_msg[] = $msg[1];
					}
                        }
                    }

					}
				} //
				
				$flg_after_save_op=1; //				
				
			}
			/*------end of Direct Msg-----*/
			$flg_after_save_op=2; //
		}
		// End save ---------------
		if(isset($error_msg) && count($error_msg) > 0){
            $flg_after_save_op=1; //	
        }
		//--
		/*
		//start code to get fax cover letter
		$qryFaxCoverLetter="select consultTemplateData from ".$table." where consultTemplateType='fax_cover_letter'";
		$resFaxCoverLetter=imw_query($qryFaxCoverLetter)or die(imw_error());
		$faxCoverLetter="";
		$getFaxCoverLetter="";
		if(imw_num_rows($resFaxCoverLetter)>0){
			$rowFaxCoverLetter=imw_fetch_assoc($resFaxCoverLetter);
			$faxCoverLetterData=($rowFaxCoverLetter['consultTemplateData']);
			$faxCoverLetter=$objParser->getDataParsed($faxCoverLetterData,$patientId,$formId,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
			$faxCoverLetter = str_ireplace("+ve",'Positive',$faxCoverLetter);
			$faxCoverLetter = str_ireplace("-ve",'Negative',$faxCoverLetter);
			$faxCoverLetter.="<page></page>";
			$faxCoverLetter=urlencode($faxCoverLetter);
			//echo "<div id='divCoverLetterId' style='border:1px solid #000000'>".$faxCoverLetter."</div>";die();
		}
		*/
		//end code to get fax cover letter
		//GET TRANSITION OF CARE PHYSICIAN NAME
		$transitionPhyName = "";
		//echo $_SESSION['patient'] .'<<>>'.$formId;
		if($_SESSION['patient'] && $formId){
			$qry = "SELECT `doctorName_id`, `doctor_name` FROM `chart_assessment_plans` WHERE patient_id='".$_SESSION['patient']."' AND form_id='".
			$formId."'";
			$res = imw_exec($qry);
			if(!empty($res)){
				$transitionPhyId = $res['doctorName_id'];
				$transitionPhyName = $res['doctor_name'];
			}
		}
		
		include($GLOBALS['fileroot']."/interface/chart_notes/view/consult_letter.php");		
	
	}//end function -----------------
	
	function sendfax(){
		
		header("Cache-control: private, no-cache"); 
		header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
		header("Pragma: no-cache");
		//include_once("../globals.php");
		
		//================UPDOX FAX WORKS STARTS HERE======================
		$faxname		= $_REQUEST['txtFaxname'];
		$faxnumber		= $_REQUEST['txtFaxNo'];
		
		$faxnameCc1		= $_REQUEST['txtFaxNameCc1'];
		$faxnumberCc1	= $_REQUEST['txtFaxNoCc1'];
		
		$faxnameCc2		= $_REQUEST['txtFaxNameCc2'];
		$faxnumberCc2	= $_REQUEST['txtFaxNoCc2'];

		$faxnameCc3		= $_REQUEST['txtFaxNameCc3'];
		$faxnumberCc3	= $_REQUEST['txtFaxNoCc3'];
		
		$patientConsultId  = trim($_REQUEST['pat_temp_id']); //PATIENT CONSULT ID
		$ref_phy_id_fax= $_REQUEST['ref_phy_id']; //REF. PHY. ID
		
		$faxnumber	= preg_replace('/[^0-9+]/', "", $faxnumber);
		$faxnumberCc1	= preg_replace('/[^0-9+]/', "", $faxnumberCc1);
		$faxnumberCc2	= preg_replace('/[^0-9+]/', "", $faxnumberCc2);
		$faxnumberCc3	= preg_replace('/[^0-9+]/', "", $faxnumberCc3);
		
		$txtFaxPdfName  = $_REQUEST['txtFaxPdfName'];//-	
		$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/"; 
		$setNameFaxPDF	= $_REQUEST['pdf_name'];
		if(empty($setNameFaxPDF)&&!empty($txtFaxPdfName)){ $setNameFaxPDF = $txtFaxPdfName;  }
		
		$filename= $setNameFaxPDF.'.pdf';		
		
		$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
		//copy_file_new('../common/new_html2pdf', '../main/uploaddir/PatientId_'.$_SESSION['patient'].'/fax_log', $setNameFaxPDF.'.pdf', $log_file_name);
		
		if(file_exists($pdfPath.$filename)){ // copy to patient folder
			$oSaveFile =  new SaveFile($this->pid);
			$up_dir = $oSaveFile->getUploadDirPath();
			$oSaveFile->copy_file_p2p($pdfPath, $up_dir.'/PatientId_'.$this->pid.'/fax_log', $setNameFaxPDF.'.pdf', $log_file_name);
			//$filename=	$up_dir.'/PatientId_'.$this->pid.'/fax_log'.$log_file_name; //
		}else{
			
		}

		
		
		$responseData = array('primary'=>array(), 'cc1'=>array(), 'cc2'=>array(), 'cc3'=>array());
		
		if( is_updox('fax') ){
			$pdfContent = base64_encode(file_get_contents($pdfPath.$filename));
		
			//=================UPDOX FAX WORKS STARTS HERE======================
			include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //UPDOX LIBRAY FILE
			$updox = new updoxFax();  //UPDOX OBJECT
			

			/*
			 * Send Fax using updox bulk fax functions.
			 * Only if Recepients fax numbers sent in an array.
			*/
			$bulkFaxArr = array();
			if( array_key_exists('ccArr', $_REQUEST) )
			{
				/*Reset Array Indexes for bulk fax sending*/
				$ccArrNm = array();
				if(isset($_REQUEST['ccArr']) && empty($_REQUEST['ccArr']) == false) $ccArrNm = array_keys($_REQUEST['ccArr']);

				$_REQUEST['ccArr'] = array_values($_REQUEST['ccArr']);

				/*Filter blank values*/
				$faxNumbers = array_filter($_REQUEST['ccArr']);

				/*Remove non numbric characters from the fax numbers provided*/
				array_walk($faxNumbers, function(&$value){
					$value = preg_replace('/[^0-9+]/', "", $value);
				});

				/*Pocess only if non blank entries exists in recepients list*/
				if( count($faxNumbers) > 0 )
				{
					foreach($faxNumbers as $arrKey => $faxNumber){
						//Fetching Phy. name
						$faxNm = $resp = $faxType = "";
						if($ccArrNm[$arrKey]){
							$tmpNm = $ccArrNm[$arrKey];
							if($tmpNm == 'refPhy'){
								$tmpNm = 'txtFaxname';
								$faxType = 'primary';
							}

							if(empty($faxType)){
								$tmpName = str_ireplace('txtFaxName', '', $tmpNm);
								if(empty($tmpName) == false) $faxType = $tmpName;
							}
							
							if($_REQUEST[$tmpNm]) $faxNm = trim($_REQUEST[$tmpNm]);
                            if($faxNm=='' && ($_REQUEST['tempFaxNameCc'.$arrKey]!='') ){ $faxNm = trim($_REQUEST['tempFaxNameCc'.$arrKey]);}
						}
						//pre($faxNm);
						$resp = $updox->sendFax($faxNm, $faxNumber, $pdfContent);
						if($resp['status']=='success'){
							$bulkFaxArr['success'][] = array('faxId' => $resp['data']->faxId, 'number' => $faxNumber, 'name' => $faxNm, 'type' => $faxType);
						}
						else{
							$bulkFaxArr['error'][] = array('message' => $resp['message'], 'number' => $faxNumber, 'name' => $faxNm, 'type' => $faxType);
						}
					}
					/*
					 * Trigger Updox bulk fax sending API call in the IMW updox library.
					 **********Updox - Third party API service**********
					*/
					// $resp  = $updox->sendFaxMulti($faxNumbers, $pdfContent);
					
					// if($resp['status']=='success'){
					// 	/*
					// 	 * Save bulk fax id frm the response data from Updox API call. 
					// 	*/
					// 	$faxnumber = implode(',', $faxNumbers);
					// 	$responseData['primary']['fax_id']=$resp['data']->bulkSendId;
					// 	$responseData['primary']['no'] = $faxnumber; 	

					// 	/*
					// 	 * Show error messgae if data exists in the list of Invalid Fax numbers
					// 	*/
					// 	/*if( count($resp['data']->invalidFaxNumbers) > 0)
					// 	{
					// 		$responseData['primary']['errors']="Invalid Fax numbers:<br/>".implode(', ', $resp['data']->invalidFaxNumbers);
					// 	}*/
					// }
					// else
					// 	$responseData['primary']['error']=$resp['message'];
				}
			}
			else
			{
				//========PRIMARY RECIPENT WORK=========
				$resp  = $updox->sendFax($faxname, $faxnumber, $pdfContent);
				if($resp['status']=='success'){
					$responseData['primary']['fax_id']=$resp['data']->faxId;
					$responseData['primary']['no'] = $faxnumber;
				}
				else
					$responseData['primary']['error']=$resp['message'];
				
				//========CC1 RECIPENT WORK=============
				if($faxnameCc1 && $faxnumberCc1) {
					$resp  = $updox->sendFax($faxnameCc1, $faxnumberCc1, $pdfContent);
					if($resp['status']=='success'){
						$responseData['cc1']['fax_id']=$resp['data']->faxId;
						$responseData['cc1']['no'] = $faxnumberCc1;
					}
					else
						$responseData['cc1']['error']=$resp['message'];
				}

				//========CC2 RECIPENT WORK=============
				if($faxnameCc2 && $faxnumberCc2) {
					$resp  = $updox->sendFax($faxnameCc2, $faxnumberCc2, $pdfContent);
					if($resp['status']=='success'){
						$responseData['cc2']['fax_id']=$resp['data']->faxId;
						$responseData['cc2']['no'] = $faxnumberCc2;
					}
					else
						$responseData['cc2']['error']=$resp['message'];
				}

				//========CC3 RECIPENT WORK=============
				if($faxnameCc3 && $faxnumberCc3) {
					$resp  = $updox->sendFax($faxnameCc3, $faxnumberCc3, $pdfContent);
					if($resp['status']=='success'){
						$responseData['cc3']['fax_id']=$resp['data']->faxId;
						$responseData['cc3']['no'] = $faxnumberCc3;
					}
					else
						$responseData['cc3']['error']=$resp['message'];
				}
			}
		}
		elseif( is_interfax() ){
			$logfield=0;
			if(!($fp = fopen($filename, "r"))){
				$responseData['primary']['error'] = "Error opening PDF file";
				$logfield=1;
			}
			if(empty($logfield)){
				$filetype = pathinfo($filename, PATHINFO_EXTENSION);
				
				$pdfContent = "";
				while(!feof($fp)) $pdfContent .= fread($fp,1024);
				fclose($fp);
				
				$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");
				
				$patient_id_str = "";
				$patient_id = $_REQUEST["patient_id"];
				if(!trim($patient_id)) {
					$patient_id = $this->pid; //$_SESSION["patient"];	
				}
				if($patient_id) {
					$patient_id_str	= "Patient ID ".$patient_id;
				}
				
				//$params = (object)[];
				$params = (object) array();
				if($_REQUEST['send_fax_subject']){$patient_id_str=$_REQUEST['send_fax_subject'];}
				$params->Username  			= "".fax_username;
				$params->Password  			= "".fax_password;
				$params->FaxNumbers 		= $faxnumber;
				$params->FilesData  		= $pdfContent;
				$params->FileTypes  		= $filetype;
				$params->FileSizes   		= strlen($pdfContent);
				$params->Postpone   		= "2005-04-25T20:31:00-04:00";
				$params->IsHighResolution   = "0";
				$params->CSID   			= "";
				$params->Subject   			= $patient_id_str;
				$params->ReplyAddress 		= "";

				$result = $client->SendFaxEx($params);
				$returnMsg=$result->SendfaxExResult; // returns the transactionID if successful
				
				if($returnMsg>0){
					$responseData['primary']['fax_id'] = $returnMsg;
					$responseData['primary']['no'] = $faxnumber;
				}
				elseif($returnMsg=='-1003')
					$responseData['primary']['error'] = "Error No. ".$returnMsg." - Authentication error";
				elseif($returnMsg=='-112')
					$responseData['primary']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
				else
					$responseData['primary']['error'] = "Error No. ".$returnMsg;
				
				
				//code start to send fax to Cc1, Cc2, Cc3	
				if($faxnumberCc1) {
					$params->FaxNumbers = $faxnumberCc1;
					$result = $client->SendFaxEx($params);
					$returnMsg=$result->SendfaxExResult;
					
					if($returnMsg>0){
						$responseData['cc1']['fax_id'] = $returnMsg;
						$responseData['cc1']['no'] = $faxnumber;
					}
					elseif($returnMsg=='-1003')
						$responseData['cc1']['error'] = "Error No. ".$returnMsg." - Authentication error";
					elseif($returnMsg=='-112')
						$responseData['cc1']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
					else
						$responseData['cc1']['error'] = "Error No. ".$returnMsg;
					
				}
				if($faxnumberCc2) {
					$params->FaxNumbers = $faxnumberCc2;
					$result = $client->SendFaxEx($params);
					$returnMsg=$result->SendfaxExResult; 
					
					if($returnMsg>0){
						$responseData['cc2']['fax_id'] = $returnMsg;
						$responseData['cc2']['no'] = $faxnumber;
					}
					elseif($returnMsg=='-1003')
						$responseData['cc2']['error'] = "Error No. ".$returnMsg." - Authentication error";
					elseif($returnMsg=='-112')
						$responseData['cc2']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
					else
						$responseData['cc2']['error'] = "Error No. ".$returnMsg;
				}
				if($faxnumberCc3) {
					$params->FaxNumbers = $faxnumberCc3;
					$result = $client->SendFaxEx($params);
					$returnMsg=$result->SendfaxExResult;
					
					if($returnMsg>0){
						$responseData['cc3']['fax_id'] = $returnMsg;
						$responseData['cc3']['no'] = $faxnumber;
					}
					elseif($returnMsg=='-1003')
						$responseData['cc3']['error'] = "Error No. ".$returnMsg." - Authentication error";
					elseif($returnMsg=='-112')
						$responseData['cc3']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
					else
						$responseData['cc3']['error'] = "Error No. ".$returnMsg;
				}
			}//
		}
		//logfield:
		//If Bulk Fax has sent fax successfully
		$sentBulkFax = array();
		$sendFaxInsertId = array();
		if($bulkFaxArr['success'] && count($bulkFaxArr['success']) > 0){
			foreach($bulkFaxArr['success'] as $faxObj){
				$sql = "INSERT INTO `send_fax_log_tbl` SET
							`patient_id`=".((int)$this->pid).",
							`folder_date`='".date('Y-m-d')."',
							`operator_id`=".((int)$_SESSION['authId']).",
							`section_name`='consult',
							`updox_id`='".$faxObj['faxId']."',
							`updox_status`='queued',
							`fax_type`='".$faxObj['type']."',
							`file_name`='".$log_file_name."',
							`fax_number`='".$faxObj['number']."'";
				$resp = imw_query($sql);
				if($resp){
					$sentBulkFax[$faxObj['faxId']] = $faxObj['number'];
					$sendFaxInsertId[]= imw_insert_id();
				}
			}
		} else {
            foreach($bulkFaxArr['error'] as $faxObj){
                $type=strtolower($faxObj['type']);
                $responseData[$type]['error']=$faxObj['message'];
			}
        }

        //
		if(isset($_REQUEST['savedfrom']) && ($_REQUEST['savedfrom']=="pt_consult_letters")){ //This Block will be executed with called from saved consult letter --
			
			$patientConsultId  = trim($_REQUEST['pat_temp_id']); //PATIENT CONSULT ID
			$ref_phy_id_fax= $_REQUEST['ref_phy_id']; //REF. PHY. ID
			
			//=========CHECK IF FAX SEND SUCCESSFULLY============
			if(
				($responseData['primary']['fax_id'] && !empty($responseData['primary']['fax_id'])) ||
				($responseData['cc1']['fax_id']  && !empty($responseData['cc1']['fax_id'])) ||
				($responseData['cc2']['fax_id'] && (!empty($responseData['cc2']['fax_id']))) ||
				($responseData['cc3']['fax_id'] && !empty($responseData['cc3']['fax_id']))
			 ){
			//==========FAX STATUS UPDATED INTO CONSULT TABLE=====
			if($patientConsultId && !empty($patientConsultId)){
				$updateConsultTable=imw_query("UPDATE `patient_consult_letter_tbl` set fax_status='1', report_sent_date='".date('Y-m-d')."',fax_ref_phy_id='".$ref_phy_id_fax."',fax_number='".$faxnumber."' where patient_consult_id='".$patientConsultId."'");
				
			//=======GET CONSULT DATA FOR INSERTION INTO SEND_FAX_LOG_TBL ON BASED OF REQUESTED CONSULT ID=========
				$getConsultData="SELECT patient_id, templateId, templateName, operator_id FROM `patient_consult_letter_tbl` WHERE patient_consult_id='".$patientConsultId."'";
					$getConsultRow = imw_query($getConsultData);
					if(imw_num_rows($getConsultRow)>0){
						$getConsultRes = imw_fetch_assoc($getConsultRow);
					    
						$patient_consult_id = $getConsultRes['patient_consult_id'];
						$patient_id = $getConsultRes['patient_id'];
						$template_id = $getConsultRes['templateId'];
						$template_name = $getConsultRes['templateName'];
						$operator_id = $getConsultRes['operator_id'];
					  
					  //==PRIMARY RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
						if($responseData['primary']['fax_id']){
							$qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['primary']['fax_id']."', updox_status='queued', `fax_type`='Primary', `file_name`='".$log_file_name."', fax_number='".$faxnumber."'");
						}
					  //==CC1 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
					  if($responseData['cc1']['fax_id']){
						  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc1']['fax_id']."', updox_status='queued', `fax_type`='CC1', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc1."'");
					  }
					  //==CC2 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
					  if($responseData['cc2']['fax_id']){
						  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc2']['fax_id']."', updox_status='queued', `fax_type`='CC2', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc2."'");
					  }
					  //==CC3 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
					  if($responseData['cc3']['fax_id']){
						  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc3']['fax_id']."', updox_status='queued', `fax_type`='CC3', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc3."'");
					}
				 }
			  }
			}
			//==RESPONSE JSON ENCODE AND SEND BACK TO CONSULT_LETTER_PAGE.PHP FILE FUNCTION sendSavedFax()====
			$logData=$responseData;
		}else{		
			$logData = array();
			/*Lg in DB*/
			foreach($responseData as $key=>$respData){
				$logData[$key] = array();
				if( isset($respData['error']) || !isset($respData['fax_id'])){
					$logData[$key] = $respData;
					continue;
				}
				
				$logData[$key] = $respData;
				/*Insert in to DB*/
				$sql = "INSERT INTO `send_fax_log_tbl` SET
							`patient_id`=".((int)$this->pid).",
							`folder_date`='".date('Y-m-d')."',
							`operator_id`=".((int)$_SESSION['authId']).",
							`section_name`='consult',
							`updox_id`='".$respData['fax_id']."',
							`updox_status`='queued',
							`fax_type`='".$key."',
							`file_name`='".$log_file_name."',
							`fax_number`='".$respData['no']."'";
				imw_query($sql);
				$logData[$key]['log_id'] = imw_insert_id();
				$sendFaxInsertId[]= $logData[$key]['log_id'];
			}	
		}
		$logData['fax_log_id'] = implode(',', array_unique($sendFaxInsertId));
		//Appending Bulk successfull fax status notification
		if(count($sentBulkFax) > 0){
			$logData['primary']['fax_id'] = implode(';', array_keys($sentBulkFax));
			$logData['primary']['no'] = implode(';', array_unique(array_values($sentBulkFax)));
		}
		print	json_encode($logData);
	}//End sendfax
	
	function fax_pdf_creater(){
		//print_r($_POST);
		if($_POST['html_d']){
			$tempData = $_POST['html_d'];
			$temp_id=$_POST['template_id'];
			$selectedRefPhyID=$_POST['selectedRefPhyID'];
			//THIS ID IS COMING FROM CONSULT POPUP TO REPLACE THE SELECT REF. PHYSICIAN VARIABLES FROM LEFT PANEL + FAX COVER LETTER 
			if(!empty($selectedRefPhyID)){
				$patientRefTo = $selectedRefPhyID[0];
			}
			if(empty($addresseRefToId) && !empty($selectedRefPhyID))
			{
				$addresseRefToId = $selectedRefPhyID[1];
				if(empty($addresseRefToId))
				{
					$addresseRefToId = $patientRefTo;
				}	
			}
			$objParser = new PnTempParser;
			//============ Attaching Panels[Left-panel,Header,Footer] to fax letter. These was missed at receiver client side.=====//
			
			$consult_dtls = explode('!~!',$temp_id);
			$consult_template_id = sqlEscStr(str_replace('\'','',$consult_dtls[0]));
			$consult_template_name = sqlEscStr($consult_dtls[1]);
			$footer_panel = $header_panel = $leftpanel_panel = '';
			
			$sql = "select * from `consultTemplate` where `consultLeter_id`='".$consult_template_id."' ";
			$get_template_panel_details = sqlStatement($sql); 
			if(imw_num_rows($get_template_panel_details)>=1)
			{
				$cnslt_template_pnl_details=sqlFetchArray($get_template_panel_details);
				$consultTemplatefooter = $cnslt_template_pnl_details["footer"];
				$consultTemplateheader = $cnslt_template_pnl_details["patient_header"];
				$leftpanel_val = $cnslt_template_pnl_details["leftpanel"];
				//$left_margin = $cnslt_template_pnl_details->left_margin;
				//$top_margin = $cnslt_template_pnl_details->top_margin;
			}
			
			if($consultTemplatefooter==1 || $consultTemplateheader==1 || $leftpanel_val==1){
		
				$sql = "SELECT * FROM document_panels LIMIT 0,1";
				$res_panels = imw_query($sql);
				if($res_panels && imw_num_rows($res_panels)==1){
					$rs_panels		= imw_fetch_assoc($res_panels);
					$footer_panel	= trim(html_entity_decode($rs_panels['footer']));
					$header_panel	= trim(html_entity_decode($rs_panels['header']));
					$leftpanel_panel= trim(html_entity_decode($rs_panels['leftpanel']));
				}
				
				if($consultTemplatefooter!=1)	{$footer_panel 		= '';}
				if($consultTemplateheader!=1)	{$header_panel 		= '';}
				if($leftpanel_val!=1)			{$leftpanel_panel 	= '';}
			}
			
			if($header_panel && $header_panel!=''){
				$header_panel = $objParser->getDataParsed($header_panel,$this->pid,$this->fid,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
				$consultTemplateData .= $header_panel;
			}
			if($leftpanel_panel && $leftpanel_panel!=''){
				$leftpanel_panel = $objParser->getDataParsed($leftpanel_panel,$this->pid,$this->fid,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
				$consultTemplateData .=  "<table style='width:700px'><tr><td style='width:180px;text-align:left'>".$leftpanel_panel."</td><td style='width:520px;text-align:left;vertical-align:baseline'>".$tempData."</td></tr></table>";
			}else{
				$consultTemplateData .= $tempData;
			}
			
			if($header_panel == '' && $leftpanel_panel == ''){
				$consultTemplateData = "<table style='width:700px;text-align:left;border-collapse:collapse'><tr><td style='text-align:left;vertical-align:baseline'>".$tempData."</td></tr></table>";
			}
			
			if($footer_panel && $footer_panel != ''){
				$footer_panel = $objParser->getDataParsed($footer_panel,$this->pid,$this->fid,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
				$consultTemplateData .= '<page_footer>'.$footer_panel.'</page_footer>';
			}
			
			//start code to get fax cover letter
			
			$qryFaxCoverLetter="select consultTemplateData from consultTemplate where consultTemplateType='fax_cover_letter'";
			$resFaxCoverLetter=imw_query($qryFaxCoverLetter)or die(imw_error());
			$faxCoverLetter="";
			if(imw_num_rows($resFaxCoverLetter)>0){
				$rowFaxCoverLetter=imw_fetch_assoc($resFaxCoverLetter);
				$faxCoverLetterData=($rowFaxCoverLetter['consultTemplateData']);
				$faxCoverLetter=$objParser->getDataParsed($faxCoverLetterData,$this->pid,$this->fid,$patientRefTo,$addresseRefToId,$cc1RefToId,$cc2RefToId,$cc3RefToId);
				$faxCoverLetter = str_ireplace("+ve",'Positive',$faxCoverLetter);
				$faxCoverLetter = str_ireplace("-ve",'Negative',$faxCoverLetter);
				$faxCoverLetter.="<page></page>";
				
				$faxCoverLetter = $faxCoverLetter;
				$consultTemplateData = $faxCoverLetter.$consultTemplateData;
				//echo "<div id='divCoverLetterId' style='border:1px solid #000000'>".$faxCoverLetter."</div>";die();
			}
			//end code to get fax cover letter
			
			//============================================== END TASK ==========================================================//
			
			########## path and other variable replacement will take place here #################
			$consultTemplateData=consult_path_replace4pdf($consultTemplateData);
			
			/*
			$fp = fopen('../common/new_html2pdf/pdffile_fax.html','w');
			$writeData = fwrite($fp,$consultTemplateData);
			fclose($fp);
			*/
			//--
			$fp = '/tmp/pdffile_fax.html';
			$oSaveFile = new SaveFile($_SESSION["authId"],1);
			$resp = $oSaveFile->cr_file($fp,$consultTemplateData);
			//$resp_w = $oSaveFile->getFilePath($resp, "w");
			$pdfDirectHTML_path=$resp;
			//--
			
			global $myInternalIP, $web_RootDirectoryName;
			$dir = explode('/',$_SERVER['HTTP_REFERER']);
			$httpPro = $dir[0];
			$port = isset($_SERVER['SERVER_PORT']) && !empty($_SERVER['SERVER_PORT']) ? ":".$_SERVER['SERVER_PORT']:"";
			//$myHTTPAddress = $httpPro.'//'.$myInternalIP.$port.'/'.$web_RootDirectoryName.'/library/html_to_pdf/createPdf.php';
			$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
			$getPCIP=$_SESSION["authId"];			
			$getIP=str_ireplace(".","_",$getPCIP);
			$getIP=str_ireplace("::","_",$getIP);
			$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/"; 
			$setNameFaxPDF="fax_".$getIP;
			
			//delete any existing file
			//if(file_exists($GLOBALS['incdir'].'/../library/html_to_pdf/'.$setNameFaxPDF.'.pdf')){
			//	unlink($GLOBALS['incdir'].'/../library/html_to_pdf/'.$setNameFaxPDF.'.pdf');
			//}
			if(file_exists($pdfPath.$setNameFaxPDF.'.pdf')){
				unlink($pdfPath.$setNameFaxPDF.'.pdf');
			}
			
			$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&op=p&saveOption=fax&pdf_name=".$pdfPath.$setNameFaxPDF."&file_location=".$pdfDirectHTML_path;
			$curNew = curl_init();
			curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
			curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
			curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
			$data = curl_exec($curNew);
			if($_POST['typ']=='e')
			{
				if (curl_errno($curNew)){
					die("Curl Error In Send Mail: " . curl_error($curNew). " ");
				}
			}
			else
			{
				if (curl_errno($curNew)){
					die("Curl Error In Send Fax: " . curl_error($curNew). " ");
				}
			}
			curl_close($curNew);	
			
			$filename= $pdfPath.$setNameFaxPDF.'.pdf';
			$filetype= 'PDF';
			if(!($fp = fopen($filename, "r"))){
				$error="Error opening PDF file";
			}
			$arr_ret=array();
			$arr_ret["error"]=$error;
			$arr_ret["temp_id"]=$temp_id;
			$arr_ret["setNameFaxPDF"]=$setNameFaxPDF;
			
			echo json_encode($arr_ret);
		}	
	}

	function pt_consult_letters(){
		$str="";
		$sql="SELECT * FROM `patient_consult_letter_tbl` WHERE patient_id='".$this->pid."' AND status=0 ORDER BY cur_date desc";
		$rez=sqlStatement($sql);
		if(imw_num_rows($rez)>0) {
			while($getConsultTempRow = sqlFetchArray($rez)){
				$templateName =  $patient_consult_letter_to = $curDateTime = "";
				if($getConsultTempRow['templateName']!=''){
					$templateName = trim($getConsultTempRow['templateName']);
				}
				if($getConsultTempRow['patient_consult_letter_to']!=''){
					$patient_consult_letter_to = trim($getConsultTempRow['patient_consult_letter_to']);
				}
				if(($getConsultTempRow['cur_date']) && $getConsultTempRow['cur_date']!='0000-00-00 00:00:00'){
					$curDateTime = trim(date('m-d-y'.' h:i',strtotime($getConsultTempRow['cur_date'])));
				}
				
				$str.= "<tr><td class=\"text-nowrap\">".$curDateTime."</td><td class=\"text-nowrap\">".$templateName."</td><td class=\"text-nowrap\">".$patient_consult_letter_to."</td></tr>";
			}
		}
		
		if(!empty($str)){
			$str ="<div class=\"table-responsive\" style=\"height:300px;\"><table class=\"table table-striped table-bordered\"><tr><td class=\"text-nowrap\">Date</td><td class=\"text-nowrap\">Template Name</td><td class=\"text-nowrap\">Referring Physician</td></tr>".$str."</table></div>";
		}else{
			$str="No record found";
		}
		//--
		
		$ar=array();
		$ar["data"] = $str;		
		echo json_encode($ar);
		
	}
	
	function load_pt_consult(){
	
		//Move to trash etc --
		$moveToTrashConsentId = $_GET["moveToTrashConsentId"];
		$moveToTrashConsentOriginalId = $_GET["moveToTrashConsentOriginalId"];
		if(!empty($moveToTrashConsentId) || !empty($moveToTrashConsentOriginalId)){
			$st = $_GET["st"];
			if($moveToTrashConsentId) {
				$qry = "update send_fax_log_tbl set status = '$st', cur_date_time = '".date("Y-m-d H:i:s")."' 
						where id = '$moveToTrashConsentId'";
				
			}else{
				$qry = "update patient_consult_letter_tbl set status = '$st',cur_date = '".date("Y-m-d H:i:s")."' 
							where patient_consult_id = '$moveToTrashConsentOriginalId'";
			}
			$row = sqlQuery($qry);
		}
		//End Move to trash etc --	
		
		$html_left_pane="";
		$patient_id = $this->pid;
		$oPt = new Patient($patient_id);
		$patName = $oPt->getName(7);
		
		
		$operator_id = $getPCIP=$_SESSION["authId"];	
		$setNameFaxPDF="savedFax_".$getPCIP;			
		
		//
		$phyNameArr=array();
		$oUser = new User();
		$phyNameArr = $oUser->getUserArr(8, 'all');
		
		
		//---- Get Patient Consent Forms Signed Date(s)-------
		$patientConsultLetterCreatedDate=array();
		$qry = "SELECT distinct DATE_FORMAT(date, '".get_sql_date_format()."') sortDate, DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."') formCreatedDate 
				from patient_consult_letter_tbl  where patient_id='".$patient_id."' and status = '0' ORDER BY `date` desc" ;
		$rez = sqlStatement($qry);
		for($i=1;$row=sqlFetchArray($rez); $i++){
			$patientConsultLetterCreatedDate[]=$row;
		}
		
		//CODE FOR SCAN DISPLAY
		$ptScanUploadConsultLetterCreatedDate=array();
		$qry = "SELECT distinct DATE_FORMAT(created_date, '".get_sql_date_format('','Y','/')."') formCreatedDate 
				from ".IMEDIC_SCAN_DB.".scans  where patient_id=$patient_id 
				AND image_form='consultLetter' and status != '1' ORDER BY created_date desc" ;
		$rez = sqlStatement($qry);		
		for($i=1;$row=sqlFetchArray($rez); $i++){
			$ptScanUploadConsultLetterCreatedDate[]=$row;
		}
		
		//merge
		$patientConsultLetterCreatedDate = array_merge($patientConsultLetterCreatedDate,$ptScanUploadConsultLetterCreatedDate);
		
		$indx=1;
		$p=3;
		$f = 0;
		foreach($patientConsultLetterCreatedDate as $z=>$val) {
			$p++;
			$formCreatedDate=$patientConsultLetterCreatedDate[$z]['formCreatedDate'];
			//$tree->addToArray($p,$formCreatedDate,$f,"");
			
			
			//pt_consult_letters date wise
			$str_pt_con_ltr_date_wise="";
			
			
			//---- Get Patient Signed Consent Forms Created Date(s)-------
			$patientConsultLetter=array();
			$qrynew = "SELECT patient_consult_id,patient_form_id,templateData,
						date,cur_date ,templateId,templateName,operator_id, m.source_id as media_id
						from patient_consult_letter_tbl pclt LEFT JOIN ".constant('IMEDIC_SCAN_DB').".media m ON (pclt.patient_consult_id=m.source_id AND m.source='consult_letter') 
						where pclt.patient_id = '".$patient_id."' 
						and DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."')='".$formCreatedDate."' 
						and status != '1' ORDER BY status desc" ;
			$rez = sqlStatement($qrynew);		
			for($i=1;$row=sqlFetchArray($rez); $i++){
				$patientConsultLetter[]=$row;
			}
			$d = $p++;
			for($x=0;$x<count($patientConsultLetter);$x++){
				$media_id = $patientConsultLetter[$x]['media_id'];
				$pdf_icon_type = 1; //if(constant('AV_MODULE')=='YES' && $media_id>0){$pdf_icon_type=2;}
				$consentFormId = $patientConsultLetter[$x]['templateId'];
				$operator_id = $patientConsultLetter[$x]['operator_id'];
				$consentFormInfoId = $patientConsultLetter[$x]['patient_form_id'];
				$patient_consult_id = $patientConsultLetter[$x]['patient_consult_id'];
				$mod_date = date("g:i A",strtotime($patientConsultLetter[$x]['cur_date']));
				$userName = $patientConsultLetter[$x]['fname'][0];
				$userName .= $patientConsultLetter[$x]['lname'][0];
				
				$userName = strtoupper(trim($userName));
				$consentFormName = $patientConsultLetter[$x]['templateName'];//."(".$form_created_time.")";
				$p++;
				$consentFormName .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
				$consentFormName = trim(ucwords($consentFormName));
				//$tree->addToArray($p,$consentFormName,$d,"print_consent_form.php?consent_form_id=$consentFormId&consent=yes&form_information_id=$consentFormInfoId","consent_data","../../images/pdf_icon[1].png");
				//$tree->addToArray($p,$consentFormName,$d,"templatepri.php?tempId=$patient_consult_id&media_id=$media_id",$targetArea,"../../images/pdf_icon[".$pdf_icon_type."].png","../../images/b_drop.png","tree4consult_letter.php?moveToTrashConsentOriginalId=$patient_consult_id&st=1","consent_tree","Move To Trash");
				$str_pt_con_ltr_date_wise.="<li class=\"list-group-item\"><a data-url=\"".$GLOBALS['rootdir']."/chart_notes/onload_wv.php?elem_action=Pdf_consult_letters&tempId=".$patient_consult_id."&media_id=".$media_id."\" data-target=\"consent_data\" href=\"javascript:void(0);\" onclick=\"opConsult('',this)\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormName."</a>"; //<img src=\"".$GLOBAL["srcdir"]."/images/pdf_small.png\" alt=\"pdf\">
				$str_pt_con_ltr_date_wise.="<a href=\"javascript:void(0);\" onclick=\"opConsult('&moveToTrashConsentOriginalId=".$patient_consult_id."&st=1')\"><span class=\"glyphicon glyphicon-remove\" title=\"Move To Trash\"></span></a></li>"; //<img id=\"trashid\" src=\"".$GLOBAL["srcdir"]."/images/pdf_small.png\" border='0' align=\"middle\" alt=\"trash\"/>
			}	
			
			//START CODE FOR SCAN
			/* // Dead Code ??
			$scanPatientConsultLetter=array();
			$qryScan = "SELECT * FROM ".IMEDIC_SCAN_DB.".scans 
						where patient_id='".$patient_id."' 
						AND image_form='consultLetter'
						AND DATE_FORMAT(created_date, '%m/%d/%Y')='".$formCreatedDate."'
						And status != '1'   
						ORDER BY scan_id";
			$rez = sqlStatement($qryScan);		
			for($i=1;$row=sqlFetchArray($rez); $i++){
				$scanPatientConsultLetter[]=$row;
			}			
			for($w=0;$w<count($scanPatientConsultLetter);$w++){
				$scanIdConsultLetter = $scanPatientConsultLetter[$w]['scan_id'];			
				$scanFileType 		 = $scanPatientConsultLetter[$w]['file_type'];
				$consentFormNameScan = $scanPatientConsultLetter[$w]['image_name'];//."(".$form_created_time.")";
				$operator_id = $scanPatientConsultLetter[$w]['operator_id'];
				$mod_date = date("g:i A",strtotime($scanPatientConsultLetter[$w]['modi_date']));
				$scanIcon="../../images/dhtml_sheet.gif";
				if($scanFileType=='application/pdf') {
					$scanIcon="../../images/pdf_icon[1].png";
				}
				$p++;
				$consentFormNameScan = trim(ucwords($consentFormNameScan));
				if(empty($mod_date) != true || empty($phyNameArr[$operator_id]) != true){
					$consentFormNameScan .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
				}
				//$tree->addToArray($p,urldecode($consentFormNameScan),$d,"logoImg.php?from=scanImage&scan_id=$scanIdConsultLetter",$targetArea,$scanIcon,"../../images/b_drop.png","tree4consult_letter.php?scanIdConsultLetter=$scanIdConsultLetter&st=1","consent_tree","Move To Trash");
				$str_pt_con_ltr_date_wise.="<li class=\"list-group-item\"><a href=\"#\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormNameScan."</a>";
				$str_pt_con_ltr_date_wise.="<a href=\"#\" ><span class=\"glyphicon glyphicon-remove\"></span></a></li>";
			}
			*/
			//END CODE FOR SCAN			

			$str = '<div class="panel-group">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" href="#collapse'.$indx.'">'.$formCreatedDate.'</a>
							</h4>
						</div>
						<div id="collapse'.$indx.'" class="panel-collapse collapse">
							<ul class="list-group">
							'.$str_pt_con_ltr_date_wise.'
							</ul>
						</div>
					</div>
				</div>';
			$indx++;	
			//add
			if(!empty($str)){ $html_left_pane.=$str; $str_pt_con_ltr_date_wise=""; }

		}

		//--- Trash Folders ---------
		$p_trash = 1;
		$p = $p+1;
		$f = 0;
		$k=$p;
		//$tree->addToArray($p_trash,"Trash",0);
		$str_pt_con_ltr_trash="";
		$str_trash="";
		
		$qryConsultTrash = "SELECT distinct DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."') as formCreatedDate 
		from patient_consult_letter_tbl where patient_id='".$patient_id."' 
		and status = '1'  ORDER BY date desc" ;		
		$qryConsultTrashRes=array();
		$rez = sqlStatement($qryConsultTrash);
		for($i=1;$row=sqlFetchArray($rez);$i++){	$qryConsultTrashRes[]=$row;	}
		
		$trashQryRes = array();
		$trashQryRes = array_merge($qryConsultTrashRes,$trashQryRes);
		array_unique($trashQryRes);
		rsort($trashQryRes);
		
		$trashQryResNew = array();
		$tmpDtArr = array();
		for($d_fax_tmp=0;$d_fax_tmp<count($trashQryRes);$d_fax_tmp++){
			$aTemp =0;
			if(!in_array($trashQryRes[$d_fax_tmp]['formCreatedDate'],$tmpDtArr)) {
				$tmpDtArr[] = $trashQryRes[$d_fax_tmp]['formCreatedDate'];
				$trashQryResNew[$d_fax_tmp]['formCreatedDate'] = $trashQryRes[$d_fax_tmp]['formCreatedDate'];
				$aTemp++;	
			}
		}
		
		for($d=0;$d<count($trashQryResNew);$d++){
			$str_pt_con_ltr_trash="";
			$p++;
			$formCreatedDate = $trashQryResNew[$d]['formCreatedDate'];
			$sendfaxlogId = $trashQryResNew[$d]['sendfaxlog_id'];
			if(trim($formCreatedDate)) {
				//$tree->addToArray($p,$formCreatedDate,$p_trash,"");
			}
			$qrynewTrash = "SELECT patient_consult_id,patient_form_id,templateData,
						date,cur_date ,templateId as template_id,templateName as template_name,operator_id
						from patient_consult_letter_tbl 
						where patient_id = '$patient_id' 
						and DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' 
						and status != '0' ORDER BY status desc" ;				
			$rez=sqlStatement($qrynewTrash);			
			$resnewTrash = array();
			for($i=1;$row=sqlFetchArray($rez);$i++){	$resnewTrash[]=$row;	}
			//BELOW CODE COMMENTED TO STOP DISPLAY SEND_FAX_LOG_TBL SEND FAX RECORD INTO SEND FAX TRASH SECTION UNDER CONSULT LETTER ICON
			/*$qrynew = "SELECT id as sendfaxlogId, patient_consult_id, template_id, template_name,  
						folder_date, cur_date_time, operator_id  
						from `send_fax_log_tbl` 
						where patient_id = '$patient_id' 
						and DATE_FORMAT(folder_date, '".getSqlDateFormat('','Y','/')."')='$formCreatedDate' 
						and status != '0' ORDER BY status desc" ;
			
			//echo $qrynew; die;	
			$patientConsultLetter = ManageData::getQryRes($qrynew);
			$patientConsultLetter = array();
			$k=$p;
			$f=$p;
			for($x=0;$x<count($patientConsultLetter);$x++,$p++){
				$mod_date = date("g:i A",strtotime($patientConsultLetter[$x]['cur_date']));
				$k++;
				$sendfaxlogId = $patientConsultLetter[$x]['sendfaxlogId'];
				$consentFormId = $patientConsultLetter[$x]['template_id'];
				$operator_id = $patientConsultLetter[$x]['operator_id'];
				//$consentFormInfoId = $patientConsultLetter[$x]['patient_form_id'];
				$patient_consult_id = $patientConsultLetter[$x]['patient_consult_id'];
				$consentFormName = $patientConsultLetter[$x]['template_name'];//."(".$form_created_time.")";
				$consentFormName = trim(ucwords($consentFormName));
				$consentFormName .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
				//$tree->addToArray($k,$consentFormName,$f,"templatepri.php?tempId=$patient_consult_id",$targetArea,"../../images/pdf_icon[1].png","../../images/restore_icon.png","tree4consult_letter.php?moveToTrashConsentId=$sendfaxlogId&moveToTrashConsentOriginalId=&st=0","consent_tree","Move To Forms");
				//$str_pt_con_ltr_trash="";
				$str_pt_con_ltr_trash.="<li class=\"list-group-item\"><a href=\"".$GLOBALS['rootdir']."/chart_notes/onload_wv.php?elem_action=Pdf_consult_letters&tempId=".$patient_consult_id."\" target=\"consent_data\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormName."</a>"; 
				$str_pt_con_ltr_trash.="<a href=\"tree4consult_letter.php?moveToTrashConsentId=$sendfaxlogId&moveToTrashConsentOriginalId=&st=0\"><span class=\"glyphicon glyphicon-remove\" title=\"Move To Forms\"></span></a></li>"; 
			}
			*/
			for($x=0;$x<count($resnewTrash);$x++,$p++){
				$mod_date = date("g:i A",strtotime($resnewTrash[$x]['cur_date']));
				$k++;
				$sendfaxlogId = $resnewTrash[$x]['sendfaxlogId'];
				$consentFormId = $resnewTrash[$x]['template_id'];
				$operator_id = $resnewTrash[$x]['operator_id'];
				//$consentFormInfoId = $resnewTrash[$x]['patient_form_id'];
				$patient_consult_id = $resnewTrash[$x]['patient_consult_id'];
				$consentFormName = $resnewTrash[$x]['template_name'];//."(".$form_created_time.")";
				$consentFormName = trim(ucwords($consentFormName));
				$consentFormName .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
				//$tree->addToArray($k,$consentFormName,$f,"templatepri.php?tempId=$patient_consult_id",$targetArea,"../../images/pdf_icon[1].png","../../images/restore_icon.png","tree4consult_letter.php?moveToTrashConsentId=&moveToTrashConsentOriginalId=$patient_consult_id&st=0","consent_tree","Move To Forms");
				//$str_pt_con_ltr_trash="";
				$str_pt_con_ltr_trash.="<li class=\"list-group-item\"><a data-url=\"".$GLOBALS['rootdir']."/chart_notes/onload_wv.php?elem_action=Pdf_consult_letters&tempId=".$patient_consult_id."\" data-target=\"consent_data\" href=\"javascript:void(0);\" onclick=\"opConsult('',this)\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormName."</a>"; 
				$str_pt_con_ltr_trash.="<a href=\"javascript:void(0);\" onclick=\"opConsult('&moveToTrashConsentId=&moveToTrashConsentOriginalId=".$patient_consult_id."&st=0')\"><span class=\"glyphicon glyphicon-share-alt\" title=\"Move To Forms\"></span></a></li>"; 
				
			}
			
			if(!empty($str_pt_con_ltr_trash)){
				$str_trash .= '<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse'.$indx.'">'.$formCreatedDate.'</a>
									</h4>
								</div>
								<div id="collapse'.$indx.'" class="panel-collapse collapse">
									<ul class="list-group">
									'.$str_pt_con_ltr_trash.'
									</ul>
								</div>
							</div>
						</div>';
				$indx++;		
			}
		}
		
		if(!empty($str_trash)){
			$str_trash='<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse_trash'.$p_trash.'">Trash</a>
									</h4>
								</div>
								<div id="collapse_trash'.$p_trash.'" class="panel-collapse collapse">
									<div class="panel-body">'.$str_trash.'</div>
								</div>
							</div>
						</div>';
			$html_left_pane.=$str_trash;				
		}
		
		//--- End Trash Folders ---------
		
		//start code for display Outbound fax Form information
		$osf = new SaveFile($patient_id);
		$up_path_web = $osf->getUploadDirPath(1);
		
		$p_fax = 2;
		//$q = $p_fax+1;
		$q = $k+1;
		//$tree->addToArray($p_fax,"Outgoing Fax",0);
		
		$qryConsult = "SELECT 
			DATE_FORMAT(pct.date, '".get_sql_date_format('','Y','/')."') AS 'formCreatedDate',
			DATE_FORMAT(log.cur_date_time, '".get_sql_date_format('','Y','/')."') AS 'cur_date_time',
			DATE_FORMAT(pct.date, '%Y%m%d') AS 'formCreatedDate1',
			DATE_FORMAT(log.cur_date_time, '%Y%m%d') AS 'cur_date_time1'
			FROM 
			patient_consult_letter_tbl pct 
			LEFT JOIN (
			SELECT 
			patient_consult_id,
			cur_date_time
			FROM 
			send_fax_log_tbl
			) log ON(
			pct.patient_consult_id = log.patient_consult_id
			) 
			WHERE 
			pct.patient_id = '".$patient_id."'
			AND pct.fax_status = '1'
			GROUP BY formCreatedDate1
			ORDER BY formCreatedDate1 DESC";		
		//$faxQryConsultRes = $objManageData->mysqlifetchdata($qryConsult);
		$rez = sqlStatement($qryConsult);
		$faxQryConsultRes = array();
		for($i=1;$row=sqlFetchArray($rez);$i++){	$faxQryConsultRes[]=$row;	}
		$faxQryRes = array();
		$faxQryRes = array_merge($faxQryConsultRes,$faxQryRes);
		array_unique($faxQryRes);
		
		foreach($faxQryRes as $val){
			$tempData = array();
			
			$faxQryResNew[$val['formCreatedDate1']]['date'] = $val['formCreatedDate'];
			
			if($val['cur_date_time']){
				$faxQryResNew[$val['cur_date_time1']]['date'] = $val['cur_date_time'];
				$faxQryResNew[$val['cur_date_time1']]['log'] = true;
			}
		}

		krsort($faxQryResNew);
		$str_outbound="";
		
		// for($d_fax=0;$d_fax<count($faxQryResNew);$d_fax++){
		foreach($faxQryResNew as $val){
			$q++;
			$p++;
			$formCreatedDate = $val['date'];
			$is_log = (isset($val['log']) && $val['log']===true)?true:false;
			
			//if(trim($formCreatedDate)) {
				//$tree->addToArray($q,$formCreatedDate,$p_fax,"");
			//}
			
			$qryfaxConsult = "SELECT 
			pct.patient_consult_id, 
			pct.cur_date, 
			pct.templateName AS template_name, 
			pct.operator_id, 
			log.file_name, 
			log.fax_number, 
			LOWER(log.updox_status) AS 'delivery_status',
			log.id AS 'logId'
			FROM 
			patient_consult_letter_tbl pct 
			LEFT JOIN (
			SELECT 
			id,
			patient_consult_id, 
			file_name, 
			updox_status, 
			fax_number AS fax_number 
			FROM 
			send_fax_log_tbl
			) log ON(
			pct.patient_consult_id = log.patient_consult_id
			) 
			WHERE 
			pct.patient_id = '".$patient_id."' 
			AND DATE_FORMAT(pct.date, '".get_sql_date_format('','Y','/')."')= '".$formCreatedDate."' 
			AND pct.fax_status = '1' 
			AND log.id IS NULL
			ORDER BY 
			pct.cur_date ASC" ;			
			//$resfaxConsult = ManageData::getQryRes($qryfaxConsult);	
			$rez = sqlStatement($qryfaxConsult);
			$resfaxConsult = array();
			for($i=1;$row=sqlFetchArray($rez);$i++){	$resfaxConsult[]=$row;	}
			
			$patientConsultLetter = array();
			
			/*Fetch Data from Log Table*/
			$resfaxConsultLog = array();
			if($is_log){
			$qryConsultLog = "SELECT 
			pct.patient_consult_id, 
			log.cur_date_time AS 'cur_date', 
			pct.templateName AS template_name, 
			log.operator_id, 
			log.file_name, 
			log.fax_number, 
			LOWER(log.updox_status) AS 'delivery_status', 
			log.id AS 'logId' 
			FROM 
			patient_consult_letter_tbl pct 
			RIGHT JOIN (
				SELECT 
					id, 
					patient_consult_id, 
					file_name, 
					updox_status, 
					cur_date_time,
					operator_id,
					fax_number AS fax_number 
				FROM 
					send_fax_log_tbl
			) log ON(
				pct.patient_consult_id = log.patient_consult_id
			) 
			WHERE 
			pct.patient_id = '".$patient_id."' 
			AND DATE_FORMAT(log.cur_date_time, '".get_sql_date_format('','Y','/')."')= '".$formCreatedDate."' 
			AND pct.fax_status = '1'
			AND log.id IS NOT NULL
			ORDER BY 
			pct.cur_date ASC";
			//$resfaxConsultLog = ManageData::getQryRes($qryConsultLog);
			$rez = sqlStatement($qryConsultLog);
			$resfaxConsultLog = array();
			for($i=1;$row=sqlFetchArray($rez);$i++){	$resfaxConsultLog[]=$row;	}
			
			}
			/*End Fetch Data from Log Table*/
			
			$l=$q+1;
			$g=$q;
			
			$faxTimeArr = array_merge($resfaxConsult, $patientConsultLetter, $resfaxConsultLog);
			
			$sort = array();
			$str_pt_con_ltr_outbound="";
			for($x=0;$x<count($faxTimeArr);$x++,$p++){
				$mod_date = date("g:i A",strtotime($faxTimeArr[$x]['cur_date']));
				$l++;
				
				$operator_id = $faxTimeArr[$x]['operator_id'];
				$patient_consult_id = $faxTimeArr[$x]['patient_consult_id'];
				$consentFormName = $faxTimeArr[$x]['template_name'];//."(".$form_created_time.")";
				$consentFormName = trim(ucwords($consentFormName));
				$consentFormName .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
				
				$receiving_fax_no = explode(',', $faxTimeArr[$x]['fax_number']);
				$receiving_fax_no = array_map('core_phone_format', $receiving_fax_no);
				$receiving_fax_no = implode(', ', $receiving_fax_no);
				$consentFormName = ($receiving_fax_no!=='')? $consentFormName.' - '.$receiving_fax_no : $consentFormName;
				
				if($faxTimeArr[$x]['file_name']){					
					$link = $up_path_web.'/PatientId_'.$patient_id.'/fax_log/'.$faxTimeArr[$x]['file_name'].'?hidebtn';
				}
				else{
					//$link = "templatepri.php?tempId=$patient_consult_id";
					$link = $GLOBALS['rootdir']."/chart_notes/onload_wv.php?elem_action=Pdf_consult_letters&tempId=".$patient_consult_id;
				}
				
				$confirm_img = ($faxTimeArr[$x]['delivery_status']==='success')? '../../images/confirm3.png': '';				
				
				//$tree->addToArray($l,$consentFormName,$g, $link,$targetArea,"../../images/pdf_icon[1].png", $confirm_img,"javascript:void(0)","consent_tree","");
				$str_pt_con_ltr_outbound.="<li class=\"list-group-item\"><a data-url=\"".$link."\" data-target=\"consent_data\" href=\"javascript:void(0);\" onclick=\"opConsult('',this)\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormName."</a>"; 
				$str_pt_con_ltr_outbound.="</li>"; 
				
			}
			$q=$l;

			//
			if(!empty($str_pt_con_ltr_outbound)){
				$str_outbound .= '<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse'.$indx.'">'.$formCreatedDate.'</a>
									</h4>
								</div>
								<div id="collapse'.$indx.'" class="panel-collapse collapse">
									<ul class="list-group">
									'.$str_pt_con_ltr_outbound.'
									</ul>
								</div>
							</div>
						</div>';
				$indx++;		
			}
		}
		
		if(!empty($str_outbound)){
			$str_outbound='<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse_outbound'.$p_fax.'">Outgoing Fax</a>
									</h4>
								</div>
								<div id="collapse_outbound'.$p_fax.'" class="panel-collapse collapse">
									<div class="panel-body">'.$str_outbound.'</div>
								</div>
							</div>
						</div>';
			$html_left_pane.=$str_outbound;				
		}
		
		//End start code for display Outbound fax Form information
		
		//start code for display Incoming fax Form information
		//$p_fax = ($k+1);
		$p_fax = 3;
		//$q = $p_fax+1;
		$q = $q+1;
		//$g = 0;
		//$l = $k+1;
		//$tree->addToArray($p_fax,"Incoming Fax",0);
		$qryConsult = "SELECT distinct DATE_FORMAT(received_at, '".get_sql_date_format('','Y','/')."') as formCreatedDate 
		from inbound_fax where patient_id='$patient_id' 
		and del_status = '0' and fax_folder = 'consult_letters' ORDER BY `received_at` DESC";
		//$faxQryConsultRes = $objManageData->mysqlifetchdata($qryConsult);
		$rez = sqlStatement($qryConsult);
		$faxQryConsultRes = array();
		for($i=1;$row=sqlFetchArray($rez);$i++){	$faxQryConsultRes[]=$row;	}
		
		$faxQryRes = array();
		
		$faxQryRes = array_merge($faxQryConsultRes,$faxQryRes);
		array_unique($faxQryRes);
		rsort($faxQryRes);
		
		$faxQryResNew = array();
		$tmpDtArr = array();
		for($d_fax_tmp=0;$d_fax_tmp<count($faxQryRes);$d_fax_tmp++){
			$aTemp =0;
			if(!in_array($faxQryRes[$d_fax_tmp]['formCreatedDate'],$tmpDtArr)) {
				$tmpDtArr[] = $faxQryRes[$d_fax_tmp]['formCreatedDate'];
				$faxQryResNew[$d_fax_tmp]['formCreatedDate'] = $faxQryRes[$d_fax_tmp]['formCreatedDate'];	
				$aTemp++;
			}
		}
		$str_incoming="";
		for($d_fax=0;$d_fax<count($faxQryResNew);$d_fax++){
			$q++;
			$formCreatedDate = $faxQryResNew[$d_fax]['formCreatedDate'];
			$patient_consult_id = $faxQryResNew[$d_fax]['patient_consult_id']; 
			//if(trim($formCreatedDate)) {
			//	$tree->addToArray($q,$formCreatedDate,$p_fax,"");
			//}
			
			$qryfaxConsult = "SELECT `id`, `from_number`, `files`, `message`, date_format(`received_at`, '%m-%d-%Y') AS 'date_received', date_format(`received_at`, '%h:%i %p') AS 'time_received'
						from inbound_fax 
						where patient_id = '$patient_id' 
						and DATE_FORMAT(received_at, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' 
						and del_status = '0' and fax_folder = 'consult_letters' ORDER BY `received_at` DESC"; 			
			//$resfaxConsult = ManageData::getQryRes($qryfaxConsult);	
			$rez = sqlStatement($qryfaxConsult);
			$resfaxConsult = array();
			for($i=1;$row=sqlFetchArray($rez);$i++){	$resfaxConsult[]=$row;	}	
			
			$patientConsultLetter = array();						
			
			$l=$q+1;
			$g=$q;
			for($x=0;$x<count($resfaxConsult);$x++,$p++){
				$resfaxConsult[$x]['sendfaxlogId'] = '';
			}
			
			$faxTimeArr = array_merge($resfaxConsult,$patientConsultLetter);
			$sort = array();
			foreach($faxTimeArr as $k=>$v) {
				$sort['cur_date'][$k] = $v['cur_date'];
			}
			array_multisort($sort['cur_date'], SORT_ASC, $faxTimeArr);
			$str_pt_con_ltr_Incoming="";
			for($x=0;$x<count($faxTimeArr);$x++,$p++){
				$mod_date = date("g:i A",strtotime($faxTimeArr[$x]['cur_date']));
				$l++;
				$sendfaxlogId  = $faxTimeArr[$x]['sendfaxlogId'];
				$consentFormId = $faxTimeArr[$x]['template_id'];
				$operator_id = $faxTimeArr[$x]['operator_id'];
				$patient_consult_id = $faxTimeArr[$x]['patient_consult_id'];
				// print_r($faxTimeArr[$x]['files']);
				$consentFormName = $faxTimeArr[$x]['files'];//."(".$form_created_time.")";
				$consentFormName = trim(ucwords($consentFormName));
				$consentFormName = $consentFormName.'('.$mod_date.' '.$phyNameArr[$operator_id].')';
				$consentFormName = ($faxTimeArr[$x]['operator_id']!=='')?$consentFormName.' - '.core_phone_format($faxTimeArr[$x]['from_number']) : $consentFormName;
				
				/* $tree->addToArray($l,$consentFormName,$g,$GLOBALS['webroot'].'/interface/main/uploaddir/fax_files/'.$faxTimeArr[$x]['files'], $targetArea,"../../images/pdf_icon[1].png","../../images/b_drop.png","tree4consult_letter.php?moveToTrashConsentId=$sendfaxlogId&moveToTrashConsentOriginalId=$patient_consult_id&st=1","consent_tree","Move To Trash"); */
				//$tree->addToArray($l,$consentFormName,$g,$GLOBALS['webroot'].'/interface/main/uploaddir/fax_files/'.$faxTimeArr[$x]['files'], $targetArea,"../../images/pdf_icon[1].png","../../images/restore_icon_1.png","tree4consult_letter.php?moveToPendingFax=".$faxTimeArr[$x]['id'],"consent_tree",'Move to Pending');
				$link = $GLOBALS['webroot'].'/interface/main/uploaddir/fax_files/'.$faxTimeArr[$x]['files'];
				
				$str_pt_con_ltr_incoming.="<li class=\"list-group-item\"><a data-url=\"".$link."\" data-target=\"consent_data\" href=\"javascript:void(0);\" onclick=\"opConsult('',this)\" ><span class=\"glyphicon glyphicon-file\"></span>".$consentFormName."</a>"; 
				$str_pt_con_ltr_incoming.="<a href=\"javascript:void(0);\" onclick=\"opConsult('&moveToPendingFax=".$faxTimeArr[$x]['id']."')\"><span class=\"glyphicon glyphicon-share-alt\" title=\"Move to Pending\"></span></a></li>"; 
				
			
			}			
			$q=$l;
			
			//--
			if(!empty($str_pt_con_ltr_Incoming)){
				$str_incoming .= '<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse'.$indx.'">'.$formCreatedDate.'</a>
										</h4>
									</div>
									<div id="collapse'.$indx.'" class="panel-collapse collapse">
										<ul class="list-group">
										'.$str_pt_con_ltr_Incoming.'
										</ul>
									</div>
								</div>
							</div>';
				$indx++;			
			}			
		} /**/	
		
		if(!empty($str_incoming)){
			$str_incoming='<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse_incoming'.$p_fax.'">Incoming Fax</a>
									</h4>
								</div>
								<div id="collapse_incoming'.$p_fax.'" class="panel-collapse collapse">
									<div class="panel-body">'.$str_incoming.'</div>
								</div>
							</div>
						</div>';
			$html_left_pane.=$str_incoming;				
		}
		
		//End start code for display Incoming fax Form information
		
		
		// if move to trash then update left pan only
		if(!empty($moveToTrashConsentId) || !empty($moveToTrashConsentOriginalId)){
			echo $html_left_pane;
			exit();
		}
		
		//fax log
		$fax_log_btn_show="0";
		if(is_updox('fax')){
			$fax_log_btn_show="1";
		}
		
		//--
		$fax_btn_show="0";
		if($fax_log_btn_show=="1" || is_interfax()) {
			$fax_btn_show="1";
		}
		//--
		
		//
		$email_btn_show="0";
		$oAdmn = new Admn();
		$groupEmailConfig = $oAdmn->getGroupInfo();		
		if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host']){
			$email_btn_show="1";
		}
		
		include($GLOBALS['fileroot']."/interface/chart_notes/view/pt_consult_letter.php");
	}
	
	function load_pdf(){		
		$oReferringPhy = new ReferringPhy();
		
		$performed = "Not Performed";
		$tempId = $_REQUEST['tempId'];
		$patientId = $pat_id = $this->pid;
		$doc_id = $_SESSION['authId'];
		$formId = $this->fid;
		
		$today = date('Y-m-d');
		$consultTemplate = $_REQUEST['tempId'];
		$consultTemplateId = $_REQUEST['consult_form_id'];
		$templateId = $_REQUEST['templateList'];
		
		$templateIds = explode("!~!", $templateId);
		$templateId = $templateIds[0];
		$NameTemplate = $templateIds[1];
		$changeTemplate = $templateId;
		$consultTemplate = $templateId;
		$selectedList = $_REQUEST['selectedList'];
		$patientTempName = $_REQUEST['patientTempName'];
		if($_REQUEST['templateId']){
			$consultTemplate = $_REQUEST['templateId'];
		}	
		
		// patient_consult_letter_tbl
		$topMargin=$leftMargin=0;
		$refPhyAndFax			= "";
		$refPhyAndEmail			= "";
		
		$sql = "SELECT * from patient_consult_letter_tbl WHERE patient_consult_id  = '".$tempId."' ";		
		$getMasStatusRows = sqlQuery($sql);
		if($getMasStatusRows!=false){
			
			$PatientConsultId 		= $getMasStatusRows['patient_consult_id'];
			$patientConsultstatus 	= $getMasStatusRows['status'];
			$otherTemplateName 		= $getMasStatusRows['templateName'];
			$consultTemplateData 	= $getMasStatusRows['templateData'];			
			$consultEmailStatus		= $getMasStatusRows['email_status'];
			$consultFaxStatus 		= $getMasStatusRows['fax_status'];
			$topMargin 				= $getMasStatusRows['top_margin'];
			$leftMargin 			= $getMasStatusRows['left_margin'];	
			$patientConsultLetterTo	= $getMasStatusRows['patient_consult_letter_to'];
			$faxNumber 				= $getMasStatusRows['fax_number'];
			$emailId				= $getMasStatusRows['email_id'];
			$preffered_reff_email	=$getMasStatusRows['email_id'];
			$refPhyId 				= $getMasStatusRows['fax_ref_phy_id'];
			$preffered_reff_fax		= str_ireplace("'","",$getMasStatusRows['preffered_reff_fax']);
			if($refPhyId) {
				$arr_reff_phy = $oReferringPhy->get_reffphysician_detail($refPhyId);
				$refPhyAndFax = $arr_reff_phy["fax"]; 
				$refPhyAndEmail = $arr_reff_phy["email"];
			}
			if($consultFaxStatus=="1"){				
				//$refPhyFaxNo= $getMasStatusRows['fax_number'];
				if($faxNumber) {
					$refPhyAndFax=  $patientConsultLetterTo."@@".$faxNumber;			
				}
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id'];
				$cc2_ref_phy_id	= $getMasStatusRows['cc2_ref_phy_id'];
				$cc3_ref_phy_id	= $getMasStatusRows['cc3_ref_phy_id'];
			}
			//================STARTS HERE==================================================================
			//CC1 ID USED TO DEFAULT FILLED CC1 FAX NAME AND NO. FOR SAVED CONSULT LETTERS SEND FAX POPUP
			if($getMasStatusRows['cc1_ref_phy_id']!=0 && $getMasStatusRows['cc1_ref_phy_id']!=''){
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id']; 
			}
			//=================ENDS HERE===================================================================
			   
			if($consultEmailStatus=="1"){				
				if($emailId) {
					$refPhyAndEmail=  $patientConsultLetterTo."@@".$emailId;			
				}
				$cc1_ref_phy_id	= $getMasStatusRows['cc1_ref_phy_id'];
				$cc2_ref_phy_id	= $getMasStatusRows['cc2_ref_phy_id'];
				$cc3_ref_phy_id	= $getMasStatusRows['cc3_ref_phy_id'];	
			}
			
			//$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U';
			$regpattern='|<a class="cls_smart_tags_link" href=(.*) id=(.*)>(.*)</a>|U';  
			$consultTemplateData = preg_replace($regpattern, "\\3", $consultTemplateData);
			$consultTemplateData = str_ireplace('<od br=""', '&lt;od', $consultTemplateData);
			$consultTemplateData = str_ireplace("<od br=''", "&lt;od", $consultTemplateData);
			$consultTemplateData = str_ireplace('</od>', '', $consultTemplateData);
			$consultTemplateData = str_ireplace('vision="">', 'vision', $consultTemplateData);			
			$Host = $_SERVER['HTTP_HOST'];
			if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
			$consultTemplateDataPage = '';if(($topMargin==0 || $topMargin=="") && (strstr($consultTemplateData,"<page_header>"))){$topMargin=5;}			
			$consultTemplateDataPage ='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="15">'.$consultTemplateData.'</page>';			
			$consultTemplateDataPage = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace("/".$web_RootDirectoryName."/interface/common/new_html2pdf/","",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace($protocol.$Host.$web_root."/interface/main/uploaddir/",$web_root."/interface/main/uploaddir/",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace($web_root."/interface/main/uploaddir/","../../main/uploaddir/",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace($protocol.$Host.$web_root."/redactor/images/",$web_root."/redactor/images/",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace($web_root."/redactor/images/","../../../redactor/images/",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace("�","",$consultTemplateDataPage);
			$consultTemplateDataPage = str_ireplace("&shy;","",$consultTemplateDataPage);
			$consultTemplateDataPage = rawurldecode($consultTemplateDataPage); //For decoding %## codes like %20 => ' '
		}
		
		$fp = "/tmp/pdffile.html";			
		//
		$oSaveFile = new SaveFile($_SESSION["authId"],1);
		$resp = $oSaveFile->cr_file($fp,$consultTemplateDataPage);		
		
		$getPCIP=$_SESSION["authId"];	
		$setNameFaxPDF="savedFax_".$getPCIP;
		
		$printOptionStyle='p';
		header("location: ".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?setIgnoreAuth=true&op=".$printOptionStyle."&file_location=".$resp."&pdf_name=".$setNameFaxPDF."&saveOption=savedFax&onePage=false");
		exit();
	}
	
	function send_email(){
		$oAdmn = new Admn();
		$groupEmailConfig = $oAdmn->getGroupInfo();
		$txtEmailPdfName     	= $_REQUEST['txtEmailPdfName'];	
		$emailID         	= $_REQUEST['txtEmailId'];
		$emailIDname       	= $_REQUEST['txtEmailIdName'];
		$emailIDCc1         = $_REQUEST['txtEmailIdCc1'];
		$emailIDname       	= $_REQUEST['txtEmailIdNameCc1'];
		$emailIDCc2         = $_REQUEST['txtEmailIdCc2'];
		$emailIDname       	= $_REQUEST['txtEmailIdNameCc2'];
		$emailIDCc3         = $_REQUEST['txtEmailIdCc3'];
		$emailIDname       	= $_REQUEST['txtEmailIdNameCc3'];
		
		$filename= $GLOBALS['incdir'].'/../library/html_to_pdf/'.$txtEmailPdfName.'.pdf';
		$filetype= 'PDF';
		if(!($fp = fopen($filename, "r"))){
			echo "Error opening PDF file - $filename";
			exit;
		}
		
		//send smtp mail here
		// require_once $GLOBALS['incdir'].'/../library/phpmailer/PHPMailerAutoload.php';
		if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'])
		{
			//Create a new PHPMailer instance
			$mail = new PHPMailer\PHPMailer;
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug = 0;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host = $groupEmailConfig['host'];
			//Set the SMTP port number - likely to be 25, 465 or 587
			$mail->Port = $groupEmailConfig['port'];
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication
			$mail->Username = $groupEmailConfig['email'];
			//Password to use for SMTP authentication
			$mail->Password = $groupEmailConfig['pwd'];
			//Set who the message is to be sent from
			$mail->setFrom($groupEmailConfig['email'], '');
			//Set an alternative reply-to address
			//$mail->addReplyTo('replyto@example.com', 'First Last');
			//Set who the message is to be sent to
			
			if($emailID)$mail->addAddress($emailID,$emailIDname);
			if($emailIDCc1)$mail->addCC($emailIDCc1,$emailIDnameCc1);
			if($emailIDCc2)$mail->addCC($emailIDCc2,$emailIDnameCc2);
			if($emailIDCc3)$mail->addCC($emailIDCc3,$emailIDnameCc3);
			//Set the subject line
			$mail->Subject = 'imwemr Consult Letter';
			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$mail->msgHTML($groupEmailConfig['header']."<br/>Please find enclosed Consult Letter.<br/>".$groupEmailConfig['footer']);
			//Replace the plain text body with one created manually
			$mail->AltBody = '';
			//Attach an image file
			if($filename)$mail->addAttachment($filename);
			
			//send the message, check for errors
			if (!$mail->send()) {
				$returnErrMsg= "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$returnSuccMsg= "Message sent!";
			}
			
		}
		else
		{
			$returnErrMsg= "Mailer Error: Insufficent authnication data";		
		}
		//code end to send fax to Cc1, Cc2, Cc3	
		if($returnSuccMsg!=''){
			echo(" Mail sent successfully ");
		}
		
		if($returnErrMsg){
			echo $returnErrMsg;
		}
		
		if(!$returnErrMsg && !$returnSuccMsg)echo "Oops! Somthing went wrong.";
	}//
	
	function send_fax_log(){
		$patientId = $this->pid;
		
		//========GET CONSULT LETTER DATA BASED ON PROVIDED ID===========
		//========HTML & PDF CREATION====================================
		if(isset($_GET['get_html']) && $_GET['get_html'] == 'yes'){

			$consultPDFData = "";
			$consultId = $_POST['id'];

			//=======PATIENT CONSULT DATA GET ON BASED OF CONSULT ID=====
			$sql = "SELECT templateData, top_margin, left_margin FROM `patient_consult_letter_tbl` WHERE patient_consult_id='".$consultId."'";
			$consultDataRes = sqlQuery($sql);

			//===========CONSULT TEMPLATE DATA==============
			$consultPDFData	= $consultDataRes['templateData'];
			$topMargin		= $consultDataRes['top_margin'];
			$leftMargin		= $consultDataRes['left_margin'];

			//==========PDF MARGIN ADJUSTING WORK===========
			if(($topMargin==0 || $topMargin=="") && (strstr($consultPDFData,"<page_header>"))){$topMargin=5;}
			$consultPDFData ='<page backtop="'.$topMargin.'" backleft="'.$leftMargin.'" backbottom="15">'.$consultPDFData.'</page>';
			//==========IMAGES PATH REPLACEMENTS============
			global $web_root;
			$consultPDFData = str_ireplace($web_root."/interface/common/new_html2pdf/","",$consultPDFData);
			$consultPDFData = str_ireplace($web_root."/interface/reports/new_html2pdf/","",$consultPDFData);
			$consultPDFData = str_ireplace($web_root."/interface/main/uploaddir/document_logos/","../../main/uploaddir/document_logos/",$consultPDFData);
			$consultPDFData = str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$consultPDFData);

			//==========HTML FILE WRITE & PDF CREATION=======		
			$fp = "/tmp/Faxlog.html";		
			$oSaveFile = new SaveFile($_SESSION["authId"],1);
			$resp = $oSaveFile->cr_file($fp,$consultPDFData);		
			header("location: ".$GLOBALS['webroot']."/library/html_to_pdf/createPdf.php?op=P&file_location=".$resp."&pdf_name=Faxlog&saveOption=savedFax&onePage=false");
			exit;
		}
		//=======================ENDS HERE=================================
		
		$opt = new Patient($patientId);
		$patientName = $opt->getName();
		
		$str_send_fax_log="";
		//=========DISPLAY THE FAX LOG ON BASED OF UPDOX ID==========
		$sql = "SELECT patient_consult_id, template_id, template_name, cur_date_time, section_name, updox_id, updox_status, fax_type, `file_name` FROM `send_fax_log_tbl` WHERE patient_id='".$patientId."' ORDER BY id DESC";
		$rez = sqlStatement($sql);
		if($rez && imw_num_rows($rez)>0){
			
			for($i=1; $row=sqlFetchArray($rez); $i++){
				$template_name = trim($row['template_name']);
				$cur_date_time = trim($row['cur_date_time']);
				$section_name =  trim($row['section_name']);
				$updox_id =  trim($row['updox_id']);
				$updox_status =  trim($row['updox_status']);
				$patientConsultId = trim($row['patient_consult_id']);
				$file_name = trim($row['file_name']);
				$fax_type = trim($row['fax_type']);
				if($fax_type=="Primary"){
					$fax_type = "Referring Physician";
				}
				
				$str_send_fax_log.="".
				"<tr onClick=\"get_pdf('".$file_name."');\">".
					"<td >".date('m-d-y'.' h:i',strtotime($cur_date_time))."</td>".
					"<td >".$fax_type."</td>".
					"<td >".ucfirst($section_name)."</td>".
					"<td >".ucfirst($template_name)."</td>".
					"<td >".$updox_id."</td>".
					"<td >".ucfirst($updox_status)."</td>".
				"</tr>";
			
			}
			
		}
		
		//$pt_up_dir=
		$oSaveFile =  new SaveFile($this->pid);
		$up_dir = $oSaveFile->getUploadDirPath();
		$pt_up_dir= $up_dir."/PatientId_".$patientId."/fax_log";
		
		
		//str
		if(empty($str_send_fax_log)){
			$str_send_fax_log = "<tr><td colspan=\"6\">No Record Exists</td></tr>";
		}
	
		include($GLOBALS['incdir']."/chart_notes/view/send_fax_log.php");
	
	}
	
	//Check whether requested provider has direct address or not
	public function checkDirectAddress($physicianId = array(), $chkLogic = ''){
		
		if(empty($physicianId) || count($physicianId) == 0) return false;
		$returnVal = false;
		
		if(empty($chkLogic)){
			if(count($physicianId) > 0) $physicianId = implode(',', $physicianId);
			//Check Reffer Physician Table for direct email address
			$sqlQry = imw_query(' SELECT direct_email FROM refferphysician WHERE physician_Reffer_id IN ('.$physicianId.') ');
			if($sqlQry && imw_num_rows($sqlQry) > 0){
				while($rowFetch = imw_fetch_assoc($sqlQry)){
					$refDirectMail = filter_var($rowFetch['direct_email'], FILTER_SANITIZE_EMAIL);
					if(empty($refDirectMail) == false) $returnVal = true;
					
					if($returnVal == true) break;
				}
			}
		}else{
			if(count($physicianId) > 0){
				$tmpArr = array();
				foreach($physicianId as $refId){
					$getSql = " SELECT count(id) as count FROM ref_multi_direct_mail WHERE ref_id = ".$refId." AND del_status = 0 ORDER BY id ASC ";
					$resSql = imw_query($getSql) or die(imw_error());

					if($resSql && imw_num_rows($resSql) > 0){
						$row = imw_fetch_assoc($resSql);
						$countRow = $row['count'];

						if($countRow > 0) $tmpArr[$refId] = $countRow;
					}
				}
				if(count($tmpArr) > 0) $returnVal = true;

				if($returnVal == false){
					if(count($physicianId) > 0) $physicianId = implode(',', $physicianId);
					//Check Reffer Physician Table for direct email address
					$sqlQry = imw_query(' SELECT direct_email FROM refferphysician WHERE physician_Reffer_id IN ('.$physicianId.') ');
					if($sqlQry && imw_num_rows($sqlQry) > 0){
						while($rowFetch = imw_fetch_assoc($sqlQry)){
							$refDirectMail = filter_var($rowFetch['direct_email'], FILTER_SANITIZE_EMAIL);
							if(empty($refDirectMail) == false) $returnVal = true;
							
							if($returnVal == true) break;
						}
					}
				}
			}
		}

		return $returnVal;
	}
	
}

?>