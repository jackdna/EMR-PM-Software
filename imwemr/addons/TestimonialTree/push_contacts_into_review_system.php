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

/****THIS FUNCTIONALITY IS DEPENDING UPON CONFIGURATION VALUE IN PRACTICE CONFIGRUATION FILE***/

$ignoreAuth = true;
//$practicePath = 'imwemr';
if($argv[1]){
	$practicePath = trim($argv[1]);
}

$_SERVER['REQUEST_URI'] = $practicePath;
$_SERVER['HTTP_HOST']= $practicePath;

require_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
set_time_limit(0);

if(!constant('TESTIMONIALTREE_SUBMIT')) die('Testimonial Tree Data Submission not allowed.');
if(!is_array($GLOBALS['TESTIMONIALTREE_CONF'])) die('Testimonial Tree Data Submission configuration not found.');

//$today 				= date('Y-m-d');
$yesterday = date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));

$surveyId			= $GLOBALS['TESTIMONIALTREE_CONF']['surveyId'];
$ftpdomain			= $GLOBALS['TESTIMONIALTREE_CONF']['ftpdomain'];
$ftpuser			= $GLOBALS['TESTIMONIALTREE_CONF']['ftpuser'];
$ftppass			= $GLOBALS['TESTIMONIALTREE_CONF']['ftppass'];


/*****SELECT INITIAL POPULATION OF PATIENT FROM TODAY'S APPEARED PATIENTS******/
$main_q = "SELECT sa.id, sa.sa_patient_id,sa.sa_facility_id, sa.sa_doctor_id, pd.fname, pd.lname,pd.email, pd.phone_cell FROM schedule_appointments sa 
			JOIN patient_data pd ON (pd.id = sa.sa_patient_id AND pd.lname != 'doe') 
			JOIN previous_status ps ON (ps.sch_id = sa.id AND ps.status='13') 
			WHERE pd.id <> 0 AND pd.pid <> 0 
				AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND date_format(sa.sa_app_start_date,'%Y-%m-%d') = '".$yesterday."' 
			GROUP BY (sa.sa_patient_id)"; 

$res = imw_query($main_q);
if($res && imw_num_rows($res)>0){
	$oSaveFile 	= new SaveFile;
	$csv_file	= $oSaveFile->upDir.'/tmp/'."testimonialtree".date('YmdHis').".csv";
	$data_array = array();
	$patients = $appts = array();
	while($rs = imw_fetch_assoc($res)){
		$pat_email			= $rs['email'];
		$pat_cell			= str_replace(array(' ','-'),'',$rs['phone_cell']);
		
		if(!empty($pat_email)){ //if multiple email entered with comma, keep the first one.
			$pat_email_temp		= explode(',',$pat_email);
			if(count($pat_email_temp)>=1) $ptEmail = strtolower($pat_email_temp[0]);
		}
		if(!empty($pat_cell)){//if multiple cell phones entered with comma, keep the first one.
			$pat_cell_temp		= explode(',',$pat_cell);
			if(count($pat_cell_temp)>=1) $ptSMSNumber = str_replace(array(' ','-'),'',$pat_cell_temp[0]);
		}
		$sch_id				= $rs['id'];
		$pat_id				= $rs['sa_patient_id'];
		if(!empty($ptEmail)){// if email is available, then only proceed. Phone is optional
			$patients[]			= $pat_id;
			$appts[]			= $sch_id;
			$sa_doctor_id	 	= $rs['sa_doctor_id'];
			$doc_rs				= getUserDetails($sa_doctor_id,'user_npi');
			$ptName				= $rs['fname'].' '.$rs['lname'];
//			$ptEmail			= $rs['email'];
//			$ptSMSNumber		= $rs['phone_cell'];
			$doctor_id			= 'raleighop_'.$doc_rs['user_npi'];
			
			$data_array[] = array($ptName,$ptEmail,$ptSMSNumber,$doctor_id,$surveyId);
		}else{
			log_testimonial_tree($pat_id,$sch_id,'Email','Email not available.'); //LOG THE REQUEST AND RESPONSE.
		}
	}
	$csvfile 		= fopen($csv_file,"a+");
	fputcsv($csvfile, array('Name','Email','Phone','Doctor','Survey'));
	foreach( $data_array as $line ){
		fputcsv($csvfile, $line);
	}	
	fclose($csvfile);
	
	$ftp_conn = ftp_connect( $ftpdomain );
	if( !$ftp_conn )
	{
		log_testimonial_tree( '','','FTP_CONNECT','Unable to connect.' );
	}
	$login = ftp_login( $ftp_conn, $ftpuser, $ftppass );
	if( !$login ) 
	{
		log_testimonial_tree( '','','FTP_LOGIN','Unable to login.' );
	}
	// turn passive mode on as this is required to make this script working on server where fire wall is active.
	if(!ftp_pasv($ftp_conn, true))
	{
		log_testimonial_tree( '','','FTP_PASV','Unable to switch to passive mode.' );
	}
	
	
	if( ftp_put( $ftp_conn, "testimonialtree.csv", $csv_file, FTP_ASCII ) ){
		log_testimonial_tree( implode(',',$patients),implode(',',$appts),'FTP_PUT','File uploaded.' );
	}else{	
		log_testimonial_tree( implode(',',$patients),implode(',',$appts),'FTP_PUT','Unable to upload file.' );
	}
	
	
}else{
	$imw_error = trim( 'No appointment found. '.imw_error() );
	log_testimonial_tree( '','',$main_q,$imw_error );
}

function log_testimonial_tree( $pt,$sch,$request,$response )
{
	$q="INSERT INTO testimonial_tree_logs SET patient_id 	= '$pt', sch_id	= '$sch', data_sent	= '".addslashes( $request )."', response='".addslashes( $response )."', ";
	$q.= "logtime	= '".date( 'Y-m-d H:i:s' )."'";			
	$res = imw_query( $q );
}
?>