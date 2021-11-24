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

?><?php set_time_limit(600);
require_once("../../../config/globals.php");

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'ques';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';

$strComboMedTab = xss_rem($_REQUEST['f_strComboMedTab']);	/** Reject parameter with arbitrary values - Security Fix */
$strComboMedTab1 = xss_rem($_REQUEST['f_strComboMedTab1']);	/** Reject parameter with arbitrary values - Security Fix */

switch($task){
	case 'delete':
		$id = $_POST['pkId'];

		imw_query("DELETE FROM med_hx_question_answer_options where question_id IN(".$id.")");
		imw_query("DELETE FROM admn_medhx_tab  where admn_medhx_question_id  IN(".$id.")");
		$res = imw_query("DELETE FROM admn_medhx where id IN(".$id.")");
	
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
		$newInsert=0;
		$query_part = "";
		foreach($_POST as $k=>$v){
			if($k=='spl_id' || $k=='ques' || $k=='answer_type'){
				$query_part .= $k."='".htmlentities(addslashes($v))."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		if($id==''){
			$q = "INSERT INTO admn_medhx SET ".$query_part;
			$newInsert=1;
		}else{
			$q = "UPDATE admn_medhx SET ".$query_part." WHERE id='".$id."'";
		}
		$res = imw_query($q);

		// ADD MEDHX TAB
		if($newInsert==1){
			$id = imw_insert_id();
			$qryInsertMedTab = "Insert into admn_medhx_tab SET admn_medhx_question_id='".$id."', tab_name='".$strComboMedTab."'";
			imw_query($qryInsertMedTab);
		}

		// ADD/UPDATE ANSWERS
		if($id != "" && $id > 0){
			imw_query("UPDATE med_hx_question_answer_options SET del_status = 1 WHERE question_id = '".$id."'");
		}
		for($i=1; $i<=$_POST['totAnswerOpts']; $i++){
			$strTxtAnsOptionVal = htmlentities(addslashes($_POST['txtAnsOptionArr'.$i]));
			if(empty($_POST['hidId'.$i]) == true){
				if(empty($strTxtAnsOptionVal) == false){
					$qryInsertMedQueOption = "Insert into med_hx_question_answer_options SET question_id='".$id."', 
											  option_value='".$strTxtAnsOptionVal."', 
											  row_created_by='".$_SESSION['authId']."',
											  row_create_date_time=NOW()";
					$rsInsertMedQueOption = imw_query($qryInsertMedQueOption);
				}
			}
			elseif(empty($_POST['hidId'.$i])==false){
				$qryUpdateMedQueOption = "Update med_hx_question_answer_options SET 
										  question_id = '".$id."',
										  option_value = '".$strTxtAnsOptionVal."',
										  modified_by = '".$_SESSION['authId']."',
										  row_modify_date_time = NOW(),
										  del_status = 0 
										  WHERE id = '".$_POST['hidId'.$i]."'";
				$rsUpdateMedQueOption = imw_query($qryUpdateMedQueOption);
			}
		}
		
							
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';
		}
		break;
	case 'show_list':
		$q = "Select admn_medhx.id, admn_medhx.spl_id, admn_medhx.ques, admn_medhx.spl_id, admn_medhx.answer_type FROM admn_medhx 
		LEFT JOIN admn_speciality  ON admn_speciality.id=admn_medhx.spl_id  
		LEFT JOIN admn_medhx_tab ON admn_medhx_tab.admn_medhx_question_id = admn_medhx.id 
		WHERE admn_medhx.status='0'
		AND (admn_medhx_tab.tab_name = '".$strComboMedTab."' or admn_medhx_tab.tab_name = '".$strComboMedTab1."') 
		ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$arrCategories = spl_categories();
		
		echo json_encode(array('records'=>$rs_set,'arrCategories'=>$arrCategories));
		break;
	default: 
}

function spl_categories()	{
	$arrCategories=array();
	$q="SELECT * FROM admn_speciality WHERE status='0' ORDER BY name";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$result=array();
		while($rs=imw_fetch_assoc($res)){
			$arrCategories[$rs['id']]=$rs;
		}
	}
	return $arrCategories;
}

?>
