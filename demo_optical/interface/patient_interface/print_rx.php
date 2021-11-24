<?php 

require_once(dirname('__FILE__')."/../../config/config.php");
require_once($GLOBALS['DIR_PATH']."/library/classes/functions.php"); 

$dos_date=$_REQUEST['dos_date'];
$dt=explode('-', $dos_date);
$dos = $dt[2].'-'.$dt[0].'-'.$dt[1];
$dosDB = $dos;
$pat_id=$_SESSION['patient_session_id'];


//GETTING SIGNATURE
$ImgSignature=$gdFilenamePath='';
$q= "SELECT 
chart_master_table.id, chart_master_table.providerId, chart_master_table.finalize, chart_master_table.finalizerId 
FROM chart_master_table
LEFT JOIN chart_vis_master ON chart_vis_master.form_id = chart_master_table.id
LEFT JOIN chart_pc_mr ON chart_pc_mr.id_chart_vis_master = chart_vis_master.id 
WHERE chart_master_table.patient_id = '".$pat_id."' AND chart_master_table.date_of_service='".$dosDB."' 
ORDER BY chart_master_table.date_of_service DESC , chart_pc_mr.exam_date DESC limit 1";

$qryGetProvider=imw_query($q);
if(imw_num_rows($qryGetProvider)>0){

	extract(imw_fetch_array($qryGetProvider));
	if($providerId>0){

		if($providerId){
			if($finalize=='1'){
				$providerId = $finalizerId;
			}
			$getNameQry = imw_query("SELECT CONCAT_WS(' ',pro_title, fname, lname, pro_suffix) as PHYSICIANNAME,fname,mname,lname,pro_suffix,licence,user_npi,sign_path FROM users WHERE id = '".$providerId."'");
			$getNameRow = imw_fetch_assoc($getNameQry);
			$physicianFname=$physicianMname=$physicianLname=$physicianName=$physicianSuffix=$physicianLicence=$physicianNpi="";
			$physicianFname	=	$getNameRow['fname'];
			$physicianMname	=	$getNameRow['mname'];
			$physicianLname	=	$getNameRow['lname'];	
			$physicianName	=	$getNameRow['PHYSICIANNAME'];
			$physicianSuffix=	$getNameRow['pro_suffix'];
			$physicianLicence=	$getNameRow['licence'];
			$physicianNpi	=	$getNameRow['user_npi'];
			$sign_path		=	$getNameRow['sign_path'];
		}

		
		//SINGATURE PATH RECREATED FOR R8 OR R7
		$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
		
		if(empty($r8PracticeName[3]))
		{
		 $r8PracticeName[3]=$GLOBALS['SUB_DOMAIN']; //THIS PARAMETER SETS FOR CASE OF HCCS SERVERS
		}
		if(file_exists($GLOBALS['IMW_DIR_PATH'].'/interface/main/uploaddir'.$sign_path)){
			$sig_full_path=$GLOBALS['IMW_DIR_PATH'].'/interface/main/uploaddir'.$sign_path;
		}else{ 
			$sig_full_path=$GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3].$sign_path;	
		}
		if($sign_path && file_exists(trim($sig_full_path))){
			$gdFilenamePath= trim($sig_full_path);
		}else{
			$id = $providerId;
			$tblName = "users";
			$pixelFieldName = "sign";
			$idFieldName = "id";
			$imgPath = "";
			$saveImg = "3";
			
			//FILE INCLUDED FOR FUNCTION "drawOnImage_new" FROM R8 OR R7
			if(file_exists($GLOBALS['IMW_DIR_PATH']."/library/classes/imgGdFun.php")){
				include_once($GLOBALS['IMW_DIR_PATH']."/library/classes/imgGdFun.php");
			}else{
				include_once($GLOBALS['IMW_DIR_PATH']."/interface/main/imgGdFun.php");
			}
			
			$qry = "SELECT $pixelFieldName FROM $tblName WHERE $idFieldName = $id";		
			$rs = imw_query($qry);	
			$res=imw_fetch_assoc($rs);
			$pixels = $res[$pixelFieldName];

			//Get Image	
			drawOnImage_new($pixels,$imgName,$saveImg); 
			
			$gdFilenamePath= $GLOBALS['IMW_DIR_PATH']."/interface/common/new_html2pdf/tmp/".$gdFilename;
		}/*
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
			
			$gdFilenamePath=realpath(dirname(__FILE__)."/".$gdFilenamePath);
			$ChartNoteImagesString[]=$gdFilenamePath;
		}*/

		if(file_exists(trim($gdFilenamePath))){
			$ImgSignature = "<img align='left' border='0' src='".$gdFilenamePath."' height='83' width='225'>";
		}
	}	
}
//-----------------------

if($_REQUEST['givenMr'])
{
$print_type=$_REQUEST['printType'];
$print_id=$_REQUEST['chartIdPRS'];
$mr=$_REQUEST['givenMr'];


$data1="</br></br><h1>Lens Prescription</h1></br>";
/*
$cols = "chart_master_table.date_of_service,chart_master_table.facilityid,";

if($mr=="MR1"){
	$cols .= "vis_mr_od_s AS 'OD_S',";
	$cols .= "vis_mr_od_c AS 'OD_C',";
	$cols .= "vis_mr_od_a AS 'OD_AX',";
	$cols .= "vis_mr_od_add AS 'OD_AD',";
	$cols .= "vis_mr_od_p AS 'OD_PR',";
	$cols .= "vis_mr_od_prism AS 'OD_PRISM',";
	$cols .= "vis_mr_od_slash AS 'OD_SPL',";
	$cols .= "vis_mr_od_sel_1 AS 'OD_SEL',";
	$cols .= "vis_mr_os_s AS 'OS_S',";
	$cols .= "vis_mr_os_c AS 'OS_C',";
	$cols .= "vis_mr_os_a AS 'OS_AX',";
	$cols .= "vis_mr_os_add AS 'OS_AD',";
	$cols .= "vis_mr_os_p AS 'OS_PR',";
	$cols .= "vis_mr_os_prism AS 'OS_PRISM',";
	$cols .= "vis_mr_os_slash AS 'OS_SPL',";
	$cols .= "vis_mr_os_sel_1 AS 'OS_SEL',";
	$cols .= "vis_mr_desc AS 'NOTES'";
	
}*/

	//========GET iDOC GLASSES RX TEMPLATE FROM DATABASE========
	$qryiDOCTemplate = "SELECT `prescription_template_content` FROM `prescription_template` WHERE id=1";
	$rowiDOCTemplate = imw_query($qryiDOCTemplate);
	$getiDOCTemplate = imw_fetch_assoc($rowiDOCTemplate);
	
	$glRXTemplate	 = $getiDOCTemplate['prescription_template_content'];	 //GL RX TEMPLATE DATA
if($mr=="custom"){
	$RXiD = trim($_REQUEST['chartIdPRS']);
	$query=imw_query("SELECT `sphere_od` AS 'OD_S', `cyl_od` AS 'OD_C', `axis_od` AS 'OD_AX', `add_od` AS 'OD_AD',
						`sphere_os` AS 'OS_S', `cyl_os` AS 'OS_C', `axis_os` AS 'OS_AX', `add_os` AS 'OS_AD',
						`entered_date` AS 'date_of_service'
						FROM `in_optical_order_form`
						WHERE `id`='".$RXiD."'");
}
else{
 	$q="SELECT 
			c1.*,
			c2.sph as `OD_S`, c2.cyl as `OD_C`, c2.axs as `OD_AX`, c2.ad as `OD_AD`, c2.prsm_p as `OD_PR`, c2.prism as `OD_PRISM`, c2.slash as `OD_SPL`, 
			c2.sel_1 as `OD_SEL`, 			
			c3.sph as `OS_S`, c3.cyl as `OS_C`, c3.axs as `OS_AX`, c3.ad as `OS_AD`, c3.prsm_p as `OS_PR`, c3.prism as `OS_PRISM`, c3.slash as `OS_SPL`, 
			c3.sel_1 as `OD_SEL`, 
			c1.ex_desc as `NOTES`
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.form_id='".$print_id."' AND c0.patient_id = '".$pat_id."' AND c1.ex_type='MR' AND c1.mr_none_given='".$mr."' AND c1.delete_by='0'  
			Order By ex_number";
	$query=imw_query($q);
}
if(imw_num_rows($query)>0)
{
	//$data.="<table><tr><th>DOS</th><th>Vision</th><th>Sphere</th><th>Cylinder</th><th>Axis</th><th>Add</th><th>Prism</th></tr>";
	$data = stripslashes($glRXTemplate);
	while($row=imw_fetch_array($query))
	{  
		$prism_OD='';
		$prism_OS='';
		$dt=explode('-', $dos);
		$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
		
		$loc=imw_query("select * from patient_data where id=".$pat_id);
		$loc_fetch=imw_fetch_array($loc);
		
		$locID=$locTitle =$locFname =$locLname =$locDOB =$locStreet =$locStreet2 =$locCity =$locState =$locPostalCode =$locHomePh =$locWorkPh = $locMobilePh= $patientFullAddress= $stateZipCode=""; 
		
		$locID		=	$loc_fetch['id'];
		$locTitle	=	$loc_fetch['title'];
		$locFname	=	$loc_fetch['fname'];
		$locLname	=	$loc_fetch['lname'];
		$locDOB		=	$loc_fetch['DOB'];
		$locStreet	=	$loc_fetch['street'];
		$locStreet2 =	$loc_fetch['street2'];
		$locCity	=	$loc_fetch['city'];
		$locState	=	$loc_fetch['state'];
		$locPostalCode =$loc_fetch['postal_code'];
		$locHomePh	=$loc_fetch['phone_home'];
		$locWorkPh  =$loc_fetch['phone_biz'];
		$locMobilePh =$loc_fetch['phone_cell'];
		
		
		$patientFullAddress = $locStreet;
		if(!empty($locStreet2)){$patientFullAddress.= $locStreet2.", ";}
		if(!empty($locCity)){$patientFullAddress .= $locCity.", "; }
		if(!empty($locState)){$patientFullAddress .= $arrPtInfo["state"].", ";}
		if(!empty($locPostalCode)){$patientFullAddress .= $locPostalCode.", ";}
		
		if($locState && $locPostalCode){
			$stateZipCode= $locState.', '.$locPostalCode;
		}else if($locState){ $stateZipCode= $locState; 
		}else{ $stateZipCode = $locPostalCode; }
	
		//======GL RX DATA REPLACEMENT WORK START HERE==================
		//------IMAGE REPLACE WORK---------------
		$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
		$data = str_ireplace('/'.$r8PracticeName[3].'/data/'.$r8PracticeName[3].'/gn_images',$GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3].'/gn_images',$data);	
			
		//------PT. DEMOGRAPHIC VARIABLE DATA REPLACEMENT WORK---------------
		$data = str_ireplace('{DATE}',date('m-d-y'),$data);	
		$data = str_ireplace('{PATIENT FIRST NAME}',$locFname,$data);			
		$data = str_ireplace('{LAST NAME}',$locLname,$data);			
		$data = str_ireplace('{PATIENT NAME TITLE}',$locTitle,$data);			
		$data = str_ireplace('{DOB}',date("m-d-Y", strtotime($locDOB)),$data);			
		$data = str_ireplace('{PatientID}',$locID,$data);			
		$data = str_ireplace('{ADDRESS1}',$locStreet,$data);		
		$data = str_ireplace('{ADDRESS2}',$locStreet2,$data);		
		$data = str_ireplace('{PATIENT CITY}',$locCity,$data);		
		$data = str_ireplace('{FULL ADDRESS}',$patientFullAddress,$data);
		$data = str_ireplace('{STATE ZIP CODE}',$stateZipCode,$data);		
		$data = str_ireplace('{HOME PHONE}',$locHomePh,$data);		
		$data = str_ireplace('{WORK PHONE}',$locWorkPh,$data);		
		$data = str_ireplace('{MOBILE PHONE}',$locMobilePh,$data);		
		$data = str_ireplace('{PHYSICIAN NAME}',$physicianName,$data);		
		$data = str_ireplace('{PHYSICIAN FIRST NAME}',$physicianFname,$data);		
		$data = str_ireplace('{PHYSICIAN MIDDLE NAME}',$physicianMname,$data);
		$data = str_ireplace('{PHYSICIAN LAST NAME}',$physicianLname,$data);
		$data = str_ireplace('{PHYSICIAN NAME SUFFIX}',$physicianSuffix,$data);
		$data = str_ireplace('{PRIMARY LICENCE NUMBER}',$physicianLicence,$data);		
		$data = str_ireplace('{PHYSICIAN NPI}',$physicianNpi,$data);	
		$data = str_ireplace('{PHYSICIAN NAME SUFFIX}',$physicianSuffix,$data);	
		$data = str_ireplace('{SIGNATURE}',$ImgSignature,$data);	
		
		$data = str_ireplace('{TEXTBOX_XSMALL}',"",$data);	
		$data = str_ireplace('{TEXTBOX_SMALL}',"",$data);	
		$data = str_ireplace('{TEXTBOX_MEDIUM}',"",$data);	
		
		//------GLASSES VARIABLE DATA REPLACEMENT WORK---------------
		$odSphere=$odCylinder=$odAxis=$odAdd=$odPrism=$odBaseCurve=$odHorizontalPrism=$odVerticalPrism="";
		$osSphere=$osCylinder=$osAxis=$osAdd=$osPrism=$osBaseCurve=$osHorizontalPrism=$osVerticalPrism="";
		$expirationDate=$NOTES ="";
		
		if(!empty($row['OD_S'])){ $odSphere= $row['OD_S']; }
		if(!empty($row['OD_C'])){ $odCylinder= $row['OD_C']; }
		if(!empty($row['OD_AX'])){ $odAxis= $row['OD_AX']."&deg;"; }
		if(!empty($row['OD_AD'])){ $odAdd= $row['OD_AD']; }
		
		if(!empty($row['OS_S'])){ $osSphere= $row['OS_S']; }
		if(!empty($row['OS_C'])){ $osCylinder= $row['OS_C']; }
		if(!empty($row['OS_AX'])){ $osAxis= $row['OS_AX']."&deg;"; }
		if(!empty($row['OS_AD'])){ $osAdd= $row['OS_AD']; }
		
		if(!empty($row['NOTES'])){ $NOTES= $row['NOTES']; }
		
		if(!empty($row['OD_PR']) || !empty($row['OD_SEL']))
		{ 
			$odPrism= $row['OD_PR'].' <img src="../../images/pic_vision_pc.jpg" /> '.$row['OD_SEL']; 
			$odHorizontalPrism=$odPrism;
		}
		
		if(!empty($row['OD_SPL']) || !empty($row['OD_PRISM']))
		{ 
			$odBaseCurve= $row['OD_SPL'].$row['OD_PRISM']; 
			$odVerticalPrism=$odBaseCurve;
		}
		
		if(!empty($row['OS_PR']) || !empty($row['OS_SEL']))
		{
			$osPrism= $row['OS_PR'].' <img src="../../images/pic_vision_pc.jpg" /> '.$row['OD_SEL']; 
			$osHorizontalPrism=$osPrism;
		}
		
		if(!empty($row['OS_SPL']) || !empty($row['OS_PRISM']))
		{
			$osBaseCurve= $row['OS_SPL'].$row['OS_PRISM']; 
			$osVerticalPrism=$osBaseCurve;
		}
		
		list($dos_mnt,$dos_dy,$dos_yr) = explode("-",$dos);
		$dos_mnt = $dos_mnt + 12;
		$expirationDate = date('m-d-Y',mktime(0,0,0,$dos_mnt,$dos_dy,$dos_yr));
	
		
		$data = str_ireplace('{DOS}',$dos,$data);
		$data = str_ireplace('{EXPIRATION DATE}',$expirationDate,$data);		
		$data = str_ireplace('{NOTES}',$NOTES,$data);	
		//-------OD - RIGHT EYE DATE REPLACEMENT---------------	
		$data = str_ireplace('{OD SPHERICAL}',$odSphere,$data);		
		$data = str_ireplace('{OD CYLINDER}',$odCylinder,$data);	
		$data = str_ireplace('{OD AXIS}',$odAxis,$data);	
		$data = str_ireplace('{OD ADD}',$odAdd,$data);
		$data = str_ireplace('{OD PRISM}',$odPrism,$data);	
		$data = str_ireplace('{OD BASE CURVE}',$odBaseCurve,$data);
		$data = str_ireplace('{OD HORIZONTAL PRISM}',$odHorizontalPrism,$data);
		$data = str_ireplace('{OD VERTICAL PRISM}',$odVerticalPrism,$data);
		//-------OS - LEFT EYE DATE REPLACEMENT---------------	
		$data = str_ireplace('{OS SPHERICAL}',$osSphere,$data);		
		$data = str_ireplace('{OS CYLINDER}',$osCylinder,$data);	
		$data = str_ireplace('{OS AXIS}',$osAxis,$data);	
		$data = str_ireplace('{OS ADD}',$osAdd,$data);
		$data = str_ireplace('{OS PRISM}',$osPrism,$data);	
		$data = str_ireplace('{OS BASE CURVE}',$osBaseCurve,$data);
		$data = str_ireplace('{OS HORIZONTAL PRISM}',$osHorizontalPrism,$data);
		$data = str_ireplace('{OS VERTICAL PRISM}',$osVerticalPrism,$data);
		
		//-------APPOINTMENT VARIABLE REPLACEMENT---------------	
		$apptFacInfo =__getApptInfo($pat_id,'','','');
		
		if(!empty($apptFacInfo[10])){
			$apptFacstreet = $apptFacInfo[10].', ';	
		}
		if(!empty($apptFacInfo[11])){
			$apptFaccity = $apptFacInfo[11].', ';	
		}
		if(!empty($apptFacInfo[3])){ $apptFacPhone =  $apptFacInfo[3]; }
		$apptFacAddress =  $apptFacstreet.$apptFaccity.$apptFacInfo[12].'&nbsp;'.$apptFacInfo[13].' - '.$apptFacInfo[3]; 
		
		$data = str_ireplace('{APPT FACILITY NAME}',$apptFacInfo[2],$data);		
		$data = str_ireplace('{APPT FACILITY ADDRESS}',$apptFacAddress,$data);
		$data = str_ireplace('{APPT FACILITY PHONE}',$apptFacPhone,$data);
		
		//BELOW VARIABLE USED IN R8 TO PRINT THE SESSION FACILTY INFORMATION
		$data = str_ireplace('{LOGGED_IN_FACILITY_NAME}','',$data);
		$data = str_ireplace('{LOGGED_IN_FACILITY_ADDRESS}','',$data);
	//============REPLACEMENT WORK ENDS HERE==========================
	
	//===BELOW CODE USED TO REPLACE R8 gn_images FOLDER IMAGES======== 
		$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
	
		if(empty($r8PracticeName[3]))
		{ 
		 $r8PracticeName[3]=$GLOBALS['SUB_DOMAIN']; //SET FOR CASE OF HCCS SERVER
		}
		
		$data = str_ireplace('/data/'.$r8PracticeName[3].'/gn_images/',$GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3].'/gn_images/',$data);
		$data = str_ireplace($GLOBALS['IMW_DIR_PATH'].$GLOBALS['IMW_DIR_PATH'],$GLOBALS['IMW_DIR_PATH'],$data);
	}
}
/*$css.="<style>
table{border:1px solid;border-collapse:collapse}
table td,th{border:1px solid #CCC;border-collapse:collapse;padding:20px;}</style>";*/


if(file_put_contents('../../library/new_html2pdf/print_rx.html',($data)))
{
echo "<script>
	var url='".$GLOBALS['WEB_PATH']."/library/new_html2pdf/createPdf.php?op=p&file_name=print_rx';
	var ptwin=window.location=url;</script>";
}
}
else if($_REQUEST['scl_type'])
{
	$data="";
	$cl_type=$_REQUEST['scl_type'];
	$pat_id=$_SESSION['patient_session_id'];
	$data1="</br></br><h1>Contact Lens Prescription</h1></br>";
	if(strtolower($cl_type)=='custom')
	{
		$q=imw_query("select * from in_cl_prescriptions where patient_id='$pat_id' and id=$_REQUEST[workSheetId]");
		while($res=imw_fetch_assoc($q))
		{
			$dos= '';
			$cnt++;
			$dt=explode('-', $res['rx_dos']);
			$dos = $dt[1].'-'.$dt[2].'-'.$dt[0];
			$dos1 = $dt[1].'-'.$dt[2].'-'.substr($dt[0],-2);
			$dos3 =str_replace("-", "", $res['rx_dos']);

			$clType='';
			
			$arrRxDetails[$dos3][$cnt]['clType']='Custom';
			$arrRxDetails[$dos3][$cnt]['custom_id'] 	= $res['id'];

			$arrRxDetails[$dos3][$cnt]['DOS'] 	= $dos;
			$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['physician_id'];
			$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $res['physician_name'];


			$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
			$arrRxDetails[$dos3][$cnt]['make_od'] = $res['rx_make_od'];
			$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['sphere_od'];
			$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['cylinder_od'];
			$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['axis_od']."&deg;";
			$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['diameter_od'];
			$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['base_od'];
			$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['add_od'];
			

			$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
			$arrRxDetails[$dos3][$cnt]['make_os'] = $res['rx_make_os'];
			$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['sphere_os'];
			$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['cylinder_os'];
			$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['axis_os']."&deg;";
			$arrRxDetails[$dos3][$cnt]['orderHxDia_OS'] = $res['diameter_os'];
			$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['base_os'];
			$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS'] = $res['add_os'];
			$arrRxDetails[$dos3][$cnt]['outside_rx']	=1;
		}
	}
	else{
		
		$cl_type=(strtolower($cl_type)=='custom rgp')? 'cust_rgp' : $cl_type;
		
	$qry="Select clMaster.clws_id, clMaster.clws_type, clMaster.clws_trial_number, DATE_FORMAT(clMaster.dos, '%m-%d-%Y') as 'DOS', clMaster.provider_id, clDet.id, clDet.clEye, clDet.clType, clDet.SclsphereOD, clDet.SclCylinderOD, clDet.SclaxisOD, clDet.SclBcurveOD, clDet.SclDiameterOD, clDet.SclAddOD,
clDet.SclsphereOS, clDet.SclCylinderOS, clDet.SclaxisOS, clDet.SclBcurveOS, clDet.SclDiameterOS, clDet.SclAddOS,
clDet.RgpPowerOD, clDet.RgpBCOD ,clDet.RgpDiameterOD, clDet.RgpOZOD, clDet.RgpCTOD, clDet.RgpLatitudeOD, clDet.RgpAddOD, clDet.RgpColorOD,  
clDet.RgpPowerOS, clDet.RgpBCOS ,clDet.RgpDiameterOS, clDet.RgpOZOS, clDet.RgpCTOS, clDet.RgpLatitudeOS, clDet.RgpAddOS, clDet.RgpColorOS,
clDet.RgpCustomBCOD, clDet.RgpCustomPowerOD, clDet.RgpCustomOZOD, clDet.RgpCustomLatitudeOD, clDet.RgpCustomAddOD, clDet.RgpCustomDiameterOD, clDet.RgpCustomColorOD, 
clDet.RgpCustomBCOS, clDet.RgpCustomPowerOS, clDet.RgpCustomOZOS, clDet.RgpCustomLatitudeOS, clDet.RgpCustomAddOS, clDet.RgpCustomDiameterOS, clDet.RgpCustomColorOS,
users.fname, users.lname  
FROM contactlensmaster clMaster LEFT JOIN contactlensworksheet_det clDet 
ON clDet.clws_id = clMaster.clws_id 
LEFT JOIN users ON users.id= clMaster.provider_id 
WHERE clMaster.patient_id='".$_SESSION['patient_session_id']."' ".$qryPart." and clMaster.clws_id=".$_REQUEST['workSheetId']. " and clDet.clType='".$cl_type."' ORDER BY clMaster.clws_id DESC, clDet.clType DESC, clDet.clEye ASC"; 
//echo $qry; die();
	
$rs=imw_query($qry);
if(imw_num_rows($rs)>0){
 while($res=imw_fetch_array($rs)){
	$phyName='';
	
		$clType=($res['clType']=='cust_rgp')? 'Custom RGP' : strtoupper($res['clType']);
		
		$arrWS[$res['clws_id']]=$res['clws_id'];
		$arrWS[$res['clws_id']]['DOS']=$res['DOS'];
	 
		$dos3 =str_replace("-", "", $res['DOS']);
		if(($res['fname']!='' || $res['lname']!='') && $res['provider_id']>0){
			$phyName=$res['lname'].', '.$res['fname'];
		}
	
		if($res['clType']=='scl'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['SclsphereOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['SclCylinderOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['SclaxisOD']."&deg;";
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['SclDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['SclBcurveOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['SclAddOD'];
			}else{
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['SclsphereOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['SclCylinderOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['SclaxisOS']."&deg;";
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS'] = $res['SclDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['SclBcurveOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS'] = $res['SclAddOS'];
			}
		}
		if($res['clType']=='rgp'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['RgpPowerOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['RgpOZOD']." ".$res['RgpCTOD'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['RgpColorOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['RgpDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['RgpBCOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['RgpAddOD'];
			}else{
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['RgpPowerOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['RgpOZOS']." ".$res['RgpCTOS'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['RgpColorOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS']  = $res['RgpDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['RgpBCOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS']  = $res['RgpAddOS'];
			}
		}
		if($res['clType']=='cust_rgp'){
			//clubing od and os value in one array as it is saved in different lines from idoc
			if($clwidArr[$res['clws_id']][$clType])$cnt=$clwidArr[$res['clws_id']][$clType];
			else $cnt++;
			$clwidArr[$res['clws_id']][$clType]=$cnt;
			
			if($res['clEye']=='OD'){
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['OD'] 	= 'OD';
				$arrRxDetails[$dos3][$cnt]['orderHxS'] 	= $res['RgpCustomPowerOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxC']	= $res['RgpCustomOZOD']." ".$res['RgpCustomCTOD'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA'] 	= $res['RgpCustomColorOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia'] = $res['RgpCustomDiameterOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc'] 	= $res['RgpCustomBCOD'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd'] = $res['RgpCustomAddOD'];
			}else{
				$arrRxDetails[$dos3][$cnt]['clType']=$clType;
				$arrRxDetails[$dos3][$cnt]['DOS'] 	= $res['DOS'];
				$arrRxDetails[$dos3][$cnt]['Provider'] 	= $res['provider_id'];
				$arrRxDetails[$dos3][$cnt]['clws_type'] 	= $res['clws_type'];
				$arrRxDetails[$dos3][$cnt]['clws_trial_number'] 	= $res['clws_trial_number'];
				
				$arrRxDetails[$dos3][$cnt]['ProviderName'] 	= $phyName;
				$arrRxDetails[$dos3][$cnt]['OS'] 	= 'OS';
				$arrRxDetails[$dos3][$cnt]['orderHxS_OS'] 	= $res['RgpCustomPowerOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxC_OS']	= $res['RgpCustomOZOS']." ".$res['RgpCustomCTOS'];
				//$arrRxDetails[$dos3][$cnt]['orderHxA_OS'] 	= $res['RgpCustomColorOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxDia_OS']  = $res['RgpCustomDiameterOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxBc_OS'] 	= $res['RgpCustomBCOS'];
				$arrRxDetails[$dos3][$cnt]['orderHxAdd_OS']  = $res['RgpCustomAddOS'];
			}
		}
 }
}
	}
$data.= "<table><tr><th>DOS</th><th>Type</th><th>Vision</th><th>Sphere</th><th>Cylinder</th><th>Axis</th><th>BC</th><th>Add</th><th>Diameter</th></tr>";
 foreach($arrRxDetails as $key=>$value)
 {
	foreach($value as $key1=>$value1)
	{
		
		$loc=imw_query("select * from patient_data where id=".$pat_id);
		$loc_fetch=imw_fetch_array($loc);
		$data1.="<h4>".$loc_fetch['fname']." ".$loc_fetch['lname']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$loc_fetch['street'].", ".$loc_fetch['street2']." ".$loc_fetch['city']." ".$loc_fetch['city'].", ".$loc_fetch['state']." ".$loc_fetch['postal_code']."</h4>
		<h5>Contact Lens Rx</h5><h5>Date : ".$value1['DOS']."</h5></br>";
		
		
		$data.='<tr>
			<td rowspan=2>'.$value1['DOS'].'</td>
			<td>'.$value1['clType'].'</td>
			<td>OD</td>
			<td>'.$value1['orderHxS'].'</td>
			<td>'.$value1['orderHxC'].'</td>
			<td>'.$value1['orderHxA'].'</td>
			<td>'.$value1['orderHxBc'].'</td>
			<td>'.$value1['orderHxAdd'].'</td>
			<td>'.$value1['orderHxDia'].'</td>
			</tr>';
		
		$data.='<tr>
		<td>'.$value1['clType'].'</td>
		<td>OS</td>
		<td>'.$value1['orderHxS_OS'].'</td>
		<td>'.$value1['orderHxC_OS'].'</td>
		<td>'.$value1['orderHxA_OS'].'</td>
		<td>'.$value1['orderHxBc_OS'].'</td>
		<td>'.$value1['orderHxAdd_OS'].'</td>
		<td>'.$value1['orderHxDia_OS'].'</td>
		</tr>';
	}
 }
$data.="</table>"
.$ImgSignature;


$css.="<style>
table{border:1px solid;border-collapse:collapse}
table td,th{border:1px solid #CCC;border-collapse:collapse;padding:20px;}</style>";
	
	if(file_put_contents('../../library/new_html2pdf/print_rx.html',($data1.$data.$css)))
	{
	echo "<script>
	var url='".$GLOBALS['WEB_PATH']."/library/new_html2pdf/createPdf.php?op=p&file_name=print_rx';
	var ptwin=window.location.href=url;</script>";
	}
}
?>