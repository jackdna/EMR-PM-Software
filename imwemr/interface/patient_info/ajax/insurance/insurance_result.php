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

include_once('../../../../config/globals.php');

$dofrom = isset($_REQUEST['dofrom']) ? trim($_REQUEST['dofrom']) : false; //acc_reviewpt

$i1providerRCOIdV = "";
$providerRCOId = $_REQUEST["providerRCOId"];
if(constant("EXTERNAL_INS_MAPPING") == "YES"){
	$qryGetIdxInvRCOId = "SELECT invision_plan_code, invision_plan_description, IDX_description, IDX_FSC 
							FROM idx_invision_rco WHERE id = '".$providerRCOId."' LIMIT 1";
	$rsGetIdxInvRCOId = imw_query($qryGetIdxInvRCOId);
	if(imw_num_rows($rsGetIdxInvRCOId) > 0){
		$dbInvisionPlanCode = $dbInvisionPlanDescription = $dbIDXDescription = $dbIDXFSC = "";
		$rowGetIdxInvRCOId = imw_fetch_row($rsGetIdxInvRCOId);
		$dbInvisionPlanCode = $rowGetIdxInvRCOId[0];
		$dbInvisionPlanDescription = $rowGetIdxInvRCOId[1];
		$dbIDXDescription = $rowGetIdxInvRCOId[2];
		$dbIDXFSC = $rowGetIdxInvRCOId[3];
	}					
}
$data = array();
//$data = '<span class="closeBtn" onclick="$(\'#ins_show_div\').hide();"></span><table>';

$insuranceDetail = get_insurance_details($id);
	if($dofrom && $dofrom=='acc_reviewpt'){
		$q1 = "SELECT * FROM insurance_data WHERE provider ='".$id."' AND pid ='".$_SESSION['patient']."'";
		$r1 = imw_query($q1);
		if($r1 && imw_num_rows($r1)>0){
			$rs1 = imw_fetch_assoc($r1);
			$data['Policy#:'] = $rs1['policy_number'];
			//$data .= '<tr><td><b>Policy#: </b></td><td>'.$rs1['policy_number'].'</td></tr>';
		}
	}

	if($insuranceDetail->City){
		$city = $insuranceDetail->City.', '.$insuranceDetail->State.' '.$insuranceDetail->Zip;
	}
	if(constant("EXTERNAL_INS_MAPPING") == "YES"){
		if($dbInvisionPlanCode){
		$data['Name:'] = $dbInvisionPlanCode.' - '.$dbInvisionPlanDescription.' - '.$dbIDXDescription.' - '.$dbIDXFSC;
		//$data .= '<tr><td><b>Name: </b></td><td>'.$dbInvisionPlanCode.' - '.$dbInvisionPlanDescription.' - '.$dbIDXDescription.' - '.$dbIDXFSC.'</td></tr>';
		}
	}
	else{
		if($insuranceDetail->in_house_code){
		$data['Name:'] = $insuranceDetail->in_house_code.' - '.$insuranceDetail->name;
		//$data .= '<tr><td><b>Name: </b></td><td>'.$insuranceDetail->in_house_code.' - '.$insuranceDetail->name.'</td></tr>';
		}
	}
	if($insuranceDetail->contact_address){
		$address = $insuranceDetail->contact_address;
	}
	if($city){
		$address .=' - '.$city;
	}
	if($address){
	$data['Address:'] = $address;
	}
	//$data .= '<tr><td><b>Address: </b></td><td>'.$address.'</td></tr>';
	if($insuranceDetail->phone != ''){
		$data['Phone#:'] = $insuranceDetail->phone;
		//$data .= '<tr><td><b>Phone#: </b></td><td>'.$insuranceDetail->phone.'</td></tr>';
	}
	echo json_encode($data);
	//$data .= '</table>';
	//print $data;



//---------END EXTERNAL MAPPING CODE---------
/*$insuranceDetail = $objDataManage->getInsuranceDetails($id);
	if($insuranceDetail->City){
		$city = $insuranceDetail->City.', '.$insuranceDetail->State.' '.$insuranceDetail->Zip;
	}
$data = $insuranceDetail->in_house_code.' - '.$insuranceDetail->name;
if($insuranceDetail->contact_address){
	$data .= '<br>'.$insuranceDetail->contact_address;
}
if($city){
	$data .=' - '.$city;
}
$data .= '<br>'.$insuranceDetail->phone;
print $data; */
?>
