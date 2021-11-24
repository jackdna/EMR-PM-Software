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

?><?php
/*
File: get_reports_emdeon.php
Purpose: Download billing reports from clearing house.
Access Type: Include file 
*/
set_time_limit(0);
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
require_once(dirname(__FILE__).'/../../library/classes/billing_functions.php');
require_once(dirname(__FILE__).'/../../library/classes/SaveFile.php');
$objEBilling = new ElectronicBilling();
$oSaveFile = new SaveFile();

$basePath 	= $oSaveFile->upDir.'/batchfiles';	/*Path to save report file downloaded*/
$sharePath 	= $basePath.'/others/get_report';	/*Report copy path for shared era*/

$ClearingHouse	= $objEBilling->ClearingHouse();
$CL_mode 		= $ClearingHouse[0]['connect_mode'];
$CL		 		= $ClearingHouse[0]['abbr'];
$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];


//GROUP DETAILS
$group_details 			= $objEBilling->get_groups_detail();

//--DUMMY URL--
//https://Sn.ediinsight.com/transfer/list.php?mode=INFO
//https://Sn.ediinsight.com/transfer/download.php?file=xxxxxxxx (“xxxxxxx” is a filename obtained from the LIST method)

//----GET LIST OF FILES FOR ALL GROUP WHICH HAVE CREDENTIALS ASSIGNED------
foreach($group_details as $group_id=>$group_rs){
	//-------SKIPPING LOOP IF ANY OF THE REQUIRED DATA IS MISSING------
	if(trim($group_rs['user_id'])=='' || trim($group_rs['user_pwd'])=='') continue;
	
	$cur = curl_init($CL_url."transfer/list.php?mode=INFO");
	curl_setopt($cur, CURLOPT_USERPWD, $group_rs['user_id'].":".$group_rs['user_pwd']);
	curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
	$output	=  curl_exec($cur);
	$error	= curl_error($cur);
	curl_close($cur);
	//echo $output.'<hr>'.$error.'<hr><hr><hr>';
	//$output = '277_21758962I1630030C2380.277|04/07/2015|277 Response File;SP_1679I1981C258.RSP;BCN1983.C258.835;INS_1683I1983C258.INS;';
	if($output!='' && $error==''){
		$objEBilling->SaveCommercialReportsList($output,$group_rs,$CL);
	}
	
}
?>