<?php 
$ignoreAuth = true;
require_once("../../../../config/globals.php");
set_time_limit(0);
require_once(dirname(__FILE__)."/../../../../library/classes/class.erx_functions.php");
$objERX			= new ERXClass;
$cookie_path 	= $objERX->cookie_path;

//--- GET ERX STATUS AND EMDEON ACCESS URL -------
$get_erx_status_and_url = $objERX->get_erx_status_and_url();
$Allow_erx_medicare	= $get_erx_status_and_url['Allow_erx_medicare'];
$EmdeonUrl			= $get_erx_status_and_url['EmdeonUrl'];

$msg_info=array();

//---FUNCTION TO GET EMDEON PRESCRIBER IDs---
function updateEmdeonPrescriberIDs($provider,$eRx_user_name,$erx_password,$eRx_facility_id,$user_npi){
	global $EmdeonUrl; global $cookie_path;
	$cookie_file = $cookie_path.'/cookie_'.$provider.'.txt';
	$cur = curl_init();
	$url = $EmdeonUrl."/servlet/DxLogin?userid=".$eRx_user_name."&PW=".$erx_password."&target=html/LoginSuccess.html&testLogin=true";
	curl_setopt($cur,CURLOPT_URL,$url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$data = curl_exec($cur);
	curl_close($cur);
	preg_match('/Login Success/',$data,$loginSuccessArr);
	if(count($loginSuccessArr)){
		$url = $EmdeonUrl."/servlet/servlets.apiPersonServlet?actionCommand=listCaregivers&apiuserid=".$eRx_user_name."&facilityobjid=".$eRx_facility_id;
		$cur = curl_init();
		curl_setopt($cur,CURLOPT_URL,$url);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
		$cg_data = curl_exec($cur);
		preg_match('<--BEGIN CAREGIVER LIST>',$cg_data,$correct_cg_data);
		if($correct_cg_data){
			$cg_data = preg_replace('/<--BEGIN CAREGIVER LIST>/','',$cg_data);
			$cg_data = preg_replace('/<--END CAREGIVER LIST>/','',$cg_data);
		//	file_put_contents($cookie_path."/caregivers_".date('Ymd').".txt",$cg_data);
			$cg_data_arr = preg_split('/'.chr(10).'/', $cg_data, -1, PREG_SPLIT_NO_EMPTY);	//pre($cg_data_arr);
			if(count($cg_data_arr)>=3){
				$erx_provider_id = $erx_provider_name = $erx_provider_npi = '';
				for($i=0;$i<count($cg_data_arr);$i++){
					$curr_line = $cg_data_arr[$i];
					$curr_line_arr = explode('=',$curr_line);
					$field = $curr_line_arr[0];
					$value = $curr_line_arr[1];
					switch($field){
						case 'CAREGIVEROBJID'	:	$erx_provider_id	= $value; break;
						case 'CAREGIVERNAME'	:	$erx_provider_name	= $value; break;
						case 'CAREGIVERNPI'		:	$erx_provider_npi	= $value; break;
					}
					
					if($erx_provider_id != '' && $erx_provider_name != '' && $erx_provider_npi != '' && ($user_npi == $erx_provider_npi)){
						$sql1 = "UPDATE users SET eRx_prescriber_id='".$erx_provider_id."' WHERE user_npi = '$user_npi' AND id = '".$provider."' AND user_npi <> '' LIMIT 1";
						$res1 = imw_query($sql1);
						if($res1){
							return '<font color="green">Prescriber ID updated for eRx username: '.$eRx_user_name.'</font>';
						}
						//echo $sql1 .imw_error().'<hr>';
						$erx_provider_id = $erx_provider_name = $erx_provider_npi = '';
					}
				}
			}						
		}else{
			$error = 'Unable to get CHC Caregiver ID. Contact administrator.';
		}
		//--- Log out from emdeon erx --------
		$cur = curl_init();
		$url = $EmdeonUrl."/servlet/lab.security.DxLogout?userid=".$eRx_user_name."&BaseUrl=".$EmdeonUrl."&LogoutPath=/html/AutoPrintFinished.html";
		curl_setopt($cur,CURLOPT_URL,$url);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
		$data = curl_exec($cur);
		curl_close($cur);
		unlink($cookie_file);
		
	}else{
		$error = "Unable to login to eRx Portal with username: $eRx_user_name";
		unlink($cookie_file);
	}
	return $error;
}


$q	= "SELECT id,eRx_user_name,erx_password,eRx_facility_id,user_npi FROM users WHERE eRx_user_name!='' AND erx_password!='' AND eRx_facility_id!='' AND ";
$q .= "(eRx_prescriber_id='' || eRx_prescriber_id='0') AND user_npi != '' AND delete_status = 0";
$res=imw_query($q);
if($res && imw_num_rows($res)==0){
	$msg_info[] = "<br><br><b>No user left with eRx CareGiver ID. You may close this page now.</b>";
    $color = "green";
}else if($res && imw_num_rows($res)>0){
	while($rs = imw_fetch_assoc($res)){
		$user_id 			= $rs['id'];
		$erx_user_name 		= $rs['eRx_user_name'];
		$erx_password 		= $rs['erx_password'];
		$erx_facility_id 	= $rs['eRx_facility_id'];
		$user_npi 			= $rs['user_npi'];
		if($Allow_erx_medicare){
		//	echo $user_id.'<br>'.$erx_user_name.'<br>'.$erx_password.'<br>'.$erx_facility_id.'<br>'.$user_npi.'<hr>';
			$error = updateEmdeonPrescriberIDs($user_id,$erx_user_name,$erx_password,$erx_facility_id,$user_npi);
			$msg_info[] = $error;
		    $color = "red";
		}else{
			$msg_info[] = '<br><br><b>eRx Not allowed.</b><br>';
		    $color = "red";
		}
	}	
}else if(!$res){
    $msg_info[] = '<br><br><b>Update 13 run FAILED!</b><br>';
    $color = "red";
}
?>
<html>
<head>
<title>Update 13 - This update may take longer time than expected.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<h3>update complete.</h3>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>