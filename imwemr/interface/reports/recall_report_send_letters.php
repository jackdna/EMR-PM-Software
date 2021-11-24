<?php
require_once '../../config/globals.php';
// require_once '../../library/phpmailer/PHPMailerAutoload.php';
require_once '../../library/classes/pnTempParser.php';
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');
use PHPMailer\PHPMailer;
$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(imw_error());
if(imw_num_rows($queryEmailCheck)>=1)
{
	$dEmailCheck=imw_fetch_object($queryEmailCheck);
	$groupEmailConfig['email']=$dEmailCheck->config_email;
	$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
	$groupEmailConfig['host']=$dEmailCheck->config_host;
	$groupEmailConfig['header']=$dEmailCheck->config_header;
	$groupEmailConfig['footer']=$dEmailCheck->config_footer;
	$groupEmailConfig['port']=$dEmailCheck->config_port;
	
	//EMAIL SUBJECT DYNAMIC WORK
	$queryEmailSubject = "SELECT 
						 	email_subject_reminder 
						 FROM 
							default_patient_direct_credentials 
						 WHERE 
							email_subject_reminder!=''";
	$rowEmailSubject = imw_query($queryEmailSubject)or die(imw_error());
	if(imw_num_rows($rowEmailSubject)>=1)
	{
		$dEmailSubjCheck=imw_fetch_object($rowEmailSubject);
		$groupEmailConfig['email_subject_reminder']=$dEmailSubjCheck->email_subject_reminder;
	}
	else
	{
		$groupEmailConfig['email_subject_reminder']= 'imwemr Appt. Reminder';
	}		
}
//$queryEmailCheck.close;

$sent=0;
if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host'])
{
	?>  <script>
		top.fAlert("Email not configured.");
		top.show_loading_image('hide');
        </script>
        
	<?php
	die();	
}

$months = $_REQUEST['months'];
$years = $_REQUEST['years'];
$last_nam_frm=$_REQUEST['last_nam_frm'];
$last_nam_to=$_REQUEST['last_nam_to'];
$recall_date_from=$_REQUEST['recall_date_from'];
$recall_date_to=$_REQUEST['recall_date_to'];
$recall_id=implode(',',$_REQUEST['pat_id_imp']);
$facility_name=implode(',',$_REQUEST['facility_name']);
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

$recalldate = date("Y-m-d",mktime(0,0,0,date("m")+$months,date("d"),date("y")));

if($recall_id){
	$where=" WHERE par.id in($recall_id)";
}

if($facility_name != ""){
	$whr_fac = " AND par.facility_id in($facility_name)";
}
if($procedures != ""){
	$whr_proc = " AND par.procedure_id in($procedures)";
}	

//get template html
if($recallTemplatesListId) {
	$recallTemplateData		= '';
	$recallTemplateQry 		= "SELECT * FROM recalltemplate WHERE recallLeter_id='".$recallTemplatesListId."'";
	$recallTemplateRes 		= imw_query($recallTemplateQry);
	$recallTemplateNumRow 	= imw_num_rows($recallTemplateRes);
	if($recallTemplateNumRow>0) {
		$recallTemplateRow 	= imw_fetch_array($recallTemplateRes);
		$recallTemplateData = stripslashes($recallTemplateRow['recallTemplateData']);	
	}
}
	
$qry = "SELECT par.*,pd.email, CONCAT(pd.lname,' ',pd.mname,', ',pd.fname) as `pat_name`, 
 		DATE_FORMAT(par.recalldate, '%m-%d-%Y') as 'recall_date', par.id as recall_id FROM patient_app_recall as par
		LEFT JOIN patient_data as pd
		ON par.patient_id=pd.id
		$whr_fac
		$whr_proc
		$where 
		group by par.patient_id ORDER BY pd.lname,pd.fname asc";

$res = imw_query($qry) or die(imw_error());
$num = imw_num_rows($res);

while($rw1 = imw_fetch_array($res)){
	$patientidArr[$rw1['patient_id']]= $rw1['patient_id'];
	$rw_tmp[]=$rw1;
}
$pat_id_imp2=implode(',',$patientidArr);

//qery to get patient last no show marked appointment
$last_no_show=array();
$ns_res = imw_query("select sa_patient_id, DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
			  TIME_FORMAT(sa_app_starttime, '%h:%i %p') as starttime from schedule_appointments 
			  WHERE sa_patient_app_status_id=3 and sa_patient_id IN ($pat_id_imp2) order by sa_app_start_date ASC") or die(imw_error());
while($ns_rec=imw_fetch_object($ns_res))
{
	$last_no_show[$ns_rec->sa_patient_id]=$ns_rec->apptDate.' '.$ns_rec->starttime;
}

if($num > 0){
	$strCSS = '
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



	$i = 1;
	$j = 1;
	$t=0;
	//send smtp mail here
	// require_once '../../library/phpmailer/PHPMailerAutoload.php';
	
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
	//$mail->SMTPSecure = 'ssl';//ssl, tls
	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
	// SMTP connection will not close after each email sent, reduces SMTP overhead
	$mail->SMTPKeepAlive = true;
	//Username to use for SMTP authentication
	$mail->Username = $groupEmailConfig['email'];
	//Password to use for SMTP authentication
	$mail->Password = $groupEmailConfig['pwd'];
	//Set who the message is to be sent from
	$mail->setFrom($groupEmailConfig['email'], '');
	//Set an alternative reply-to address
	//$mail->addReplyTo('replyto@example.com', 'First Last');
	//Set who the message is to be sent to
	//Set the subject line
	$mail->Subject = $groupEmailConfig['email_subject_reminder'];//'imwemr Appt. Reminder';
	/*if($emailID)$mail->addAddress($emailID,$emailIDname);
	//Replace the plain text body with one created manually
	$mail->AltBody = '';
	//Attach an image file
	if($filename)$mail->addAttachment($filename);
	
	//send the message, check for errors
	if (!$mail->send()) {
		$customMsg = "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$customMsg = "Mail sent successfully";
	}*/
	 
	foreach($rw_tmp as $rw){
		$t++;
		$patientid				= $rw['patient_id'];
		$patient_app_recalldate	= $rw['recalldate'];
		$recallDesc				= $rw['descriptions'];
		$recallProcedureId		= $rw['procedure_id'];
		$qu=imw_query("select * from patient_data where id='".$patientid."'");
		$patient_deta			= imw_fetch_assoc($qu);
		$recallProcedureName	= getProcedureName($recallProcedureId);
		$recallLastDos			= get_vocab_lastdos($patientid); //For {LAST DOS} Vocabulary
		if($recallProcedureId<0) {$recallProcedureName	= $rw['procedure_name'];}
		$patientRefTo			= '';
		$recallData 			= $recallTemplateData;
		
		$PtDOB 					= $patient_deta['DOB'];
		if($PtDOB && $PtDOB!='0000-00-00') { $PtDOB = date(''.phpDateFormat().'',strtotime($PtDOB));}
		
		//GET FACILITY
		$facilityName='';
		$facPhoneFormat='';
		$rs1=imw_query("Select facility.name,facility.phone FROM schedule_appointments JOIN facility ON schedule_appointments.sa_facility_id=facility.id 
		WHERE sa_patient_id='".$patientid."' ORDER BY schedule_appointments.id DESC LIMIT 0,1");
		if(imw_num_rows($rs1)>0){
			$res1=imw_fetch_array($rs1);
			$facilityName=$res1['name'];
			if(strlen(trim($rw["phone"]))>=1) {
				$facPhoneFormat = str_ireplace("-","",$rw["phone"]);
				$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
			}
		}/*else{
			$rs2=imw_query("select pos_facilityies_tbl.facilityPracCode as name,
			pos_facilityies_tbl.pos_facility_id as id,
			pos_tbl.pos_prac_code
			from pos_facilityies_tbl
			left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
			WHERE pos_facilityies_tbl.pos_facility_id='".$patient_deta[26]."'");
			if(imw_num_rows($rs2)>0){
				$res2=imw_query($rs2);
				$facilityName=$res2['name'].' - '.$res2['pos_prac_code'];
			}
		}*/
		
		$ProviderName=$where='';
		if($recall_date_from && $recall_date_to)
		{
			$where="and (sa.sa_app_start_date between '".$recall_date_from."' and '".$recall_date_to."') ";
		}
	    $rs="Select users.fname, users.lname, users.lname as doctorLastName, users.sign_path 
		FROM schedule_appointments as sa JOIN users ON sa.sa_doctor_id=users.id 
		WHERE sa.sa_patient_id='".$patientid."' $where
		ORDER BY sa.id DESC LIMIT 0,1";
	    $rs1 = imw_query($rs);
		  if(imw_num_rows($rs1)>0){
			  $res1=imw_fetch_array($rs1);
			 
			  $ProviderLname = $res1['lname'];
			  $ProviderFname = $res1['fname'];
			  $doctorLastName= $res1['doctorLastName'];
			  $ProviderName=$ProviderLname.'&nbsp;'.$ProviderFname;
			  $SignPath = $res1['sign_path'];	
			  $physical_path=data_path();
			  
				if(file_exists($physical_path.$SignPath)){
					$ProviderSignPath=$physical_path.$SignPath;
					$ProviderSign =  "<img  src='".$ProviderSignPath."'>";
				}
		  }
		//MODIFIED VARIABLES FOR CONSISTENCY
		$recallData = str_ireplace("{PATIENT NAME TITLE}",$patient_deta['title'],$recallData);
		$recallData = str_ireplace("{PATIENT FIRST NAME}",$patient_deta['fname'],$recallData);
		$recallData = str_ireplace("{MIDDLE NAME}",$patient_deta['mname'],$recallData);
		$recallData = str_ireplace("{LAST NAME}",$patient_deta['lname'],$recallData);
		$recallData = str_ireplace("{PATIENT CITY}",$patient_deta['city'],$recallData);
		$recallData = str_ireplace("{HOME PHONE}",$patient_deta['phone_home'],$recallData);
		$recallData = str_ireplace("{WORK PHONE}",$patient_deta['phone_biz'],$recallData);
		$recallData = str_ireplace("{MOBILE PHONE}",$patient_deta['phone_cell'],$recallData);
		//MODIFIED VARIABLES FOR CONSISTENCY
		
		
		$recallData = str_ireplace("{PT-KEY}",$patient_deta['temp_key'],$recallData); 
		$recallData = str_ireplace("{PatientID}",$patientid,$recallData);
		$recallData = str_ireplace("{DOB}",$PtDOB,$recallData);
		$recallData = str_ireplace("{ADDRESS1}",$patient_deta['street'],$recallData);
		$recallData = str_ireplace("{ADDRESS2}",$patient_deta['street2'],$recallData);
		$recallData = str_ireplace("{STATE, ZIP CODE}",$patient_deta['postal_code'],$recallData);
		$recallData = str_ireplace("{STATE ZIP CODE}",$patient_deta['postal_code'],$recallData);
		$recallData = str_ireplace("{PATIENT STATE}",$patient_deta['state'],$recallData);
		$recallData = str_ireplace("{PATIENT ZIP}",$arrStateZip['postal_code'],$recallData);
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
		
		$recallData = str_ireplace("{APPT PROVIDER LAST NAME}",$doctorLastName,$recallData);
		$recallData = str_ireplace("{APPT PROC}",'',$recallData);	
		$recallData = str_ireplace("{APPT DATE_F}",'',$recallData);
		$recallData = str_ireplace("{APPT FACILITY PHONE}",$facPhoneFormat,$recallData);	
		
		//$recallData = $objParser->getDataParsed($recallData,$patientid,$formIdRecallLetter,$patientRefTo);		

		$strHTML = '<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="text" valign="top"  style="margion:0px;">
								'.$recallData.'
							</td>
						</tr>
					</table>';
		$strHTML = str_ireplace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$strHTML);
		$strHTML = str_ireplace("/$web_RootDirectoryName/interface/reports/new_html2pdf/","",$strHTML);
		$strHTML = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$strHTML);
		$strHTML = str_ireplace("/iMedicR4-Dev/interface/common/new_html2pdf/","",$strHTML);
		$strHTML = str_ireplace("/imwemr/interface/common/new_html2pdf/","",$strHTML);
		$strHTML = str_ireplace("</br>","",$strHTML);
		
		//Read an HTML message body from an external file, convert referenced images to coded,
		preg_match_all("/src=\"(.*?)\"/",$strHTML,$matches);
		foreach($matches[1] as $key=>$path)
		{
			$type = pathinfo($path, PATHINFO_EXTENSION);
				
			//fix relative path issue
			if(!strstr($path,'http'))
			$pathNew=$webServerRootDirectoryName.$path;
			else
			$pathNew=$path;

			
			$data = file_get_contents($pathNew);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			$strHTML=str_replace($path,$base64,$strHTML);
		}
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($groupEmailConfig['header']."<br/>$strCSS $strHTML<br/>".$groupEmailConfig['footer']);

		$mail->addAddress($rw['email'], $rw['pat_name']);
		if (!$mail->send()) {
			$failed++;
			//$customMsg = "Mailer Error: " . $mail->ErrorInfo;
		} else {
			$sent++;
			//save record for sent track
			imw_query("insert into exclude_sent_email set pt_id='$patientid', appt_id='$rw[recall_id]', appt_date='$rw[recalldate]', appt_time='', temp_id='$recallTemplatesListId', sent_on='".date('Y-m-d H:i:s')."', sent_by='$_SESSION[authUserID]', report='Recalls'");
		}
		// Clear all addresses and attachments for next loop
		$mail->clearAddresses();
		$mail->clearAttachments();
		
	}
	?>
    <script>
		top.fAlert("<?php echo "$sent/$num";?> mails sent successfully.");
		top.show_loading_image('hide');
    </script>
    <?php
}
else
{
?>
    <script type="text/javascript">
    top.fAlert('No Record Found.');
    top.show_loading_image('hide');
    </script>
        
<?php
}
?>