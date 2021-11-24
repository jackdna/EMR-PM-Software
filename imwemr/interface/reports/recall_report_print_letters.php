<?php
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');
include_once($GLOBALS['fileroot'] . '/library/classes/print_pt_key.php');


//--- GET FACILITY ----
$fac_query = "select id,name,phone from facility";
$fac_query_res = imw_query($fac_query);
$arrAllFacilites = array();
$arrFacilityPhone=array();
while ($res = imw_fetch_array($fac_query_res)) {
	$arrAllFacilites[$res['id']]=$res['name'];
	$arrFacilityPhone[$res['id']]=$res['phone'];
}

//--- GET ALL PROVIDER NAME ----
$providerRs = imw_query("Select id,fname,mname,lname,sign_path from users");
$arrAllProviders = array();
$arrProviderSign=array();
while($providerResArr = imw_fetch_assoc($providerRs)){
	$arrAllProviders[$providerResArr['id']] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	$arrProviderSign[$providerResArr['id']]= $providerResArr['sign_path'];
}


$obj_print_pt_key=new print_pt_key;

$months = $_REQUEST['months'];
$years = $_REQUEST['years'];
$last_nam_frm=$_REQUEST['last_nam_frm'];
$last_nam_to=$_REQUEST['last_nam_to'];
$recall_date_from=$_REQUEST['recall_date_from'];
$recall_date_to=$_REQUEST['recall_date_to'];
$pat_id_imp=$_REQUEST['pat_id_imp'];
$facility_name=$_REQUEST['facility_name'];
$procedures=$_REQUEST['procedures'];
$recallTemplatesListId=$_REQUEST['recallTemplatesListId'];

//setting margins
$sql_margin=imw_query("select * from create_margins where margin_type='recall'");
$row_margin=imw_fetch_array($sql_margin);
$top_margin = $row_margin['top_margin'];
$bottom_margin = $row_margin['bottom_margin'];
$line_margin = $row_margin['line_margin'];
$coloumn_margin = $row_margin['column_margin'];

$arrMonth = array("01" => "January","02" => "Febraury","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December",);

$where = "WHERE 1=1";
$recalldate = date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("y")));
if($months != ""){
	$where .= " AND MONTH(recalldate) = '".$months."' ";
}
if($years != ""){
	$where .= " AND YEAR(recalldate) = '".$years."'";
}
if($last_nam_frm){
	$last_whr=" and (pd.lname between '$last_nam_frm' and '$last_nam_to' or pd.lname like '$last_nam_to%')";
}
if($pat_id_imp){
	$un_app_whr=" and par.patient_id in($pat_id_imp)";
}

if($facility_name != ""){
	$whr_fac = " AND par.facility_id in($facility_name)";
}
if($procedures != ""){
	$whr_proc = " AND par.procedure_id in($procedures)";
}	

if($recall_date_from!='' || $recall_date_to!='') {
	$where = "WHERE 1=1";	
	if($recall_date_from!='') 	{ 
		$dtFromExplode = explode('-',$recall_date_from);
		$recall_date_fromNew = date('Y-m-d',mktime(0,0,0,$dtFromExplode[0],$dtFromExplode[1],$dtFromExplode[2]));
		$where .= " AND recalldate>='".$recall_date_fromNew."' ";	
	}
	if($recall_date_to!='') 	{ 
		$dtToExplode = explode('-',$recall_date_to);
		$recall_date_toNew = date('Y-m-d',mktime(0,0,0,$dtToExplode[0],$dtToExplode[1]+1,$dtToExplode[2]));
		
		$where .= " AND recalldate < '".$recall_date_toNew."' ";		
	}
}
//flag to set create label/house call on off (by default its off)
if($recallTemplatesListId) {
	$recallTemplateData		= '';
	$recallTemplateQry 		= "SELECT * FROM recalltemplate WHERE recallLeter_id='".$recallTemplatesListId."'";
	$recallTemplateRes 		= @imw_query($recallTemplateQry);
	$recallTemplateNumRow 	= @imw_num_rows($recallTemplateRes);
	if($recallTemplateNumRow>0) {
		$recallTemplateRow 	= @imw_fetch_array($recallTemplateRes);
		$recallTemplateData = stripslashes($recallTemplateRow['recallTemplateData']);	
	}
}
/*
$qry = "SELECT par.*,clch.form_id FROM 
		patient_app_recall as par, patient_data as pd, chart_left_cc_history as clch 
		$where 
		AND par.patient_id=pd.id  
		$last_whr 
		$un_app_whr 
		AND  clch.date_of_service<=par.recalldate
		AND  clch.patient_id=par.patient_id
		group by patient_id 
		ORDER BY lname asc,fname asc";
*/		
/*
$qry = "SELECT par.*  FROM patient_app_recall as par
		INNER JOIN patient_data as pd ON (pd.id=par.patient_id AND pd.hipaa_mail='1')
		$last_whr
		$where
		$un_app_whr
		group by patient_id
		ORDER BY lname asc,fname asc ";
*/	
function getvocablastdos($id){
	$qry="select date_format(date_of_service,'".get_sql_date_format()."') as dos from `chart_master_table` where patient_id='".$id ."' order by date_of_service desc";
	$r=@imw_query($qry);
	if(@imw_num_rows($r)>0){
		$row_d=@imw_fetch_assoc($r);
		$last_dos=$row_d['dos'];
	}
	return $last_dos;
}	
$qry = "SELECT par.*, DATE_FORMAT(par.recalldate, '".get_sql_date_format()."') as 'recall_date' FROM patient_app_recall as par, patient_data as pd
		$where 
		AND par.patient_id=pd.id 
		AND pd.hipaa_mail='1' 
		AND par.descriptions != 'MUR_PATCH' 
		$last_whr 
		$un_app_whr 
		$whr_fac
		$whr_proc
		group by patient_id 
		ORDER BY lname asc,fname asc ";
	
$res = imw_query($qry) or die(imw_error());
$num = imw_num_rows($res);

while($rw1 = imw_fetch_array($res)){
	$patientidArr[$rw1['patient_id']]= $rw1['patient_id'];
	$rw_tmp[]=$rw1;
}

if(!$pat_id_imp)
{
	$pat_id_imp=implode(',',$patientidArr);
}

if($pat_id_imp){
	//qery to get patient last no show marked appointment
	$last_no_show=array();
	$ns_res = imw_query("select sa_patient_id, DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
				  TIME_FORMAT(sa_app_starttime, '%h:%i %p') as starttime, sa_facility_id, sa_doctor_id from schedule_appointments 
				  WHERE sa_patient_app_status_id=3 and sa_patient_id IN ($pat_id_imp) order by sa_app_start_date ASC") or die(imw_error());
	while($ns_rec=imw_fetch_object($ns_res))
	{
		$last_no_show[$ns_rec->sa_patient_id]=$ns_rec->apptDate.' '.$ns_rec->starttime;
		$arrApptFacility[$ns_rec->sa_patient_id]=$ns_rec->sa_facility_id;
		$arrApptDoctor[$ns_rec->sa_patient_id]=$ns_rec->sa_doctor_id;
	}
}

ob_start();
if($num > 0){
	$strHTML = '
			<style>
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#FFFFFF;
					background-color:#4684AB;
				}
				.text{
					font-size:11px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
			</style>
			';
	$strHTML .= '<page backtop="0mm" backbottom="0mm">';
	$strHTML .= '<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">'.$showPageInfo.'</td>
					</tr>
				</table>
			</page_footer>';


	$i = 1;
	$j = 1;
	$t=0;
	$physical_path=data_path();
	foreach($rw_tmp as $rw){
		$t++;
		$patientid				= $rw['patient_id'];
		$patient_app_recalldate	= $rw['recalldate'];
		$recallDesc				= $rw['descriptions'];
		$recallProcedureId		= $rw['procedure_id'];
		$qu=imw_query("select * from patient_data where id='".$patientid."'");
		$patient_deta			= imw_fetch_assoc($qu);
		$recallProcedureName	= getProcedureName($recallProcedureId);
		$recallLastDos			= getvocablastdos($patientid); //For {LAST DOS} Vocabulary
		if($recallProcedureId<0) {$recallProcedureName	= $rw['procedure_name'];}
		$patientRefTo			= '';
		$recallData 			= $recallTemplateData;
		//------NEW APPOINTMENT VARIABLE INFORMATION-------------------------
		 $apptInfoArr2 = $obj_print_pt_key->getApptInfo($patientid,'','','',1);
		//By Jaswant Sir
		//$PtDOB 					= $patient_deta[4];
		//if($PtDOB && $PtDOB!='0000-00-00') { $PtDOB = date('m-d-Y',strtotime($PtDOB));}
		
		$facilityName=$facilityPhone='';
		$sch_facility_id=$arrApptFacility[$patientid];
		$facilityName= $arrAllFacilites[$sch_facility_id];
		$facilityPhone= $arrFacilityPhone[$sch_facility_id];
		
		
		//By Karan
		$PtDObFormat = explode("-",$patient_deta['DOB']);
		if($patient_deta[4] && $patient_deta['DOB']!='0000-00-00')
		{
			$PtDOB = date(''.phpDateFormat().'', mktime(0,0,0, date($PtDObFormat[0]), date($PtDObFormat[1]), date($PtDObFormat[2])));
		}
		else
		{
			$PtDOB = $patient_deta['DOB'];
		}
		
		//GET FACILITY
		/*
		$facilityName='';
		$rs1=imw_query("Select facility.name, facility.phone FROM schedule_appointments JOIN facility ON schedule_appointments.sa_facility_id=facility.id 
		WHERE sa_patient_id='".$patientid."' ORDER BY schedule_appointments.id DESC LIMIT 0,1");
		if(imw_num_rows($rs1)>0){
			$res1=imw_fetch_array($rs1);
			$facilityName=$res1['name'];
			$facilityPhone=$res1['phone'];
			
		} else{
			$rs2=imw_query("select pos_facilityies_tbl.facilityPracCode as name, 
			pos_facilityies_tbl.phone as phone, pos_facilityies_tbl.pos_facility_id as id, 
			pos_tbl.pos_prac_code
			from pos_facilityies_tbl
			left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
			WHERE pos_facilityies_tbl.pos_facility_id='".$patient_deta[26]."'");
			if(imw_num_rows($rs2)>0){
				$res2=imw_query($rs2);
				$facilityName=$res2['name'].' - '.$res2['pos_prac_code'];
				$facilityPhone=$res1['phone'];
			}
		} */
		
		/*
		$ProviderName='';
	    $rs="Select users.fname, users.lname, users.sign_path FROM schedule_appointments JOIN users ON schedule_appointments.sa_doctor_id=users.id 
			 WHERE sa_patient_id='".$patientid."' '".$recall_date_from."' '".$recall_date_to."' ORDER BY schedule_appointments.id DESC LIMIT 0,1";
			$rs1 = imw_query($rs);
			if(imw_num_rows($rs1)>0){
				$res1=imw_fetch_array($rs1);
				$ProviderLname = $res1['lname'];
				$ProviderFname = $res1['fname'];
				$ProviderName=$ProviderLname.'&nbsp;'.$ProviderFname;
				$SignPath = $res1['sign_path'];	
				$physical_path=data_path();
				if(file_exists($physical_path.$SignPath)){
					$ProviderSignPath=$physical_path.$SignPath;
					$ProviderSign =  "<img  src='".$ProviderSignPath."'>";
				}
			}*/

		$$ProviderName='';	
		$apptDoctor=$arrApptDoctor[$patientid];
		$ProviderName= $arrAllProviders[$apptDoctor];
		$SignPath= $arrProviderSign[$apptDoctor];
		if(file_exists($physical_path.$SignPath)){
			$ProviderSignPath=$physical_path.$SignPath;
			$ProviderSign =  "<img  src='".$ProviderSignPath."'>";
		}		

		//OLD VARIABLES
		/*
		$recallData = str_ireplace("{TITLE NAME}",'<b>'.$patient_deta[10].'</b>',$recallData);
		$recallData = str_ireplace("{FIRST NAME}",'<b>'.$patient_deta[11].'</b>',$recallData);
		$recallData = str_ireplace("{MIDDLE INITIAL}",'<b>'.$patient_deta[12].'</b>',$recallData);
		$recallData = str_ireplace("{CITY}",'<b>'.$patient_deta[5].'</b>',$recallData);
		$recallData = str_ireplace("{PHONE}",'<b>'.$patient_deta[8].'</b>',$recallData);
		*/
		//MODIFIED VARIABLES FOR CONSISTENCY
		$recallData = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH,'../../data/'.PRACTICE_PATH,$recallData);
		
		$recallData = str_ireplace($GLOBALS['webroot'].'/library/images/',$fileroot.'/library/images/',$recallData);
		$recallData = str_ireplace('/'.$web_RootDirectoryName.'/interface/common/new_html2pdf/','',$recallData);	
		
		$recallData = str_ireplace("{PATIENT NAME TITLE}",$patient_deta['title'],$recallData);
		$recallData = str_ireplace("{PATIENT FIRST NAME}",$patient_deta['fname'],$recallData);
		$recallData = str_ireplace("{MIDDLE NAME}",$patient_deta['mname'],$recallData);
		$recallData = str_ireplace("{LAST NAME}",$patient_deta['lname'],$recallData);
		$recallData = str_ireplace("{PATIENT CITY}",$patient_deta['city'],$recallData);
		$recallData = str_ireplace("{HOME PHONE}",$patient_deta['phone_home'],$recallData);
		$recallData = str_ireplace("{WORK PHONE}",$patient_deta['phone_biz'],$recallData);
		$recallData = str_ireplace("{MOBILE PHONE}",$patient_deta['phone_cell'],$recallData);
		
		//MODIFIED VARIABLES FOR CONSISTENCY
		$state_zip2 =  $patient_deta['state']."  ".$patient_deta['postal_code'];
		$state_zip = ($patient_deta['state'] != "" && $patient_deta['zip']) ? $patient_deta['state'].",  ".$patient_deta['postal_code'] : $state_zip2;
		
		$recallData = str_ireplace("{PT-KEY}",$patient_deta['temp_key'],$recallData); 
		$recallData = str_ireplace("{PatientID}",$patientid,$recallData);
		$recallData = str_ireplace("{DOB}",$PtDOB,$recallData);
		$recallData = str_ireplace("{ADDRESS1}",$patient_deta['street'],$recallData);
		$recallData = str_ireplace("{ADDRESS2}",$patient_deta['street2'],$recallData);
		$recallData = str_ireplace("{STATE, ZIP CODE}",$state_zip,$recallData);
		$recallData = str_ireplace("{STATE ZIP CODE}",$state_zip2,$recallData);
		$recallData = str_ireplace("{PATIENT STATE}",$patient_deta['state'],$recallData);
		$recallData = str_ireplace("{PATIENT ZIP}",$patient_deta['postal_code'],$recallData);
		$recallData = str_ireplace("{DATE}",date(''.phpDateFormat().''),$recallData);
		$recallData = str_ireplace("{RECALL DESCRIPTION}",$recallDesc,$recallData);
		$recallData = str_ireplace("{RECALL PROCEDURE}",$recallProcedureName,$recallData);
		$recallData = str_ireplace("{LAST DOS}",$recallLastDos,$recallData);
		$recallData = str_ireplace("{APPT FACILITY}",$facilityName,$recallData);
		$recallData = str_ireplace("{APPT DATE}",$rw['recall_date'],$recallData);
		$recallData = str_ireplace("{NO SHOW APPOINTMENT}",$last_no_show[$patientid],$recallData);
		$recallData = str_ireplace("{APPT TIME}",'----',$recallData);
		$recallData = str_ireplace("{APPT PROVIDER}",$ProviderName,$recallData);
		$recallData = str_ireplace("{APPT PROVIDER SIGNATURE}",$ProviderSign,$recallData);
		
		$recallData = str_ireplace("{ETHNICITY}",$patient_deta['ethnicity'],$recallData);	
		$recallData = str_ireplace("{LANGUAGE}",$patient_deta['language'],$recallData);
		$recallData = str_ireplace("{PATIENT MRN}",$patient_deta['External_MRN_1'],$recallData);
		$recallData = str_ireplace("{PATIENT MRN2}",$patient_deta['External_MRN_2'],$recallData);
		$recallData = str_ireplace("{RACE}",$patient_deta['race'],$recallData);
		
		$recallData = str_ireplace("{APPT PROVIDER LAST NAME}",$ProviderLname,$recallData);
		$recallData = str_ireplace("{APPT PROC}",$recallProcedureName,$recallData);	
		$recallData = str_ireplace("{APPT DATE_F}",'',$recallData);
		$recallData = str_ireplace("{APPT FACILITY PHONE}",$facilityPhone,$recallData);
    //----------NEW APPOINTMENT VARIABLES REPLACEMENT WORK--------------------
	//----------FACILITY ADDRESS VARIABLE CONCATENATION-----------------------
	
	if($apptInfoArr2[10] && $apptInfoArr2[11])
	{
		$facilityAddress .= $apptInfoArr2[10].',&nbsp;'.$apptInfoArr2[11].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];	
	}
	else if($apptInfoArr2[10])
	{
		$facilityAddress .= $apptInfoArr2[10].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];	
	}
	else if($apptInfoArr2[11])
	{
		$facilityAddress .= $apptInfoArr[11].',&nbsp;'.$apptInfoArr2[12].'&nbsp;'.$Zip_code_ext.'&nbsp;'.$apptInfoArr2[3];
	}


	
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_DATE}",$apptInfoArr2[0],$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TIME}",$apptInfoArr2[8],$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PROVIDER}",$apptInfoArr2[5],$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_LOCATION}",$facilityAddress,$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_PRIREASON}",$apptInfoArr2[4],$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_SECREASON}",$apptInfoArr2[16],$recallData);
	$recallData = str_ireplace("{PATIENT_NEXT_APPOINTMENT_TERREASON}",$apptInfoArr2[17],$recallData);
		
		//$recallData = $objParser->getDataParsed($recallData,$patientid,$formIdRecallLetter,$patientRefTo);		

		$strHTML .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="text" valign="top"  style="margion:0px;">
								'.$recallData.'
							</td>
						</tr>
					</table>
					</page>		
					';
		if($num>$t) {
			$strHTML .= '<page pageset="old">';
		}
	}
}

$bl_printed = true;
$file_location = '';
if(trim($strHTML) != ""){
	$file_location = write_html($strHTML);
}else{
	$bl_printed = false;
}
if($bl_printed == false){
	?>
    <html>
		<body>		
		<table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">No Record Found.</td>
			</tr>
		</table>
        </body>
    </html>
	<?php
}else{
	?>
	<form name="printFrmALLPDF" action="<?php echo $GLOBALS['webroot'] ?>/library/html_to_pdf/createPdf.php" method="POST" >
	<input type="hidden" name="page" value="1.3" >
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="p" >
	<input type="hidden" name="font_size" value="7.5">
	<input type="hidden" name="file_location" value="<?php echo $file_location; ?>">
</form>	
	<script>
		document.printFrmALLPDF.submit();
	</script>
	<?php
	}
?>