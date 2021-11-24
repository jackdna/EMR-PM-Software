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
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST'] = $practicePath;
}
$ignoreAuth = true;
ini_set("memory_limit","3072M");
set_time_limit (0);
include_once(dirname(__FILE__)."/../../config/globals.php");
$_SESSION['authUserID']=1;
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');
$entered_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
$file_date_range=date("mdY",mktime(0,0,0,date("m"),date("d")-7,date("Y"))).'_'.date("mdY");
$pfx=",";
$credit_card_arr = array("AX"=>"American Express","Care Credit"=>"Care Credit","Dis"=>"Discover","MC"=>"Master Card","Visa"=>"Visa","Others"=>"Others");
$tender_type_code_arr = array("Self Pay"=>'Y',"MWEC-JE"=>'M',"Check"=>'K',"Cash"=>'H',"Credit Card"=>'C',"Batch Check"=>'G',"EFT"=>'Z',"EFT-CB"=>'P',"EFT CMS"=>'E',"Check-CB"=>'S',"CC-CB"=>'X',"Care Credit"=>'N',"VPG Payment"=>'D',"VPG JE"=>'D');

$fac_query = imw_query("select id,name from facility order by name");
while ($fac_res = imw_fetch_assoc($fac_query)) {
    $facArr[$fac_res['id']] = str_replace(',',' ',stripslashes($fac_res['name']));
}

$qry = imw_query("select patient_data.fname,patient_data.lname,patient_data.mname,patient_charge_list.patient_id,pcpi.payment_mode,pcpi.date_of_payment,pcpi.creditCardCo,pcdpi.paidForProc,pcdpi.payment_details_id,pcpi.checkNo,pcpi.facility_id
		from patient_charges_detail_payment_info as pcdpi join patient_chargesheet_payment_info as pcpi on pcpi.payment_id = pcdpi.payment_id
		join patient_charge_list on patient_charge_list.encounter_id=pcpi.encounter_id
		join patient_data on patient_data.id=patient_charge_list.patient_id
		where pcdpi.deletePayment='0' and pcdpi.paidForProc>0 and pcdpi.entered_date>='$entered_date' and patient_charge_list.del_status='0'
		order by pcpi.payment_mode asc,pcdpi.payment_details_id asc");
while ($row = imw_fetch_assoc($qry)){
	if($row['payment_mode']==""){$row['payment_mode']="Cash";}
	$pay_data_arr[trim($row['payment_mode'])][]=$row;
}
foreach($pay_data_arr as $pay_mod_key => $pay_mod_val){
	$pay_type="Patient Name";
	$eft_num="Blank";
	if($pay_mod_key=="Credit Card" || $pay_mod_key=="CC CB"){
		$pay_type="Card Type";
	}
	if(strstr(strtolower($pay_mod_key),'eft')){
		$pay_type="Blank";
		$eft_num="EFT#";
	}
	if(strstr(strtolower($pay_mod_key),'mwec je')){
		$pay_type="Blank";
		$eft_num="Blank";
	}
	if($pay_mod_key=="Batch Ck" || $pay_mod_key=="Batch Check" || $pay_mod_key=="Check" || $pay_mod_key=="Check CB" || $pay_mod_key=="Self Pay" || $pay_mod_key=="VPG JE" || $pay_mod_key=="VPG Payment"){
		$pay_type="CK#";
	}
	$csv_data.=" \n".$pay_mod_key.$pfx." \n";	
	$csv_data.="System generated transaction #".$pfx;
	$csv_data.="Date".$pfx;
	$csv_data.="Tender Type".$pfx;
	$csv_data.=$pay_type.$pfx;
	$csv_data.=$eft_num.$pfx;
	$csv_data.="Location code".$pfx;
	$csv_data.="Amt".$pfx . "\n"; 
	$tender_type_code=$tender_type_code_arr[$pay_mod_key];
	if($tender_type_code==""){
		$tender_type_code=$tender_type_code_arr[str_replace(' ','-',$pay_mod_key)];
	}
	foreach($pay_data_arr[$pay_mod_key] as $pay_data_key => $pay_data_val){
		$pay_data=$pay_data_arr[$pay_mod_key][$pay_data_key];
		$patientName = str_replace(',',' ',ucwords(trim($pay_data['fname']." ".$pay_data['lname']." ".$pay_data['mname'])));
		
		$csv_data.=$pay_data['payment_details_id'].$pfx;
		$csv_data.=get_date_format($pay_data['date_of_payment']).$pfx;
		$csv_data.=$tender_type_code.$pfx;
		if($pay_type=="Card Type"){
			$csv_data.=$credit_card_arr[$pay_data['creditCardCo']].$pfx;
			$csv_data.=''.$pfx;
		}else if($eft_num=="EFT#"){
			$csv_data.=''.$pfx;
			$csv_data.=$pay_data['checkNo'].$pfx;
		}else if($eft_num=="EFT#" || $pay_type=="CK#"){
			$csv_data.=$pay_data['checkNo'].$pfx;
			$csv_data.=''.$pfx;
		}else if($pay_type=="Patient Name"){
			$csv_data.=$patientName.$pfx;
			$csv_data.=''.$pfx;
		}else{
			$csv_data.=''.$pfx;
			$csv_data.=''.$pfx;
		}
		$csv_data.=$facArr[$pay_data['facility_id']].$pfx;
		$csv_data.=$pay_data['paidForProc'].$pfx." \n"; 
	}
}
$destFileName="sage_".$file_date_range.".csv";
$file_name="/sage/".$destFileName;
$csv_file_name= write_html("",$file_name);
$fp = fopen($csv_file_name,'a+');
fwrite($fp,$csv_data);
fclose($fp);

FUNC_uploadFile($destFileName,$csv_file_name);

function FUNC_uploadFile($csvFileName, $fileDir)
{
	$sage_query = imw_query("select * from sage_sftp_credentials");
	$sage_res = imw_fetch_assoc($sage_query);
	$strTimeOut	= 1000;

	$strServer = $sage_res['sage_host_name'];
	$strServerPort = $sage_res['port_number'];
	
	$strServerUsername = $sage_res['sage_sftp_username'];
	$strServerPassword = $sage_res['sage_sftp_password'];
		
	$remote_directory = $sage_res['sage_directory_path'];
	
	/* Pear package */
	include('Net/SFTP.php');
	define('NET_SFTP_LOGGING', NET_SFTP_LOG_COMPLEX); // or NET_SFTP_LOG_SIMPLE
	define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
	//define('NET_SSH2_LOGGING', 2);
	
	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($strServer,$strServerPort,$strTimeOut);
	if( !$sftp->login($strServerUsername,$strServerPassword) )
	{
		echo"\n Login Failed \n sftp error log \n";
		print_r($sftp->getSFTPErrors());
		echo"\n last sftp error \n";
		print_r($sftp->getLastSFTPError());
		echo"\n sftp log \n";
		print_r($sftp->getSFTPLog());
		exit();
	}
	else
	{
		echo "\n Login Success";
	}
	
	try
	{
		//now check is that folder exist on server if not then create it
		if( !$sftp->file_exists($remote_directory) )
		{
			//create directory
			$sftp->mkdir($remote_directory);
		}
		
	}
	catch (Exception $e)
	{
		echo "\n Caught exception: ",  $e->getMessage();
	}
	
	/* Upload the local file to the remote server 
	   put('remote file', 'local file');
	 */
	if(file_exists($fileDir))
	{
		echo"\n Trying to upload $fileDir/$csvFileName on ".$remote_directory.'/'.$csvFileName;
		$success = $sftp->put($remote_directory.'/'.$csvFileName, $fileDir, NET_SFTP_LOCAL_FILE);
		
		echo "\n Upload :".$success;
		
		if($success)
		{
			//rename($fileDir.'/'.$csvFileName, $fileDir.'/Archive/'.$csvFileName);//move that file to archieve folder
		}
	}
	else
	{
		echo "\n ".$fileDir." file not exist";
	}
}
?>