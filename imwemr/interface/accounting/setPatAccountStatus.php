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
$saved=0;
$acId=$_REQUEST['acId'];
$patId = $_REQUEST['patId'];
$otherVal = $_REQUEST['otherVal'];
$selectedText = $_REQUEST['selectedText'];
$oldStatus = $_REQUEST['oldStatus'];
$callFrom = $_REQUEST['callFrom'];
if(!$patId)
{
	$patId = $_SESSION['patient'];
}
// GET ID OF COLLECTION STATUS
$collectionId = get_account_status_id_collections();
$collectionIdArr  = explode(',', $collectionId);

// SET VALUES

if($otherVal!='')
{
	$rs = imw_query("Insert into account_status SET status_name='".$otherVal."', deactive=0, del_status = 0, status_type = '' ");
	if($rs){
		$acId=imw_insert_id();
	}
}

if($acId>0){

	// ADD RECORD IN PATIENT NEXT HISTORY ACTION TABLE
/*	if($callFrom=='reports'){
		$selRs=imw_query("Select id, pat_account_status FROM patient_data WHERE id IN(".$patId.")");
		while($selRes = imw_fetch_array($selRs)){
			$rs = imw_query("Insert into patient_next_action_history SET 
			patient_id ='".$selRes['id']."',
			user_id ='".$_SESSION['authId']."',
			old_account_sts ='".$selRes['pat_account_status']."',
			new_account_sts ='".$acId."',
			change_date ='".date('Y-m-d')."',
			change_time ='".date('H:i:s')."'
			");
		}
	}else{
		$rs = imw_query("Insert into patient_next_action_history SET 
		patient_id ='".$patId."',
		user_id ='".$_SESSION['authId']."',
		old_account_sts ='".$oldStatus."',
		new_account_sts ='".$acId."',
		change_date ='".date('Y-m-d')."',
		change_time ='".date('H:i:s')."'
		");
	}*/
	
	// UPDATE patient data account status	
	$rs=imw_query("Update patient_data SET pat_account_status=".$acId." WHERE id IN(".$patId.")");
	// UPDATE ALL ENCOUNTER OF PATIENT HAVING totalBalance > 0
	$collectionDate = date('Y-m-d');
	if(in_array($acId, $collectionIdArr)){
		$rs=imw_query("Select encounter_id,totalBalance FROM patient_charge_list WHERE del_status='0' and collection!='true' AND totalBalance>0 AND patient_id IN(".$patId.")");
		while($res = imw_fetch_array($rs)){
			$updateStr = "UPDATE patient_charge_list SET collection = 'true',collectionAmount = '".$res['totalBalance']."',collectionDate = '".$collectionDate."' WHERE encounter_id ='".$res['encounter_id']."'";
			$updateRs = imw_query($updateStr);
			
			$updateStr = "UPDATE report_enc_detail SET collection = 'true',collectionAmount = '".$res['totalBalance']."',collectionDate = '".$collectionDate."' WHERE encounter_id ='".$res['encounter_id']."'";
			$updateRs = imw_query($updateStr);
			if($updateRs){ $saved=1; }			  
		}
	}else{
		$rs=imw_query("Select encounter_id,totalBalance FROM patient_charge_list WHERE del_status='0' and patient_id IN(".$patId.")");
		while($res = imw_fetch_array($rs)){
			$updateStr = "UPDATE patient_charge_list SET collection = 'false',collectionDate = '' WHERE encounter_id ='".$res['encounter_id']."'";
			$updateRs = imw_query($updateStr);
			
			$updateStr = "UPDATE report_enc_detail SET collection = 'false',collectionDate = '' WHERE encounter_id ='".$res['encounter_id']."'";
			$updateRs = imw_query($updateStr);
			if($updateRs){ $saved=1; }	
		}
	}
}

// MAKE DROP DOWN IF OTHER CASE
$acOptions ='';
if($callFrom!='reports'){
	if($otherVal!=''){
		$acRs = imw_query("Select * from account_status WHERE del_status=0 ORDER BY status_name");
		
		$acOptions = '';
		//$acOptions='<select name="account_status" id="account_status" class="selectpicker form-control" title="Patient Account Status" data-source="select" data-action="set_patient_status">';
		while($acRes = imw_fetch_array($acRs)){
			$sel = '';
			if($acRes['id']==$acId){ $sel='SELECTED'; }
			$acOptions.='<option value="'.$acRes['id'].'" '.$sel.' >'.$acRes['status_name'].'</option>';
		}
		
		if($_SESSION['sess_privileges']['priv_admin']==1){
			$acOptions.='<option value="other">Other</option>';
		}
		//$acOptions.='</select>';
	}
}
echo $saved.'~~~'.$acOptions;
?>