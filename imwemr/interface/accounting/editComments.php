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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
$patient_id=$_SESSION['patient'];
$operatorName = $_SESSION['authUser'];
$authUserID = $_SESSION['authUserID'];
	//--------------------	GETTING OPERATOR NAME --------------------//
		$getOperatorNameStr = "SELECT * FROM users";
		$getOperatorNameQry = imw_query($getOperatorNameStr);
		while($getOperatorNameRow = imw_fetch_array($getOperatorNameQry)){
			$OperatorId = $getOperatorNameRow['id'];
			$OperatorFName = $getOperatorNameRow['fname'];
			$OperatorMName = $getOperatorNameRow['mname'];
			$OperatorLName = $getOperatorNameRow['lname'];		
			$OperatorName_arr[$OperatorId] = substr($OperatorFName,0,1).substr($OperatorLName,0,1);
			$OperatorNameFull_arr[$OperatorId] = $OperatorLName.', '.$OperatorFName;
		}
	//--------------------	GETTING OPERATOR NAME --------------------//

$today = getdate();
$today_year = $today['year'];
$today_mon = $today['mon'];
$today_day = $today['mday'];
if($today_day<=9){
	$today_day='0'.$today_day;
}
if($today_mon<=9){
	$today_mon='0'.$today_mon;
}
$todate = $today_year."-".$today_mon."-".$today_day;
$totime = date('H:i:s');
$commentId = $_REQUEST['commId'];
$taskDone = (isset($_REQUEST['taskDone']) && $_REQUEST['taskDone']!='')?trim($_REQUEST['taskDone']):'';
if($taskDone == '2') {
    $updateCommentsStr = "UPDATE paymentscomment SET
						task_done ='$taskDone'
						WHERE commentId = '$commentId'";
    $updateCommentsQry = imw_query($updateCommentsStr);
    if($updateCommentsQry) {
        $task_status=1;
        $sql ="Update tm_assigned_rules set status='".$task_status."', operatorid='".$_SESSION['authId']."' where payment_comtId = '$commentId'";
        imw_query($sql);
    }
    echo 'success';
} else {
    $newComments = core_refine_user_input($_REQUEST['newComments']);
    $typeComment = $_REQUEST['typeComment'];
    $reminder_date = getDateFormatDB($_REQUEST['reminder_date']);
	$type_task_for = $_REQUEST['type_task_for'];
    $type_task_for_exp = explode(',',$type_task_for);
    $task_assign=1;
    $task_assign_date = '';
    $task_modify_date = '';
    $task_assign_by = '';
    if($type_task_for!='null' && empty($type_task_for) == false && is_array($type_task_for_exp) && count($type_task_for_exp) > 0){
		$task_assign = 2;
        $task_assign_date = date('Y-m-d H:i:s');
        $task_modify_date = date('Y-m-d H:i:s');
        $task_assign_by = $_SESSION['authId'];
    } else {
        $type_task_for='';
    }
	$task_on_reminder=(isset($_REQUEST['task_on_reminder']) && $_REQUEST['task_on_reminder']=='yes') ?'1':'0';
    $updateCommentsStr = "UPDATE paymentscomment SET
                            encComments = '$newComments',
                            commentsType = '$typeComment',
                            encCommentsDate = '$todate',
                            encCommentsTime = '$totime',
                            encCommentsOperatorId = '$authUserID',
                            reminder_date ='$reminder_date',
							task_assign_for='$type_task_for',
                            task_assign='$task_assign',
                            task_assign_date='$task_assign_date',
                            task_modify_date='$task_modify_date',
                            task_assign_by='$task_assign_by',
							task_onreminder='".$task_on_reminder."'
                            WHERE commentId = '$commentId'";
    $updateCommentsQry = imw_query($updateCommentsStr);
    if($updateCommentsQry) {
        $sql ="Update tm_assigned_rules set changed_value='".$newComments."',reminder_date='".$reminder_date."',task_on_reminder='".$task_on_reminder."' where payment_comtId = '$commentId' and status=0 ";
        imw_query($sql);
    }
    $todate = $today_mon."-".$today_day."-".$today_year.' '.date('h:i A', strtotime($totime));
	
	$task_assign_for_name="Not Assigned";
	if($type_task_for!='' && count($type_task_for_exp)>0){
		if(count($type_task_for_exp)>1){
			$task_assign_for_name="Multi";
		}else{
			$task_assign_for_name=$OperatorNameFull_arr[$type_task_for];
		}
	}
    
    $return =array();
    $return['newComments'] =nl2br(core_extract_user_input($newComments));
    $return['todate'] =$todate;
    $return['OperatorName'] =$OperatorName_arr[$authUserID];
    $return['task_assign_for_name'] =$task_assign_for_name;
    $return['type_task_for'] =$type_task_for;
	$return['task_on_reminder'] =$task_on_reminder;
    
    echo json_encode($return);
    //echo $newComments = nl2br(core_extract_user_input($newComments))."~~~".$todate."~~~".$OperatorName_arr[$authUserID]."~~~".$task_assign_for_name;
}
?>
