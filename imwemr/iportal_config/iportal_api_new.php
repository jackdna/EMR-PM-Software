<?php
$ignoreAuth=true;

include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	//$arr_return="<br>".constant("IPORTAL_SERVER")."=".md5($_REQUEST['IPORTAL_REQUEST'])."<br>".md5(constant("IPORTAL_SERVER"));
	die("[Error]:401 Unauthorized Access ");
}
// require_once '../library/phpmailer/PHPMailerAutoload.php';
use PHPMailer\PHPMailer;
include("./vocabulary.php");

function get_records($qryQry){
	$qryRowsReturn=array();
	$qryQryRes = imw_query($qryQry);
	if($qryQryRes){
		while($qryRow = imw_fetch_object($qryQryRes)){
			$qryRowsReturn[] = $qryRow;
		}		
		
	}else if(!$qryQryRes){
		$qryRowsReturn[]="[Error-]: query failed: $statement (" . imw_error() . ")";
	 }
	return $qryRowsReturn;	
}
function get_records_arr($qryQry){
	$qryRowsReturn=array();
	$qryQryRes = imw_query($qryQry);
	if($qryQryRes){
		while($qryRow = imw_fetch_assoc($qryQryRes)){
			$qryRowsReturn[] = $qryRow;
		}		
	}else if(!$qryQryRes){
		$qryRowsReturn[]="[Error-]:  query failed: $statement (" . imw_error() . ")";
	}
	return $qryRowsReturn;	
}
function sqlQuery_ip($statement){
  $rez=array();
  $query = imw_query($statement);
  if($query && imw_num_rows($query)>0){
  	$rez = @imw_fetch_assoc($query);
  }else if(!$query){
	$rez[]="[Error-]: query failed: $statement (" . imw_error() . ")";
  }
  return $rez;
}
function save_pt_sig($img_content,$img_name,$htmlFolder){
	/*if(!$htmlFolder) {
		$htmlFolder = "new_html2pdf";	
	}*/
	$ret=0;
	$sig_path='';
	global $webServerRootDirectoryName;
	global $web_RootDirectoryName;
	if(trim($img_content) && $img_name) {
		//THIS WAS THE R7 OLD ADDRESS WHICH IS CORRECTED..
		//$sigFolder = $webServerRootDirectoryName.$web_RootDirectoryName."/interface/common/".$htmlFolder."/iportal_sig";
		$sigFolder = data_path()."iportal_sig";
		if(!is_dir($sigFolder)){		
			mkdir($sigFolder, 0777);
		}
		$img_data=base64_decode($img_content);
		if($img_data){
			$sig_path = $sigFolder."/".$img_name;
			file_put_contents($sig_path, $img_data);
			$imagecreate = imagecreatefromjpeg($sig_path);
			if($imagecreate){
				$ret = 1;
			}
		}
		
	}
	return $ret;
}

function decode_vals($value){
	return stripslashes(html_entity_decode($value));
}

$req_qry1=unserialize(trim($_REQUEST['all_queries']));
$req_qry1 = $req_qry1['qry'];
$resp_array = array();
$req_img_name=trim(urldecode($_REQUEST['get_img_name']));
foreach($req_qry1 as $key=>$rQueries){
	
	$resp_array[$key] = "";
	$req_qry=trim(urldecode($rQueries['sql_satement']));
	$req_qry_type=$rQueries['type'];
	
	if($req_qry && $req_qry_type){
		switch($req_qry_type) {
			case "select":
				$retun_arr=array();
				$qry_res=sqlQuery_ip($req_qry);
				if(is_array($qry_res)){
					$resp_array[$key] = $qry_res;
				}
			break;
			case "multi_select":
				$qry_res=get_records($req_qry);
				$resp_array[$key] = $qry_res;
			break;
			case "multi_select_assoc":
				$qry_res=get_records_arr($req_qry);
				$resp_array[$key] = $qry_res;	
			break;
			case "insert":
				$qrt_insert=imw_query($req_qry);
				if($qrt_insert){
					$ret=imw_insert_id();
				}else{$ret="[Error-]: ".imw_error().$qrt_insert;}
				$resp_array[$key] = $ret;
			break;
			case "update":
				$qrt_update=imw_query($req_qry);
				if($qrt_update){
					$ret="1";
				}else{$ret="[Error-]: ".imw_error().$qrt_insert;}
				$resp_array[$key] = $ret;
			break;
			case "delete":
				$qrt_delete=imw_query($req_qry);
				if($qrt_delete){
					$ret=imw_affected_rows();
					$resp_array[$key] = $ret;
				}
			break;
			case "image":
				$data = file_get_contents($req_qry);
				$ret=base64_encode($data);
				$resp_array[$key] = $ret;
			break;
			case "iportal_patient_sign":
				$htmlFolder = "html2pdf";
				if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
					$htmlFolder = "new_html2pdf";
				}			
				$ret = save_pt_sig($req_qry,$req_img_name,$htmlFolder);
				$resp_array[$key] = $ret;
			break;
			case "pt_login":
				$login_sucess=0;
				$qry_user_arr=$req_qry;
				$qry_u_explode=$qry_user_arr;
				//=========================IF PASSWORS SHA1 FORMAT IN DATABASE===========================================//
				$arr_data=explode("'",$qry_u_explode);
				$username_encode=$arr_data[1];
				$password_encode=$arr_data[3];
				if($username_encode && $password_encode){
					$qry_user_arr="SELECT id,username,fname,lname,mname from patient_data where username='".$username_encode."' AND password='".$password_encode."' AND locked = 0 limit 0,1";
				}
				//============================================================================================//
				$resp_data = array();
				$resp_data['success'] = false;
				
				$arr_ret=array();
				if($qry_user_arr!=""){					
					$q_pt_pass=$qry_user_arr;
					$r_pt_pass=imw_query($q_pt_pass);
					if($r_pt_pass && imw_num_rows($r_pt_pass)>0){
						$row_pt_pass=imw_fetch_assoc($r_pt_pass);
						$resp_data['success'] = true;
						$resp_data['data'] = $row_pt_pass;
						$log_qry = "insert into patient_loginhistory set patient_id='".$row_pt_pass['id']."',logindatetime=now()";
						imw_query($log_qry);
					}
				}
				$resp_array[$key] = $resp_data;
			break;
			case "resp_login":
				$login_sucess=0;
				$qry_user_arr=$req_qry;
				
				$resp_data = array();
				$resp_data['success'] = false;
				
				if($qry_user_arr!=""){
					$q_pt_pass=$qry_user_arr;
					$r_pt_pass=imw_query($q_pt_pass);
					if($r_pt_pass && imw_num_rows($r_pt_pass)>0){
						$row_pt_pass=imw_fetch_assoc($r_pt_pass);
						
						$qry_pd="select id,username,fname,lname,mname from patient_data where id='".$row_pt_pass['patient_id']."' limit 0,1";
						$pd = imw_query($qry_pd);
						$pdata = array();
						if($pd && imw_num_rows($pd)){
							$pdata = imw_fetch_assoc($pd);
							$log_qry = "insert into patient_loginhistory set patient_id='".$pdata['id']."',logindatetime=now() , pt_rp_id = '".$pdata['id']."'";
							imw_query($log_qry);
						}
						$resp_data['success'] = true;
						$resp_data['data'] = $row_pt_pass;
						$resp_data['pd'] = $pdata;
					}
				}
				$resp_array[$key] = $resp_data;
			break;
			case "register_patient":
				$qrt_insert=imw_query($req_qry);
				
				if($qrt_insert){
					$ret=imw_insert_id();
					/*Send Email to the patient*/
						//$queryEmailCheck=imw_query("SELECT `g`.*, `f`.`name` AS `facility_name` FROM `groups_new` `g` JOIN `facility` `f` ON(`g`.`gro_id`=`f`.`default_group`) WHERE `f`.`facility_type`='1'");
					
						$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1");
						
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
						
						
						$mail_data = "";
						$mail_dt = imw_query("SELECT `data`, `forwarder` FROM `iportal_autoresponder_templates` WHERE `type`='4' AND `del_status`='0' AND `status`='1' LIMIT 1");
						if($mail_dt){$mail_dt=imw_fetch_assoc($mail_dt);$mail_dt=$mail_dt['data'];}
						$obj = new vocabulary;
						$obj->reg_token = $rQueries["reg_token"];
						$mail_data = $obj->parse($mail_dt);
						
						if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'] && $mail_data!=""){
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
							$mail->SMTPSecure = '';
							//Set who the message is to be sent from
							$mail->setFrom($groupEmailConfig['email'],  $groupEmailConfig['facility_name']);
							//Set an alternative reply-to address
							$mail->addAddress($rQueries['user_email'],$rQueries['user_name']);
							
							//Set the subject line
							//$mail->Subject = $groupEmailConfig['facility_name'].': Confirm Email Id';
							$mail->Subject = 'Confirm Email Id';
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
					/*End sending email*/
				}else{$ret="[Error-]: ".imw_error().$qrt_insert;}
				$resp_array[$key]['ins_id'] = $ret;
			break;
			case "appt_resp_message":
			
				file_put_contents(dirname(__FILE__).'/testing.txt', 'IMW Testng');
			
				$resp_array[$key]['msg']="";
				$template = "";
				$msg = "";
				$dt1 = imw_query($req_qry);
				if($dt1){$template=imw_fetch_assoc($dt1);}
				$dt2 = imw_query("SELECT pd.fname, pd.mname, pd.lname, pm.sender_id, pm.msg_subject, pm.msg_data, DATE_FORMAT(pm.msg_date_time,'%m %h:%i %p') AS msg_date_time FROM patient_messages pm INNER JOIN patient_data pd ON(pm.sender_id=pd.id) WHERE pt_msg_id = '".$rQueries['msg_id']."'");
				if($dt2){$msg=imw_fetch_assoc($dt2);}
				if(isset($template['data'])){
					$name_sendTo = $msg['lname'].', '.$msg['fname'];
					$ORsenderName = "Patient Co-ordinator";
					$sentDate = $msg["msg_date_time"];
					$originalSubject = $msg["msg_subject"];
					$originalTextPrefix = "<br /><br />----ORIGINAL MESSAGE----<br />";
					$originalTextPrefix .= "	From: ".$name_sendTo."<br />";
					$originalTextPrefix .= "	To: ".$ORsenderName."<br />";
					$originalTextPrefix .= "	Sent: ".$sentDate."<br />";
					$originalTextPrefix .= "	Subject: ".$originalSubject."<br /><br />";
					
					$originalTextPrefix .= $msg["msg_data"];
					$obj = new vocabulary;
					$obj->pt_id = ($msg['sender_id']=="")?$rQueries['pat_id']:$msg['sender_id'];
					$obj->phy_id = $rQueries["phy_id"];
					$obj->fac_name = $rQueries["fac_name"];
					$template['data'] = $obj->parse($template['data']);
					
					$msg_data = $template['data'].$originalTextPrefix;
					$resp_array[$key]['msg'] = trim($template['data']);  /*Show Message on send Appointment Screen*/
					
					$mail_data = $msg_data; /*Forward Message */
					
					$msg_subject = imw_real_escape_string("Re: ".$msg["msg_subject"]);
					$msg_data = imw_real_escape_string($msg_data);
					
					$req_qry = "INSERT INTO patient_messages SET receiver_id = '".$msg['sender_id']."', sender_id = '0', communication_type = 1, msg_subject = '".$msg_subject."', msg_data = '".$msg_data."', message_urgent='".$message_urgent."', replied_id='".$rQueries['msg_id']."'";
					
					
					$req_qry_obj = imw_query($req_qry);
					
					if($req_qry_obj){
						/*Send Email to the patient*/
						$queryEmailCheck=imw_query("SELECT `g`.*, `f`.`name` AS `facility_name` FROM `groups_new` `g` JOIN `facility` `f` ON(`g`.`gro_id`=`f`.`default_group`) WHERE `f`.`facility_type`='1'");
						
						$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1");
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
						
						
						if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'] && $mail_data!="" && $template['forwarder']!=""){
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
							$mail->SMTPSecure = '';
							//Set who the message is to be sent from
							$mail->setFrom($groupEmailConfig['email'], $groupEmailConfig['facility_name']);
							//Set an alternative reply-to address
							$mail->addAddress($template['forwarder']);
							
							//Set the subject line
							$mail->Subject = 'Forward Message';
							//Read an HTML message body from an external file, convert referenced images to embedded,
							//convert HTML into a basic plain-text alternative body
							$mail->msgHTML($mail_data);
							//Replace the plain text body with one created manually
							$mail->AltBody = '';
							//Attach an image file

							//send the message, check for errors
							if (!$mail->send()) {
								//$returnErrMsg= "Mailer Error: " . $mail->ErrorInfo;
								//$resp_array[$key]['mail_status']=0;
							} else {
								$returnSuccMsg= "Message sent!";
								//$resp_array[$key]['mail_status']=1;
							}
						}
						else{
							//$resp_array[$key]['mail_status']=2;
						}
					/*End sending email*/
					}
				}
				$resp_array[$key]['status']="success";				
			break;
		}
	}	
}
$resp_array = serialize($resp_array);
echo $resp_array;
?>