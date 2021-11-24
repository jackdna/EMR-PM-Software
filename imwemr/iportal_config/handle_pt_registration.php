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
?>
<?php
/*
File: handle_pt_registration.php
Purpose: Action script to approve/decline patients registered through patient portal
Access Type: Direct
*/
include_once("../config/globals.php");
include("./vocabulary.php");
$response = array();
function decode_vals($value){
	return stripslashes(html_entity_decode($value));
}

function tempKeyGen($size = '6',$pid) {
	$string = '';
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for ($i = 0; $i < $size; $i++) {
        $string .= $characters[mt_rand(0, (strlen($characters) - 1))];
		/*if($i==2) {
			$string .=$pid;	
		}*/
	}
	
    return $string;
}
if($_REQUEST['sel_op']=='approve' && $_REQUEST['row_id']!=""){
	$rowId = $_REQUEST['row_id'];
	$sql = "SELECT `fname`, `lname`, `email`, `dob`, `sex`, `address`, `city`, `state`, `postal_code`, `phone_home`, `phone_cell`, `phone_biz`, `phone_biz_ext` FROM `iportal_register_patient` WHERE `id`='".$rowId."'";
	$row = imw_query($sql);
	if($row){
		$data = imw_fetch_assoc($row);
		extract($data);
		
		/*Make Sure that Patient With same data does not exists*/
		$sql_confirm = "SELECT * FROM `patient_data` WHERE LOWER(`fname`)='".strtolower($fname)."' AND LOWER(`lname`)='".strtolower($lname)."' AND `DOB`='".$dob."' AND`sex`='".$sex."' AND `postal_code`='".$postal_code."' LIMIT 1";
		$sql_confirm_resp	= imw_query($sql_confirm);
		if($sql_confirm_resp && imw_num_rows($sql_confirm_resp)==0){
			$vquery = "select max(id) as ppid from patient_data";		
			$vsql = imw_query($vquery);
			$rt = imw_fetch_assoc($vsql);
			$idd=$rt["ppid"]+1;
			
			$temp_key = tempKeyGen(6, $idd);
			$sql1 = "INSERT INTO `patient_data`(`fname`, `lname`, `email`, `DOB`, `sex`, `street`, `city`, `state`, `postal_code`, `phone_home`, `phone_cell`, `phone_biz`, `phone_biz_ext`, `pid`, `temp_key`, `created_by`,`patientStatus`,`date`,`hipaa_mail`,`hipaa_email`,`hipaa_voice`) VALUES('".$fname."', '".$lname."', '".$email."', '".$dob."', '".$sex."', '".$address."', '".$city."', '".$state."', '".$postal_code."', '".$phone_home."', '".$phone_cell."', '".$phone_biz."', '".$phone_biz_ext."', '".$idd."', '".$temp_key."', '".$_SESSION['authUserID']."' , 'Active','".date("Y-m-d H:i:s")."','1','1','1')";
			
			$resp = imw_query($sql1);
			if($resp){
				$pt_id = imw_insert_id();
				$response = array('status'=>'success', 'pt_id'=>$pt_id);
				
				/*Set Request Status to Approved*/
				imw_query("UPDATE `iportal_register_patient` SET `approved`='1', `patient_id`='".$pt_id."' WHERE `id`='".$rowId."'");
			}
			else{
				$response = array('status'=>'error');
			}
		}
		else{
			/*Set Request Status to Rejected*/
			imw_query("UPDATE `iportal_register_patient` SET `approved`='2' WHERE `id`='".$rowId."'");
		}
	}
}
elseif($_REQUEST['sel_op']=='decline' && $_REQUEST['row_id']!=""){
	$rowId = $_REQUEST['row_id'];
	$resp = imw_query("UPDATE `iportal_register_patient` SET `approved`='2' WHERE `id`='".$rowId."'");
	if($resp){
		$response = array('status'=>'success');
	}
	else{
		$response = array('status'=>'error');
	}
}
if($response['status'] == 'success'){
	// include_once($GLOBALS['fileroot'].'/library/phpmailer/PHPMailerAutoload.php');
	$queryEmailCheck=imw_query("SELECT `g`.*, `f`.`name` AS `facility_name` FROM `groups_new` `g` JOIN `facility` `f` ON(`g`.`gro_id`=`f`.`default_group`) WHERE `f`.`facility_type`='1'");
	if(imw_num_rows($queryEmailCheck)>=1){
		$dEmailCheck=imw_fetch_object($queryEmailCheck);
		$groupEmailConfig['facility_name']=$dEmailCheck->facility_name;
		$groupEmailConfig['email']=decode_vals($dEmailCheck->config_email);
		$groupEmailConfig['pwd']=decode_vals($dEmailCheck->config_pwd);
		$groupEmailConfig['host']=decode_vals($dEmailCheck->config_host);
		$groupEmailConfig['header']=decode_vals($dEmailCheck->config_header);
		$groupEmailConfig['footer']=decode_vals($dEmailCheck->config_footer);
		$groupEmailConfig['port']=decode_vals($dEmailCheck->config_port);
		$groupEmailConfig['facility_name']=decode_vals($dEmailCheck->facility_name);
	}
	
	$sql = "SELECT `data`, `forwarder` FROM `iportal_autoresponder_templates` WHERE `type`='5' AND `del_status`='0' AND `status`='1' LIMIT 1";
	$template = imw_query($sql);
	$mail_data = "";
	if($template){
		$row = imw_fetch_assoc($template);
		$mail_data = $row['data'];
							
		$obj = new vocabulary;
		$obj->pt_id = $pt_id;
		$mail_data = $obj->parse($mail_data);
	}
	
	if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'] && $mail_data!=""){
		//Create a new PHPMailer instance
		$mail = new PHPMailer\PHPMailer;
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
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
		$mail->SMTPSecure = '';
		//Set who the message is to be sent from
		$mail->setFrom($groupEmailConfig['email'], $groupEmailConfig['facility_name']); 
		//Set an alternative reply-to address
		$mail->addAddress($email, $fname." ".$lname);
		
		//Set the subject line
		$mail->Subject = $groupEmailConfig['facility_name'].': Account Verified';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($mail_data);
		//Replace the plain text body with one created manually
		$mail->AltBody = '';
		//Attach an image file
		
		//send the message, check for errors
		if (!$mail->send()) {
			$returnErrMsg= "Mailer Error: " . $mail->ErrorInfo;
			$resp_array[$key]['mail_status']=0;
		} else {
			$returnSuccMsg= "Message sent!";
			$resp_array[$key]['mail_status']=1;
		}
	}
	else{
		$resp_array[$key]['mail_status']=2;
	}
}


echo json_encode($response);exit;
?>