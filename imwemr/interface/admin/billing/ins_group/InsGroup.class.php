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
require_once("../../admin_header.php");
require_once("../../../../library/classes/common_function.php");

class InsGroup {
	public $grpID;
	var $operator_id;
	var $date_time;
	
	function __construct(){
		$this->operator_id = $_SESSION['authId'];
		$this->date_time = date('Y-m-d H:i:s');
	}
	function ins_grp_form(){
		
		$editId = isset($_GET['editid']) ? intval($_GET['editid']) : false;
		$btnCaption = 'Save Group';	
		if($editId){
		$btnCaption = 'Update Group';
		}
		$frm_text .= <<<DATA
				<div class="row pt10">
					<input type="hidden" name="editid" id="editid" value="">
					<div class="col-sm-1">
						Insurance Group:
					</div>
					<div class="col-sm-4">
						<input type="text" id="insGrpName" name="insGrpName" value="$phrase2" class="form-control">
					</div>
					<div class="col-sm-4">
						<input style="width:120px;" type="button" name="btn_save" id="btn_save" class="btn btn-success" value="$btnCaption" onclick="saveGrp();" />
					</div>
				</div>
DATA;
		return $frm_text;
	}
	function get_ins_grps(){
		
		$div1_height = $_SESSION['wn_height']-825;
		/*--FETCHING RECORDS FROM TABLE smart_tags--*/
		$records = '';
		$fetch_query = "SELECT id,title from  ins_comp_groups WHERE delete_status=0"; //fetching main categories.
		$fetch_result = imw_query($fetch_query);
		if(imw_num_rows($fetch_result)>0){
			$records .= '<div class="row">';
			$cellCounter = 1;
			while($fetch_rs = imw_fetch_assoc($fetch_result))
			{
				$insGrpId = $fetch_rs['id'];
				$insGrpName = core_refine_user_input($fetch_rs['title']);
				$records .= '<div class="col-sm-3" style="margin-top:10px;">
					<div class="bg-danger pt10 pointer" style="padding-left:10px;">
					<span>
						<a href="javascript:delGrp('.$insGrpId.');"><img class="noborder" src="../../../../library/images/close_small.png"></a>
					</span>
					&nbsp;<span onclick="getInsComp('.$insGrpId.',1,\'\',\''.$insGrpName.'\');editGrp('.$insGrpId.',\''.$insGrpName.'\',\'EmptyInsCom\')">'.core_extract_user_input($insGrpName).'</span><br /><br />
					</div>
				</div>';
				$cellCounter ++;
				}//end of while.
			}else{
			$records .= '<span class="row text-center">No Record Found</span>';
		}
		$records .= '</div>';
		return $records;
	}
	function saveGrp(){
		$grpid = $this->grpID;
		$insGrpName = isset($_GET['insGrpName']) ? core_refine_user_input(trim($_GET['insGrpName'])) : '';
		if($insGrpName != ''){
			$res_select = imw_query("SELECT * FROM ins_comp_groups WHERE title = '".$insGrpName."' AND delete_status = '0'");
			$row = imw_fetch_array($res_select);
			if(imw_num_rows($res_select)<=0){
				$query_mode = $grpid ? 'UPDATE ' : 'INSERT INTO ';
				$save_query = $query_mode."ins_comp_groups SET 
						title = '".$insGrpName."'
						";
				if(!$grpid){
					$save_query .= " , createdby = ".$this->operator_id.", 
									createdon = '".$this->date_time."'";
				}
				if($grpid){
					$save_query .= " WHERE id=".$grpid;
				}//echo $save_query;die();
				$save_result = imw_query($save_query);
				if($save_result)
				echo 'Group saved successfully.~~';
			}else{
				if($row['id'] != $grpid)
				echo 'Duplicate groups not allowed.~~';
				else echo 'Record saved successfully.~~';
			}
		}//end of main if (saving record).
	}
	function delGrp(){
		
		if($this->grpID){
			$del_result = imw_query("UPDATE ins_comp_groups SET delete_status=1,deletedby='".$this->operator_id."', deletedon='".$this->date_time."' WHERE id='".$this->grpID."'");
			if($del_result){echo 'Group deleted successfully.';}
			else{echo 'Unable to delete group.';}
		}
	}
	function getInsComp(){
		if($this->grpID){
			$grpID = $this->grpID;
			$page = isset($_GET['page'])?$_GET['page']:'';
			$name = isset($_GET['text'])?$_GET['text']:'';
			//include_once("../add_insurance/insurance_companies.php");
		}
	}
}

$do = isset($_GET['do']) ? trim($_GET['do']) : false;
$id = isset($_GET['id']) ? intval($_GET['id']) : false;
$objInsGrp = new InsGroup();
$objInsGrp->grpID = $id;
//echo $objInsGrp->grpID;die();
switch($do){
	case 'getAllMainTags':
		$objInsGrp->get_ins_grps();
		break;
	case 'saveGrp':
			$objInsGrp -> saveGrp();
			echo $objInsGrp -> get_ins_grps();
			echo $objInsGrp->ins_grp_form();
		break;
	case 'delGrp':
			$objInsGrp ->delGrp();
			echo $objInsGrp ->get_ins_grps();
			echo $objInsGrp->ins_grp_form();
		break;
	case 'getInsComp':
		$fetch_query = "SELECT title from  ins_comp_groups WHERE id='".$objInsGrp->grpID."' limit 0,1"; //fetching main categories.
		$fetch_result = imw_query($fetch_query);
		$fetch_row = imw_fetch_assoc($fetch_result);
		echo "<div style='height:20px;font-weight:bold'>Insurance Group : ".$fetch_row['title']."</div>";
		$objInsGrp ->getInsComp();
		break;
}
?>