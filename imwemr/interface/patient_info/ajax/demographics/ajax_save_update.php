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
?>
<?php
/*	
File: delete_family_info.php
Purpose: Recieve Ajax Request to Delete Responsible Party/ License Image
Access Type: Direct 
*/
include_once("../../../../config/globals.php");
$patient_id = $_SESSION['patient'];

$return = array('success' => false, 'msg' => ''); 

if(isset($_REQUEST['respId']) &&  isset($_REQUEST['action']))
{
	$respId	= intval($_REQUEST['respId']);
	$action	= trim($_REQUEST['action']);	
	$return['action'] = $action; 
	/*--GET LICENSE PIC IF ANY--*/
	$lic_query = "SELECT licence_image FROM resp_party WHERE id=".$respId." AND patient_id=".$patient_id;
	$lic_result = imw_query($lic_query);
	$lic_rs = imw_fetch_array($lic_result); 
	$lic_file_name = $lic_rs['licence_image'];
	$lic_file = "../../../../data/".PRACTICE_PATH."/PatientId_".$patient_id."/".$lic_file_name;
	
	if($lic_file_name != '' && file_exists($lic_file)){
		unlink($lic_file);
	}
	
	if($action == 'delete_resp_license')
	{
		$respQuery = "Update resp_party Set licence_image = '' WHERE id=".$respId." AND patient_id=".$patient_id;	
		$msg	= "Responsible Party / Guarantor Driving License Deleted successfully";
	}
	else if($action == 'delete_resp_party')
	{
		$respQuery = "DELETE FROM resp_party WHERE id=".$respId." AND patient_id=".$patient_id;
		$msg = "Responsible Party/Guarantor deleted successfully"	;
	}
	
	$result = imw_query($respQuery);
	if($result) {
		$return['success'] = true;
		$return['msg'] 		 = $msg;
	}
	
}
elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == "delete_licence")
{
	$patId = $_SESSION['selectedPatientId'];
	$user	= $_SESSION['authUser'];
	$userId = $_SESSION['authUserID'];
	$date = date('Y-m-d H:i:s');
	
	$photo = imw_query("SELECT `licence_photo` FROM `patient_data` WHERE `id`='".$patId."'");
	$photo = imw_fetch_assoc($photo);
	$photo = $photo['licence_photo'];
	
	$del = imw_query("INSERT INTO `driving_licence_deleted`(
										 `patient_id`, `licence_photo`, `datetime`, `del_operator_id`, `del_operator`)
										 VALUES('".$patId."', '".$photo."', '".$date."', '".$userId."', '".$user."')");
	
	if($del){
		$delImg = imw_query("UPDATE `patient_data` SET `licence_photo`='' WHERE `id`='".$patId."'");
		if($delImg) $return['success'] = true;
	}
}
else if(isset($_REQUEST['action']) && $_REQUEST['action']=='change_external_id' && $_REQUEST['pt_id'] && $_REQUEST['new_external_id'])
{
	if($_SESSION['sess_privileges']['priv_admin']== 1)
	{
		$resp = 'Y';
		$pt_id = $_REQUEST['pt_id'];
		$new_external_id = trim($_REQUEST['new_external_id']);
		if($new_external_id && $pt_id)
		{
			$qry_external = "Select id From patient_data where External_MRN_1='".$new_external_id."' and id!='".$pt_id."'";
			$row_external = imw_query($qry_external) or $resp = imw_error();
			if(imw_num_rows($row_external) > 0){
				$resp="External ID already assign to another patient";	
			}else{
				$qry_update_id="UPDATE patient_data set External_MRN_1='".$new_external_id."' WHERE id='".$pt_id."'";
				$row_update_id=imw_query($qry_update_id) or $resp = imw_error();
				if($row_update_id) { $return['success'] =  true; $resp = 'Y'; }
			}	
		}
	}else{
		$resp = "You do not have admin priviliges to set External ID";
	}
	$return['msg'] = $resp;
}

echo json_encode($return);
?>
