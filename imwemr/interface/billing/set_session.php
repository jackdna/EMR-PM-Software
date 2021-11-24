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
File: electronic_billing.php
Purpose: Electronic Billing Main interface.
Access Type: Direct Access (in frame) 
*/

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
$app_base			= new app_base();

$md="eb";
if($_REQUEST['md']!=""){
	$md=$_REQUEST['md'];
}

//Clear --
$app_base->clean_patient_session();

$_SESSION['alertShowForMedication']='';
$_SESSION['patient'] = "";
$_SESSION['patient'] = NULL;
$_SESSION['new_casetype'] = NULL;
$_SESSION['first_avail_alert']='';//from scheduler
unset($_SESSION['patient']);

//FormId
$_SESSION['form_id'] = "";
$_SESSION['form_id'] = NULL;
unset($_SESSION['form_id']);

$_SESSION["PT_DOC_ALERT_STATUS"] = "";
$_SESSION["PT_DOC_ALERT_STATUS"] = NULL;
unset($_SESSION["PT_DOC_ALERT_STATUS"]);

//FinalizedID	
$_SESSION['finalize_id'] = "";
$_SESSION['finalize_id'] = NULL;
unset($_SESSION['finalize_id']);

//Patient_Viewed	
$_SESSION['Patient_Viewed'] = "";
$_SESSION['Patient_Viewed'] = NULL;
unset($_SESSION['Patient_Viewed']);

//clear alertShowForThisSession session variable so that in new session alert could come
$_SESSION['alertShowForThisSession'] = "";
$_SESSION["alertShowForThisSession"] = NULL;
unset($_SESSION["alertShowForThisSession"]);

//clear alertImmShowForThisSession session variable so that in new session imm alert could come
$_SESSION['alertImmShowForThisSession'] = "";
$_SESSION["alertImmShowForThisSession"] = NULL;
unset($_SESSION["alertImmShowForThisSession"]);


$_SESSION['patient'] = xss_rem($_REQUEST['patient'], 3);	/** Reject parameter with arbitrary values - Security Fix */

$_SESSION['encounter_id'] = $_REQUEST['eid'];

//--- Code to save five recent patients ----
if($_SESSION['patient']!= ''){
	patient_monitor_daily("PATIENT_LOAD");
	$max_recent_pts = $GLOBALS["max_recent_search_cache"];
	$auth_id 		= $_SESSION['authId'];
	$patient_id 	= $_SESSION['patient'];
	$patientFindBy 	= $_SESSION['patientFindBy'];
	$qry = "select patient_id,recent_user_id from recent_users 
			where provider_id = $auth_id order by enter_date desc";
	$res = imw_query($qry);
	$patientRecent = false;
	if(imw_num_rows($res) > 0){
		$next_recent_user_id = '';
		$i = 0;
		while($qryRes = imw_fetch_assoc($res)){
			$recent_user_id = $qryRes['recent_user_id']; 
			if($_SESSION['patient'] == $qryRes['patient_id']){
				$patientRecent = true;
				$recent_user_id = $qryRes['recent_user_id'];
				$curDate = date('Y-m-d H:i:s');
				$qry = "update recent_users set patient_id = $patient_id,
							 enter_date = '$curDate' where recent_user_id = $recent_user_id";
				imw_query($qry);
			}
			$i++;
			if($i==$max_recent_pts && !$patientRecent) $next_recent_user_id = $recent_user_id;
		}
	}	
	if($patientRecent == false){
		$curDate = date('Y-m-d H:i:s');	
		if(imw_num_rows($res) == $max_recent_pts){
			$qry = "update recent_users set patient_id = $patient_id,
					provider_id = $auth_id,patientFindBy = '$patientFindBy',enter_date = '$curDate'  
					where recent_user_id = $next_recent_user_id";
		}else{			
			$qry = "insert into recent_users set patient_id = $patient_id,
					provider_id = $auth_id,patientFindBy = '$patientFindBy',enter_date = '$curDate'";			
		}
		imw_query($qry);
	}
}


header("Location:../core/index.php?md=".$md."&ptid=".$_REQUEST['patient']);



?>
