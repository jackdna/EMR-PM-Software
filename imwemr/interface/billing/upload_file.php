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
File: upload_file.php
Purpose: Receives and saved uploaded reports (999/997).
Access Type: Direct Access 
*/
set_time_limit(30);
require_once(dirname(__FILE__).'/../../config/globals.php');
ob_start();
require_once(dirname(__FILE__).'/../../library/classes/class.electronic_billing.php');
require_once(dirname(__FILE__).'/../../library/classes/billing_functions.php');
$objEBilling = new ElectronicBilling();

if($saveUploadFile == 'Upload'){
	if($_FILES['uploadFile']['name']){
		$fileName = $_FILES['uploadFile']['name'];
		$file_mime = check_txt_mime($_FILES['uploadFile']['tmp_name']);
		if(empty($fileName) === false && $file_mime){			
			$extName	= substr($fileName,-4);
			$file_data = file_get_contents($_FILES['uploadFile']['tmp_name']);
			
			$respType = $objEBilling->checkCLresponseType($file_data);
			$sent_to		= 'PI';
			$reference		= '';
			$date			= date('D M d h:i:s Y'); //Mon Aug 25 05:50:51 2008
			$subject		= $ClaimFile;
			$size			= '';
			$response		= $response;
			if($respType=='999'){//save report also.
				//--FIRST INSERT THE 999/997 DATA TO TABLE----
				$CL_rpt_q = "INSERT INTO emdeon_reports SET 
							 ws_file_name		= '".$fileName."', 
							 report_data		= '".$file_data."', 
							 report_recieve_date= '".date('Y-m-d H:i:s')."', 
							 operator_id		= '".$_SESSION['authId']."', 
							 wsUserID 			= '',
							 report_status		= '0', 
							 group_id 			= ''";
				$CL_rpt_res	= imw_query($CL_rpt_q);
				$report_id	= imw_insert_id();
				
			}
					
		}else{
			die('Invalid File Type uploaded');
		}
	}
}
ob_end_clean();
header("Location: get_batch_file_report.php#?emd_page=1&page=1");
?>
