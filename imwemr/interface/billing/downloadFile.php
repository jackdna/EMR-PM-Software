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
File: downloadFile.php
Purpose: Download batch file data.
Access Type: Direct Access
*/

//-- DOWNLOAD ELECTRONICS BATCH FILE ---------
require_once(dirname(__FILE__).'/../../config/globals.php');

$era_id = xss_rem($era_id, 3);	/* Reject parameter/variable with arbitrary values - Security Fix */

if($era_id){
	$query = "select file_name,file_contents from electronicfiles_tbl where id = '$era_id' limit 0,1";
	$res = imw_query($query);
	while($row = imw_fetch_array($res)){
		$file_name = $row['file_name'];
		$file_contents = stripslashes($row['file_contents']);
		downloadFiles($file_name,$file_contents);
	}
}

if($_REQUEST['batch_file']){
	$file_name=base64_decode($_REQUEST['batch_file']);
	downloadFiles($file_name,'');
}

if($file_id){
	$reportDetails = $objManageData->__getEmdeonReport($file_id);
	$fileContent = stripslashes($reportDetails[0]['report_data']);
	$report_recieve_date = preg_replace('/(-)|(:)|( )/','_',$reportDetails[0]['report_recieve_date']);
	$new_file_name = 'emdeon_report_'.$report_recieve_date.'.txt';
	downloadFiles($new_file_name,$fileContent);
}

if($batch_file_submitte_id){
	$res = imw_query("select file_name,file_data from batch_file_submitte where batch_file_submitte_id = '$batch_file_submitte_id' LIMIT 0,1");
	$fileQryRes = imw_fetch_assoc($res);
	$file_name = $fileQryRes['file_name'];
	$file_data = stripslashes($fileQryRes['file_data']);
//	$file_data = str_replace("*","\r\n",$file_data);
	downloadFiles($file_name,$file_data);
}

if(empty($rqVSRecId) === false){
	$query = imw_query("select vs_file_name, vs_file_data from vision_share_batch_receive_list where id = '$rqVSRecId' LIMIT 0,1");
	$fileQryRes = imw_fetch_assoc($query);
	$file_name = $fileQryRes['vs_file_name'];
	$file_data = stripslashes($fileQryRes['vs_file_data']);
	
	if(empty($replace_star) === false){	
		$file_data = str_replace("*","\r\n",$file_data);
	}
	
	downloadFiles($file_name,$file_data);
}

if($_REQUEST['day_charges_file']){
	$file_name=base64_decode($_REQUEST['day_charges_file']);
	downloadFiles($file_name,'');
}
?>