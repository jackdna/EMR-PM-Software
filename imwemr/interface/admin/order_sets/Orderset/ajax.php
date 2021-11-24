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
require_once("../../../../config/globals.php");
set_time_limit(600);

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'orderset_name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';
//ajax.php?task=show_list&s=Active&so=pos_prac_code&soAD=ASC
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q		= "Update order_sets set delete_status = '1',modified_by='".$_SESSION['authId']."',modified_on=now() where id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		$query_part = "";

		foreach($_POST as $k=>$v){
			if($k!='id' && $k!='orders_dx_code' && $k!='order_id' && $k!='consult_letter_id'){
				if($k=='sel_order_id' || $k=='sel_consult_letter_id'){
					$k= substr($k, 4, strlen($k));
				}
				if($k=='sel_orders_dx_code') {
					$k= 'orders_dx_icd10_code';	
				}
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		
		if($id==''){
			$q = "INSERT INTO order_sets SET ".$query_part;
		}else{
			$q = "UPDATE order_sets SET ".$query_part." WHERE id='".$id."'";
		}
		$res = imw_query($q);
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.'.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
		$arrAllDxCodes= getAllDxCodes();
		$arrAllOrderIds =getAllOrderIds();
		$arrAllConsultLetters =getAllConsultLetters();

		$q = "Select id,orderset_name,orders_dx_icd10_code AS orders_dx_code,order_id,order_set_option,recall_code,consult_letter_id 
		FROM order_sets WHERE delete_status ='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
				if($rs['orders_dx_code']!=''){
					$arrOrderedDxCodes[] = $rs['orders_dx_code'];
				}
			}
		}
		if(count($arrOrderedDxCodes)>0)
		$strOrderedDxCodes = implode(',', $arrOrderedDxCodes);
		else
		$strOrderedDxCodes = '';
		
		echo json_encode(array('records'=>$rs_set, 'arrAllDxCodes'=>$arrAllDxCodes[0], 'arrAllDxIds'=>$arrAllDxCodes[1], 'arrAllDxCds'=>$arrAllDxCodes[2], 'arrAllOrderIds'=>$arrAllOrderIds[0], 'arrAllOrderIdsNew'=>$arrAllOrderIds[1], 'arrAllNameNew'=>$arrAllOrderIds[2], 'arrAllConsultLetters'=>$arrAllConsultLetters[0], 'arrAllConsultLettersId'=>$arrAllConsultLetters[1], 'arrAllConsultLettersName'=>$arrAllConsultLetters[2], 'strOrderedDxCodes'=>$strOrderedDxCodes));
		break;
	default: 
}

function getAllDxCodes(){
	$arrAllDxCodes=array();
	//$qry = "Select diagnosis_id,d_prac_code,diag_description FROM diagnosis_code_tbl ORDER BY diag_description ASC";
	$qry = "Select id AS diagnosis_id, icd10 AS d_prac_code, icd10_desc AS diag_description FROM icd10_data ORDER BY diag_description ASC";
	$dxRs=imw_query($qry);
	$dxOptions = '';
	while($dxRes  =imw_fetch_array($dxRs)){
		$diagnosis_id = $dxRes['diagnosis_id'];
		$d_prac_code = $dxRes['d_prac_code'];
		$d_prac_code .= '  '.$dxRes['diag_description'];
		$arrAllDxCodes[$diagnosis_id] = $d_prac_code;

		$arrAllDxIds[] = $diagnosis_id;
		$arrAllDxCds[] = $d_prac_code;
		
	}
	return array($arrAllDxCodes,$arrAllDxIds,$arrAllDxCds);
}

function getAllOrderIds(){
	$arrAllOrderIds=array();
	$qry = "Select id,name from order_details 
			where delete_status ='0' 
			AND (
				(order_type_id != '' and order_type_id != NULL and order_type_id != 0) 
				OR 
				(o_type IN ('Meds','Medication','Labs','Lab','Imaging/Rad','Radiology/Imaging','Imaging','Procedure/Sx','Surgery','Procedural','Information/Instructions','Information'))
				) 
			ORDER BY name
			";
	$rs1=imw_query($qry);
	while($res1=imw_fetch_array($rs1)){
		$id = $res1['id'];
		$name = trim(ucwords($res1['name']));
		$arrAllOrderIds[$id] = $name;
		$arrAllOrderIdsNew[] = $id;
		$arrAllNameNew[] = $name;
	}
	return array($arrAllOrderIds,$arrAllOrderIdsNew,$arrAllNameNew);
}

function getAllConsultLetters(){
	$arrAllConsultLetters=array();
	$t_responsible = "Select consultLeter_id,consultTemplateName FROM consultTemplate ORDER BY consultTemplateName";
	$sqlt_responsible = imw_query($t_responsible);
	while($res_responsible=imw_fetch_array($sqlt_responsible)){
		$consultLeter_id = $res_responsible['consultLeter_id'];
		$consultTemplateName = ucfirst($res_responsible['consultTemplateName']);
		$arrAllConsultLetters[$consultLeter_id] = $consultTemplateName;
		$arrAllConsultLettersId[] = $consultLeter_id;
		$arrAllConsultLettersName[] = $consultTemplateName;
	}
	return array($arrAllConsultLetters,$arrAllConsultLettersId,$arrAllConsultLettersName);
}
?>
