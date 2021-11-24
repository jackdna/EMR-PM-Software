<?php
set_time_limit(900);
require_once '../../config/globals.php';
// require_once '../../library/phpmailer/PHPMailerAutoload.php';
require_once '../../library/classes/pnTempParser.php';
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');

use PHPMailer\PHPMailer;

$returnArr = array();
$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(mysql_error());
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
imw_free_result($queryEmailCheck);

$sent=0;
$objParser = new PnTempParser;
$strHTML ='';
if(!$groupEmailConfig['email'] || !$groupEmailConfig['pwd'] || !$groupEmailConfig['host']){
	?>  <script>
		top.fAlert("Email not configured.");
		top.show_loading_image('hide');
        </script>
   <?php
	die();	
}
if(!$_POST['recallTemplatesListId1']){
	?>
        <script>
		top.fAlert("No template selected.");
		top.show_loading_image('hide');
        </script>
    <?php
	die();
}

$appt_id_imp=implode(",", $_POST['pat_email']);

if(trim($appt_id_imp) != ""){
	$recallTemplatesListId=$_REQUEST['recallTemplatesListId1'];
	
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

	$ns_res=imw_query("select b.sa_patient_id, DATE_FORMAT(b.sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
			  TIME_FORMAT(b.sa_app_starttime, '%h:%i %p') as starttime from schedule_appointments a INNER JOIN  schedule_appointments b on a.sa_patient_id=b.sa_patient_id 
			  WHERE b.sa_patient_app_status_id=3 and a.id IN($appt_id_imp) order by b.sa_app_start_date DESC LIMIT 1")or die(imw_error());
	while($ns_rec=imw_fetch_object($ns_res))
	{
		$last_no_show[$ns_rec->sa_patient_id]=$ns_rec->apptDate.' '.$ns_rec->starttime;
	}
	$qry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate, sc.sa_patient_id, sc.sa_app_start_date,
			DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.id as appt_id,
			sc.procedure_site as appSite,
			DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime, sc.sa_app_starttime,
			sc.sa_patient_app_status_id as appStatus, 
			CONCAT_WS(', ', us.lname, us.fname) as doctorName, 
			us.lname as doctorLastName, 
			fac.name as facName,
			fac.phone as facPhone,
			slp.proc as procName, 
			sc.sa_comments,
			sc.sa_doctor_id
			FROM schedule_appointments sc 
			LEFT JOIN users us ON us.id = sc.sa_doctor_id 
			LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
			LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
			WHERE sc.id IN($appt_id_imp)
			ORDER BY sc.sa_app_start_date DESC ";
	
	$res = imw_query($qry) or die(imw_error());
	$num = imw_num_rows($res);

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
		$mail->Subject = $groupEmailConfig['email_subject_reminder']; //'imwemr Appt. Reminder';
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
		
		while($rw = imw_fetch_assoc($res)){
			//IF NOT RECALL EXIST OR IF EXIST THEN SHOULD NOT MUR_PATCH
			if($rw['sa_patient_id']) {
				//pre($rw); die;
				$patientid				= $rw['sa_patient_id'];	
				$recallProcedureId		= $rw['procedure_id'];
				//$patient_deta			= patient_data($patientid);
				
				$qu=imw_query("select * from patient_data where id='".$patientid."'");
				$patient_deta			= imw_fetch_assoc($qu);
				
				$sch_app_qry=imw_query("SELECT * FROM schedule_appointments sa LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id where sa.sa_patient_id='$patientid' and sa.sa_app_start_date>='$from_date' and sa.sa_patient_app_status_id NOT IN(201,18,203)");		
				$sa_pt_data			= imw_fetch_assoc($sch_app_qry);
				
				$patientRefTo			= '';
				$recallData 			= $recallTemplateData;
				
				if($_POST['sendUnique'])
				{
					if($sentEmailId[$patient_deta['email']])
					{
						//add email in array to skipp it
						$sentEmailId[$patient_deta['email']]	=$patient_deta['email'];
						continue;//skip this as it is alreday sent
					}
					
				}
				//add email in array to skipp it
				$sentEmailId[$patient_deta['email']]	=$patient_deta['email'];	
				$t++;
				
				$PtDOB 					= $patient_deta['DOB'];
				if($PtDOB && $PtDOB!='0000-00-00') { $PtDOB = date('m-d-Y',strtotime($PtDOB));}
				//$recallData = $objParser->getDataParsed($recallData,$patientid,$formIdRecallLetter,$patientRefTo);	
				if(strlen(trim($rw["facPhone"]))>=1) {
					$facPhoneFormat = str_ireplace("-","",$rw["facPhone"]);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}
				$appStrtTime 			= $rw['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }
				
				//GET provider detail
				$rs="Select sign_path FROM users where id ='".$rw["sa_doctor_id"]."' ORDER BY id DESC LIMIT 0,1";
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
				}
				  
				//RECALL VARIABLE
				$recallData = str_ireplace("{APPT DATE}",$rw["appStrtDate"],$recallData);
				$recallData = str_ireplace("{APPT DATE_F}",$rw["appStrtDate_FORMAT"],$recallData);
				$recallData = str_ireplace("{APPT FACILITY}",$rw["facName"],$recallData);
				$recallData = str_ireplace("{APPT FACILITY PHONE}",$facPhoneFormat,$recallData);
				$recallData = str_ireplace("{APPT PROC}",$rw["procName"],$recallData);
				$recallData = str_ireplace("{APPT PROVIDER}",$rw["doctorName"],$recallData);
				$recallData = str_ireplace("{APPT PROVIDER LAST NAME}",$rw["doctorLastName"],$recallData);
				$recallData = str_ireplace("{APPT PROVIDER SIGNATURE}",$ProviderSign,$recallData);
				$recallData = str_ireplace("{LAST DOS}",'',$recallData);//--------------------------not done
				$recallData = str_ireplace("{APPT TIME}",$appStrtTime,$recallData);
				$recallData = str_ireplace("{NO SHOW APPOINTMENT}",$last_no_show[$patientid],$recallData);
				
				$recallData = str_ireplace("{RECALL DESCRIPTION}",'',$recallData);//---------  no data available
				$recallData = str_ireplace("{RECALL PROCEDURE}",'',$recallData);//-----------  no data available
				//PATIENT VARIABLE
				$recallData = str_ireplace("{DATE}",date('m-d-Y'),$recallData);	
				$recallData = str_ireplace("{ETHNICITY}",$patient_deta['ethnicity'],$recallData);	
				$recallData = str_ireplace("{LANGUAGE}",$patient_deta['language'],$recallData);
				$recallData = str_ireplace("{ADDRESS1}",$patient_deta['street'],$recallData);
				$recallData = str_ireplace("{ADDRESS2}",$patient_deta['street2'],$recallData);
				$recallData = str_ireplace("{PATIENT CITY}",$patient_deta['city'],$recallData);
				$recallData = str_ireplace("{DOB}",$PtDOB,$recallData);
				$recallData = str_ireplace("{PATIENT FIRST NAME}",$patient_deta['fname'],$recallData);
				$recallData = str_ireplace("{HOME PHONE}",$patient_deta['phone_home'],$recallData);
				$recallData = str_ireplace("{PatientID}",$patient_deta['id'],$recallData);
				$recallData = str_ireplace("{LAST NAME}",$patient_deta['lname'],$recallData);
				$recallData = str_ireplace("{PATIENT MRN}",$patient_deta['External_MRN_1'],$recallData);
				$recallData = str_ireplace("{PATIENT MRN2}",$patient_deta['External_MRN_2'],$recallData);
				$recallData = str_ireplace("{MIDDLE NAME}",$patient_deta['mname'],$recallData);
				$recallData = str_ireplace("{MOBILE PHONE}",$patient_deta['phone_cell'],$recallData);
				$recallData = str_ireplace("{PATIENT NAME TITLE}",$patient_deta['title'],$recallData);
				//$arrStateZip = explode(" ",$patient_deta[14]);
				$recallData = str_ireplace("{PATIENT STATE}",$patient_deta['state'],$recallData);
				$recallData = str_ireplace("{WORK PHONE}",$patient_deta['phone_biz'],$recallData);
				$recallData = str_ireplace("{PATIENT ZIP}",$patient_deta['postal_code'],$recallData);
				$recallData = str_ireplace("{RACE}",$patient_deta['race'],$recallData);
				$recallData = str_ireplace("{PT-KEY}",$patient_deta['temp_key'],$recallData);
				$stateZipCode="";
				if($patient_deta['state'] && $patient_deta['postal_code']){
					$stateZipCode = $patient_deta['state'].', '.$patient_deta['postal_code'];
				}else if($patient_deta['state']){
					$stateZipCode = $patient_deta['state'];
				}else if($patient_deta['postal_code']){
					$stateZipCode = $patient_deta['postal_code'];
				}
				$recallData = str_ireplace("{STATE, ZIP CODE}",$stateZipCode,$recallData);
				$recallData = str_ireplace("{STATE ZIP CODE}",$stateZipCode,$recallData);
				
				$strHTMLptSpec= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text" valign="top"  style="margin:0px;">
										'.$recallData.'
									</td>
								</tr>
							</table>';
							
				$strHTMLptSpec = str_ireplace("/$web_RootDirectoryName/interface/common/new_html2pdf/","",$strHTMLptSpec);
				$strHTMLptSpec = str_ireplace("/iMedicR4/interface/common/new_html2pdf/","",$strHTMLptSpec);
				$strHTMLptSpec = str_ireplace("/iMedicR4-Dev/interface/common/new_html2pdf/","",$strHTMLptSpec);
				$strHTMLptSpec = str_ireplace("/imwemr/interface/common/new_html2pdf/","",$strHTMLptSpec);
				$strHTMLptSpec = str_ireplace("</br>","",$strHTMLptSpec);
				
				//Read an HTML message body from an external file, convert referenced images to coded,
				preg_match_all("/src=\"(.*?)\"/",$strHTMLptSpec,$matches);
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
					$strHTMLptSpec=str_replace($path,$base64,$strHTMLptSpec);
				}
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($groupEmailConfig['header']."<br/>$strHTML $strHTMLptSpec<br/>".$groupEmailConfig['footer']);
			
				$mail->addAddress($patient_deta['email'],$patient_deta['sa_patient_name']);
				if (!$mail->send()) {
					$failed++;
					$error.='<br>Mailer error: ' . $mail->ErrorInfo;
				} else {
					$sent++;
					//save record for sent track
					imw_query("insert into exclude_sent_email set pt_id='$patientid', appt_id='$rw[appt_id]', appt_date='$rw[sa_app_start_date]', appt_time='$rw[sa_app_starttime]', temp_id='$recallTemplatesListId', sent_on='".date('Y-m-d H:i:s')."', sent_by='$_SESSION[authUserID]', report='Day Appt'");
				}
				// Clear all addresses and attachments for next loop
				$mail->clearAddresses();
				$mail->clearAttachments();
			}
		}
		//file_put_contents('test.txt',$error, FILE_APPEND);
		?>
        
		<script>
		top.fAlert("<?php echo "$sent/$num";?> mails sent successfully.");
		top.show_loading_image('hide');
        </script>
        
	<?php
		die(); 
		
	}
	else{
	?>
        <script>
		top.fAlert("No patient selected.");
		top.show_loading_image('hide');
        </script>
        
	<?php
	}
}else {
	?>
        <script>
		top.fAlert("No patient selected.");
		top.show_loading_image('hide');
        </script>
        
	<?php
}
?>