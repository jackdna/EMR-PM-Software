<?php

$ignoreAuth = true;
$practicePath = 'imwemr';
if($argv[1]){
	$practicePath = trim($argv[1]);
}

$_SERVER['REQUEST_URI'] = $practicePath;
$_SERVER['HTTP_HOST']= $practicePath;

require_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);

if(!constant('REVIEWINC_SUBMIT')) die('ReviewInc Submission not defined');


$today 			= date('Y-m-d');
$mode			= $GLOBALS['REVIEWINC_CONF']['mode'];
$corporateId	= $GLOBALS['REVIEWINC_CONF']['corporateId'];
$apiKey			= $GLOBALS['REVIEWINC_CONF']['apiKey'];;


/*****SELECT INITIAL POPULATION OF PATIENT FROM TODAY'S APPEARED PATIENTS******/
$main_q = "SELECT sa.id, sa.sa_patient_id,sa.sa_facility_id, sa.sa_doctor_id, pd.fname, pd.lname,pd.email, pd.phone_cell FROM schedule_appointments sa 
			JOIN patient_data pd ON (pd.id = sa.sa_patient_id AND pd.lname != 'doe') 
			JOIN previous_status ps ON (ps.sch_id = sa.id AND ps.status='13') 
			WHERE pd.id <> 0 AND pd.pid <> 0 
				AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND date_format(sa.sa_app_start_date,'%Y-%m-%d') = '".$today."' 
			GROUP BY (sa.sa_patient_id)"; 

$res = imw_query($main_q);echo imw_error();
if($res && imw_num_rows($res)>0){
	while($rs = imw_fetch_assoc($res)){
		$pat_email			= $rs['email'];
		$pat_cell			= str_replace(array(' ','-'),'',$rs['phone_cell']);
		
		if(!empty($pat_email)){ //if multiple email entered with comma
			$pat_email_temp		= explode(',',$pat_email);
			if(count($pat_email_temp)>1) $pat_email = $pat_email_temp[0];
		}
		if(!empty($pat_cell)){//if multiple cell phones entered with comma
			$pat_cell_temp		= explode(',',$pat_cell);
			if(count($pat_cell_temp)>1) $pat_cell = $pat_cell_temp[0];
		}
		
		if(!empty($pat_email) || !empty($pat_cell)){// if email or phone cell is available, then only proceed.
			$sch_id				= $rs['id'];
			$pat_id				= $rs['sa_patient_id'];
			$sa_facility_id 	= $rs['sa_facility_id'];
			$sa_doctor_id	 	= $rs['sa_doctor_id'];
			
			$fac_rs				= get_facility_details($sa_facility_id);
			$doc_rs				= getUserDetails($sa_doctor_id,'lname');
			
			$branchApiId		= $sa_doctor_id.'_'.urlencode($doc_rs['lname']);
			$reviewName			= urlencode($rs['fname'].' '.$rs['lname']);
			$reviewEmail		= $rs['email'];
			$reviewSMSNumber	= $rs['phone_cell'];
			$hidden1			= urlencode($fac_rs['name']);
			$productionMode		= $mode=='T' ? 'false' : 'true';
			$sendType			= 'email';
			if(!empty($pat_cell)) $sendType			= 'sms';
			$curl_site_url 	= "https://www.nr4.me/PostInvite.aspx";
			$post_data		= "corporateId=$corporateId&apiKey=$apiKey&branchApiId=$branchApiId&reviewName=$reviewName&reviewEmail=$reviewEmail&";
			$post_data	   .= "reviewSMSNumber=$reviewSMSNumber&hidden1=$hidden1&productionMode=$productionMode&sendType=$sendType";
			//echo $curl_site_url;echo '<hr>';echo $post_data; die;
			$cur 		= curl_init($curl_site_url);
			curl_setopt($cur, CURLOPT_POST, 1);
			curl_setopt($cur, CURLOPT_POSTFIELDS,$post_data);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($cur, CURLOPT_TIMEOUT, 300);
			$output	=  curl_exec($cur);
			if (curl_errno($cur)){
				$ERROR = curl_errno($cur).': '.curl_error($cur);
				log_review_inc($pat_id,$sch_id,$mode,$curl_site_url.'?'.$post_data,$ERROR); //LOG THE REQUEST AND RESPONSE.
				echo "Curl Error: " . curl_error($cur);     
				echo "<br>";
			} 
			else{
				log_review_inc($pat_id,$sch_id,$mode,$curl_site_url.'?'.$post_data,$output); //LOG THE REQUEST AND RESPONSE.
				echo ($output);
			}
			curl_close($cur);
		}else{
			log_review_inc($pat_id,$sch_id,$mode,'','Email and Cellphone not available.'); //LOG THE REQUEST AND RESPONSE.
		}
	}
	
}else{
	$imw_error = trim('No appointment found. '.imw_error());
	log_review_inc('0','0',$mode,$main_q,$imw_error);
}

function log_review_inc($pt,$sch,$mode,$request,$response){
	$q = "INSERT INTO review_inc_logs SET 
			patient_id 	= '$pt', 
			sch_id		= '$sch', 
			mode		= '$mode', 
			data_sent	= '".addslashes($request)."', 
			response	= '".addslashes($response)."', 
			sentdate	= '".date('Y-m-d H:i:s')."'";
	$res = imw_query($q); if(!$res) echo $q.imw_error();
}
?>